<?php
require_once '../config/database.php';

setCORSHeaders();

$database = new Database();
$db = $database->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

// Handle different voting operations
if ($method === 'POST') {
    castVote($db);
} elseif ($method === 'GET' && strpos($request_uri, '/results') !== false) {
    getVoteResults($db);
} elseif ($method === 'GET' && strpos($request_uri, '/check') !== false) {
    checkVotingEligibility($db);
} else {
    switch ($method) {
        case 'GET':
            getVotes($db);
            break;
            
        default:
            errorResponse('Method not allowed', 405);
    }
}

function castVote($db) {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['video_id']) || !isset($input['vote_value'])) {
            errorResponse('Missing required fields: video_id and vote_value');
        }
        
        $video_id = $input['video_id'];
        $vote_value = intval($input['vote_value']);
        $modality = $input['modality'] ?? 'virtual';
        
        // Validate vote value
        if ($vote_value < 1 || $vote_value > 5) {
            errorResponse('Vote value must be between 1 and 5');
        }
        
        // Validate modality
        if (!in_array($modality, ['presencial', 'virtual'])) {
            errorResponse('Invalid modality. Must be presencial or virtual');
        }
        
        // Map vote value to label
        $vote_labels = [
            1 => 'Bien',
            2 => 'Muy Bien',
            3 => 'Excelente',
            4 => 'Maravilloso',
            5 => 'Fenomenal'
        ];
        $vote_label = $vote_labels[$vote_value];
        
        // Get voter information
        $voter_ip = getClientIpAddress();
        $session_id = session_id();
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // If no session ID, create one
        if (empty($session_id)) {
            $session_id = generateUUID();
            session_id($session_id);
        }
        
        // Check if video exists and is approved
        $query = "SELECT v.id, r.full_name FROM videos v 
                  JOIN registrations r ON v.registration_id = r.id 
                  WHERE v.id = :video_id AND v.upload_status = 'approved'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':video_id', $video_id);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            errorResponse('Video not found or not approved for voting', 404);
        }
        
        $video = $stmt->fetch();
        
        // Check if user already voted for this video
        $query = "SELECT id FROM votes WHERE video_id = :video_id AND voter_ip = :voter_ip AND session_id = :session_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':video_id', $video_id);
        $stmt->bindParam(':voter_ip', $voter_ip);
        $stmt->bindParam(':session_id', $session_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            errorResponse('You have already voted for this video');
        }
        
        // Record or update vote session
        $session_uuid = generateUUID();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $query = "INSERT INTO vote_sessions (id, session_id, voter_ip, user_agent, total_votes) 
                  VALUES (:id, :session_id, :voter_ip, :user_agent, 1)
                  ON DUPLICATE KEY UPDATE 
                  last_vote_at = CURRENT_TIMESTAMP,
                  total_votes = total_votes + 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $session_uuid);
        $stmt->bindParam(':session_id', $session_id);
        $stmt->bindParam(':voter_ip', $voter_ip);
        $stmt->bindParam(':user_agent', $user_agent);
        $stmt->execute();
        
        // Cast the vote
        $vote_id = generateUUID();
        $query = "INSERT INTO votes (id, video_id, vote_value, vote_label, voter_ip, session_id, modality) 
                  VALUES (:id, :video_id, :vote_value, :vote_label, :voter_ip, :session_id, :modality)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $vote_id);
        $stmt->bindParam(':video_id', $video_id);
        $stmt->bindParam(':vote_value', $vote_value);
        $stmt->bindParam(':vote_label', $vote_label);
        $stmt->bindParam(':voter_ip', $voter_ip);
        $stmt->bindParam(':session_id', $session_id);
        $stmt->bindParam(':modality', $modality);
        
        if ($stmt->execute()) {
            $vote_data = [
                'id' => $vote_id,
                'video_id' => $video_id,
                'participant_name' => $video['full_name'],
                'vote_value' => $vote_value,
                'vote_label' => $vote_label,
                'modality' => $modality,
                'cast_at' => date('Y-m-d H:i:s'),
                'message' => 'Vote cast successfully'
            ];
            
            jsonResponse($vote_data, 201);
        } else {
            errorResponse('Failed to cast vote', 500);
        }
        
    } catch (Exception $e) {
        error_log("Cast vote error: " . $e->getMessage());
        errorResponse('Failed to cast vote', 500);
    }
}

