<?php
require_once '../config/database.php';

setCORSHeaders();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse('Method not allowed', 405);
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Count total registrations
    $query = "SELECT COUNT(*) as total_registrations FROM registrations";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $total_registrations = $stmt->fetch()['total_registrations'];
    
    // Count unique municipalities
    $query = "SELECT COUNT(DISTINCT municipality) as participating_municipalities FROM registrations";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $participating_municipalities = $stmt->fetch()['participating_municipalities'];
    
    // Count unique sectors
    $query = "SELECT COUNT(DISTINCT sector) as represented_sectors FROM registrations";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $represented_sectors = $stmt->fetch()['represented_sectors'];
    
    $statistics = [
        'total_registrations' => intval($total_registrations),
        'participating_municipalities' => intval($participating_municipalities),
        'represented_sectors' => intval($represented_sectors)
    ];
    
    jsonResponse($statistics);
    
} catch (Exception $e) {
    error_log("Statistics API error: " . $e->getMessage());
    errorResponse('Failed to fetch statistics', 500);
}
?>