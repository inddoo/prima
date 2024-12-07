<?php
session_start();
require_once '../../../config/database.php';

// Cek role admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Filter pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Buat filter untuk MongoDB
$filter = [];
if (!empty($search)) {
    $filter['$or'] = [
        ['title' => new MongoDB\BSON\Regex($search, 'i')],
        ['author' => new MongoDB\BSON\Regex($search, 'i')]
    ];
}
if (!empty($category)) {
    $filter['category'] = $category;
}

// Ambil semua buku dengan filter
$booksArray = $books->find($filter, [
    'sort' => ['created_at' => -1]
])->toArray();
$totalBooks = count($booksArray);

// Ambil daftar kategori unik
$categories = $books->distinct('category');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku - Admin MPUSBaru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-indigo-700 text-white transition-transform duration-300 transform">
        <div class="flex items-center justify-center h-16 bg-indigo-800">
            <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Books.png" 
                 alt="Logo" 
                 class="h-8 w-8 mr-2">
            <span class="text-xl font-bold">MPUSBaru Admin</span>
        </div>
        
        <nav class="mt-8">
            <a class="flex items-center px-6 py-3 text-white hover:bg-indigo-800" href="../dashboard.php">
                <i class="fas fa-home mr-3"></i>
                Dashboard
            </a>
            <a class="flex items-center px-6 py-3 text-white bg-indigo-800" href="index.php">
                <i class="fas fa-book mr-3"></i>
                Kelola Buku
            </a>
            <a class="flex items-center px-6 py-3 text-white hover:bg-indigo-800" href="../users/index.php">
                <i class="fas fa-users mr-3"></i>
                Kelola Pengguna
            </a>
            <a class="flex items-center px-6 py-3 text-white hover:bg-indigo-800" href="../badges/index.php">
                <i class="fas fa-medal mr-3"></i>
                Kelola Badge
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="ml-64 p-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Kelola Buku</h1>
                <p class="text-gray-600 mt-1">Kelola koleksi buku perpustakaan digital</p>
            </div>
            <a href="create.php" 
               class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Buku
            </a>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
            <form method="GET" class="flex gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </span>
                        <input type="text" 
                               name="search"
                               value="<?php echo htmlspecialchars($search); ?>"
                               placeholder="Cari judul atau penulis..." 
                               class="pl-10 pr-4 py-2 w-full border rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
                <select name="category" 
                        class="border rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>"
                            <?php echo $category === $cat ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" 
                        class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition duration-200">
                    <i class="fas fa-search mr-2"></i>
                    Cari
                </button>
                <?php if (!empty($search) || !empty($category)): ?>
                <a href="index.php" 
                   class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                    <i class="fas fa-times mr-2"></i>
                    Reset
                </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Search Results Info -->
        <?php if (!empty($search) || !empty($category)): ?>
        <div class="mb-6">
            <p class="text-gray-600">
                Menampilkan <?php echo $totalBooks; ?> hasil 
                <?php if (!empty($search)): ?>
                    untuk pencarian "<?php echo htmlspecialchars($search); ?>"
                <?php endif; ?>
                <?php if (!empty($category)): ?>
                    dalam kategori "<?php echo htmlspecialchars($category); ?>"
                <?php endif; ?>
            </p>
        </div>
        <?php endif; ?>

        <!-- Books Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($totalBooks > 0): ?>
                <?php foreach ($booksArray as $book): ?>
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition duration-200">
                    <div class="relative h-48 bg-gray-200">
                        <img src="<?php 
                            if (!empty($book->cover_image)) {
                                echo '../../../public/uploads/covers/' . basename($book->cover_image);
                            } else {
                                echo 'https://via.placeholder.com/400x300?text=No+Cover';
                            }
                        ?>" 
                             alt="<?php echo htmlspecialchars($book->title); ?>"
                             class="w-full h-full object-cover">
                        <div class="absolute top-0 right-0 mt-2 mr-2">
                            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                <?php echo htmlspecialchars($book->category); ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">
                            <?php echo htmlspecialchars($book->title); ?>
                        </h3>
                        <p class="text-gray-600 text-sm mb-2">
                            <i class="fas fa-user mr-2"></i>
                            <?php echo htmlspecialchars($book->author); ?>
                        </p>
                        <p class="text-gray-600 text-sm mb-4">
                            <i class="fas fa-users mr-2"></i>
                            Usia <?php echo htmlspecialchars($book->age_range); ?> tahun
                        </p>
                        <div class="flex justify-between items-center">
                            <div class="space-x-2">
                                <a href="edit.php?id=<?php echo $book->_id; ?>" 
                                   class="text-indigo-600 hover:text-indigo-800 transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $book->_id; ?>" 
                                   class="text-red-600 hover:text-red-800 transition"
                                   onclick="return confirm('Yakin ingin menghapus buku ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                            <span class="text-xs text-gray-500">
                                <?php 
                                $date = $book->created_at->toDateTime();
                                echo $date->format('d M Y'); 
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- No Results State -->
                <div class="col-span-3 text-center py-12">
                    <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Magnifying%20Glass%20Tilted%20Left.png" 
                         alt="No Results" 
                         class="w-24 h-24 mx-auto mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Tidak Ada Hasil</h3>
                    <p class="text-gray-500 mt-2">
                        <?php if (!empty($search) || !empty($category)): ?>
                            Coba ubah kata kunci atau filter pencarian
                        <?php else: ?>
                            Belum ada buku yang ditambahkan
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($search) || !empty($category)): ?>
                    <a href="index.php" 
                       class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Semua Buku
                    </a>
                    <?php else: ?>
                    <a href="create.php" 
                       class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Buku Pertama
                    </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Empty State -->
        <?php if ($totalBooks === 0): ?>
        <div class="text-center py-12">
            <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Books.png" 
                 alt="No Books" 
                 class="w-24 h-24 mx-auto mb-4">
            <h3 class="text-lg font-medium text-gray-900">Belum Ada Buku</h3>
            <p class="text-gray-500 mt-2">Mulai tambahkan buku ke perpustakaan digital</p>
            <a href="create.php" 
               class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i>
                Tambah Buku Pertama
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Toast Notification (optional) -->
    <?php if (isset($_GET['success'])): ?>
    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg" 
         id="toast">
        <div class="flex items-center space-x-2">
            <i class="fas fa-check-circle"></i>
            <span>Buku berhasil <?php echo $_GET['success']; ?></span>
        </div>
    </div>
    <script>
        setTimeout(() => {
            document.getElementById('toast').style.display = 'none';
        }, 3000);
    </script>
    <?php endif; ?>
</body>
</html>
