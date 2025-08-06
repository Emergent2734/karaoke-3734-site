<?php
require_once '../config/database.php';
require_once '../config/auth.php';

setCORSHeaders();

$database = new Database();
$db = $database->getConnection();
$auth = new Auth();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        getBrands($db);
        break;
        
    case 'POST':
        createBrand($db, $auth);
        break;
        
    default:
        errorResponse('Method not allowed', 405);
}

function getBrands($db) {
    try {
        $query = "SELECT * FROM brands ORDER BY created_at ASC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $brands = [];
        while ($row = $stmt->fetch()) {
            $brands[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'logo_url' => $row['logo_url'],
                'created_at' => $row['created_at']
            ];
        }
        
        jsonResponse($brands);
        
    } catch (Exception $e) {
        error_log("Get brands error: " . $e->getMessage());
        errorResponse('Failed to fetch brands', 500);
    }
}

function createBrand($db, $auth) {
    // Require admin authentication
    $user = $auth->requireAdmin();
    
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['name']) || !isset($input['logo_url'])) {
            errorResponse('Missing required fields');
        }
        
        $id = generateUUID();
        
        $query = "INSERT INTO brands (id, name, logo_url) VALUES (:id, :name, :logo_url)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $input['name']);
        $stmt->bindParam(':logo_url', $input['logo_url']);
        
        if ($stmt->execute()) {
            $brand = [
                'id' => $id,
                'name' => $input['name'],
                'logo_url' => $input['logo_url'],
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            jsonResponse($brand, 201);
        } else {
            errorResponse('Failed to create brand', 500);
        }
        
    } catch (Exception $e) {
        error_log("Create brand error: " . $e->getMessage());
        errorResponse('Failed to create brand', 500);
    }
}
?>