<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votación Pública - Karaoke Sensō</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .voting-container {
            margin: 30px auto;
            max-width: 1200px;
        }
        .video-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .video-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        .video-player {
            width: 100%;
            height: 300px;
            background: #000;
        }
        .participant-info {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }
        .participant-name {
            font-size: 1.25rem;
            font-weight: bold;
            color: #2C1810;
            margin-bottom: 5px;
        }
        .participant-location {
            color: #666;
            font-size: 0.95rem;
        }
        .voting-section {
            padding: 25px;
        }
        .vote-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }
        .vote-btn {
            flex: 1;
            min-width: 120px;
            padding: 12px 15px;
            border: 2px solid;
            border-radius: 25px;
            font-weight: bold;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }
        .vote-btn-1 { border-color: #6c757d; color: #6c757d; }
        .vote-btn-1:hover { background: #6c757d; color: white; }
        .vote-btn-2 { border-color: #17a2b8; color: #17a2b8; }
        .vote-btn-2:hover { background: #17a2b8; color: white; }
        .vote-btn-3 { border-color: #28a745; color: #28a745; }
        .vote-btn-3:hover { background: #28a745; color: white; }
        .vote-btn-4 { border-color: #fd7e14; color: #fd7e14; }
        .vote-btn-4:hover { background: #fd7e14; color: white; }
        .vote-btn-5 { border-color: #dc3545; color: #dc3545; }
        .vote-btn-5:hover { background: #dc3545; color: white; }
        .vote-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .vote-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
        .vote-btn.voted {
            background: #D4AF37;
            border-color: #D4AF37;
            color: white;
        }
        .vote-results {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .result-bar {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        .result-label {
            width: 100px;
            font-weight: bold;
        }
        .result-progress {
            flex: 1;
            height: 20px;
            background: #e9ecef;
            border-radius: 10px;
            margin: 0 10px;
            overflow: hidden;
        }
        .result-fill {
            height: 100%;
            background: linear-gradient(90deg, #D4AF37, #B8860B);
            transition: width 0.3s ease;
        }
        .result-count {
            min-width: 30px;
            text-align: right;
            font-weight: bold;
        }
        .average-score {
            text-align: center;
            padding: 15px;
            background: linear-gradient(135deg, #D4AF37, #B8860B);
            color: white;
            border-radius: 10px;
            margin-top: 15px;
        }
        .no-videos {
            text-align: center;
            padding: 60px;
            color: #666;
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .modality-selector {
            text-align: center;
            margin-bottom: 30px;
        }
        .modality-btn {
            margin: 0 10px;
            padding: 10px 25px;
            border: 2px solid #D4AF37;
            background: transparent;
            color: #D4AF37;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .modality-btn.active {
            background: #D4AF37;
            color: white;
        }
        .alert-info {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border: none;
            color: #0d47a1;
            margin-bottom: 30px;
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
                <a class="nav-link" href="upload-video.php">Subir Video</a>
                <a class="nav-link active" href="voting.php">Votación</a>
                <a class="nav-link" href="#results">Resultados</a>
            </div>
        </div>
    </nav>

    <div class="container voting-container">
        <div class="text-center mb-5">
            <h1 style="color: #D4AF37;">🗳️ Votación Pública</h1>
            <p class="lead text-muted">Vota por tu participante favorito en el certamen Karaoke Sensō</p>
        </div>

        <div class="alert alert-info">
            <strong>📋 Instrucciones de Votación:</strong>
            <ul class="mb-0 mt-2">
                <li><strong>Escala de Votación:</strong> 1 (Bien) - 2 (Muy Bien) - 3 (Excelente) - 4 (Maravilloso) - 5 (Fenomenal)</li>
                <li><strong>Solo puedes votar una vez</strong> por cada video desde tu dispositivo</li>
                <li><strong>Modalidad:</strong> Presencial (en eventos) o Virtual (online)</li>
                <li>Los votos se registran inmediatamente y son definitivos</li>
            </ul>
        </div>

        <div class="modality-selector">
            <h5>Modalidad de Votación:</h5>
            <button class="modality-btn active" onclick="setVotingModality('virtual')">🌐 Virtual</button>
            <button class="modality-btn" onclick="setVotingModality('presencial')">🏢 Presencial</button>
        </div>

        <div id="loadingMessage" class="loading">
            <div class="spinner-border text-warning" role="status"></div>
            <p class="mt-3">Cargando videos disponibles...</p>
        </div>

        <div id="videosList" style="display: none;"></div>

        <div id="noVideos" class="no-videos" style="display: none;">
            <h3>📹 No hay videos disponibles</h3>
            <p>Aún no hay videos aprobados para votación. ¡Vuelve pronto!</p>
        </div>

        <!-- Results Section -->
        <div id="results" class="mt-5">
            <h2 class="text-center mb-4" style="color: #D4AF37;">📊 Resultados de Votación</h2>
            <div id="overallResults"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let currentModality = 'virtual';
        let videos = [];
        let votingEligibility = {};

        // Set voting modality
        function setVotingModality(modality) {
            currentModality = modality;
            
            // Update UI
            document.querySelectorAll('.modality-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            console.log('Voting modality set to:', modality);
        }

        // Load videos and voting data
        async function loadVideos() {
            try {
                const response = await fetch('api/videos/public');
                if (!response.ok) throw new Error('Failed to load videos');
                
                videos = await response.json();
                
                if (videos.length === 0) {
                    showNoVideos();
                } else {
                    await checkVotingEligibility();
                    displayVideos();
                }
                
            } catch (error) {
                console.error('Error loading videos:', error);
                showError('❌ Error al cargar los videos. Por favor recarga la página.');
            }
        }

        // Check voting eligibility for each video
        async function checkVotingEligibility() {
            for (const video of videos) {
                try {
                    const response = await fetch(`api/votes/check?video_id=${video.id}`);
                    if (response.ok) {
                        const eligibility = await response.json();
                        votingEligibility[video.id] = eligibility;
                    }
                } catch (error) {
                    console.error('Error checking eligibility for video', video.id, error);
                }
            }
        }

        // Display videos
        function displayVideos() {
            const videosList = document.getElementById('videosList');
            videosList.innerHTML = '';
            
            videos.forEach(video => {
                const eligibility = votingEligibility[video.id] || { can_vote: true, has_voted: false };
                const videoCard = createVideoCard(video, eligibility);
                videosList.appendChild(videoCard);
            });
            
            document.getElementById('loadingMessage').style.display = 'none';
            videosList.style.display = 'block';
        }

        // Create video card element
        function createVideoCard(video, eligibility) {
            const card = document.createElement('div');
            card.className = 'video-card';
            
            card.innerHTML = `
                <video class="video-player" controls>
                    <source src="uploads/videos/${video.filename}" type="video/mp4">
                    Tu navegador no soporta el elemento video.
                </video>
                
                <div class="participant-info">
                    <div class="participant-name">${video.participant_name}</div>
                    <div class="participant-location">📍 ${video.municipality}</div>
                </div>
                
                <div class="voting-section">
                    <h6 class="text-center mb-3">Califica esta presentación:</h6>
                    
                    <div class="vote-buttons">
                        <div class="vote-btn vote-btn-1 ${!eligibility.can_vote ? 'disabled' : ''}" onclick="castVote('${video.id}', 1)">
                            <span>⭐</span>
                            <span>Bien</span>
                        </div>
                        <div class="vote-btn vote-btn-2 ${!eligibility.can_vote ? 'disabled' : ''}" onclick="castVote('${video.id}', 2)">
                            <span>⭐⭐</span>
                            <span>Muy Bien</span>
                        </div>
                        <div class="vote-btn vote-btn-3 ${!eligibility.can_vote ? 'disabled' : ''}" onclick="castVote('${video.id}', 3)">
                            <span>⭐⭐⭐</span>
                            <span>Excelente</span>
                        </div>
                        <div class="vote-btn vote-btn-4 ${!eligibility.can_vote ? 'disabled' : ''}" onclick="castVote('${video.id}', 4)">
                            <span>⭐⭐⭐⭐</span>
                            <span>Maravilloso</span>
                        </div>
                        <div class="vote-btn vote-btn-5 ${!eligibility.can_vote ? 'disabled' : ''}" onclick="castVote('${video.id}', 5)">
                            <span>⭐⭐⭐⭐⭐</span>
                            <span>Fenomenal</span>
                        </div>
                    </div>
                    
                    ${eligibility.has_voted ? 
                        '<div class="alert alert-success text-center">✅ Ya votaste por este video</div>' : 
                        !eligibility.can_vote ? 
                            '<div class="alert alert-warning text-center">⚠️ No puedes votar por este video</div>' : 
                            '<div class="text-center text-muted">Selecciona tu calificación</div>'
                    }
                    
                    <div class="vote-results">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Votos totales:</strong> ${eligibility.current_votes || 0}
                            </div>
                            <div class="col-md-6 text-end">
                                <strong>Promedio:</strong> ${(eligibility.current_average || 0).toFixed(1)} ⭐
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            return card;
        }

        // Cast vote
        async function castVote(videoId, voteValue) {
            try {
                const response = await fetch('api/votes', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        video_id: videoId,
                        vote_value: voteValue,
                        modality: currentModality
                    })
                });
                
                if (response.ok) {
                    const result = await response.json();
                    showAlert(`✅ ¡Voto registrado! Calificaste con ${result.vote_label} a ${result.participant_name}`, 'success');
                    
                    // Reload videos to update voting status
                    await loadVideos();
                    await loadResults();
                    
                } else {
                    const error = await response.json();
                    showAlert(`❌ Error: ${error.error}`, 'danger');
                }
                
            } catch (error) {
                console.error('Error casting vote:', error);
                showAlert('❌ Error de conexión. Por favor intenta nuevamente.', 'danger');
            }
        }

        // Load and display results
        async function loadResults() {
            try {
                const response = await fetch('api/votes/results');
                if (!response.ok) throw new Error('Failed to load results');
                
                const data = await response.json();
                displayResults(data);
                
            } catch (error) {
                console.error('Error loading results:', error);
            }
        }

        // Display results
        function displayResults(data) {
            const resultsContainer = document.getElementById('overallResults');
            
            if (data.results.length === 0) {
                resultsContainer.innerHTML = '<div class="text-center text-muted">No hay resultados disponibles aún.</div>';
                return;
            }
            
            let resultsHTML = `
                <div class="row mb-4">
                    <div class="col-md-3 text-center">
                        <div class="average-score">
                            <h4>Videos con Votos</h4>
                            <h2>${data.statistics.total_videos_with_votes}</h2>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="average-score">
                            <h4>Total Votos</h4>
                            <h2>${data.statistics.total_votes_cast}</h2>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="average-score">
                            <h4>Promedio General</h4>
                            <h2>${data.statistics.overall_average_score.toFixed(1)} ⭐</h2>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="average-score">
                            <h4>Votantes Únicos</h4>
                            <h2>${data.statistics.unique_voters}</h2>
                        </div>
                    </div>
                </div>
                
                <h4 class="mb-3">🏆 Ranking de Participantes</h4>
            `;
            
            data.results.forEach((result, index) => {
                const rankIcon = index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : `${index + 1}º`;
                const maxVotes = Math.max(...data.results.map(r => r.total_votes));
                
                resultsHTML += `
                    <div class="video-card mb-3">
                        <div class="participant-info">
                            <div class="row align-items-center">
                                <div class="col-md-1 text-center">
                                    <h3>${rankIcon}</h3>
                                </div>
                                <div class="col-md-4">
                                    <div class="participant-name">${result.participant_name}</div>
                                    <div class="participant-location">📍 ${result.municipality}</div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="average-score">
                                        <h5>Promedio</h5>
                                        <h3>${result.average_score.toFixed(1)} ⭐</h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="result-bar">
                                        <span class="result-label">Bien:</span>
                                        <div class="result-progress">
                                            <div class="result-fill" style="width: ${(result.votes_breakdown.bien / maxVotes) * 100}%"></div>
                                        </div>
                                        <span class="result-count">${result.votes_breakdown.bien}</span>
                                    </div>
                                    <div class="result-bar">
                                        <span class="result-label">Muy Bien:</span>
                                        <div class="result-progress">
                                            <div class="result-fill" style="width: ${(result.votes_breakdown.muy_bien / maxVotes) * 100}%"></div>
                                        </div>
                                        <span class="result-count">${result.votes_breakdown.muy_bien}</span>
                                    </div>
                                    <div class="result-bar">
                                        <span class="result-label">Excelente:</span>
                                        <div class="result-progress">
                                            <div class="result-fill" style="width: ${(result.votes_breakdown.excelente / maxVotes) * 100}%"></div>
                                        </div>
                                        <span class="result-count">${result.votes_breakdown.excelente}</span>
                                    </div>
                                    <div class="result-bar">
                                        <span class="result-label">Maravilloso:</span>
                                        <div class="result-progress">
                                            <div class="result-fill" style="width: ${(result.votes_breakdown.maravilloso / maxVotes) * 100}%"></div>
                                        </div>
                                        <span class="result-count">${result.votes_breakdown.maravilloso}</span>
                                    </div>
                                    <div class="result-bar">
                                        <span class="result-label">Fenomenal:</span>
                                        <div class="result-progress">
                                            <div class="result-fill" style="width: ${(result.votes_breakdown.fenomenal / maxVotes) * 100}%"></div>
                                        </div>
                                        <span class="result-count">${result.votes_breakdown.fenomenal}</span>
                                    </div>
                                    <div style="text-align: center; margin-top: 10px;">
                                        <strong>Total: ${result.total_votes} votos</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            resultsContainer.innerHTML = resultsHTML;
        }

        // Show no videos message
        function showNoVideos() {
            document.getElementById('loadingMessage').style.display = 'none';
            document.getElementById('noVideos').style.display = 'block';
        }

        // Show error message
        function showError(message) {
            const videosList = document.getElementById('videosList');
            videosList.innerHTML = `<div class="alert alert-danger text-center">${message}</div>`;
            document.getElementById('loadingMessage').style.display = 'none';
            videosList.style.display = 'block';
        }

        // Show alert message
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.container').firstChild);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadVideos();
            loadResults();
        });
    </script>
</body>
</html>