from fastapi import FastAPI, APIRouter, HTTPException, Depends, status
from fastapi.security import HTTPBearer, HTTPAuthorizationCredentials
from dotenv import load_dotenv
from starlette.middleware.cors import CORSMiddleware
from motor.motor_asyncio import AsyncIOMotorClient
import os
import logging
from pathlib import Path
from pydantic import BaseModel, Field, EmailStr
from typing import List, Optional
import uuid
from datetime import datetime, timedelta
import jwt
from passlib.context import CryptContext
import hashlib

ROOT_DIR = Path(__file__).parent
load_dotenv(ROOT_DIR / '.env')

# MongoDB connection
mongo_url = os.environ['MONGO_URL']
client = AsyncIOMotorClient(mongo_url)
db = client[os.environ['DB_NAME']]

# Security
SECRET_KEY = "karaoke-senso-secret-key-2025"
ALGORITHM = "HS256"
ACCESS_TOKEN_EXPIRE_MINUTES = 1440  # 24 hours

pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")
security = HTTPBearer()

# Create the main app
app = FastAPI(title="Karaoke Sensō API")
api_router = APIRouter(prefix="/api")

# Models
class User(BaseModel):
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    email: EmailStr
    password_hash: str
    is_admin: bool = False
    created_at: datetime = Field(default_factory=datetime.utcnow)

class UserLogin(BaseModel):
    email: EmailStr
    password: str

class Event(BaseModel):
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    name: str
    municipality: str
    venue: str
    date: datetime
    max_participants: int = 50
    created_at: datetime = Field(default_factory=datetime.utcnow)

class EventCreate(BaseModel):
    name: str
    municipality: str
    venue: str
    date: datetime
    max_participants: int = 50

class Registration(BaseModel):
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    full_name: str
    age: int
    municipality: str
    sector: str  # Educativo, Empresarial, Cultural, etc.
    phone: str
    email: EmailStr
    event_id: str
    payment_status: str = "pendiente"  # pendiente, pagado
    video_url: Optional[str] = None
    created_at: datetime = Field(default_factory=datetime.utcnow)

class RegistrationCreate(BaseModel):
    full_name: str
    age: int
    municipality: str
    sector: str
    phone: str
    email: EmailStr
    event_id: str

class Brand(BaseModel):
    id: str = Field(default_factory=lambda: str(uuid.uuid4()))
    name: str
    logo_url: str
    created_at: datetime = Field(default_factory=datetime.utcnow)

class BrandCreate(BaseModel):
    name: str
    logo_url: str

class Token(BaseModel):
    access_token: str
    token_type: str

# Helper functions
def verify_password(plain_password, hashed_password):
    return pwd_context.verify(plain_password, hashed_password)

def hash_password(password):
    return pwd_context.hash(password)

def create_access_token(data: dict, expires_delta: Optional[timedelta] = None):
    to_encode = data.copy()
    if expires_delta:
        expire = datetime.utcnow() + expires_delta
    else:
        expire = datetime.utcnow() + timedelta(minutes=15)
    to_encode.update({"exp": expire})
    encoded_jwt = jwt.encode(to_encode, SECRET_KEY, algorithm=ALGORITHM)
    return encoded_jwt

async def get_current_user(credentials: HTTPAuthorizationCredentials = Depends(security)):
    credentials_exception = HTTPException(
        status_code=status.HTTP_401_UNAUTHORIZED,
        detail="Could not validate credentials",
        headers={"WWW-Authenticate": "Bearer"},
    )
    try:
        payload = jwt.decode(credentials.credentials, SECRET_KEY, algorithms=[ALGORITHM])
        email: str = payload.get("sub")
        if email is None:
            raise credentials_exception
    except jwt.PyJWTError:
        raise credentials_exception
    
    user = await db.users.find_one({"email": email})
    if user is None:
        raise credentials_exception
    return User(**user)

# Initialize admin user
async def create_admin_user():
    admin_exists = await db.users.find_one({"email": "admin@karaokesenso.com"})
    if not admin_exists:
        admin_user = User(
            email="admin@karaokesenso.com",
            password_hash=hash_password("Senso2025*"),
            is_admin=True
        )
        await db.users.insert_one(admin_user.dict())
        print("Admin user created: admin@karaokesenso.com")

