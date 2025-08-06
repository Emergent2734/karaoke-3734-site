<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Video - Karaoke Sensō</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .upload-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .upload-zone {
            border: 3px dashed #D4AF37;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .upload-zone:hover {
            border-color: #B8860B;
            background: rgba(212, 175, 55, 0.05);
        }
        .upload-zone.drag-over {
            border-color: #B8860B;
            background: rgba(212, 175, 55, 0.1);
        }
        .upload-progress {
            display: none;
            margin: 20px 0;
        }
        .video-preview {
            display: none;
            margin: 20px 0;
        }
        .alert-info {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border: none;
            color: #0d47a1;
        }
        .btn-upload {
            background: linear-gradient(135deg, #D4AF37, #B8860B);
            border: none;
            color: white;
            padding: 12px 30px;
            font-weight: bold;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .btn-upload:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
            color: white;
        }
        .btn-upload:disabled {
            opacity: 0.6;
            transform: none;
        }
        .file-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #2C1810, #1a0e08);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                🎤 Karaoke Sensō
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Inicio</a>
                <a class="nav-link" href="voting.php">Votación</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="upload-container">
            <div class="text-center mb-4">
                <h2 style="color: #D4AF37;">🎬 Subir Video</h2>
                <p class="text-muted">Sube tu video de participación para el certamen Karaoke Sensō</p>
            </div>

            <div class="alert alert-info">
                <strong>📋 Requisitos:</strong>
                <ul class="mb-0 mt-2">
                    <li>Formato permitido: MP4, MOV, AVI, MPEG</li>
                    <li>Tamaño máximo: 50 MB</li>
                    <li>Solo puedes subir un video por registro</li>
                    <li>El video será revisado antes de aparecer en votación</li>
                </ul>
            </div>

            <form id="uploadForm" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="registration_id" class="form-label">ID de Registro *</label>
                    <input type="text" class="form-control" id="registration_id" name="registration_id" 
                           placeholder="Ingresa tu ID de registro" required>
                    <div class="form-text">Este ID te fue proporcionado al momento de registrarte</div>
                </div>

                <div class="upload-zone" id="uploadZone" onclick="document.getElementById('videoFile').click()">
                    <div id="uploadMessage">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #D4AF37; margin-bottom: 15px;"></i>
                        <h5>Arrastra tu video aquí o haz clic para seleccionar</h5>
                        <p class="text-muted">Selecciona el archivo de video desde tu computadora</p>
                    </div>
                </div>

                <input type="file" id="videoFile" name="video" accept=".mp4,.mov,.avi,.mpeg" style="display: none;">

                <div class="file-info" id="fileInfo" style="display: none;">
                    <h6>📁 Información del Archivo:</h6>
                    <div id="fileDetails"></div>
                </div>

                <div class="video-preview" id="videoPreview">
                    <h6>👀 Vista Previa:</h6>
                    <video id="previewVideo" controls width="100%" height="300"></video>
                </div>

                <div class="upload-progress" id="uploadProgress">
                    <h6>📤 Subiendo archivo...</h6>
                    <div class="progress">
                        <div class="progress-bar" id="progressBar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div class="text-center mt-2" id="progressText">0%</div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-upload" id="submitBtn" disabled>
                        🚀 Subir Video
                    </button>
                </div>
            </form>

            <div class="mt-4" id="resultMessage"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    
    <script>
        const uploadZone = document.getElementById('uploadZone');
        const videoFile = document.getElementById('videoFile');
        const fileInfo = document.getElementById('fileInfo');
        const fileDetails = document.getElementById('fileDetails');
        const videoPreview = document.getElementById('videoPreview');
        const previewVideo = document.getElementById('previewVideo');
        const submitBtn = document.getElementById('submitBtn');
        const uploadForm = document.getElementById('uploadForm');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const resultMessage = document.getElementById('resultMessage');

        // File size limit (50MB)
        const MAX_FILE_SIZE = 50 * 1024 * 1024;
        const ALLOWED_TYPES = ['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/mpeg'];

        // Drag and drop functionality
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('drag-over');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('drag-over');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelection(files[0]);
            }
        });

        // File input change
        videoFile.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileSelection(e.target.files[0]);
            }
        });

        function handleFileSelection(file) {
            // Validate file type
            if (!ALLOWED_TYPES.includes(file.type)) {
                showAlert('❌ Tipo de archivo no válido. Solo se permiten videos MP4, MOV, AVI y MPEG.', 'danger');
                return;
            }

            // Validate file size
            if (file.size > MAX_FILE_SIZE) {
                showAlert('❌ El archivo es demasiado grande. El tamaño máximo es 50MB.', 'danger');
                return;
            }

            // Show file information
            showFileInfo(file);

            // Show video preview
            showVideoPreview(file);

            // Enable submit button
            submitBtn.disabled = false;
        }

        function showFileInfo(file) {
            const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
            fileDetails.innerHTML = `
                <div><strong>Nombre:</strong> ${file.name}</div>
                <div><strong>Tamaño:</strong> ${sizeInMB} MB</div>
                <div><strong>Tipo:</strong> ${file.type}</div>
                <div><strong>Última modificación:</strong> ${new Date(file.lastModified).toLocaleString()}</div>
            `;
            fileInfo.style.display = 'block';
        }

        function showVideoPreview(file) {
            const videoURL = URL.createObjectURL(file);
            previewVideo.src = videoURL;
            videoPreview.style.display = 'block';
            
            previewVideo.onload = function() {
                URL.revokeObjectURL(videoURL);
            };
        }

        function showAlert(message, type) {
            resultMessage.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
            setTimeout(() => {
                resultMessage.innerHTML = '';
            }, 5000);
        }

        // Form submission
        uploadForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const registrationId = document.getElementById('registration_id').value.trim();
            if (!registrationId) {
                showAlert('❌ Por favor ingresa tu ID de registro.', 'danger');
                return;
            }

            if (!videoFile.files.length) {
                showAlert('❌ Por favor selecciona un video para subir.', 'danger');
                return;
            }

            const formData = new FormData();
            formData.append('video', videoFile.files[0]);
            formData.append('registration_id', registrationId);

            // Show progress
            uploadProgress.style.display = 'block';
            submitBtn.disabled = true;

            try {
                const xhr = new XMLHttpRequest();

                // Upload progress
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        progressBar.style.width = percentComplete + '%';
                        progressText.textContent = Math.round(percentComplete) + '%';
                    }
                });

                xhr.onload = function() {
                    uploadProgress.style.display = 'none';
                    
                    if (xhr.status === 201) {
                        const response = JSON.parse(xhr.responseText);
                        showAlert(`✅ ¡Video subido exitosamente! Tu video "${response.original_name}" está en proceso de revisión.`, 'success');
                        
                        // Reset form
                        uploadForm.reset();
                        fileInfo.style.display = 'none';
                        videoPreview.style.display = 'none';
                        submitBtn.disabled = true;
                        
                    } else {
                        const error = JSON.parse(xhr.responseText);
                        showAlert(`❌ Error: ${error.error || 'No se pudo subir el video'}`, 'danger');
                        submitBtn.disabled = false;
                    }
                };

                xhr.onerror = function() {
                    uploadProgress.style.display = 'none';
                    showAlert('❌ Error de conexión. Por favor intenta nuevamente.', 'danger');
                    submitBtn.disabled = false;
                };

                xhr.open('POST', 'api/videos/upload', true);
                xhr.send(formData);

            } catch (error) {
                console.error('Upload error:', error);
                uploadProgress.style.display = 'none';
                showAlert('❌ Error inesperado. Por favor intenta nuevamente.', 'danger');
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html>