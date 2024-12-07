<?php
session_start();
require_once '../../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$book_id = new MongoDB\BSON\ObjectId($_GET['id']);
$book = $books->findOne(['_id' => $book_id]);

if (!$book) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $update = [
        'title' => $_POST['title'],
        'author' => $_POST['author'],
        'description' => $_POST['description'],
        'category' => $_POST['category'],
        'age_range' => $_POST['age_range'],
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ];

    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $target_dir = "../../../public/uploads/covers/";
        $file_extension = pathinfo($_FILES["cover_image"]["name"], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
            $update['cover_image'] = '/uploads/covers/' . $file_name;
        }
    }

    $books->updateOne(
        ['_id' => $book_id],
        ['$set' => $update]
    );

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - Admin MPUSBaru</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="ml-64 p-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Buku</h1>
            
            <form action="" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                        Judul Buku
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           id="title" 
                           type="text" 
                           name="title" 
                           value="<?php echo htmlspecialchars($book->title); ?>"
                           required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="author">
                        Penulis
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           id="author" 
                           type="text" 
                           name="author" 
                           value="<?php echo htmlspecialchars($book->author); ?>"
                           required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        Deskripsi
                    </label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                              id="description" 
                              name="description" 
                              rows="4"
                              value="<?php echo htmlspecialchars($book->description); ?>"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="category">
                        Kategori
                    </label>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="category" 
                            name="category" 
                            value="<?php echo htmlspecialchars($book->category); ?>"
                            required>
                        <option value="Petualangan">Petualangan</option>
                        <option value="Sains">Sains</option>
                        <option value="Dongeng">Dongeng</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="age_range">
                        Rentang Usia
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                           id="age_range" 
                           type="text" 
                           name="age_range" 
                           value="<?php echo htmlspecialchars($book->age_range); ?>"
                           placeholder="contoh: 7-12"
                           required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="cover_image">
                        Cover Buku
                    </label>
                    <input type="file" 
                           id="cover_image" 
                           name="cover_image" 
                           accept="image/*"
                           class="w-full">
                </div>

                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" 
                            type="submit">
                        Update Buku
                    </button>
                    <a href="index.php" 
                       class="text-gray-500 hover:text-gray-700">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
