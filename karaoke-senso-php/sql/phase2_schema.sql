-- Karaoke Sensō - Phase 2 Database Extensions
-- Additional tables for voting system, video management, and notifications

USE karaoke_senso;

-- Videos table to track uploaded videos
CREATE TABLE videos (
    id VARCHAR(36) PRIMARY KEY,
    registration_id VARCHAR(36) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_size INT NOT NULL,
    file_path TEXT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    upload_status ENUM('uploaded', 'approved', 'rejected') DEFAULT 'uploaded',
    admin_notes TEXT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL,
    approved_by VARCHAR(36) NULL,
    FOREIGN KEY (registration_id) REFERENCES registrations(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_registration (registration_id),
    INDEX idx_status (upload_status),
    INDEX idx_uploaded_at (uploaded_at)
);

-- Votes table for public voting
CREATE TABLE votes (
    id VARCHAR(36) PRIMARY KEY,
    video_id VARCHAR(36) NOT NULL,
    vote_value INT NOT NULL CHECK (vote_value BETWEEN 1 AND 5),
    vote_label VARCHAR(50) NOT NULL,
    voter_ip VARCHAR(45) NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    modality ENUM('presencial', 'virtual') NOT NULL DEFAULT 'virtual',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (video_id) REFERENCES videos(id) ON DELETE CASCADE,
    INDEX idx_video (video_id),
    INDEX idx_voter_ip (voter_ip),
    INDEX idx_session (session_id),
    INDEX idx_created_at (created_at),
    UNIQUE KEY unique_vote_per_session (video_id, voter_ip, session_id)
);

-- Vote sessions table for duplicate control
CREATE TABLE vote_sessions (
    id VARCHAR(36) PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    voter_ip VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    first_vote_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_vote_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    total_votes INT DEFAULT 0,
    INDEX idx_session_ip (session_id, voter_ip),
    INDEX idx_first_vote (first_vote_at)
);

-- Email notifications log
CREATE TABLE email_notifications (
    id VARCHAR(36) PRIMARY KEY,
    recipient_email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    notification_type ENUM('registration', 'video_upload', 'other') NOT NULL,
    related_id VARCHAR(36) NULL, -- Can reference registrations, videos, etc.
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    sent_at TIMESTAMP NULL,
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_type (notification_type),
    INDEX idx_recipient (recipient_email),
    INDEX idx_created_at (created_at)
);

-- Update registrations table to link with videos
ALTER TABLE registrations 
ADD COLUMN has_video BOOLEAN DEFAULT FALSE,
ADD COLUMN video_upload_date TIMESTAMP NULL,
ADD INDEX idx_has_video (has_video);

-- Vote value mappings (for reference)
-- 1 = 'Bien'
-- 2 = 'Muy Bien' 
-- 3 = 'Excelente'
-- 4 = 'Maravilloso'
-- 5 = 'Fenomenal'

-- Create views for easier data access
CREATE VIEW registration_videos AS
SELECT 
    r.id as registration_id,
    r.full_name,
    r.municipality,
    r.email,
    r.payment_status,
    v.id as video_id,
    v.filename,
    v.original_name,
    v.file_size,
    v.upload_status,
    v.uploaded_at,
    v.approved_at,
    COALESCE(vs.vote_count, 0) as total_votes,
    COALESCE(vs.avg_score, 0) as average_score
FROM registrations r
LEFT JOIN videos v ON r.id = v.registration_id
LEFT JOIN (
    SELECT 
        video_id,
        COUNT(*) as vote_count,
        AVG(vote_value) as avg_score
    FROM votes 
    GROUP BY video_id
) vs ON v.id = vs.video_id;

CREATE VIEW vote_results AS
SELECT 
    v.id as video_id,
    v.filename,
    r.full_name as participant_name,
    r.municipality,
    COUNT(vo.id) as total_votes,
    AVG(vo.vote_value) as average_score,
    SUM(CASE WHEN vo.vote_value = 1 THEN 1 ELSE 0 END) as votes_bien,
    SUM(CASE WHEN vo.vote_value = 2 THEN 1 ELSE 0 END) as votes_muy_bien,
    SUM(CASE WHEN vo.vote_value = 3 THEN 1 ELSE 0 END) as votes_excelente,
    SUM(CASE WHEN vo.vote_value = 4 THEN 1 ELSE 0 END) as votes_maravilloso,
    SUM(CASE WHEN vo.vote_value = 5 THEN 1 ELSE 0 END) as votes_fenomenal
FROM videos v
LEFT JOIN registrations r ON v.registration_id = r.id
LEFT JOIN votes vo ON v.id = vo.video_id
WHERE v.upload_status = 'approved'
GROUP BY v.id, v.filename, r.full_name, r.municipality
ORDER BY average_score DESC, total_votes DESC;