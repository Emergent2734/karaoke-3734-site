<?php
session_start();

// Redirect if already authenticated
if (isset($_SESSION['auth_token'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo - Karaoke Sensō</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-black">
    <div class="min-vh-100 d-flex align-items-center justify-content-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="card card-dark">
                        <div class="card-header text-center">
                            <i class="fas fa-microphone text-gold mb-3" style="font-size: 3rem;"></i>
                            <h2 class="card-title mb-0">Acceso Administrativo</h2>
                            <small class="text-muted">Karaoke Sensō</small>
                        </div>
                        <div class="card-body">
                            <form id="login-form" class="form-dark">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input 
                                        type="email" 
                                        class="form-control" 
                                        id="email" 
                                        name="email" 
                                        value="admin@karaokesenso.com"
                                        required
                                    >
                                </div>
                                <div class="mb-4">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <div class="input-group">
                                        <input 
                                            type="password" 
                                            class="form-control" 
                                            id="password" 
                                            name="password"
                                            placeholder="Senso2025*"
                                            required
                                        >
                                        <button 
                                            type="button" 
                                            class="btn btn-outline-secondary" 
                                            onclick="togglePassword()"
                                            id="togglePasswordBtn"
                                        >
                                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-gold w-100 py-3 fw-bold">
                                    Iniciar Sesión
                                </button>
                            </form>
                        </div>
                        <div class="card-footer text-center">
                            <small class="text-muted">
                                <a href="../index.php" class="text-gold text-decoration-none">
                                    <i class="fas fa-arrow-left me-2"></i>
                                    Volver al sitio principal
                                </a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="../assets/js/api.js"></script>
    
    <script>
        // Login form handler
        document.getElementById('login-form').addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const form = event.target;
            const submitButton = form.querySelector('button[type="submit"]');
            const formData = new FormData(form);
            
            const email = formData.get('email').trim();
            const password = formData.get('password');
            
            if (!email || !password) {
                showMessage('Email y contraseña son requeridos', 'error');
                return;
            }
            
            try {
                setLoadingState(submitButton, true);
                
                await api.login(email, password);
                
                showMessage('¡Login exitoso! Redirigiendo...');
                
                // Redirect to admin panel
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1000);
                
            } catch (error) {
                console.error('Login error:', error);
                showMessage('Credenciales incorrectas. Verifica tu email y contraseña.', 'error');
            } finally {
                setLoadingState(submitButton, false);
            }
        });
        
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }
        
        // Focus on password field if email is pre-filled
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            
            if (emailInput.value) {
                passwordInput.focus();
            }
        });
    </script>
</body>
</html>