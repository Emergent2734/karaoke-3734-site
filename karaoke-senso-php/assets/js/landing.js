// Landing Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize landing page components
    initializeStatistics();
    initializeRegistrationForm();
    initializeBrandSlider();
    initializeNavigation();
    initializeFloatingButton();
    
    console.log('Landing page initialized');
});

// Statistics Section
async function initializeStatistics() {
    try {
        const statsData = await api.getStatistics();
        updateStatisticsDisplay(statsData);
    } catch (error) {
        console.error('Error loading statistics:', error);
        // Show default values on error
        updateStatisticsDisplay({
            total_registrations: 0,
            participating_municipalities: 0,
            represented_sectors: 0
        });
    }
}

function updateStatisticsDisplay(stats) {
    const elements = {
        totalRegistrations: document.getElementById('total-registrations'),
        participatingMunicipalities: document.getElementById('participating-municipalities'),
        representedSectors: document.getElementById('represented-sectors')
    };

    if (elements.totalRegistrations) {
        elements.totalRegistrations.textContent = stats.total_registrations;
    }
    if (elements.participatingMunicipalities) {
        elements.participatingMunicipalities.textContent = stats.participating_municipalities;
    }
    if (elements.representedSectors) {
        elements.representedSectors.textContent = stats.represented_sectors;
    }
}

// Registration Form
async function initializeRegistrationForm() {
    const form = document.getElementById('registration-form');
    if (!form) return;

    // Load events for the select dropdown
    await loadEvents();

    form.addEventListener('submit', handleRegistrationSubmit);
}

async function loadEvents() {
    try {
        const events = await api.getEvents();
        const eventSelect = document.getElementById('event-id');
        
        if (eventSelect) {
            // Clear existing options except the first one
            eventSelect.innerHTML = '<option value="">Selecciona tu sede preferida</option>';
            
            events.forEach(event => {
                const option = document.createElement('option');
                option.value = event.id;
                const eventDate = new Date(event.date);
                option.textContent = `${event.name} - ${event.municipality} (${eventDate.toLocaleDateString()})`;
                eventSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading events:', error);
        showMessage('Error al cargar las fechas disponibles', 'error');
    }
}

async function handleRegistrationSubmit(event) {
    event.preventDefault();
    
    const form = event.target;
    const submitButton = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);
    
    // Validate form data
    const registrationData = {
        full_name: formData.get('full_name')?.trim(),
        age: parseInt(formData.get('age')),
        municipality: formData.get('municipality')?.trim(),
        sector: formData.get('sector'),
        phone: formData.get('phone')?.trim(),
        email: formData.get('email')?.trim(),
        event_id: formData.get('event_id')
    };

    // Validation
    if (!registrationData.full_name) {
        showMessage('El nombre completo es requerido', 'error');
        return;
    }

    if (!registrationData.age || registrationData.age < 16 || registrationData.age > 99) {
        showMessage('La edad debe estar entre 16 y 99 años', 'error');
        return;
    }

    if (!registrationData.municipality) {
        showMessage('El municipio es requerido', 'error');
        return;
    }

    if (!registrationData.sector) {
        showMessage('Debes seleccionar un sector', 'error');
        return;
    }

    if (!registrationData.phone || !validatePhone(registrationData.phone)) {
        showMessage('Por favor ingresa un número de teléfono válido', 'error');
        return;
    }

    if (!registrationData.email || !validateEmail(registrationData.email)) {
        showMessage('Por favor ingresa un email válido', 'error');
        return;
    }

    if (!registrationData.event_id) {
        showMessage('Debes seleccionar una sede', 'error');
        return;
    }

    try {
        setLoadingState(submitButton, true);
        
        await api.createRegistration(registrationData);
        
        showMessage('¡Registro exitoso! Tu inscripción ha sido recibida. El pago de $300 MXN se validará por el equipo organizador.');
        
        // Reset form
        form.reset();
        
        // Reload statistics to show updated numbers
        await initializeStatistics();
        
    } catch (error) {
        console.error('Registration error:', error);
        showMessage(error.message || 'Error en el registro. Por favor intenta nuevamente.', 'error');
    } finally {
        setLoadingState(submitButton, false);
    }
}

// Brand Slider
async function initializeBrandSlider() {
    try {
        const brands = await api.getBrands();
        updateBrandSlider(brands);
    } catch (error) {
        console.error('Error loading brands:', error);
        // Use fallback brands if API fails
        const fallbackBrands = [
            { id: '1', name: 'PVA', logo_url: 'https://via.placeholder.com/150x60/D4AF37/000000?text=PVA' },
            { id: '2', name: 'Impactos Digitales', logo_url: 'https://via.placeholder.com/150x60/D4AF37/000000?text=IMPACTOS' },
            { id: '3', name: 'Club de Leones', logo_url: 'https://via.placeholder.com/150x60/D4AF37/000000?text=LEONES' },
            { id: '4', name: 'Radio UAQ', logo_url: 'https://via.placeholder.com/150x60/D4AF37/000000?text=RADIO+UAQ' },
            { id: '5', name: 'CIJ', logo_url: 'https://via.placeholder.com/150x60/D4AF37/000000?text=CIJ' }
        ];
        updateBrandSlider(fallbackBrands);
    }
}

function updateBrandSlider(brands) {
    const sliderTrack = document.getElementById('brand-slider-track');
    if (!sliderTrack) return;

    // Clear existing content
    sliderTrack.innerHTML = '';

    // Duplicate brands for infinite scroll effect
    const duplicatedBrands = [...brands, ...brands];

    duplicatedBrands.forEach(brand => {
        const brandElement = document.createElement('div');
        brandElement.className = 'flex-shrink-0 mx-4';
        brandElement.innerHTML = `
            <img 
                src="${brand.logo_url}" 
                alt="${brand.name}"
                class="brand-logo"
                onerror="this.src='https://via.placeholder.com/150x60/D4AF37/000000?text=${encodeURIComponent(brand.name)}'"
            />
        `;
        sliderTrack.appendChild(brandElement);
    });
}

// Navigation
function initializeNavigation() {
    // Add smooth scroll behavior to navigation links
    const navLinks = document.querySelectorAll('a[href^="#"]');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            scrollToSection(targetId);
        });
    });

    // Update active nav link on scroll
    window.addEventListener('scroll', updateActiveNavLink);
}

