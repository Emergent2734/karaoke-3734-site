<?php
require_once '../config/database.php';
require_once '../config/auth.php';

setCORSHeaders();

$auth = new Auth();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        if (strpos($path, '/login') !== false) {
            login($auth);
        } elseif (strpos($path, '/logout') !== false) {
            logout($auth);
        } else {
            errorResponse('Endpoint not found', 404);
        }
        break;
        
    default:
        errorResponse('Method not allowed', 405);
}

function login($auth) {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input || !isset($input['email']) || !isset($input['password'])) {
            errorResponse('Email and password required');
        }
        
        $result = $auth->login($input['email'], $input['password']);
        
        if ($result['success']) {
            jsonResponse([
                'access_token' => $result['access_token'],
                'token_type' => $result['token_type']
            ]);
        } else {
            errorResponse('Incorrect email or password', 401);
        }
        
    } catch (Exception $e) {
        error_log("Login API error: " . $e->getMessage());
        errorResponse('Login failed', 500);
    }
}

function logout($auth) {
    try {
        $result = $auth->logout();
        jsonResponse($result);
        
    } catch (Exception $e) {
        error_log("Logout API error: " . $e->getMessage());
        errorResponse('Logout failed', 500);
    }
}
?>