# Routes
@api_router.get("/")
async def root():
    return {"message": "Karaoke Sensō API"}

# Authentication
@api_router.post("/auth/login", response_model=Token)
async def login(user_data: UserLogin):
    user = await db.users.find_one({"email": user_data.email})
    if not user or not verify_password(user_data.password, user["password_hash"]):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Incorrect email or password",
            headers={"WWW-Authenticate": "Bearer"},
        )
    
    access_token_expires = timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
    access_token = create_access_token(
        data={"sub": user["email"]}, expires_delta=access_token_expires
    )
    return {"access_token": access_token, "token_type": "bearer"}

# Events
@api_router.get("/events", response_model=List[Event])
async def get_events():
    events = await db.events.find().to_list(1000)
    return [Event(**event) for event in events]

@api_router.post("/events", response_model=Event)
async def create_event(event_data: EventCreate, current_user: User = Depends(get_current_user)):
    if not current_user.is_admin:
        raise HTTPException(status_code=403, detail="Admin access required")
    
    event = Event(**event_data.dict())
    await db.events.insert_one(event.dict())
    return event

# Registrations
@api_router.post("/registrations", response_model=Registration)
async def create_registration(registration_data: RegistrationCreate):
    # Check if event exists
    event = await db.events.find_one({"id": registration_data.event_id})
    if not event:
        raise HTTPException(status_code=404, detail="Event not found")
    
    # Check if user already registered for this event
    existing = await db.registrations.find_one({
        "email": registration_data.email,
        "event_id": registration_data.event_id
    })
    if existing:
        raise HTTPException(status_code=400, detail="Already registered for this event")
    
    registration = Registration(**registration_data.dict())
    await db.registrations.insert_one(registration.dict())
    return registration

@api_router.get("/registrations", response_model=List[Registration])
async def get_registrations(current_user: User = Depends(get_current_user)):
    if not current_user.is_admin:
        raise HTTPException(status_code=403, detail="Admin access required")
    
    registrations = await db.registrations.find().to_list(1000)
    return [Registration(**reg) for reg in registrations]

@api_router.put("/registrations/{registration_id}/payment")
async def update_payment_status(
    registration_id: str, 
    payment_status: str,
    current_user: User = Depends(get_current_user)
):
    if not current_user.is_admin:
        raise HTTPException(status_code=403, detail="Admin access required")
    
    if payment_status not in ["pendiente", "pagado"]:
        raise HTTPException(status_code=400, detail="Invalid payment status")
    
    result = await db.registrations.update_one(
        {"id": registration_id},
        {"$set": {"payment_status": payment_status}}
    )
    
    if result.matched_count == 0:
        raise HTTPException(status_code=404, detail="Registration not found")
    
    return {"message": "Payment status updated"}

# Statistics
class Statistics(BaseModel):
    total_registrations: int
    participating_municipalities: int
    represented_sectors: int

@api_router.get("/statistics", response_model=Statistics)
async def get_statistics():
    # Count total registrations
    total_registrations = await db.registrations.count_documents({})
    
    # Count unique municipalities
    municipalities = await db.registrations.distinct("municipality")
    participating_municipalities = len(municipalities)
    
    # Count unique sectors
    sectors = await db.registrations.distinct("sector")
    represented_sectors = len(sectors)
    
    return Statistics(
        total_registrations=total_registrations,
        participating_municipalities=participating_municipalities,
        represented_sectors=represented_sectors
    )

# Brands
@api_router.get("/brands", response_model=List[Brand])
async def get_brands():
    brands = await db.brands.find().to_list(1000)
    return [Brand(**brand) for brand in brands]

@api_router.post("/brands", response_model=Brand)
async def create_brand(brand_data: BrandCreate, current_user: User = Depends(get_current_user)):
    if not current_user.is_admin:
        raise HTTPException(status_code=403, detail="Admin access required")
    
    brand = Brand(**brand_data.dict())
    await db.brands.insert_one(brand.dict())
    return brand

# Include router
app.include_router(api_router)

# CORS
app.add_middleware(
    CORSMiddleware,
    allow_credentials=True,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

# Startup
@app.on_event("startup")
async def startup_event():
    await create_admin_user()

@app.on_event("shutdown")
async def shutdown_db_client():
    client.close()

# Configure logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s'
)
logger = logging.getLogger(__name__)