function getVoteResults($db) {
    try {
        // Get results from the view we created
        $query = "SELECT * FROM vote_results ORDER BY average_score DESC, total_votes DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[] = [
                'video_id' => $row['video_id'],
                'filename' => $row['filename'],
                'participant_name' => $row['participant_name'],
                'municipality' => $row['municipality'],
                'total_votes' => intval($row['total_votes']),
                'average_score' => floatval($row['average_score'] ?? 0),
                'votes_breakdown' => [
                    'bien' => intval($row['votes_bien'] ?? 0),
                    'muy_bien' => intval($row['votes_muy_bien'] ?? 0),
                    'excelente' => intval($row['votes_excelente'] ?? 0),
                    'maravilloso' => intval($row['votes_maravilloso'] ?? 0),
                    'fenomenal' => intval($row['votes_fenomenal'] ?? 0)
                ]
            ];
        }
        
        // Also get overall statistics
        $query = "SELECT 
                    COUNT(DISTINCT v.video_id) as total_videos_with_votes,
                    COUNT(v.id) as total_votes_cast,
                    AVG(v.vote_value) as overall_average,
                    COUNT(DISTINCT CONCAT(v.voter_ip, v.session_id)) as unique_voters
                  FROM votes v";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats = $stmt->fetch();
        
        $response = [
            'results' => $results,
            'statistics' => [
                'total_videos_with_votes' => intval($stats['total_videos_with_votes']),
                'total_votes_cast' => intval($stats['total_votes_cast']),
                'overall_average_score' => floatval($stats['overall_average'] ?? 0),
                'unique_voters' => intval($stats['unique_voters'])
            ]
        ];
        
        jsonResponse($response);
        
    } catch (Exception $e) {
        error_log("Get vote results error: " . $e->getMessage());
        errorResponse('Failed to get vote results', 500);
    }
}

function checkVotingEligibility($db) {
    try {
        if (!isset($_GET['video_id'])) {
            errorResponse('Video ID is required');
        }
        
        $video_id = $_GET['video_id'];
        $voter_ip = getClientIpAddress();
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $session_id = session_id();
        
        // Check if user already voted for this video
        $query = "SELECT id FROM votes WHERE video_id = :video_id AND voter_ip = :voter_ip AND session_id = :session_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':video_id', $video_id);
        $stmt->bindParam(':voter_ip', $voter_ip);
        $stmt->bindParam(':session_id', $session_id);
        $stmt->execute();
        
        $has_voted = $stmt->rowCount() > 0;
        
        // Get current vote count for this video
        $query = "SELECT COUNT(*) as vote_count, AVG(vote_value) as average_score 
                  FROM votes WHERE video_id = :video_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':video_id', $video_id);
        $stmt->execute();
        $vote_info = $stmt->fetch();
        
        $response = [
            'can_vote' => !$has_voted,
            'has_voted' => $has_voted,
            'video_id' => $video_id,
            'current_votes' => intval($vote_info['vote_count']),
            'current_average' => floatval($vote_info['average_score'] ?? 0),
            'voter_session' => $session_id
        ];
        
        jsonResponse($response);
        
    } catch (Exception $e) {
        error_log("Check voting eligibility error: " . $e->getMessage());
        errorResponse('Failed to check voting eligibility', 500);
    }
}

function getVotes($db) {
    // This would be admin-only function, but for now we'll keep it simple
    try {
        $query = "SELECT v.*, vi.filename, r.full_name 
                  FROM votes v 
                  JOIN videos vi ON v.video_id = vi.id 
                  JOIN registrations r ON vi.registration_id = r.id 
                  ORDER BY v.created_at DESC 
                  LIMIT 100";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $votes = [];
        while ($row = $stmt->fetch()) {
            $votes[] = [
                'id' => $row['id'],
                'video_id' => $row['video_id'],
                'participant_name' => $row['full_name'],
                'filename' => $row['filename'],
                'vote_value' => intval($row['vote_value']),
                'vote_label' => $row['vote_label'],
                'modality' => $row['modality'],
                'voter_ip' => $row['voter_ip'],
                'created_at' => $row['created_at']
            ];
        }
        
        jsonResponse($votes);
        
    } catch (Exception $e) {
        error_log("Get votes error: " . $e->getMessage());
        errorResponse('Failed to get votes', 500);
    }
}

// Helper function to get client IP address
function getClientIpAddress() {
    $ip_keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            $ip = $_SERVER[$key];
            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}
?>