import React, { useState, useEffect } from "react";
import "./App.css";
import axios from "axios";
import { Button } from "./components/ui/button";
import { Input } from "./components/ui/input";
import { Label } from "./components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "./components/ui/card";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "./components/ui/select";
import { Badge } from "./components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "./components/ui/tabs";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "./components/ui/table";
import { Mic, Music, Trophy, Users, Calendar, MapPin, Phone, Mail, TrendingUp, Award, Target, Heart } from "lucide-react";

const BACKEND_URL = process.env.REACT_APP_BACKEND_URL;
const API = `${BACKEND_URL}/api`;

// Statistics Component
const StatisticsSection = () => {
  const [stats, setStats] = useState({
    total_registrations: 0,
    participating_municipalities: 0,
    represented_sectors: 0
  });

  useEffect(() => {
    fetchStatistics();
  }, []);

  const fetchStatistics = async () => {
    try {
      const response = await axios.get(`${API}/statistics`);
      setStats(response.data);
    } catch (error) {
      console.error("Error fetching statistics:", error);
    }
  };

  return (
    <section className="py-16 bg-gradient-to-r from-black via-gray-900 to-black">
      <div className="container mx-auto px-4">
        <div className="text-center mb-12">
          <h3 className="text-3xl font-bold text-gold mb-4">El Impacto del Certamen</h3>
          <p className="text-gray-300 max-w-2xl mx-auto">
            Números que reflejan la pasión y participación de nuestra comunidad
          </p>
        </div>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
          <div className="text-center p-6 bg-black/50 rounded-lg border border-gold/30">
            <TrendingUp className="w-12 h-12 text-gold mx-auto mb-4" />
            <div className="text-4xl font-bold text-gold mb-2">{stats.total_registrations}</div>
            <div className="text-gray-300">Participantes Inscritos</div>
          </div>
          <div className="text-center p-6 bg-black/50 rounded-lg border border-gold/30">
            <MapPin className="w-12 h-12 text-gold mx-auto mb-4" />
            <div className="text-4xl font-bold text-gold mb-2">{stats.participating_municipalities}</div>
            <div className="text-gray-300">Municipios Representados</div>
          </div>
          <div className="text-center p-6 bg-black/50 rounded-lg border border-gold/30">
            <Users className="w-12 h-12 text-gold mx-auto mb-4" />
            <div className="text-4xl font-bold text-gold mb-2">{stats.represented_sectors}</div>
            <div className="text-gray-300">Sectores Participando</div>
          </div>
        </div>
      </div>
    </section>
  );
};

