<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

if (isset($_GET['id'])) {
    $book_id = new MongoDB\BSON\ObjectId($_GET['id']);
    
    // Hapus buku
    $books->deleteOne(['_id' => $book_id]);
}

header("Location: index.php");
exit;
?> 