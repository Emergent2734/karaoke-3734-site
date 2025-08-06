import requests
import sys
import json
from datetime import datetime, timedelta

class KaraokeAPITester:
    def __init__(self, base_url="https://7fd1a79b-09cd-4f7a-a9ac-4c94c80f68c1.preview.emergentagent.com"):
        self.base_url = base_url
        self.api_url = f"{base_url}/api"
        self.token = None
        self.tests_run = 0
        self.tests_passed = 0
        self.created_event_id = None
        self.created_registration_id = None

    def run_test(self, name, method, endpoint, expected_status, data=None, params=None):
        """Run a single API test"""
        url = f"{self.api_url}/{endpoint}"
        headers = {'Content-Type': 'application/json'}
        if self.token:
            headers['Authorization'] = f'Bearer {self.token}'

        self.tests_run += 1
        print(f"\n🔍 Testing {name}...")
        print(f"   URL: {url}")
        
        try:
            if method == 'GET':
                response = requests.get(url, headers=headers, params=params)
            elif method == 'POST':
                response = requests.post(url, json=data, headers=headers)
            elif method == 'PUT':
                response = requests.put(url, json=data, headers=headers, params=params)

            success = response.status_code == expected_status
            if success:
                self.tests_passed += 1
                print(f"✅ Passed - Status: {response.status_code}")
                try:
                    response_data = response.json()
                    print(f"   Response: {json.dumps(response_data, indent=2)[:200]}...")
                    return True, response_data
                except:
                    return True, {}
            else:
                print(f"❌ Failed - Expected {expected_status}, got {response.status_code}")
                try:
                    error_data = response.json()
                    print(f"   Error: {error_data}")
                except:
                    print(f"   Error: {response.text}")
                return False, {}

        except Exception as e:
            print(f"❌ Failed - Error: {str(e)}")
            return False, {}

    def test_root_endpoint(self):
        """Test root API endpoint"""
        return self.run_test("Root API", "GET", "", 200)

    def test_admin_login(self):
        """Test admin login"""
        success, response = self.run_test(
            "Admin Login",
            "POST",
            "auth/login",
            200,
            data={"email": "admin@karaokesenso.com", "password": "Senso2025*"}
        )
        if success and 'access_token' in response:
            self.token = response['access_token']
            print(f"   Token obtained: {self.token[:50]}...")
            return True
        return False

    def test_invalid_login(self):
        """Test invalid login credentials"""
        return self.run_test(
            "Invalid Login",
            "POST", 
            "auth/login",
            401,
            data={"email": "wrong@email.com", "password": "wrongpass"}
        )

    def test_get_events(self):
        """Test getting events list"""
        return self.run_test("Get Events", "GET", "events", 200)

    def test_create_event(self):
        """Test creating a new event"""
        future_date = (datetime.now() + timedelta(days=30)).isoformat()
        event_data = {
            "name": "Querétaro Capital",
            "municipality": "Querétaro", 
            "venue": "Plaza de Armas",
            "date": future_date,
            "max_participants": 50
        }
        
        success, response = self.run_test(
            "Create Event",
            "POST",
            "events", 
            200,
            data=event_data
        )
        
        if success and 'id' in response:
            self.created_event_id = response['id']
            print(f"   Created event ID: {self.created_event_id}")
            return True
        return False

    def test_create_event_unauthorized(self):
        """Test creating event without admin token"""
        old_token = self.token
        self.token = None
        
        future_date = (datetime.now() + timedelta(days=30)).isoformat()
        event_data = {
            "name": "Unauthorized Event",
            "municipality": "Test",
            "venue": "Test Venue", 
            "date": future_date
        }
        
        success, _ = self.run_test(
            "Create Event (Unauthorized)",
            "POST",
            "events",
            401,
            data=event_data
        )
        
        self.token = old_token
        return success

    def test_create_registration(self):
        """Test creating a user registration"""
        if not self.created_event_id:
            print("❌ Cannot test registration - no event created")
            return False
            
        registration_data = {
            "full_name": "Juan Pérez Test",
            "age": 25,
            "municipality": "Querétaro",
            "sector": "Educativo",
            "phone": "4421234567",
            "email": f"test_{datetime.now().strftime('%H%M%S')}@test.com",
            "event_id": self.created_event_id
        }
        
        success, response = self.run_test(
            "Create Registration",
            "POST",
            "registrations",
            200,
            data=registration_data
        )
        
        if success and 'id' in response:
            self.created_registration_id = response['id']
            print(f"   Created registration ID: {self.created_registration_id}")
            return True
        return False

    def test_duplicate_registration(self):
        """Test duplicate registration prevention"""
        if not self.created_event_id:
            print("❌ Cannot test duplicate registration - no event created")
            return False
            
        registration_data = {
            "full_name": "Duplicate Test",
            "age": 30,
            "municipality": "Querétaro", 
            "sector": "Empresarial",
            "phone": "4429876543",
            "email": "duplicate@test.com",
            "event_id": self.created_event_id
        }
        
        # Create first registration
        self.run_test("First Registration", "POST", "registrations", 200, data=registration_data)
        
        # Try duplicate registration
        return self.run_test(
            "Duplicate Registration",
            "POST",
            "registrations", 
            400,
            data=registration_data
        )

    def test_get_registrations_admin(self):
        """Test getting registrations as admin"""
        return self.run_test("Get Registrations (Admin)", "GET", "registrations", 200)

    def test_get_registrations_unauthorized(self):
        """Test getting registrations without admin token"""
        old_token = self.token
        self.token = None
        
        success, _ = self.run_test(
            "Get Registrations (Unauthorized)",
            "GET",
            "registrations",
            401
        )
        
        self.token = old_token
        return success

    def test_update_payment_status(self):
        """Test updating payment status"""
        if not self.created_registration_id:
            print("❌ Cannot test payment update - no registration created")
            return False
            
        return self.run_test(
            "Update Payment Status",
            "PUT",
            f"registrations/{self.created_registration_id}/payment",
            200,
            params={"payment_status": "pagado"}
        )

    def test_get_brands(self):
        """Test getting brands list"""
        return self.run_test("Get Brands", "GET", "brands", 200)

    def test_create_brand(self):
        """Test creating a brand"""
        brand_data = {
            "name": "Test Brand",
            "logo_url": "https://via.placeholder.com/150x60/D4AF37/000000?text=TEST"
        }
        
        return self.run_test(
            "Create Brand",
            "POST",
            "brands",
            200,
            data=brand_data
        )

    def test_create_brand_unauthorized(self):
        """Test creating brand without admin token"""
        old_token = self.token
        self.token = None
        
        brand_data = {
            "name": "Unauthorized Brand",
            "logo_url": "https://test.com/logo.png"
        }
        
        success, _ = self.run_test(
            "Create Brand (Unauthorized)",
            "POST",
            "brands",
            401,
            data=brand_data
        )
        
        self.token = old_token
        return success

    def test_statistics_endpoint(self):
        """Test statistics endpoint - main focus of testing"""
        print("\n🎯 TESTING STATISTICS ENDPOINT (PRIMARY FOCUS)")
        
        success, response = self.run_test(
            "Get Statistics",
            "GET",
            "statistics",
            200
        )
        
        if not success:
            return False
            
        # Verify response structure
        required_fields = ['total_registrations', 'participating_municipalities', 'represented_sectors']
        for field in required_fields:
            if field not in response:
                print(f"❌ Missing required field: {field}")
                return False
                
        # Verify data types
        if not isinstance(response['total_registrations'], int):
            print(f"❌ total_registrations should be int, got {type(response['total_registrations'])}")
            return False
            
        if not isinstance(response['participating_municipalities'], int):
            print(f"❌ participating_municipalities should be int, got {type(response['participating_municipalities'])}")
            return False
            
        if not isinstance(response['represented_sectors'], int):
            print(f"❌ represented_sectors should be int, got {type(response['represented_sectors'])}")
            return False
            
        # Verify real-time counting (should reflect registrations created in previous tests)
        if response['total_registrations'] < 2:  # We created at least 2 registrations
            print(f"❌ Expected at least 2 registrations, got {response['total_registrations']}")
            return False
            
        if response['participating_municipalities'] < 1:  # We created registrations from Querétaro
            print(f"❌ Expected at least 1 municipality, got {response['participating_municipalities']}")
            return False
            
        if response['represented_sectors'] < 2:  # We created registrations from Educativo and Empresarial sectors
            print(f"❌ Expected at least 2 sectors, got {response['represented_sectors']}")
            return False
            
        print(f"✅ Statistics structure validated:")
        print(f"   - Total registrations: {response['total_registrations']}")
        print(f"   - Participating municipalities: {response['participating_municipalities']}")
        print(f"   - Represented sectors: {response['represented_sectors']}")
        
        return True

    def test_statistics_with_more_data(self):
        """Test statistics with additional diverse data"""
        if not self.created_event_id:
            print("❌ Cannot test statistics with more data - no event created")
            return False
            
        # Create registrations from different municipalities and sectors
        test_registrations = [
            {
                "full_name": "María González",
                "age": 28,
                "municipality": "San Juan del Río",
                "sector": "Cultural",
                "phone": "4271234567",
                "email": f"maria_{datetime.now().strftime('%H%M%S')}@test.com",
                "event_id": self.created_event_id
            },
            {
                "full_name": "Carlos Rodríguez",
                "age": 35,
                "municipality": "Tequisquiapan",
                "sector": "Empresarial",
                "phone": "4141234567",
                "email": f"carlos_{datetime.now().strftime('%H%M%S')}@test.com",
                "event_id": self.created_event_id
            },
            {
                "full_name": "Ana López",
                "age": 22,
                "municipality": "San Juan del Río",
                "sector": "Educativo",
                "phone": "4271234568",
                "email": f"ana_{datetime.now().strftime('%H%M%S')}@test.com",
                "event_id": self.created_event_id
            }
        ]
        
        # Create the registrations
        for i, reg_data in enumerate(test_registrations):
            success, _ = self.run_test(
                f"Create Test Registration {i+1}",
                "POST",
                "registrations",
                200,
                data=reg_data
            )
            if not success:
                print(f"❌ Failed to create test registration {i+1}")
                return False
        
        # Now test statistics again
        success, response = self.run_test(
            "Get Statistics (After More Data)",
            "GET",
            "statistics",
            200
        )
        
        if not success:
            return False
            
        # Verify the counts have increased appropriately
        print(f"✅ Updated statistics after adding diverse data:")
        print(f"   - Total registrations: {response['total_registrations']}")
        print(f"   - Participating municipalities: {response['participating_municipalities']}")
        print(f"   - Represented sectors: {response['represented_sectors']}")
        
        # Should have at least 3 municipalities now (Querétaro, San Juan del Río, Tequisquiapan)
        if response['participating_municipalities'] < 3:
            print(f"❌ Expected at least 3 municipalities, got {response['participating_municipalities']}")
            return False
            
        # Should have at least 3 sectors (Educativo, Empresarial, Cultural)
        if response['represented_sectors'] < 3:
            print(f"❌ Expected at least 3 sectors, got {response['represented_sectors']}")
            return False
            
        return True

