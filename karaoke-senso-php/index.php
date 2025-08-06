<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karaoke Sensō - Una Guerra de Emociones</title>
    <meta name="description" content="El certamen que trasciende la vulgaridad para encontrar el arte. Donde el egoísmo se transforma en empatía y las voces se convierten en puentes.">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#inicio">
                <i class="fas fa-microphone me-2" style="font-size: 1.5rem;"></i>
                Karaoke Sensō
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#registro">Registro</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#estructura">Estructura</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#bases">Bases</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#fechas">Fechas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contacto">Contacto</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Floating Registration Button -->
    <button id="floating-register-btn" class="btn btn-gold floating-btn">
        ¡Inscribirme!
    </button>

    <!-- Hero Section -->
    <section id="inicio" class="hero-section">
        <div class="container">
            <div class="hero-content">
                <!-- Logo placeholder - ready for official logo -->
                <i class="fas fa-microphone animate-pulse mb-4" style="font-size: 6rem; color: var(--gold);"></i>
                <h1 class="font-orbitron">KARAOKE SENSŌ</h1>
                <h2>Una guerra de emociones. Una voz. Un escenario.</h2>
                <p class="lead">
                    El certamen que trasciende la vulgaridad para encontrar el arte. 
                    Donde el egoísmo se transforma en empatía y las voces se convierten en puentes.
                </p>
                <a href="#registro" class="btn btn-gold btn-lg px-5 py-3">
                    Regístrate Ahora
                </a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="section-padding section-dark">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 text-center">
                    <i class="fas fa-trophy icon-gold mb-3" style="font-size: 4rem;"></i>
                    <h3 class="text-gold">Competencia Intersectorial</h3>
                    <p class="text-muted">Participantes de todos los sectores: educativo, empresarial, cultural y más.</p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-users icon-gold mb-3" style="font-size: 4rem;"></i>
                    <h3 class="text-gold">Comunidad Unida</h3>
                    <p class="text-muted">Un certamen que une voces y corazones en toda la región de Querétaro.</p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-music icon-gold mb-3" style="font-size: 4rem;"></i>
                    <h3 class="text-gold">Arte y Expresión</h3>
                    <p class="text-muted">Más que karaoke, una plataforma para la expresión artística genuina.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <h3 class="display-5 text-gold mb-4">El Impacto del Certamen</h3>
                <p class="text-muted lead">Números que reflejan la pasión y participación de nuestra comunidad</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <div class="stats-card">
                        <i class="fas fa-chart-line icon-gold mb-3" style="font-size: 3rem;"></i>
                        <div id="total-registrations" class="stats-number">0</div>
                        <div class="stats-label">Participantes Inscritos</div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="stats-card">
                        <i class="fas fa-map-marker-alt icon-gold mb-3" style="font-size: 3rem;"></i>
                        <div id="participating-municipalities" class="stats-number">0</div>
                        <div class="stats-label">Municipios Representados</div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="stats-card">
                        <i class="fas fa-users icon-gold mb-3" style="font-size: 3rem;"></i>
                        <div id="represented-sectors" class="stats-number">0</div>
                        <div class="stats-label">Sectores Participando</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Philosophy Section -->
    <section class="philosophy-section section-padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="text-center mb-5">
                        <i class="fas fa-heart icon-gold animate-pulse mb-4" style="font-size: 4rem;"></i>
                        <h2 class="display-4 text-gold font-orbitron">MANIFIESTO SENSŌ</h2>
                    </div>
                    <div class="manifesto-card">
                        <blockquote class="manifesto-quote text-center">
                            "Una declaración de guerra contra la vulgaridad,<br>
                            la insensibilidad y la indiferencia.<br>
                            <span class="manifesto-highlight">Solo un arma: tu voz.</span>"
                        </blockquote>
                        <div class="text-center">
                            <p class="mb-4">
                                En un mundo saturado de ruido vacío, Karaoke Sensō emerge como un grito de autenticidad. 
                                No buscamos solo voces que canten, sino almas que se atrevan a sentir.
                            </p>
                            <p class="mb-4">
                                Cada participante lleva consigo una historia, una emoción, una verdad que merece ser escuchada. 
                                Aquí, el egoísmo se transforma en empatía, y las voces se convierten en puentes 
                                que conectan corazones.
                            </p>
                            <p class="text-gold fw-bold">
                                Esta es nuestra revolución silenciosa. Esta es nuestra guerra contra lo superficial.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Section -->
    <section id="registro" class="section-padding section-black">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 text-gold">Únete al Certamen</h2>
                <p class="lead text-muted">
                    Registra tu participación y elige tu sede preferida. La cuota de inscripción es de $300 MXN.
                </p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card card-dark">
                        <div class="card-header text-center">
                            <h4 class="card-title mb-0">Registro al Certamen</h4>
                            <small class="text-muted">Cuota de inscripción: $300 MXN</small>
                        </div>
                        <div class="card-body">
                            <form id="registration-form" class="form-dark">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="full-name" class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control" id="full-name" name="full_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="age" class="form-label">Edad</label>
                                        <input type="number" class="form-control" id="age" name="age" min="16" max="99" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="municipality" class="form-label">Municipio</label>
                                        <input type="text" class="form-control" id="municipality" name="municipality" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="sector" class="form-label">Sector</label>
                                        <select class="form-select" id="sector" name="sector" required>
                                            <option value="">Selecciona tu sector</option>
                                            <option value="Educativo">Educativo</option>
                                            <option value="Empresarial">Empresarial</option>
                                            <option value="Cultural">Cultural</option>
                                            <option value="Deportivo">Deportivo</option>
                                            <option value="Social">Social</option>
                                            <option value="Religioso">Religioso</option>
                                            <option value="Gubernamental">Gubernamental</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Teléfono</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <div class="col-12">
                                        <label for="event-id" class="form-label">Sede y Fecha</label>
                                        <select class="form-select" id="event-id" name="event_id" required>
                                            <option value="">Selecciona tu sede preferida</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-gold w-100 py-3 fw-bold">
                                            Registrarse Ahora
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contest Structure Section -->
    <section id="estructura" class="section-padding section-dark">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 text-gold">Estructura del Certamen</h2>
                <p class="lead text-muted">Un sistema de competencia diseñado para descubrir y potenciar el verdadero talento artístico</p>
            </div>

            <!-- Phases -->
            <div class="mb-5">
                <h3 class="text-gold text-center mb-4">Fases del Evento</h3>
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card card-dark h-100 text-center">
                            <div class="card-body">
                                <div class="display-3 text-gold fw-bold">1</div>
                                <h4 class="text-gold">KOE SAN</h4>
                                <p class="text-white fw-bold">Representante inicial</p>
                                <p class="text-muted small">Primera fase donde cada participante demuestra su talento inicial</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card card-dark h-100 text-center">
                            <div class="card-body">
                                <div class="display-3 text-gold fw-bold">2</div>
                                <h4 class="text-gold">KOE SAI</h4>
                                <p class="text-white fw-bold">Representante de sede</p>
                                <p class="text-muted small">Los mejores de cada sede compiten por representar su ubicación</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card card-dark h-100 text-center">
                            <div class="card-body">
                                <div class="display-3 text-gold fw-bold">3</div>
                                <h4 class="text-gold">TSUKAMU KOE</h4>
                                <p class="text-white fw-bold">Representante de ciudad</p>
                                <p class="text-muted small">La gran final donde se elige al representante de toda la ciudad</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Details -->
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card card-dark h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-award icon-gold me-2"></i>
                                Modalidad de Votación
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="fas fa-circle text-gold me-2" style="font-size: 0.5rem;"></i>
                                    <strong>Votación Presencial:</strong> El público presente en cada sede participa en la evaluación directa
                                </li>
                                <li>
                                    <i class="fas fa-circle text-gold me-2" style="font-size: 0.5rem;"></i>
                                    <strong>Votación Virtual:</strong> Transmisión en vivo permite participación de la comunidad extendida
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card card-dark h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-target icon-gold me-2"></i>
                                Criterios de Evaluación
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled small">
                                <li class="mb-1"><i class="fas fa-circle text-gold me-2" style="font-size: 0.4rem;"></i>Mensaje y conexión emocional</li>
                                <li class="mb-1"><i class="fas fa-circle text-gold me-2" style="font-size: 0.4rem;"></i>Interpretación y expresión artística</li>
                                <li class="mb-1"><i class="fas fa-circle text-gold me-2" style="font-size: 0.4rem;"></i>Conexión genuina con el público</li>
                                <li class="mb-1"><i class="fas fa-circle text-gold me-2" style="font-size: 0.4rem;"></i>Calidad vocal y afinación</li>
                                <li class="mb-1"><i class="fas fa-circle text-gold me-2" style="font-size: 0.4rem;"></i>Presencia escénica</li>
                                <li><i class="fas fa-circle text-gold me-2" style="font-size: 0.4rem;"></i>Originalidad en la presentación</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bases Section -->
    <section id="bases" class="section-padding section-black">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 text-gold">Bases del Certamen</h2>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-5">
                    <div class="card card-dark h-100">
                        <div class="card-header">
                            <h5 class="card-title text-gold">Participación</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">• Cuota de inscripción: $300 MXN</li>
                                <li class="mb-2">• Edad mínima: 16 años</li>
                                <li class="mb-2">• Abierto a todos los sectores</li>
                                <li>• Una inscripción por sede</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card card-dark h-100">
                        <div class="card-header">
                            <h5 class="card-title text-gold">Formato</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">• Sedes múltiples en Querétaro</li>
                                <li class="mb-2">• Eliminatorias y finales</li>
                                <li class="mb-2">• Votación pública</li>
                                <li>• Premios por categorías</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Events Section -->
    <section id="fechas" class="section-padding section-dark" style="display: none;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 text-gold">Próximas Fechas</h2>
            </div>
            <div id="events-container" class="row g-4">
                <!-- Events will be loaded here by JavaScript -->
            </div>
        </div>
    </section>

    <!-- Sponsors Section -->
    <section class="brand-slider">
        <div class="container">
            <div class="text-center mb-5">
                <h3 class="display-5 text-gold">Nuestros Patrocinadores</h3>
            </div>
            <div class="overflow-hidden">
                <div id="brand-slider-track" class="brand-track">
                    <!-- Brands will be loaded here by JavaScript -->
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contacto" class="section-padding section-black">
        <div class="container">
            <div class="text-center">
                <h2 class="display-4 text-gold mb-5">Contacto</h2>
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="mb-4">
                            <i class="fas fa-phone icon-gold me-3" style="font-size: 1.5rem;"></i>
                            <span class="text-muted">WhatsApp: +52 442 123 4567</span>
                        </div>
                        <div>
                            <i class="fas fa-envelope icon-gold me-3" style="font-size: 1.5rem;"></i>
                            <span class="text-muted">coordinacion@karaokesenso.com</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-dark">
        <div class="container">
            <div class="text-center mb-4">
                <h3 class="footer-title">Karaoke Sensō 2025</h3>
                <p class="text-muted">
                    © 2025 Karaoke Sensō. Con el apoyo de PVA, Impactos Digitales, Club de Leones Querétaro, Radio UAQ y CIJ.
                </p>
            </div>
            <hr class="border-secondary">
            <div class="row g-4 text-center text-md-start">
                <div class="col-md-4">
                    <h6 class="footer-title">Legal</h6>
                    <p class="small text-muted">Aviso de Privacidad</p>
                    <p class="small text-muted">Términos y Condiciones</p>
                </div>
                <div class="col-md-4 text-center">
                    <h6 class="footer-title">Organización</h6>
                    <p class="small text-muted">Coordinación General</p>
                    <p class="small text-muted">Comité Técnico</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <h6 class="footer-title">Contacto</h6>
                    <p class="small text-muted">coordinacion@karaokesenso.com</p>
                    <p class="small text-muted">+52 442 123 4567</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/api.js"></script>
    <script src="assets/js/landing.js"></script>
</body>
</html>