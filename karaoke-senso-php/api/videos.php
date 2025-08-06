<?php
require_once '../config/database.php';
require_once '../config/auth.php';

setCORSHeaders();

$database = new Database();
$db = $database->getConnection();
$auth = new Auth();

$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

// Handle different video operations
if ($method === 'POST' && strpos($request_uri, '/upload') !== false) {
    uploadVideo($db);
} elseif ($method === 'PUT' && strpos($request_uri, '/approve') !== false) {
    approveVideo($db, $auth);
} elseif ($method === 'GET' && strpos($request_uri, '/public') !== false) {
    getPublicVideos($db);
} else {
    switch ($method) {
        case 'GET':
            getVideos($db, $auth);
            break;
            
        case 'DELETE':
            deleteVideo($db, $auth);
            break;
            
        default:
            errorResponse('Method not allowed', 405);
    }
}

function uploadVideo($db) {
    try {
        // Check if file was uploaded
        if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
            errorResponse('No video file uploaded or upload error occurred');
        }
        
        // Check if registration_id is provided
        if (!isset($_POST['registration_id']) || empty($_POST['registration_id'])) {
            errorResponse('Registration ID is required');
        }
        
        $registration_id = $_POST['registration_id'];
        $file = $_FILES['video'];
        
        // Verify registration exists
        $query = "SELECT id, full_name, email FROM registrations WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $registration_id);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            errorResponse('Registration not found', 404);
        }
        
        $registration = $stmt->fetch();
        
        // Check if participant already uploaded a video
        $query = "SELECT id FROM videos WHERE registration_id = :registration_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':registration_id', $registration_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            errorResponse('Video already uploaded for this registration');
        }
        
        // File validation
        $allowed_types = ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo'];
        $max_size = 50 * 1024 * 1024; // 50MB in bytes
        
        if (!in_array($file['type'], $allowed_types)) {
            errorResponse('Invalid file type. Only MP4, MPEG, MOV, and AVI files are allowed');
        }
        
        if ($file['size'] > $max_size) {
            errorResponse('File size exceeds 50MB limit');
        }
        
        // Create uploads directory if it doesn't exist
        $upload_dir = '../uploads/videos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generate unique filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = generateUUID() . '.' . $file_extension;
        $file_path = $upload_dir . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            errorResponse('Failed to save video file', 500);
        }
        
        // Save video record to database
        $video_id = generateUUID();
        $query = "INSERT INTO videos (id, registration_id, filename, original_name, file_size, file_path, mime_type, upload_status) 
                  VALUES (:id, :registration_id, :filename, :original_name, :file_size, :file_path, :mime_type, 'uploaded')";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $video_id);
        $stmt->bindParam(':registration_id', $registration_id);
        $stmt->bindParam(':filename', $filename);
        $stmt->bindParam(':original_name', $file['name']);
        $stmt->bindParam(':file_size', $file['size']);
        $stmt->bindParam(':file_path', $file_path);
        $stmt->bindParam(':mime_type', $file['type']);
        
        if (!$stmt->execute()) {
            // Delete uploaded file if database insert fails
            unlink($file_path);
            errorResponse('Failed to save video record', 500);
        }
        
        // Update registration to mark it has video
        $query = "UPDATE registrations SET has_video = TRUE, video_upload_date = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $registration_id);
        $stmt->execute();
        
        // TODO: Send email notification to admin
        // This will be implemented when we add email functionality
        
        $video_data = [
            'id' => $video_id,
            'registration_id' => $registration_id,
            'participant_name' => $registration['full_name'],
            'filename' => $filename,
            'original_name' => $file['name'],
            'file_size' => $file['size'],
            'upload_status' => 'uploaded',
            'uploaded_at' => date('Y-m-d H:i:s')
        ];
        
        jsonResponse($video_data, 201);
        
    } catch (Exception $e) {
        error_log("Video upload error: " . $e->getMessage());
        errorResponse('Failed to upload video', 500);
    }
}

