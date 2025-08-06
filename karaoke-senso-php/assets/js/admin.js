// Admin Panel JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is on admin page and authenticated
    if (window.location.pathname.includes('admin')) {
        if (!api.isAuthenticated()) {
            // Redirect to login if not authenticated
            window.location.href = '/karaoke-senso/admin/login.php';
            return;
        }
        
        initializeAdminPanel();
    }
});

async function initializeAdminPanel() {
    try {
        // Load initial data
        await Promise.all([
            loadRegistrations(),
            loadEvents(),
            loadBrands()
        ]);
        
        // Initialize event listeners
        initializeEventListeners();
        
        console.log('Admin panel initialized');
    } catch (error) {
        console.error('Error initializing admin panel:', error);
        showMessage('Error al cargar el panel administrativo', 'error');
    }
}

function initializeEventListeners() {
    // Event creation form
    const eventForm = document.getElementById('create-event-form');
    if (eventForm) {
        eventForm.addEventListener('submit', handleCreateEvent);
    }
    
    // Brand creation form  
    const brandForm = document.getElementById('create-brand-form');
    if (brandForm) {
        brandForm.addEventListener('submit', handleCreateBrand);
    }
    
    // Logout button
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', handleLogout);
    }
    
    // Tab switching
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(event) {
            const targetTab = event.target.getAttribute('data-bs-target');
            if (targetTab === '#registrations-tab') {
                loadRegistrations();
            } else if (targetTab === '#events-tab') {
                loadEvents();
            } else if (targetTab === '#brands-tab') {
                loadBrands();
            }
        });
    });
}

// Registrations Management
async function loadRegistrations() {
    try {
        const registrations = await api.getRegistrations();
        updateRegistrationsTable(registrations);
    } catch (error) {
        console.error('Error loading registrations:', error);
        showMessage('Error al cargar los registros', 'error');
    }
}

function updateRegistrationsTable(registrations) {
    const tbody = document.getElementById('registrations-tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (registrations.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted">No hay registros disponibles</td>
            </tr>
        `;
        return;
    }
    
    registrations.forEach(registration => {
        const row = document.createElement('tr');
        
        const paymentBadgeClass = registration.payment_status === 'pagado' ? 'bg-success' : 'bg-warning';
        const actionButton = registration.payment_status === 'pendiente' 
            ? `<button class="btn btn-sm btn-success" onclick="updatePaymentStatus('${registration.id}', 'pagado')">Marcar Pagado</button>`
            : `<button class="btn btn-sm btn-outline-secondary" onclick="updatePaymentStatus('${registration.id}', 'pendiente')">Marcar Pendiente</button>`;
        
        row.innerHTML = `
            <td class="text-white">${registration.full_name}</td>
            <td class="text-white">${registration.email}</td>
            <td class="text-white">${registration.municipality}</td>
            <td class="text-white">${registration.sector}</td>
            <td>
                <span class="badge ${paymentBadgeClass}">${registration.payment_status}</span>
            </td>
            <td>${actionButton}</td>
        `;
        
        tbody.appendChild(row);
    });
}

async function updatePaymentStatus(registrationId, paymentStatus) {
    try {
        await api.updatePaymentStatus(registrationId, paymentStatus);
        showMessage('Estado de pago actualizado correctamente');
        await loadRegistrations(); // Reload the table
    } catch (error) {
        console.error('Error updating payment status:', error);
        showMessage('Error al actualizar el estado de pago', 'error');
    }
}

// Events Management
async function loadEvents() {
    try {
        const events = await api.getEvents();
        updateEventsDisplay(events);
    } catch (error) {
        console.error('Error loading events:', error);
        showMessage('Error al cargar los eventos', 'error');
    }
}

function updateEventsDisplay(events) {
    const container = document.getElementById('events-list');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (events.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted">
                <p>No hay eventos programados</p>
            </div>
        `;
        return;
    }
    
    events.forEach(event => {
        const eventCard = document.createElement('div');
        eventCard.className = 'card card-dark mb-3';
        
        const eventDate = new Date(event.date);
        eventCard.innerHTML = `
            <div class="card-body">
                <h5 class="card-title text-gold">${event.name}</h5>
                <p class="text-white mb-2">${event.municipality} - ${event.venue}</p>
                <p class="text-muted mb-2">${formatDateTime(event.date)}</p>
                <small class="text-muted">Máximo: ${event.max_participants} participantes</small>
            </div>
        `;
        
        container.appendChild(eventCard);
    });
}

