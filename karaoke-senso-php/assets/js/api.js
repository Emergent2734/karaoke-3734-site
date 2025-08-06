// API Configuration and Helper Functions
class KaraokeAPI {
    constructor() {
        this.baseURL = '/karaoke-senso/api';
        this.token = localStorage.getItem('auth_token');
    }

    // Helper method for making API requests
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
            }
        };

        // Add authorization header if token exists
        if (this.token && options.requireAuth !== false) {
            defaultOptions.headers.Authorization = `Bearer ${this.token}`;
        }

        const finalOptions = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(url, finalOptions);
            
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('API Request Error:', error);
            throw error;
        }
    }

    // Authentication methods
    async login(email, password) {
        try {
            const response = await this.request('/auth/login', {
                method: 'POST',
                body: JSON.stringify({ email, password }),
                requireAuth: false
            });
            
            if (response.access_token) {
                this.token = response.access_token;
                localStorage.setItem('auth_token', this.token);
            }
            
            return response;
        } catch (error) {
            throw new Error('Login failed: ' + error.message);
        }
    }

    async logout() {
        try {
            await this.request('/auth/logout', { method: 'POST' });
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            this.token = null;
            localStorage.removeItem('auth_token');
        }
    }

    // Statistics
    async getStatistics() {
        return await this.request('/statistics.php', { requireAuth: false });
    }

    // Events
    async getEvents() {
        return await this.request('/events.php', { requireAuth: false });
    }

    async createEvent(eventData) {
        return await this.request('/events.php', {
            method: 'POST',
            body: JSON.stringify(eventData)
        });
    }

    // Registrations
    async getRegistrations() {
        return await this.request('/registrations.php');
    }

    async createRegistration(registrationData) {
        return await this.request('/registrations.php', {
            method: 'POST',
            body: JSON.stringify(registrationData),
            requireAuth: false
        });
    }

    async updatePaymentStatus(registrationId, paymentStatus) {
        return await this.request(`/registrations.php/${registrationId}/payment?payment_status=${paymentStatus}`, {
            method: 'PUT'
        });
    }

    // Brands
    async getBrands() {
        return await this.request('/brands.php', { requireAuth: false });
    }

    async createBrand(brandData) {
        return await this.request('/brands.php', {
            method: 'POST',
            body: JSON.stringify(brandData)
        });
    }

    // Check if user is authenticated
    isAuthenticated() {
        return !!this.token;
    }
}

// Create global API instance
const api = new KaraokeAPI();

// Utility functions
function showMessage(message, type = 'success') {
    // Remove existing messages
    const existingAlert = document.querySelector('.alert-message');
    if (existingAlert) {
        existingAlert.remove();
    }

    // Create new message
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-message position-fixed`;
    alertDiv.style.cssText = `
        top: 100px; 
        left: 50%; 
        transform: translateX(-50%); 
        z-index: 1050;
        min-width: 300px;
        text-align: center;
    `;
    alertDiv.textContent = message;
    
    document.body.appendChild(alertDiv);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv && alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0;
        const v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

// Smooth scroll function
function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}

// Form validation helpers
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[\d\s\-\+\(\)]{10,}$/;
    return re.test(phone);
}

// Loading state helpers
function setLoadingState(element, isLoading) {
    if (isLoading) {
        element.disabled = true;
        element.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Cargando...';
    } else {
        element.disabled = false;
        element.innerHTML = element.getAttribute('data-original-text') || element.textContent;
    }
}

// Initialize loading state text
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('button[type="submit"]');
    buttons.forEach(button => {
        if (!button.getAttribute('data-original-text')) {
            button.setAttribute('data-original-text', button.textContent);
        }
    });
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { KaraokeAPI, api };
}