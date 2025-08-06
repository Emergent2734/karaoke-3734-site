<?php
require_once '../config/auth.php';

$auth = new Auth();
$user = $auth->validateToken();

// Redirect to login if not authenticated
if (!$user || !$user['is_admin']) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo - Karaoke Sensō</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-black text-white">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <nav class="navbar navbar-expand-lg navbar-custom">
                    <div class="container-fluid">
                        <div class="navbar-brand d-flex align-items-center">
                            <i class="fas fa-microphone me-2"></i>
                            <span class="text-gold fw-bold">Panel Administrativo</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="me-3 text-muted">
                                <i class="fas fa-user me-2"></i>
                                <?php echo htmlspecialchars($user['email']); ?>
                            </span>
                            <a href="../index.php" class="btn btn-outline-gold btn-sm me-2">
                                <i class="fas fa-home me-1"></i>
                                Sitio Principal
                            </a>
                            <button id="logout-btn" class="btn btn-outline-gold btn-sm">
                                <i class="fas fa-sign-out-alt me-1"></i>
                                Cerrar Sesión
                            </button>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row mt-4">
            <div class="col-12">
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs nav-fill mb-4" id="adminTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="registrations-tab" data-bs-toggle="tab" data-bs-target="#registrations" type="button" role="tab">
                            <i class="fas fa-users me-2"></i>
                            Registros
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab">
                            <i class="fas fa-calendar me-2"></i>
                            Eventos
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="brands-tab" data-bs-toggle="tab" data-bs-target="#brands" type="button" role="tab">
                            <i class="fas fa-handshake me-2"></i>
                            Patrocinadores
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="adminTabsContent">
                    <!-- Registrations Tab -->
                    <div class="tab-pane fade show active" id="registrations" role="tabpanel">
                        <div class="card card-dark">
                            <div class="card-header">
                                <h5 class="card-title mb-0 text-gold">
                                    <i class="fas fa-users me-2"></i>
                                    Registros de Participantes
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-dark table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Email</th>
                                                <th>Municipio</th>
                                                <th>Sector</th>
                                                <th>Estado de Pago</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="registrations-tbody">
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">
                                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                                    Cargando registros...
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Events Tab -->
                    <div class="tab-pane fade" id="events" role="tabpanel">
                        <div class="row g-4">
                            <!-- Create Event Form -->
                            <div class="col-lg-6">
                                <div class="card card-dark">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0 text-gold">
                                            <i class="fas fa-plus me-2"></i>
                                            Crear Nuevo Evento
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="create-event-form" class="form-dark">
                                            <div class="mb-3">
                                                <label for="event-name" class="form-label">Nombre del Evento</label>
                                                <input type="text" class="form-control" id="event-name" name="name" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="event-municipality" class="form-label">Municipio</label>
                                                <input type="text" class="form-control" id="event-municipality" name="municipality" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="event-venue" class="form-label">Lugar</label>
                                                <input type="text" class="form-control" id="event-venue" name="venue" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="event-date" class="form-label">Fecha y Hora</label>
                                                <input type="datetime-local" class="form-control" id="event-date" name="date" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="event-max-participants" class="form-label">Máximo de Participantes</label>
                                                <input type="number" class="form-control" id="event-max-participants" name="max_participants" value="50" min="1">
                                            </div>
                                            <button type="submit" class="btn btn-gold w-100 fw-bold">
                                                Crear Evento
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Events List -->
                            <div class="col-lg-6">
                                <div class="card card-dark">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0 text-gold">
                                            <i class="fas fa-calendar-alt me-2"></i>
                                            Eventos Programados
                                        </h5>
                                    </div>
                                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                        <div id="events-list">
                                            <div class="text-center text-muted">
                                                <i class="fas fa-spinner fa-spin me-2"></i>
                                                Cargando eventos...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Brands Tab -->
                    <div class="tab-pane fade" id="brands" role="tabpanel">
                        <div class="row g-4">
                            <!-- Create Brand Form -->
                            <div class="col-lg-6">
                                <div class="card card-dark">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0 text-gold">
                                            <i class="fas fa-plus me-2"></i>
                                            Agregar Patrocinador
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="create-brand-form" class="form-dark">
                                            <div class="mb-3">
                                                <label for="brand-name" class="form-label">Nombre</label>
                                                <input type="text" class="form-control" id="brand-name" name="name" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="brand-logo-url" class="form-label">URL del Logo</label>
                                                <input type="url" class="form-control" id="brand-logo-url" name="logo_url" required>
                                                <div class="form-text">Ingresa la URL completa del logo (ej: https://ejemplo.com/logo.png)</div>
                                            </div>
                                            <button type="submit" class="btn btn-gold w-100 fw-bold">
                                                Agregar Patrocinador
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Brands List -->
                            <div class="col-lg-6">
                                <div class="card card-dark">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0 text-gold">
                                            <i class="fas fa-handshake me-2"></i>
                                            Patrocinadores Actuales
                                        </h5>
                                    </div>
                                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                                        <div id="brands-list">
                                            <div class="text-center text-muted">
                                                <i class="fas fa-spinner fa-spin me-2"></i>
                                                Cargando patrocinadores...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
    <script src="../assets/js/admin.js"></script>
</body>
</html>