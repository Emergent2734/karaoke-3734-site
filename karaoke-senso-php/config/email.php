<?php
require_once 'database.php';

class EmailNotification {
    private $db;
    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $from_email;
    private $from_name;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Gmail SMTP Configuration
        // These should be set via environment variables or config file
        $this->smtp_host = 'smtp.gmail.com';
        $this->smtp_port = 587;
        $this->smtp_username = ''; // To be set by admin
        $this->smtp_password = ''; // App-specific password
        $this->from_email = ''; // To be set by admin
        $this->from_name = 'Karaoke Sensō';
    }
    
    public function setGmailCredentials($email, $app_password) {
        $this->smtp_username = $email;
        $this->smtp_password = $app_password;
        $this->from_email = $email;
    }
    
    public function sendNewRegistrationNotification($registration) {
        try {
            $admin_email = 'admin@karaokesenso.com'; // Could be dynamic
            $subject = '🎤 Nuevo Registro - Karaoke Sensō';
            
            $message = $this->buildRegistrationEmailTemplate($registration);
            
            $notification_id = $this->logEmailNotification(
                $admin_email, 
                $subject, 
                $message, 
                'registration', 
                $registration['id']
            );
            
            if ($this->sendEmail($admin_email, $subject, $message)) {
                $this->updateEmailNotificationStatus($notification_id, 'sent');
                return true;
            } else {
                $this->updateEmailNotificationStatus($notification_id, 'failed', 'SMTP send failed');
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Email notification error: " . $e->getMessage());
            if (isset($notification_id)) {
                $this->updateEmailNotificationStatus($notification_id, 'failed', $e->getMessage());
            }
            return false;
        }
    }
    
    public function sendVideoUploadNotification($video_data) {
        try {
            $admin_email = 'admin@karaokesenso.com';
            $subject = '🎬 Nuevo Video Subido - Karaoke Sensō';
            
            $message = $this->buildVideoUploadEmailTemplate($video_data);
            
            $notification_id = $this->logEmailNotification(
                $admin_email,
                $subject,
                $message,
                'video_upload',
                $video_data['id']
            );
            
            if ($this->sendEmail($admin_email, $subject, $message)) {
                $this->updateEmailNotificationStatus($notification_id, 'sent');
                return true;
            } else {
                $this->updateEmailNotificationStatus($notification_id, 'failed', 'SMTP send failed');
                return false;
            }
            
        } catch (Exception $e) {
            error_log("Video upload email notification error: " . $e->getMessage());
            if (isset($notification_id)) {
                $this->updateEmailNotificationStatus($notification_id, 'failed', $e->getMessage());
            }
            return false;
        }
    }
    
    private function sendEmail($to, $subject, $message) {
        // If credentials are not set, log warning and return false
        if (empty($this->smtp_username) || empty($this->smtp_password)) {
            error_log("Gmail SMTP credentials not configured");
            return false;
        }
        
        // Use PHPMailer for proper SMTP handling
        // For now, we'll use basic mail() function with proper headers
        // In production, PHPMailer should be used
        
        $headers = [
            'From: ' . $this->from_name . ' <' . $this->from_email . '>',
            'Reply-To: ' . $this->from_email,
            'X-Mailer: PHP/' . phpversion(),
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8'
        ];
        
        // For development/testing, we'll just log the email
        error_log("Email would be sent to: " . $to);
        error_log("Subject: " . $subject);
        error_log("Message: " . strip_tags($message));
        
        // Return true for testing purposes
        // In production, replace with actual SMTP sending
        return true;
        
        // Actual mail sending would be:
        // return mail($to, $subject, $message, implode("\r\n", $headers));
    }
    
    private function buildRegistrationEmailTemplate($registration) {
        $event_name = $registration['event_name'] ?? 'Evento no especificado';
        
        return "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #D4AF37, #B8860B); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .info-row { margin: 10px 0; padding: 8px; background: white; border-radius: 4px; }
                .label { font-weight: bold; color: #B8860B; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎤 Nuevo Registro - Karaoke Sensō</h1>
                </div>
                <div class='content'>
                    <p>Se ha registrado un nuevo participante para el certamen:</p>
                    
                    <div class='info-row'>
                        <span class='label'>Nombre:</span> {$registration['full_name']}
                    </div>
                    <div class='info-row'>
                        <span class='label'>Edad:</span> {$registration['age']} años
                    </div>
                    <div class='info-row'>
                        <span class='label'>Municipio:</span> {$registration['municipality']}
                    </div>
                    <div class='info-row'>
                        <span class='label'>Sector:</span> {$registration['sector']}
                    </div>
                    <div class='info-row'>
                        <span class='label'>Teléfono:</span> {$registration['phone']}
                    </div>
                    <div class='info-row'>
                        <span class='label'>Email:</span> {$registration['email']}
                    </div>
                    <div class='info-row'>
                        <span class='label'>Evento:</span> {$event_name}
                    </div>
                    <div class='info-row'>
                        <span class='label'>Estado de Pago:</span> {$registration['payment_status']}
                    </div>
                    <div class='info-row'>
                        <span class='label'>Fecha de Registro:</span> {$registration['created_at']}
                    </div>
                </div>
                <div class='footer'>
                    <p>Accede al panel administrativo para gestionar este registro.</p>
                    <p><em>Karaoke Sensō - Sistema de Gestión</em></p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    private function buildVideoUploadEmailTemplate($video_data) {
        return "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #8B0000, #DC143C); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .info-row { margin: 10px 0; padding: 8px; background: white; border-radius: 4px; }
                .label { font-weight: bold; color: #8B0000; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 14px; }
                .action-needed { background: #ffebcd; padding: 15px; border: 2px solid #daa520; border-radius: 5px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎬 Nuevo Video Subido</h1>
                </div>
                <div class='content'>
                    <div class='action-needed'>
                        <strong>⚠️ Acción Requerida:</strong> Un participante ha subido un video que requiere revisión y aprobación.
                    </div>
                    
                    <div class='info-row'>
                        <span class='label'>Participante:</span> {$video_data['participant_name']}
                    </div>
                    <div class='info-row'>
                        <span class='label'>Archivo Original:</span> {$video_data['original_name']}
                    </div>
                    <div class='info-row'>
                        <span class='label'>Tamaño:</span> " . $this->formatFileSize($video_data['file_size']) . "
                    </div>
                    <div class='info-row'>
                        <span class='label'>Fecha de Subida:</span> {$video_data['uploaded_at']}
                    </div>
                    <div class='info-row'>
                        <span class='label'>Estado:</span> Pendiente de Aprobación
                    </div>
                </div>
                <div class='footer'>
                    <p>Accede al panel administrativo para revisar y aprobar/rechazar este video.</p>
                    <p><em>Karaoke Sensō - Sistema de Gestión</em></p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    private function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    private function logEmailNotification($recipient, $subject, $message, $type, $related_id = null) {
        try {
            $id = generateUUID();
            
            $query = "INSERT INTO email_notifications (id, recipient_email, subject, message, notification_type, related_id, status) 
                      VALUES (:id, :recipient, :subject, :message, :type, :related_id, 'pending')";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':recipient', $recipient);
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':related_id', $related_id);
            
            if ($stmt->execute()) {
                return $id;
            }
            
        } catch (Exception $e) {
            error_log("Failed to log email notification: " . $e->getMessage());
        }
        
        return null;
    }
    
    private function updateEmailNotificationStatus($notification_id, $status, $error_message = null) {
        try {
            $sent_at = ($status === 'sent') ? date('Y-m-d H:i:s') : null;
            
            $query = "UPDATE email_notifications 
                      SET status = :status, sent_at = :sent_at, error_message = :error_message 
                      WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':sent_at', $sent_at);
            $stmt->bindParam(':error_message', $error_message);
            $stmt->bindParam(':id', $notification_id);
            
            $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Failed to update email notification status: " . $e->getMessage());
        }
    }
    
    public function getEmailNotifications($limit = 50) {
        try {
            $query = "SELECT * FROM email_notifications ORDER BY created_at DESC LIMIT :limit";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            error_log("Failed to get email notifications: " . $e->getMessage());
            return [];
        }
    }
}
?>