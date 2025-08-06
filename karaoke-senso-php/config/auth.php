<?php
require_once 'database.php';

// JWT simulation using sessions for simplicity in XAMPP environment
class Auth {
    private $db;
    private $secret_key = "karaoke-senso-secret-key-2025";
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        session_start();
    }
    
    public function login($email, $password) {
        try {
            $query = "SELECT id, email, password_hash, is_admin FROM users WHERE email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Create session token
                $token = base64_encode(json_encode([
                    'user_id' => $user['id'],
                    'email' => $user['email'],
                    'is_admin' => $user['is_admin'],
                    'expires' => time() + (24 * 60 * 60) // 24 hours
                ]));
                
                $_SESSION['auth_token'] = $token;
                
                return [
                    'success' => true,
                    'access_token' => $token,
                    'token_type' => 'bearer'
                ];
            }
            
            return ['success' => false, 'message' => 'Invalid credentials'];
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed'];
        }
    }
    
    public function validateToken($token = null) {
        if (!$token) {
            $token = $_SESSION['auth_token'] ?? null;
        }
        
        if (!$token) {
            return false;
        }
        
        try {
            $decoded = json_decode(base64_decode($token), true);
            
            if (!$decoded || $decoded['expires'] < time()) {
                return false;
            }
            
            return $decoded;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function requireAuth() {
        $user = $this->validateToken();
        if (!$user) {
            errorResponse('Unauthorized', 401);
        }
        return $user;
    }
    
    public function requireAdmin() {
        $user = $this->requireAuth();
        if (!$user['is_admin']) {
            errorResponse('Admin access required', 403);
        }
        return $user;
    }
    
    public function logout() {
        unset($_SESSION['auth_token']);
        session_destroy();
        return ['success' => true, 'message' => 'Logged out'];
    }
}

// Create default admin user if doesn't exist
function createDefaultAdmin() {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT COUNT(*) as count FROM users WHERE email = 'admin@karaokesenso.com'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        if ($result['count'] == 0) {
            $id = generateUUID();
            $password_hash = password_hash('Senso2025*', PASSWORD_DEFAULT);
            
            $query = "INSERT INTO users (id, email, password_hash, is_admin) VALUES (:id, :email, :password_hash, 1)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':email', $email = 'admin@karaokesenso.com');
            $stmt->bindParam(':password_hash', $password_hash);
            $stmt->execute();
            
            error_log("Default admin user created");
        }
    } catch (Exception $e) {
        error_log("Error creating default admin: " . $e->getMessage());
    }
}

// Initialize default admin on first run
createDefaultAdmin();
?>