// Contest Structure Component
const ContestStructureSection = () => {
  const phases = [
    {
      name: "KOE SAN",
      description: "Representante inicial",
      detail: "Primera fase donde cada participante demuestra su talento inicial"
    },
    {
      name: "KOE SAI", 
      description: "Representante de sede",
      detail: "Los mejores de cada sede compiten por representar su ubicación"
    },
    {
      name: "TSUKAMU KOE",
      description: "Representante de ciudad", 
      detail: "La gran final donde se elige al representante de toda la ciudad"
    }
  ];

  const criteria = [
    "Mensaje y conexión emocional",
    "Interpretación y expresión artística", 
    "Conexión genuina con el público",
    "Calidad vocal y afinación",
    "Presencia escénica",
    "Originalidad en la presentación"
  ];

  const prizes = [
    "Producción musical profesional",
    "Playera oficial del certamen",
    "Presentación en medios locales",
    "Reconocimiento público oficial",
    "Oportunidades de colaboración artística"
  ];

  return (
    <section className="py-20 bg-gradient-to-b from-black to-gray-900">
      <div className="container mx-auto px-4">
        <div className="max-w-6xl mx-auto">
          <h2 className="text-4xl font-bold text-gold text-center mb-4">Estructura del Certamen</h2>
          <p className="text-xl text-gray-300 text-center max-w-3xl mx-auto mb-12">
            Un sistema de competencia diseñado para descubrir y potenciar el verdadero talento artístico
          </p>

          {/* Phases */}
          <div className="mb-16">
            <h3 className="text-2xl font-bold text-gold mb-8 text-center">Fases del Evento</h3>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              {phases.map((phase, index) => (
                <Card key={phase.name} className="bg-black/80 border-gold/30 text-center">
                  <CardContent className="p-6">
                    <div className="text-3xl font-bold text-gold mb-2">{index + 1}</div>
                    <h4 className="text-xl font-bold text-gold mb-2">{phase.name}</h4>
                    <p className="text-white font-semibold mb-3">{phase.description}</p>
                    <p className="text-gray-300 text-sm">{phase.detail}</p>
                  </CardContent>
                </Card>
              ))}
            </div>
          </div>

          {/* Voting and Criteria */}
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {/* Voting Modality */}
            <Card className="bg-black/80 border-gold/30">
              <CardHeader>
                <CardTitle className="text-gold flex items-center">
                  <Award className="w-6 h-6 mr-2" />
                  Modalidad de Votación
                </CardTitle>
              </CardHeader>
              <CardContent className="text-gray-300">
                <div className="space-y-4">
                  <div className="flex items-start space-x-3">
                    <div className="w-2 h-2 bg-gold rounded-full mt-2 flex-shrink-0"></div>
                    <div>
                      <h5 className="font-semibold text-white">Votación Presencial</h5>
                      <p className="text-sm">El público presente en cada sede participa en la evaluación directa</p>
                    </div>
                  </div>
                  <div className="flex items-start space-x-3">
                    <div className="w-2 h-2 bg-gold rounded-full mt-2 flex-shrink-0"></div>
                    <div>
                      <h5 className="font-semibold text-white">Votación Virtual</h5>
                      <p className="text-sm">Transmisión en vivo permite participación de la comunidad extendida</p>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Evaluation Criteria */}
            <Card className="bg-black/80 border-gold/30">
              <CardHeader>
                <CardTitle className="text-gold flex items-center">
                  <Target className="w-6 h-6 mr-2" />
                  Criterios de Evaluación
                </CardTitle>
              </CardHeader>
              <CardContent className="text-gray-300">
                <ul className="space-y-2">
                  {criteria.map((criterion, index) => (
                    <li key={index} className="flex items-center space-x-2">
                      <div className="w-1.5 h-1.5 bg-gold rounded-full"></div>
                      <span className="text-sm">{criterion}</span>
                    </li>
                  ))}
                </ul>
              </CardContent>
            </Card>
          </div>

          {/* Prizes */}
          <Card className="mt-12 bg-black/80 border-gold/30">
            <CardHeader>
              <CardTitle className="text-gold text-center flex items-center justify-center">
                <Trophy className="w-6 h-6 mr-2" />
                Premios y Reconocimientos
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {prizes.map((prize, index) => (
                  <div key={index} className="flex items-center space-x-3 p-3 bg-gray-900/50 rounded-lg">
                    <Award className="w-5 h-5 text-gold flex-shrink-0" />
                    <span className="text-gray-300 text-sm">{prize}</span>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </section>
  );
};

// Philosophy/Manifesto Section
const PhilosophySection = () => {
  return (
    <section className="py-20 bg-gradient-to-r from-gray-900 via-black to-gray-900">
      <div className="container mx-auto px-4">
        <div className="max-w-4xl mx-auto text-center">
          <div className="mb-8">
            <Heart className="w-16 h-16 text-gold mx-auto mb-6 animate-pulse" />
            <h2 className="text-4xl md:text-5xl font-bold text-gold mb-6 font-orbitron">
              MANIFIESTO SENSŌ
            </h2>
          </div>
          
          <div className="bg-black/60 p-8 md:p-12 rounded-lg border border-gold/30 backdrop-blur-sm">
            <blockquote className="text-2xl md:text-3xl text-white font-light leading-relaxed mb-8">
              "Una declaración de guerra contra la vulgaridad, 
              <br />
              la insensibilidad y la indiferencia.
              <br />
              <span className="text-gold font-bold">Solo un arma: tu voz.</span>"
            </blockquote>
            
            <div className="space-y-6 text-lg text-gray-300">
              <p>
                En un mundo saturado de ruido vacío, Karaoke Sensō emerge como un grito de autenticidad. 
                No buscamos solo voces que canten, sino almas que se atrevan a sentir.
              </p>
              <p>
                Cada participante lleva consigo una historia, una emoción, una verdad que merece ser escuchada. 
                Aquí, el egoísmo se transforma en empatía, y las voces se convierten en puentes 
                que conectan corazones.
              </p>
              <p className="text-gold font-semibold">
                Esta es nuestra revolución silenciosa. Esta es nuestra guerra contra lo superficial.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

// Brand Slider Component
const BrandSlider = ({ brands }) => {
  return (
    <div className="py-12 bg-gradient-to-r from-gray-900 to-black">
      <div className="container mx-auto px-4">
        <h3 className="text-2xl font-bold text-center text-gold mb-8">Nuestros Patrocinadores</h3>
        <div className="flex overflow-hidden">
          <div className="flex animate-scroll">
            {brands.concat(brands).map((brand, index) => (
              <div key={index} className="flex-shrink-0 mx-6">
                <img 
                  src={brand.logo_url} 
                  alt={brand.name}
                  className="h-16 w-auto object-contain filter brightness-75 hover:brightness-100 transition-all duration-300"
                />
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
};

// Registration Form Component
const RegistrationForm = ({ events, onSuccess }) => {
  const [formData, setFormData] = useState({
    full_name: "",
    age: "",
    municipality: "",
    sector: "",
    phone: "",
    email: "",
    event_id: ""
  });
  const [loading, setLoading] = useState(false);

  const sectors = [
    "Educativo",
    "Empresarial", 
    "Cultural",
    "Deportivo",
    "Social",
    "Religioso",
    "Gubernamental",
    "Otro"
  ];

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    
    try {
      const response = await axios.post(`${API}/registrations`, {
        ...formData,
        age: parseInt(formData.age)
      });
      
      onSuccess("¡Registro exitoso! Tu inscripción ha sido recibida. El pago de $300 MXN se validará por el equipo organizador.");
      setFormData({
        full_name: "",
        age: "",
        municipality: "",
        sector: "",
        phone: "",
        email: "",
        event_id: ""
      });
    } catch (error) {
      console.error("Registration error:", error);
      onSuccess("Error en el registro. Por favor intenta nuevamente.", "error");
    } finally {
      setLoading(false);
    }
  };

  return (
    <Card className="max-w-2xl mx-auto bg-black/80 border-gold/30">
      <CardHeader>
        <CardTitle className="text-2xl text-gold text-center">Registro al Certamen</CardTitle>
        <p className="text-gray-300 text-center">Cuota de inscripción: $300 MXN</p>
      </CardHeader>
      <CardContent>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label htmlFor="full_name" className="text-gray-300">Nombre Completo</Label>
              <Input
                id="full_name"
                value={formData.full_name}
                onChange={(e) => setFormData({...formData, full_name: e.target.value})}
                required
                className="bg-gray-900 border-gray-700 text-white"
              />
            </div>
            <div>
              <Label htmlFor="age" className="text-gray-300">Edad</Label>
              <Input
                id="age"
                type="number"
                min="16"
                max="99"
                value={formData.age}
                onChange={(e) => setFormData({...formData, age: e.target.value})}
                required
                className="bg-gray-900 border-gray-700 text-white"
              />
            </div>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label htmlFor="municipality" className="text-gray-300">Municipio</Label>
              <Input
                id="municipality"
                value={formData.municipality}
                onChange={(e) => setFormData({...formData, municipality: e.target.value})}
                required
                className="bg-gray-900 border-gray-700 text-white"
              />
            </div>
            <div>
              <Label htmlFor="sector" className="text-gray-300">Sector</Label>
              <Select onValueChange={(value) => setFormData({...formData, sector: value})}>
                <SelectTrigger className="bg-gray-900 border-gray-700 text-white">
                  <SelectValue placeholder="Selecciona tu sector" />
                </SelectTrigger>
                <SelectContent>
                  {sectors.map((sector) => (
                    <SelectItem key={sector} value={sector}>{sector}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <Label htmlFor="phone" className="text-gray-300">Teléfono</Label>
              <Input
                id="phone"
                type="tel"
                value={formData.phone}
                onChange={(e) => setFormData({...formData, phone: e.target.value})}
                required
                className="bg-gray-900 border-gray-700 text-white"
              />
            </div>
            <div>
              <Label htmlFor="email" className="text-gray-300">Correo Electrónico</Label>
              <Input
                id="email"
                type="email"
                value={formData.email}
                onChange={(e) => setFormData({...formData, email: e.target.value})}
                required
                className="bg-gray-900 border-gray-700 text-white"
              />
            </div>
          </div>

          <div>
            <Label htmlFor="event_id" className="text-gray-300">Sede y Fecha</Label>
            <Select onValueChange={(value) => setFormData({...formData, event_id: value})}>
              <SelectTrigger className="bg-gray-900 border-gray-700 text-white">
                <SelectValue placeholder="Selecciona tu sede preferida" />
              </SelectTrigger>
              <SelectContent>
                {events.map((event) => (
                  <SelectItem key={event.id} value={event.id}>
                    {event.name} - {event.municipality} ({new Date(event.date).toLocaleDateString()})
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          <Button 
            type="submit" 
            disabled={loading}
            className="w-full bg-gold hover:bg-gold/80 text-black font-bold py-3"
          >
            {loading ? "Registrando..." : "Registrarse Ahora"}
          </Button>
        </form>
      </CardContent>
    </Card>
  );
};

// Admin Panel Component
const AdminPanel = ({ token, onLogout }) => {
  const [registrations, setRegistrations] = useState([]);
  const [events, setEvents] = useState([]);
  const [newEvent, setNewEvent] = useState({
    name: "",
    municipality: "",
    venue: "",
    date: "",
    max_participants: 50
  });

  useEffect(() => {
    fetchRegistrations();
    fetchEvents();
  }, []);

  const fetchRegistrations = async () => {
    try {
      const response = await axios.get(`${API}/registrations`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      setRegistrations(response.data);
    } catch (error) {
      console.error("Error fetching registrations:", error);
    }
  };

  const fetchEvents = async () => {
    try {
      const response = await axios.get(`${API}/events`);
      setEvents(response.data);
    } catch (error) {
      console.error("Error fetching events:", error);
    }
  };

  const createEvent = async (e) => {
    e.preventDefault();
    try {
      await axios.post(`${API}/events`, newEvent, {
        headers: { Authorization: `Bearer ${token}` }
      });
      setNewEvent({
        name: "",
        municipality: "",
        venue: "",
        date: "",
        max_participants: 50
      });
      fetchEvents();
    } catch (error) {
      console.error("Error creating event:", error);
    }
  };

  const updatePaymentStatus = async (registrationId, status) => {
    try {
      await axios.put(`${API}/registrations/${registrationId}/payment?payment_status=${status}`, {}, {
        headers: { Authorization: `Bearer ${token}` }
      });
      fetchRegistrations();
    } catch (error) {
      console.error("Error updating payment status:", error);
    }
  };

  return (
    <div className="min-h-screen bg-black text-white p-6">
      <div className="max-w-7xl mx-auto">
        <div className="flex justify-between items-center mb-8">
          <h1 className="text-3xl font-bold text-gold">Panel Administrativo</h1>
          <Button onClick={onLogout} variant="outline" className="border-gold text-gold">
            Cerrar Sesión
          </Button>
        </div>

        <Tabs defaultValue="registrations" className="space-y-6">
          <TabsList className="bg-gray-900">
            <TabsTrigger value="registrations">Registros</TabsTrigger>
            <TabsTrigger value="events">Eventos</TabsTrigger>
          </TabsList>

          <TabsContent value="registrations">
            <Card className="bg-gray-900 border-gray-700">
              <CardHeader>
                <CardTitle className="text-gold">Registros de Participantes</CardTitle>
              </CardHeader>
              <CardContent>
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead className="text-gray-300">Nombre</TableHead>
                      <TableHead className="text-gray-300">Email</TableHead>
                      <TableHead className="text-gray-300">Municipio</TableHead>
                      <TableHead className="text-gray-300">Sector</TableHead>
                      <TableHead className="text-gray-300">Pago</TableHead>
                      <TableHead className="text-gray-300">Acciones</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {registrations.map((reg) => (
                      <TableRow key={reg.id}>
                        <TableCell className="text-white">{reg.full_name}</TableCell>
                        <TableCell className="text-white">{reg.email}</TableCell>
                        <TableCell className="text-white">{reg.municipality}</TableCell>
                        <TableCell className="text-white">{reg.sector}</TableCell>
                        <TableCell>
                          <Badge variant={reg.payment_status === "pagado" ? "default" : "secondary"}>
                            {reg.payment_status}
                          </Badge>
                        </TableCell>
                        <TableCell>
                          {reg.payment_status === "pendiente" ? (
                            <Button 
                              size="sm"
                              onClick={() => updatePaymentStatus(reg.id, "pagado")}
                              className="bg-green-600 hover:bg-green-700"
                            >
                              Marcar Pagado
                            </Button>
                          ) : (
                            <Button 
                              size="sm"
                              variant="outline"
                              onClick={() => updatePaymentStatus(reg.id, "pendiente")}
                            >
                              Marcar Pendiente
                            </Button>
                          )}
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="events">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <Card className="bg-gray-900 border-gray-700">
                <CardHeader>
                  <CardTitle className="text-gold">Crear Nuevo Evento</CardTitle>
                </CardHeader>
                <CardContent>
                  <form onSubmit={createEvent} className="space-y-4">
                    <div>
                      <Label htmlFor="event_name" className="text-gray-300">Nombre del Evento</Label>
                      <Input
                        id="event_name"
                        value={newEvent.name}
                        onChange={(e) => setNewEvent({...newEvent, name: e.target.value})}
                        required
                        className="bg-gray-800 border-gray-600 text-white"
                      />
                    </div>
                    <div>
                      <Label htmlFor="event_municipality" className="text-gray-300">Municipio</Label>
                      <Input
                        id="event_municipality"
                        value={newEvent.municipality}
                        onChange={(e) => setNewEvent({...newEvent, municipality: e.target.value})}
                        required
                        className="bg-gray-800 border-gray-600 text-white"
                      />
                    </div>
                    <div>
                      <Label htmlFor="event_venue" className="text-gray-300">Lugar</Label>
                      <Input
                        id="event_venue"
                        value={newEvent.venue}
                        onChange={(e) => setNewEvent({...newEvent, venue: e.target.value})}
                        required
                        className="bg-gray-800 border-gray-600 text-white"
                      />
                    </div>
                    <div>
                      <Label htmlFor="event_date" className="text-gray-300">Fecha y Hora</Label>
                      <Input
                        id="event_date"
                        type="datetime-local"
                        value={newEvent.date}
                        onChange={(e) => setNewEvent({...newEvent, date: e.target.value})}
                        required
                        className="bg-gray-800 border-gray-600 text-white"
                      />
                    </div>
                    <Button type="submit" className="w-full bg-gold hover:bg-gold/80 text-black">
                      Crear Evento
                    </Button>
                  </form>
                </CardContent>
              </Card>

              <Card className="bg-gray-900 border-gray-700">
                <CardHeader>
                  <CardTitle className="text-gold">Eventos Programados</CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {events.map((event) => (
                      <div key={event.id} className="p-4 bg-gray-800 rounded-lg">
                        <h4 className="font-bold text-white">{event.name}</h4>
                        <p className="text-gray-300">{event.municipality} - {event.venue}</p>
                        <p className="text-gray-400">{new Date(event.date).toLocaleString()}</p>
                        <p className="text-sm text-gray-500">Máximo: {event.max_participants} participantes</p>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  );
};

// Login Component
const Login = ({ onLogin }) => {
  const [credentials, setCredentials] = useState({ email: "", password: "" });
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    
    try {
      const response = await axios.post(`${API}/auth/login`, credentials);
      onLogin(response.data.access_token);
    } catch (error) {
      console.error("Login error:", error);
      alert("Error de autenticación. Verifica tus credenciales.");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-black flex items-center justify-center">
      <Card className="w-full max-w-md bg-gray-900 border-gray-700">
        <CardHeader>
          <CardTitle className="text-2xl text-gold text-center">Acceso Administrativo</CardTitle>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <Label htmlFor="email" className="text-gray-300">Email</Label>
              <Input
                id="email"
                type="email"
                value={credentials.email}
                onChange={(e) => setCredentials({...credentials, email: e.target.value})}
                required
                className="bg-gray-800 border-gray-600 text-white"
              />
            </div>
            <div>
              <Label htmlFor="password" className="text-gray-300">Contraseña</Label>
              <Input
                id="password"
                type="password"
                value={credentials.password}
                onChange={(e) => setCredentials({...credentials, password: e.target.value})}
                required
                className="bg-gray-800 border-gray-600 text-white"
              />
            </div>
            <Button 
              type="submit" 
              disabled={loading}
              className="w-full bg-gold hover:bg-gold/80 text-black"
            >
              {loading ? "Iniciando..." : "Iniciar Sesión"}
            </Button>
          </form>
        </CardContent>
      </Card>
    </div>
  );
};

// Main App Component
function App() {
  const [currentView, setCurrentView] = useState("landing");
  const [events, setEvents] = useState([]);
  const [brands, setBrands] = useState([]);
  const [adminToken, setAdminToken] = useState(localStorage.getItem("admin_token"));
  const [message, setMessage] = useState("");
  const [showFloatingButton, setShowFloatingButton] = useState(true);

  useEffect(() => {
    fetchEvents();
    fetchBrands();
    // Check if accessing admin route
    if (window.location.pathname === '/admin') {
      setCurrentView("login");
    }
    // Handle scroll for floating button
    const handleScroll = () => {
      const heroSection = document.getElementById('inicio');
      if (heroSection) {
        const heroBottom = heroSection.offsetTop + heroSection.offsetHeight;
        setShowFloatingButton(window.scrollY > heroBottom);
      }
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const fetchEvents = async () => {
    try {
      const response = await axios.get(`${API}/events`);
      setEvents(response.data);
    } catch (error) {
      console.error("Error fetching events:", error);
    }
  };

  const fetchBrands = async () => {
    try {
      const response = await axios.get(`${API}/brands`);
      setBrands(response.data.length ? response.data : [
        { id: "1", name: "PVA", logo_url: "https://via.placeholder.com/150x60/D4AF37/000000?text=PVA" },
        { id: "2", name: "Club de Leones", logo_url: "https://via.placeholder.com/150x60/D4AF37/000000?text=LEONES" },
        { id: "3", name: "Impactos Digitales", logo_url: "https://via.placeholder.com/150x60/D4AF37/000000?text=IMPACTOS" }
      ]);
    } catch (error) {
      console.error("Error fetching brands:", error);
      setBrands([
        { id: "1", name: "PVA", logo_url: "https://via.placeholder.com/150x60/D4AF37/000000?text=PVA" },
        { id: "2", name: "Club de Leones", logo_url: "https://via.placeholder.com/150x60/D4AF37/000000?text=LEONES" },
        { id: "3", name: "Impactos Digitales", logo_url: "https://via.placeholder.com/150x60/D4AF37/000000?text=IMPACTOS" }
      ]);
    }
  };

  const handleLogin = (token) => {
    localStorage.setItem("admin_token", token);
    setAdminToken(token);
    setCurrentView("admin");
  };

  const handleLogout = () => {
    localStorage.removeItem("admin_token");
    setAdminToken(null);
    setCurrentView("landing");
  };

  const showMessage = (msg, type = "success") => {
    setMessage(msg);
    setTimeout(() => setMessage(""), 5000);
  };

  if (currentView === "admin" && adminToken) {
    return <AdminPanel token={adminToken} onLogout={handleLogout} />;
  }

  if (currentView === "login") {
    return <Login onLogin={handleLogin} />;
  }

  // Handle direct admin access
  if (window.location.pathname === '/admin' && !adminToken) {
    return <Login onLogin={handleLogin} />;
  }

  return (
    <div className="App bg-black text-white min-h-screen">
      {/* Header */}
      <header className="fixed top-0 w-full bg-black/95 backdrop-blur-sm border-b border-gold/20 z-50">
        <div className="container mx-auto px-4 py-4 flex justify-between items-center">
          <div className="flex items-center space-x-2">
            <Mic className="w-8 h-8 text-gold" />
            <h1 className="text-2xl font-bold text-gold">Karaoke Sensō</h1>
          </div>
          <nav className="hidden md:flex space-x-6">
            <a href="#inicio" className="text-gray-300 hover:text-gold transition-colors">Inicio</a>
            <a href="#registro" className="text-gray-300 hover:text-gold transition-colors">Registro</a>
            <a href="#estructura" className="text-gray-300 hover:text-gold transition-colors">Estructura</a>
            <a href="#bases" className="text-gray-300 hover:text-gold transition-colors">Bases</a>
            <a href="#fechas" className="text-gray-300 hover:text-gold transition-colors">Fechas</a>
            <a href="#contacto" className="text-gray-300 hover:text-gold transition-colors">Contacto</a>
          </nav>
        </div>
      </header>

      {/* Message */}
      {message && (
        <div className="fixed top-20 left-1/2 transform -translate-x-1/2 bg-green-600 text-white px-6 py-3 rounded-lg z-50">
          {message}
        </div>
      )}

      {/* Floating Registration Button */}
      {showFloatingButton && (
        <button
          onClick={() => document.getElementById('registro').scrollIntoView({ behavior: 'smooth' })}
          className="fixed bottom-6 right-6 bg-gold hover:bg-gold/80 text-black font-bold px-6 py-3 rounded-full shadow-lg z-40 animate-pulse hover:animate-none transition-all duration-300 hover:scale-105"
        >
          ¡Inscribirme!
        </button>
      )}

      {/* Hero Section */}
      <section id="inicio" className="relative min-h-screen flex items-center justify-center bg-gradient-to-b from-black to-gray-900">
        <div className="absolute inset-0 bg-black/50"></div>
        <div className="relative z-10 text-center px-4">
          <div className="mb-8">
            {/* TODO: Replace with official logo when uploaded */}
            <Mic className="w-24 h-24 text-gold mx-auto mb-4 animate-pulse" />
            <h1 className="text-6xl md:text-8xl font-bold text-gold mb-4 font-orbitron">
              KARAOKE SENSŌ
            </h1>
            <h2 className="text-2xl md:text-4xl text-white mb-6">
              Una guerra de emociones. Una voz. Un escenario.
            </h2>
            <p className="text-xl text-gray-300 max-w-2xl mx-auto mb-8">
              El certamen que trasciende la vulgaridad para encontrar el arte. 
              Donde el egoísmo se transforma en empatía y las voces se convierten en puentes.
            </p>
            <Button 
              size="lg"
              onClick={() => document.getElementById('registro').scrollIntoView({ behavior: 'smooth' })}
              className="bg-gold hover:bg-gold/80 text-black font-bold px-8 py-4 text-lg"
            >
              Regístrate Ahora
            </Button>
          </div>
        </div>
      </section>

      {/* About Section */}
      <section className="py-20 bg-gradient-to-r from-gray-900 to-black">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div className="text-center">
              <Trophy className="w-16 h-16 text-gold mx-auto mb-4" />
              <h3 className="text-xl font-bold text-gold mb-2">Competencia Intersectorial</h3>
              <p className="text-gray-300">Participantes de todos los sectores: educativo, empresarial, cultural y más.</p>
            </div>
            <div className="text-center">
              <Users className="w-16 h-16 text-gold mx-auto mb-4" />
              <h3 className="text-xl font-bold text-gold mb-2">Comunidad Unida</h3>
              <p className="text-gray-300">Un certamen que une voces y corazones en toda la región de Querétaro.</p>
            </div>
            <div className="text-center">
              <Music className="w-16 h-16 text-gold mx-auto mb-4" />
              <h3 className="text-xl font-bold text-gold mb-2">Arte y Expresión</h3>
              <p className="text-gray-300">Más que karaoke, una plataforma para la expresión artística genuina.</p>
            </div>
          </div>
        </div>
      </section>

      {/* Statistics Section */}
      <StatisticsSection />

      {/* Philosophy Section */}
      <PhilosophySection />

      {/* Registration Section */}
      <section id="registro" className="py-20 bg-black">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold text-gold mb-4">Únete al Certamen</h2>
            <p className="text-xl text-gray-300 max-w-2xl mx-auto">
              Registra tu participación y elige tu sede preferida. La cuota de inscripción es de $300 MXN.
            </p>
          </div>
          <RegistrationForm events={events} onSuccess={showMessage} />
        </div>
      </section>

      {/* Contest Structure Section */}
      <section id="estructura">
        <ContestStructureSection />
      </section>

      {/* Bases Section */}
      <section id="bases" className="py-20 bg-gradient-to-r from-gray-900 to-black">
        <div className="container mx-auto px-4">
          <div className="max-w-4xl mx-auto">
            <h2 className="text-4xl font-bold text-gold text-center mb-12">Bases del Certamen</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
              <Card className="bg-black/80 border-gold/30">
                <CardHeader>
                  <CardTitle className="text-gold">Participación</CardTitle>
                </CardHeader>
                <CardContent className="text-gray-300">
                  <ul className="space-y-2">
                    <li>• Cuota de inscripción: $300 MXN</li>
                    <li>• Edad mínima: 16 años</li>
                    <li>• Abierto a todos los sectores</li>
                    <li>• Una inscripción por sede</li>
                  </ul>
                </CardContent>
              </Card>
              
              <Card className="bg-black/80 border-gold/30">
                <CardHeader>
                  <CardTitle className="text-gold">Formato</CardTitle>
                </CardHeader>
                <CardContent className="text-gray-300">
                  <ul className="space-y-2">
                    <li>• Sedes múltiples en Querétaro</li>
                    <li>• Eliminatorias y finales</li>
                    <li>• Votación pública</li>
                    <li>• Premios por categorías</li>
                  </ul>
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </section>

      {/* Events Section */}
      {events.length > 0 && (
        <section id="fechas" className="py-20 bg-black">
          <div className="container mx-auto px-4">
            <h2 className="text-4xl font-bold text-gold text-center mb-12">Próximas Fechas</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {events.map((event) => (
                <Card key={event.id} className="bg-gray-900 border-gray-700">
                  <CardContent className="p-6">
                    <div className="flex items-start space-x-4">
                      <Calendar className="w-8 h-8 text-gold flex-shrink-0 mt-1" />
                      <div>
                        <h3 className="text-lg font-bold text-white mb-2">{event.name}</h3>
                        <div className="flex items-center text-gray-300 mb-1">
                          <MapPin className="w-4 h-4 mr-2" />
                          <span>{event.municipality}</span>
                        </div>
                        <p className="text-gray-400 text-sm">{event.venue}</p>
                        <p className="text-gold font-semibold mt-2">
                          {new Date(event.date).toLocaleDateString()} - {new Date(event.date).toLocaleTimeString()}
                        </p>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Brands Slider */}
      <BrandSlider brands={brands} />

      {/* Contact Section */}
      <section id="contacto" className="py-20 bg-black">
        <div className="container mx-auto px-4">
          <div className="max-w-2xl mx-auto text-center">
            <h2 className="text-4xl font-bold text-gold mb-8">Contacto</h2>
            <div className="space-y-4">
              <div className="flex items-center justify-center space-x-3">
                <Phone className="w-6 h-6 text-gold" />
                <span className="text-gray-300">WhatsApp: +52 442 123 4567</span>
              </div>
              <div className="flex items-center justify-center space-x-3">
                <Mail className="w-6 h-6 text-gold" />
                <span className="text-gray-300">coordinacion@karaokesenso.com</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-gray-900 py-12">
        <div className="container mx-auto px-4">
          <div className="max-w-4xl mx-auto">
            <div className="text-center mb-8">
              <h3 className="text-2xl font-bold text-gold mb-4">Karaoke Sensō 2025</h3>
              <p className="text-gray-400 mb-6">
                © 2025 Karaoke Sensō. Con el apoyo de PVA, Impactos Digitales, Club de Leones Querétaro, Radio UAQ y CIJ.
              </p>
            </div>
            
            <div className="border-t border-gray-700 pt-6">
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-500">
                <div className="text-center md:text-left">
                  <h4 className="text-gold font-semibold mb-2">Legal</h4>
                  <p>Aviso de Privacidad</p>
                  <p>Términos y Condiciones</p>
                </div>
                <div className="text-center">
                  <h4 className="text-gold font-semibold mb-2">Organización</h4>
                  <p>Coordinación General</p>
                  <p>Comité Técnico</p>
                </div>
                <div className="text-center md:text-right">
                  <h4 className="text-gold font-semibold mb-2">Contacto</h4>
                  <p>coordinacion@karaokesenso.com</p>
                  <p>+52 442 123 4567</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}

export default App;