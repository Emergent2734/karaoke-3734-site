<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../config/email.php';

setCORSHeaders();

$database = new Database();
$db = $database->getConnection();
$auth = new Auth();

$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

// Handle payment status updates
if ($method === 'PUT' && strpos($request_uri, '/payment') !== false) {
    updatePaymentStatus($db, $auth);
} else {
    switch ($method) {
        case 'GET':
            getRegistrations($db, $auth);
            break;
            
        case 'POST':
            createRegistration($db);
            break;
            
        default:
            errorResponse('Method not allowed', 405);
    }
}

function getRegistrations($db, $auth) {
    // Require admin authentication
    $user = $auth->requireAdmin();
    
    try {
        $query = "SELECT r.*, e.name as event_name FROM registrations r 
                  LEFT JOIN events e ON r.event_id = e.id 
                  ORDER BY r.created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $registrations = [];
        while ($row = $stmt->fetch()) {
            $registrations[] = [
                'id' => $row['id'],
                'full_name' => $row['full_name'],
                'age' => intval($row['age']),
                'municipality' => $row['municipality'],
                'sector' => $row['sector'],
                'phone' => $row['phone'],
                'email' => $row['email'],
                'event_id' => $row['event_id'],
                'event_name' => $row['event_name'],
                'payment_status' => $row['payment_status'],
                'video_url' => $row['video_url'],
                'created_at' => $row['created_at']
            ];
        }
        
        jsonResponse($registrations);
        
    } catch (Exception $e) {
        error_log("Get registrations error: " . $e->getMessage());
        errorResponse('Failed to fetch registrations', 500);
    }
}

function createRegistration($db) {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['full_name']) || !isset($input['age']) || 
            !isset($input['municipality']) || !isset($input['sector']) ||
            !isset($input['phone']) || !isset($input['email']) || !isset($input['event_id'])) {
            errorResponse('Missing required fields');
        }
        
        // Check if event exists
        $query = "SELECT id FROM events WHERE id = :event_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':event_id', $input['event_id']);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            errorResponse('Event not found', 404);
        }
        
        // Check if user already registered for this event
        $query = "SELECT id FROM registrations WHERE email = :email AND event_id = :event_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $input['email']);
        $stmt->bindParam(':event_id', $input['event_id']);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            errorResponse('Already registered for this event');
        }
        
        $id = generateUUID();
        
        $query = "INSERT INTO registrations (id, full_name, age, municipality, sector, phone, email, event_id, payment_status) 
                  VALUES (:id, :full_name, :age, :municipality, :sector, :phone, :email, :event_id, 'pendiente')";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':full_name', $input['full_name']);
        $stmt->bindParam(':age', $input['age']);
        $stmt->bindParam(':municipality', $input['municipality']);
        $stmt->bindParam(':sector', $input['sector']);
        $stmt->bindParam(':phone', $input['phone']);
        $stmt->bindParam(':email', $input['email']);
        $stmt->bindParam(':event_id', $input['event_id']);
        
        if ($stmt->execute()) {
            // Get event name for email notification
            $query = "SELECT name FROM events WHERE id = :event_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':event_id', $input['event_id']);
            $stmt->execute();
            $event = $stmt->fetch();
            
            $registration = [
                'id' => $id,
                'full_name' => $input['full_name'],
                'age' => intval($input['age']),
                'municipality' => $input['municipality'],
                'sector' => $input['sector'],
                'phone' => $input['phone'],
                'email' => $input['email'],
                'event_id' => $input['event_id'],
                'event_name' => $event['name'] ?? 'Evento no especificado',
                'payment_status' => 'pendiente',
                'video_url' => null,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Send email notification to admin
            $emailNotifier = new EmailNotification();
            // Note: Gmail credentials need to be configured
            $emailNotifier->sendNewRegistrationNotification($registration);
            
            jsonResponse($registration, 201);
        } else {
            errorResponse('Failed to create registration', 500);
        }
        
    } catch (Exception $e) {
        error_log("Create registration error: " . $e->getMessage());
        errorResponse('Failed to create registration', 500);
    }
}

function updatePaymentStatus($db, $auth) {
    // Require admin authentication
    $user = $auth->requireAdmin();
    
    try {
        // Extract registration ID from URL
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathParts = explode('/', $path);
        $registrationId = null;
        
        for ($i = 0; $i < count($pathParts); $i++) {
            if ($pathParts[$i] === 'registrations' && isset($pathParts[$i + 1])) {
                $registrationId = $pathParts[$i + 1];
                break;
            }
        }
        
        if (!$registrationId) {
            errorResponse('Registration ID not found in URL');
        }
        
        // Get payment status from query parameter
        $payment_status = $_GET['payment_status'] ?? null;
        
        if (!$payment_status || !in_array($payment_status, ['pendiente', 'pagado'])) {
            errorResponse('Invalid payment status');
        }
        
        $query = "UPDATE registrations SET payment_status = :payment_status WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':payment_status', $payment_status);
        $stmt->bindParam(':id', $registrationId);
        
        if ($stmt->execute() && $stmt->rowCount() > 0) {
            jsonResponse(['message' => 'Payment status updated']);
        } else {
            errorResponse('Registration not found', 404);
        }
        
    } catch (Exception $e) {
        error_log("Update payment status error: " . $e->getMessage());
        errorResponse('Failed to update payment status', 500);
    }
}
?>