function getVideos($db, $auth) {
    // Require admin authentication
    $user = $auth->requireAdmin();
    
    try {
        $query = "SELECT v.*, r.full_name, r.municipality, r.email 
                  FROM videos v 
                  JOIN registrations r ON v.registration_id = r.id 
                  ORDER BY v.uploaded_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $videos = [];
        while ($row = $stmt->fetch()) {
            $videos[] = [
                'id' => $row['id'],
                'registration_id' => $row['registration_id'],
                'participant_name' => $row['full_name'],
                'municipality' => $row['municipality'],
                'email' => $row['email'],
                'filename' => $row['filename'],
                'original_name' => $row['original_name'],
                'file_size' => intval($row['file_size']),
                'upload_status' => $row['upload_status'],
                'admin_notes' => $row['admin_notes'],
                'uploaded_at' => $row['uploaded_at'],
                'approved_at' => $row['approved_at']
            ];
        }
        
        jsonResponse($videos);
        
    } catch (Exception $e) {
        error_log("Get videos error: " . $e->getMessage());
        errorResponse('Failed to fetch videos', 500);
    }
}

function getPublicVideos($db) {
    try {
        $query = "SELECT v.id, v.filename, r.full_name, r.municipality,
                         COUNT(vo.id) as vote_count,
                         AVG(vo.vote_value) as average_score
                  FROM videos v 
                  JOIN registrations r ON v.registration_id = r.id 
                  LEFT JOIN votes vo ON v.id = vo.video_id
                  WHERE v.upload_status = 'approved'
                  GROUP BY v.id, v.filename, r.full_name, r.municipality
                  ORDER BY average_score DESC, vote_count DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $videos = [];
        while ($row = $stmt->fetch()) {
            $videos[] = [
                'id' => $row['id'],
                'filename' => $row['filename'],
                'participant_name' => $row['full_name'],
                'municipality' => $row['municipality'],
                'vote_count' => intval($row['vote_count']),
                'average_score' => floatval($row['average_score'] ?? 0)
            ];
        }
        
        jsonResponse($videos);
        
    } catch (Exception $e) {
        error_log("Get public videos error: " . $e->getMessage());
        errorResponse('Failed to fetch public videos', 500);
    }
}

function approveVideo($db, $auth) {
    // Require admin authentication
    $user = $auth->requireAdmin();
    
    try {
        // Extract video ID from URL
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathParts = explode('/', $path);
        $videoId = null;
        
        for ($i = 0; $i < count($pathParts); $i++) {
            if ($pathParts[$i] === 'videos' && isset($pathParts[$i + 1])) {
                $videoId = $pathParts[$i + 1];
                break;
            }
        }
        
        if (!$videoId) {
            errorResponse('Video ID not found in URL');
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $status = $input['status'] ?? null;
        $admin_notes = $input['admin_notes'] ?? null;
        
        if (!$status || !in_array($status, ['approved', 'rejected'])) {
            errorResponse('Invalid status. Must be approved or rejected');
        }
        
        $approved_at = ($status === 'approved') ? date('Y-m-d H:i:s') : null;
        
        $query = "UPDATE videos 
                  SET upload_status = :status, admin_notes = :admin_notes, 
                      approved_at = :approved_at, approved_by = :approved_by 
                  WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':admin_notes', $admin_notes);
        $stmt->bindParam(':approved_at', $approved_at);
        $stmt->bindParam(':approved_by', $user['id']);
        $stmt->bindParam(':id', $videoId);
        
        if ($stmt->execute() && $stmt->rowCount() > 0) {
            jsonResponse(['message' => 'Video status updated to ' . $status]);
        } else {
            errorResponse('Video not found', 404);
        }
        
    } catch (Exception $e) {
        error_log("Approve video error: " . $e->getMessage());
        errorResponse('Failed to update video status', 500);
    }
}

function deleteVideo($db, $auth) {
    // Require admin authentication
    $user = $auth->requireAdmin();
    
    try {
        // Extract video ID from URL
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathParts = explode('/', $path);
        $videoId = null;
        
        for ($i = 0; $i < count($pathParts); $i++) {
            if ($pathParts[$i] === 'videos' && isset($pathParts[$i + 1])) {
                $videoId = $pathParts[$i + 1];
                break;
            }
        }
        
        if (!$videoId) {
            errorResponse('Video ID not found in URL');
        }
        
        // Get video file path before deletion
        $query = "SELECT file_path FROM videos WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $videoId);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            errorResponse('Video not found', 404);
        }
        
        $video = $stmt->fetch();
        $file_path = $video['file_path'];
        
        // Delete from database
        $query = "DELETE FROM videos WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $videoId);
        
        if ($stmt->execute()) {
            // Delete physical file
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            
            jsonResponse(['message' => 'Video deleted successfully']);
        } else {
            errorResponse('Failed to delete video', 500);
        }
        
    } catch (Exception $e) {
        error_log("Delete video error: " . $e->getMessage());
        errorResponse('Failed to delete video', 500);
    }
}
?>