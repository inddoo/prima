<?php
require_once '../../middleware/auth.php';
checkLogin();

require_once '../../config/database.php';

// Ambil ID buku dari URL
$book_id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$book_id) {
    header("Location: ../dashboard.php");
    exit;
}

try {
    // Ambil detail buku
    $book = $books->findOne(['_id' => new MongoDB\BSON\ObjectId($book_id)]);
    if (!$book) {
        header("Location: ../dashboard.php");
        exit;
    }
} catch (Exception $e) {
    header("Location: ../dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book->title); ?> - MPUSBaru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="catalog.php" class="text-gray-800 hover:text-gray-600">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto py-8 px-4">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <!-- Book Header -->
            <div class="p-6 md:p-8">
                <div class="flex flex-col md:flex-row">
                    <!-- Book Cover -->
                    <div class="bg-white rounded-xl shadow-lg p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Image Section -->
                            <div class="relative">
                                <img src="<?php 
                                    if (!empty($book->cover_image)) {
                                        echo '/mpusbaru/public/uploads/covers/' . basename($book->cover_image);
                                    } else {
                                        echo 'https://via.placeholder.com/400x500?text=No+Cover';
                                    }
                                ?>" 
                                    alt="<?php echo htmlspecialchars($book->title); ?>"
                                    class="w-full h-[400px] object-cover rounded-lg shadow-md">
                                
                                <!-- Badge kategori -->
                                <div class="absolute top-4 right-4">
                                    <span class="bg-white/90 backdrop-blur-sm text-indigo-600 px-4 py-2 rounded-full text-sm font-semibold shadow-lg">
                                        <?php echo htmlspecialchars($book->category); ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Book Info Section -->
                            <div class="space-y-6">
                                <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($book->title); ?></h1>
                                <div class="space-y-3 mb-6">
                                    <p class="flex items-center text-gray-600">
                                        <i class="fas fa-user-edit w-6"></i>
                                        <span class="ml-2">Penulis: <?php echo htmlspecialchars($book->author); ?></span>
                                    </p>
                                    <p class="flex items-center text-gray-600">
                                        <i class="fas fa-tag w-6"></i>
                                        <span class="ml-2">Kategori: <?php echo htmlspecialchars($book->category); ?></span>
                                    </p>
                                    <p class="flex items-center text-gray-600">
                                        <i class="fas fa-child w-6"></i>
                                        <span class="ml-2">Usia: <?php echo htmlspecialchars($book->age_range); ?> tahun</span>
                                    </p>
                                    <p class="flex items-center text-gray-600">
                                        <i class="fas fa-clock w-6"></i>
                                        <span class="ml-2">Estimasi: <?php echo htmlspecialchars($book->reading_time ?? '10-15'); ?> menit</span>
                                    </p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="space-x-4">
                                    <a href="read.php?id=<?php echo $book->_id; ?>" 
                                       class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        <i class="fas fa-book-reader mr-2"></i>
                                        Mulai Membaca
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Book Description -->
            <div class="border-t border-gray-200">
                <div class="p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Tentang Buku</h2>
                    <div class="prose max-w-none text-gray-600">
                        <?php echo nl2br(htmlspecialchars($book->description ?? 'Tidak ada deskripsi')); ?>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="border-t border-gray-200 bg-gray-50">
                <div class="p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Informasi Tambahan</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-medium text-gray-700 mb-2">Manfaat Membaca</h3>
                            <ul class="list-disc list-inside text-gray-600 space-y-1">
                                <li>Menambah pengetahuan</li>
                                <li>Mengembangkan imajinasi</li>
                                <li>Meningkatkan kosakata</li>
                                <li>Melatih fokus dan konsentrasi</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-700 mb-2">Tips Membaca</h3>
                            <ul class="list-disc list-inside text-gray-600 space-y-1">
                                <li>Pilih tempat yang nyaman</li>
                                <li>Atur pencahayaan yang cukup</li>
                                <li>Baca dengan teliti</li>
                                <li>Pahami setiap bagian</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 