def main():
    print("🚀 Starting Karaoke Sensō API Tests")
    print("=" * 50)
    
    tester = KaraokeAPITester()
    
    # Test sequence
    tests = [
        ("Root Endpoint", tester.test_root_endpoint),
        ("Admin Login", tester.test_admin_login),
        ("Invalid Login", tester.test_invalid_login),
        ("Get Events", tester.test_get_events),
        ("Create Event", tester.test_create_event),
        ("Create Event (Unauthorized)", tester.test_create_event_unauthorized),
        ("Create Registration", tester.test_create_registration),
        ("Duplicate Registration", tester.test_duplicate_registration),
        ("Get Registrations (Admin)", tester.test_get_registrations_admin),
        ("Get Registrations (Unauthorized)", tester.test_get_registrations_unauthorized),
        ("Update Payment Status", tester.test_update_payment_status),
        ("Get Brands", tester.test_get_brands),
        ("Create Brand", tester.test_create_brand),
        ("Create Brand (Unauthorized)", tester.test_create_brand_unauthorized),
    ]
    
    failed_tests = []
    
    for test_name, test_func in tests:
        try:
            success = test_func()
            if not success:
                failed_tests.append(test_name)
        except Exception as e:
            print(f"❌ {test_name} - Exception: {str(e)}")
            failed_tests.append(test_name)
    
    # Print results
    print("\n" + "=" * 50)
    print("📊 TEST RESULTS")
    print("=" * 50)
    print(f"Tests run: {tester.tests_run}")
    print(f"Tests passed: {tester.tests_passed}")
    print(f"Tests failed: {len(failed_tests)}")
    print(f"Success rate: {(tester.tests_passed/tester.tests_run)*100:.1f}%")
    
    if failed_tests:
        print(f"\n❌ Failed tests:")
        for test in failed_tests:
            print(f"   - {test}")
    else:
        print(f"\n✅ All tests passed!")
    
    return 0 if len(failed_tests) == 0 else 1

if __name__ == "__main__":
    sys.exit(main())