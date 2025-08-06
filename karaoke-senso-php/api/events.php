<?php
require_once '../config/database.php';
require_once '../config/auth.php';

setCORSHeaders();

$database = new Database();
$db = $database->getConnection();
$auth = new Auth();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getEvents($db);
        break;
        
    case 'POST':
        createEvent($db, $auth);
        break;
        
    default:
        errorResponse('Method not allowed', 405);
}

function getEvents($db) {
    try {
        $query = "SELECT * FROM events ORDER BY date ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $events = [];
        while ($row = $stmt->fetch()) {
            $events[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'municipality' => $row['municipality'],
                'venue' => $row['venue'],
                'date' => $row['date'],
                'max_participants' => intval($row['max_participants']),
                'created_at' => $row['created_at']
            ];
        }
        
        jsonResponse($events);
        
    } catch (Exception $e) {
        error_log("Get events error: " . $e->getMessage());
        errorResponse('Failed to fetch events', 500);
    }
}

function createEvent($db, $auth) {
    // Require admin authentication
    $user = $auth->requireAdmin();
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['name']) || !isset($input['municipality']) || 
            !isset($input['venue']) || !isset($input['date'])) {
            errorResponse('Missing required fields');
        }
        
        $id = generateUUID();
        $max_participants = $input['max_participants'] ?? 50;
        
        $query = "INSERT INTO events (id, name, municipality, venue, date, max_participants) 
                  VALUES (:id, :name, :municipality, :venue, :date, :max_participants)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $input['name']);
        $stmt->bindParam(':municipality', $input['municipality']);
        $stmt->bindParam(':venue', $input['venue']);
        $stmt->bindParam(':date', $input['date']);
        $stmt->bindParam(':max_participants', $max_participants);
        
        if ($stmt->execute()) {
            $event = [
                'id' => $id,
                'name' => $input['name'],
                'municipality' => $input['municipality'],
                'venue' => $input['venue'],
                'date' => $input['date'],
                'max_participants' => intval($max_participants),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            jsonResponse($event, 201);
        } else {
            errorResponse('Failed to create event', 500);
        }
        
    } catch (Exception $e) {
        error_log("Create event error: " . $e->getMessage());
        errorResponse('Failed to create event', 500);
    }
}
?>