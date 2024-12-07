<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    // Koneksi ke MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");
    
    // Pilih database
    $database = $client->mpusbaru;
    
    // Buat collections
    $users = $database->users;
    $books = $database->books;
    $reading_progress = $database->reading_progress;
    $badges = $database->badges;
    $user_badges = $database->user_badges;

    // Insert admin default jika belum ada
    $admin = $users->findOne(['email' => 'admin@mpusbaru.com']);
    if (!$admin) {
        $users->insertOne([
            'name' => 'Admin',
            'email' => 'admin@mpusbaru.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ]);
    }

    // Insert badge default jika belum ada
    $existingBadges = $badges->find([])->toArray();
    if (empty($existingBadges)) {
        $badges->insertMany([
            [
                'name' => 'Pembaca Aktif',
                'description' => 'Membaca buku pertama',
                'requirement_count' => 1,
                'category' => 'achievement',
                'icon_path' => 'https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Open%20Book.png',
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ],
            [
                'name' => 'Petualang',
                'description' => 'Membaca 5 buku petualangan',
                'requirement_count' => 5,
                'category' => 'genre',
                'icon_path' => 'https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Travel%20and%20places/World%20Map.png',
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ],
            [
                'name' => 'Suka Sains',
                'description' => 'Membaca 3 buku sains',
                'requirement_count' => 3,
                'category' => 'genre',
                'icon_path' => 'https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Microscope.png',
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ],
            [
                'name' => 'Dongeng Master',
                'description' => 'Membaca 10 buku dongeng',
                'requirement_count' => 10,
                'category' => 'genre',
                'icon_path' => 'https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Books.png',
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ],
            [
                'name' => 'Super Reader',
                'description' => 'Membaca 20 buku',
                'requirement_count' => 20,
                'category' => 'achievement',
                'icon_path' => 'https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Crown.png',
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ],
            [
                'name' => 'Sahabat Buku',
                'description' => 'Membaca 30 buku',
                'requirement_count' => 30,
                'category' => 'achievement',
                'icon_path' => 'https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Books.png',
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ]
        ]);
    }

    // Insert contoh buku jika belum ada
    $existingBooks = $books->find([])->toArray();
    if (empty($existingBooks)) {
        $books->insertMany([
            [
                'title' => 'Petualangan di Hutan Ajaib',
                'author' => 'Budi Santoso',
                'description' => 'Kisah seru tentang petualangan di hutan ajaib',
                'category' => 'Petualangan',
                'age_range' => '7-12',
                'created_at' => new MongoDB\BSON\UTCDateTime(),
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ],
            [
                'title' => 'Mari Belajar Sains',
                'author' => 'Dr. Ani Wijaya',
                'description' => 'Pengenalan dasar sains untuk anak-anak',
                'category' => 'Sains',
                'age_range' => '8-13',
                'created_at' => new MongoDB\BSON\UTCDateTime(),
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ],
            [
                'title' => 'Dongeng Nusantara',
                'author' => 'Ratna Sari',
                'description' => 'Kumpulan dongeng dari berbagai daerah',
                'category' => 'Dongeng',
                'age_range' => '5-10',
                'created_at' => new MongoDB\BSON\UTCDateTime(),
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ]
        ]);
    }

} catch (Exception $e) {
    die("Error koneksi database: " . $e->getMessage());
}
?>
