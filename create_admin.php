<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = Database::getInstance()->getDb();
    
    // Data admin default
    $adminData = [
        'name' => 'Administrator',
        'email' => 'admin@mpusbaru.com',
        'password' => password_hash('admin123', PASSWORD_DEFAULT), // Ganti password sesuai kebutuhan
        'role' => 'admin',
        'created_at' => new MongoDB\BSON\UTCDateTime(),
        'total_books_read' => 0,
        'badges' => []
    ];

    // Cek apakah admin sudah ada
    $existingAdmin = $db->users->findOne(['email' => $adminData['email']]);

    if ($existingAdmin) {
        echo "Admin sudah ada dalam database!\n";
    } else {
        // Insert admin baru
        $result = $db->users->insertOne($adminData);

        if ($result->getInsertedCount()) {
            echo "Admin berhasil dibuat!\n";
            echo "Email: " . $adminData['email'] . "\n";
            echo "Password: admin123\n";
            echo "Silakan login dan ganti password segera!\n";
        } else {
            echo "Gagal membuat admin!\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 