async function handleCreateEvent(event) {
    event.preventDefault();
    
    const form = event.target;
    const submitButton = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);
    
    const eventData = {
        name: formData.get('name')?.trim(),
        municipality: formData.get('municipality')?.trim(),
        venue: formData.get('venue')?.trim(),
        date: formData.get('date'),
        max_participants: parseInt(formData.get('max_participants')) || 50
    };
    
    // Validation
    if (!eventData.name || !eventData.municipality || !eventData.venue || !eventData.date) {
        showMessage('Todos los campos son requeridos', 'error');
        return;
    }
    
    try {
        setLoadingState(submitButton, true);
        
        await api.createEvent(eventData);
        
        showMessage('Evento creado exitosamente');
        
        form.reset();
        await loadEvents();
        
    } catch (error) {
        console.error('Create event error:', error);
        showMessage(error.message || 'Error al crear el evento', 'error');
    } finally {
        setLoadingState(submitButton, false);
    }
}

// Brands Management
async function loadBrands() {
    try {
        const brands = await api.getBrands();
        updateBrandsDisplay(brands);
    } catch (error) {
        console.error('Error loading brands:', error);
        showMessage('Error al cargar las marcas', 'error');
    }
}

function updateBrandsDisplay(brands) {
    const container = document.getElementById('brands-list');
    if (!container) return;
    
    container.innerHTML = '';
    
    if (brands.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted">
                <p>No hay marcas registradas</p>
            </div>
        `;
        return;
    }
    
    brands.forEach(brand => {
        const brandCard = document.createElement('div');
        brandCard.className = 'card card-dark mb-3';
        
        brandCard.innerHTML = `
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <img src="${brand.logo_url}" alt="${brand.name}" class="brand-logo me-3" style="height: 40px;">
                    <div>
                        <h6 class="card-title text-gold mb-0">${brand.name}</h6>
                        <small class="text-muted">Agregado: ${formatDateTime(brand.created_at)}</small>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(brandCard);
    });
}

async function handleCreateBrand(event) {
    event.preventDefault();
    
    const form = event.target;
    const submitButton = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);
    
    const brandData = {
        name: formData.get('name')?.trim(),
        logo_url: formData.get('logo_url')?.trim()
    };
    
    // Validation
    if (!brandData.name || !brandData.logo_url) {
        showMessage('Nombre y URL del logo son requeridos', 'error');
        return;
    }
    
    try {
        setLoadingState(submitButton, true);
        
        await api.createBrand(brandData);
        
        showMessage('Marca creada exitosamente');
        
        form.reset();
        await loadBrands();
        
    } catch (error) {
        console.error('Create brand error:', error);
        showMessage(error.message || 'Error al crear la marca', 'error');
    } finally {
        setLoadingState(submitButton, false);
    }
}

// Logout
async function handleLogout() {
    try {
        await api.logout();
        showMessage('Sesión cerrada exitosamente');
        
        // Redirect to main page
        setTimeout(() => {
            window.location.href = '/karaoke-senso/';
        }, 1000);
        
    } catch (error) {
        console.error('Logout error:', error);
        showMessage('Error al cerrar sesión', 'error');
    }
}

// Make functions available globally for onclick handlers
window.updatePaymentStatus = updatePaymentStatus;