function updateActiveNavLink() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link[href^="#"]');
    
    let current = '';
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100; // Account for fixed navbar
        const sectionHeight = section.offsetHeight;
        
        if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
            current = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === '#' + current) {
            link.classList.add('active');
        }
    });
}

// Floating Button
function initializeFloatingButton() {
    const floatingButton = document.getElementById('floating-register-btn');
    if (!floatingButton) return;

    floatingButton.addEventListener('click', function() {
        scrollToSection('registro');
    });

    // Show/hide based on scroll position (optional - currently always visible)
    // window.addEventListener('scroll', function() {
    //     const heroSection = document.getElementById('inicio');
    //     if (heroSection) {
    //         const heroBottom = heroSection.offsetTop + heroSection.offsetHeight;
    //         if (window.scrollY > heroBottom) {
    //             floatingButton.style.display = 'block';
    //         } else {
    //             floatingButton.style.display = 'none';
    //         }
    //     }
    // });
}

// Load events and update dates section
async function loadEventsSection() {
    try {
        const events = await api.getEvents();
        const eventsContainer = document.getElementById('events-container');
        
        if (eventsContainer && events.length > 0) {
            eventsContainer.innerHTML = '';
            
            events.forEach(event => {
                const eventCard = document.createElement('div');
                eventCard.className = 'col-lg-4 col-md-6 mb-4';
                
                const eventDate = new Date(event.date);
                eventCard.innerHTML = `
                    <div class="card card-dark h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-calendar-alt icon-gold me-3 mt-1" style="font-size: 2rem;"></i>
                                <div class="flex-grow-1">
                                    <h5 class="card-title text-white">${event.name}</h5>
                                    <div class="d-flex align-items-center text-muted mb-2">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        <span>${event.municipality}</span>
                                    </div>
                                    <p class="text-muted mb-2">${event.venue}</p>
                                    <p class="text-gold fw-bold">
                                        ${eventDate.toLocaleDateString()} - ${eventDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                eventsContainer.appendChild(eventCard);
            });
            
            // Show the events section
            const eventsSection = document.getElementById('fechas');
            if (eventsSection) {
                eventsSection.style.display = 'block';
            }
        }
    } catch (error) {
        console.error('Error loading events section:', error);
    }
}

// Initialize events section when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadEventsSection();
});