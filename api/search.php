<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    // Gunakan regex untuk pencarian case-insensitive
    $regex = new MongoDB\BSON\Regex($query, 'i');
    
    $results = $books->find([
        '$or' => [
            ['title' => $regex],
            ['author' => $regex],
            ['category' => $regex]
        ]
    ], [
        'limit' => 10,
        'sort' => ['title' => 1]
    ])->toArray();

    // Convert MongoDB documents to array
    $searchResults = array_map(function($book) {
        return [
            '_id' => (string)$book->_id,
            'title' => $book->title,
            'author' => $book->author,
            'cover_url' => $book->cover_url ?? null,
            'reading_time' => $book->reading_time ?? '10-15',
            'age_range' => $book->age_range ?? '7-12',
            'category' => $book->category ?? 'Umum'
        ];
    }, $results);

    echo json_encode($searchResults);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Terjadi kesalahan saat mencari buku']);
}
?>