<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$field = $data['field'] ?? null;
$value = $data['value'] ?? null;

if (!$field || !isset($value)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $updateData = [$field => $value];
    
    // Jika field adalah age, konversi ke integer
    if ($field === 'age') {
        $updateData[$field] = intval($value);
    }

    $result = $users->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
        ['$set' => $updateData]
    );

    if ($result->getModifiedCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 