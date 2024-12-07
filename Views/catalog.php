<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/mpusbaru/config/database.php';
session_start();

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

// Ambil daftar kategori
$categories = $books->distinct('category');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog - MPUSBaru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .bayangan-kartun {
            box-shadow: 4px 4px 0px #6366f1;
        }
        .garis-kartun {
            border: 3px solid #6366f1;
        }
        @keyframes melayang {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animasi-melayang {
            animation: melayang 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo dan Hamburger -->
                <div class="flex items-center">
                    <!-- Hamburger Button -->
                    <button id="hamburger" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none md:hidden">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    
                    <!-- Logo -->
                    <a href="../home.php" class="flex items-center">
                        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Books.png" 
                             alt="Logo" 
                             class="h-8 w-8 ml-2 md:ml-0">
                        <span class="text-xl font-bold text-indigo-600 ml-2">MPUSBaru</span>
                    </a>
                </div>

                <!-- Menu Desktop -->
                <div class="hidden md:flex md:items-center md:space-x-4">
                    <a href="../home.php" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        Beranda
                    </a>
                    <a href="catalog.php" class="px-3 py-2 rounded-md text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50">
                        Katalog
                    </a>
                    <a href="../profile.php" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        Profil
                    </a>
                    <a href="../auth/logout.php" class="ml-4 px-4 py-2 rounded-md text-sm font-medium text-white bg-red-500 hover:bg-red-600">
                        Logout
                    </a>
                </div>

                <!-- User Info Desktop -->
                <div class="hidden md:flex md:items-center">
                    <a href="../profile.php" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900">
                        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Smilies/Grinning%20Face%20with%20Big%20Eyes.png" 
                             alt="Avatar" 
                             class="w-8 h-8 rounded-full">
                        <span class="text-sm font-medium">
                            <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Menu Mobile -->
        <div id="mobile-menu" class="nav-mobile fixed inset-y-0 left-0 w-64 bg-white shadow-lg md:hidden transform -translate-x-full transition-transform duration-300 ease-in-out">
            <div class="flex flex-col h-full">
                <div class="p-4 border-b">
                    <a href="../profile.php" class="flex items-center space-x-2">
                        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Smilies/Grinning%20Face%20with%20Big%20Eyes.png" 
                             alt="Avatar" 
                             class="w-10 h-10 rounded-full">
                        <span class="text-sm font-medium text-gray-600">
                            <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                        </span>
                    </a>
                </div>
                <nav class="flex-1 px-4 py-4 space-y-2">
                    <a href="../home.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        Beranda
                    </a>
                    <a href="catalog.php" class="block px-3 py-2 rounded-md text-base font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50">
                        Katalog
                    </a>
                    <a href="../profile.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        Profil
                    </a>
                </nav>
                <div class="p-4 border-t">
                    <a href="../auth/logout.php" class="block w-full px-4 py-2 text-center rounded-md text-sm font-medium text-white bg-red-500 hover:bg-red-600">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Overlay untuk mobile menu -->
    <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden"></div>

    <!-- Konten utama dengan padding top untuk navbar -->
    <div class="pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Search Section -->
            <div class="bg-white rounded-3xl bayangan-kartun garis-kartun p-6 mb-8 hover:scale-[1.02] transition-transform">
                <form method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-search text-gray-400"></i>
                            </span>
                            <input type="text" 
                                   name="search"
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   placeholder="Cari judul buku atau penulis..." 
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
                        Cari Buku
                    </button>
                </form>
            </div>

            <!-- Books Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($booksArray as $book): ?>
                <div class="bg-white rounded-3xl bayangan-kartun garis-kartun overflow-hidden hover:scale-[1.05] transition-all duration-300">
                    <div class="relative h-64 group">
                        <img src="<?php 
                            if (!empty($book->cover_image)) {
                                echo '../../public/uploads/covers/' . basename($book->cover_image);
                            } else {
                                echo 'https://via.placeholder.com/400x300?text=Tidak+Ada+Cover';
                            }
                        ?>" 
                        alt="<?php echo htmlspecialchars($book->title); ?>"
                        class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-300 rounded-t-3xl">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-t-3xl"></div>
                        <div class="absolute top-0 right-0 mt-2 mr-2 z-10">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full 
                                         border-2 border-dashed border-indigo-400
                                         bg-gradient-to-r from-indigo-100 to-purple-100 
                                         text-indigo-800 text-xs font-bold
                                         transform rotate-2 hover:rotate-0 transition-transform
                                         shadow-sm">
                                <i class="fas fa-tag mr-1.5 text-indigo-600"></i>
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
                            <span class="inline-flex items-center px-3 py-1 rounded-full 
                                         border-2 border-dashed border-yellow-400
                                         bg-gradient-to-r from-yellow-100 to-orange-100 
                                         text-yellow-800 text-xs font-bold
                                         transform -rotate-1 hover:rotate-0 transition-transform
                                         shadow-sm">
                                <i class="fas fa-users mr-1.5 text-yellow-600"></i>
                                Usia <?php echo htmlspecialchars($book->age_range); ?> tahun
                            </span>
                        </p>
                        <a href="../book_detail.php?id=<?php echo $book->_id; ?>" 
                           class="block w-full text-center bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition duration-200">
                            <i class="fas fa-book-reader mr-2"></i>
                            Baca Buku
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Empty State -->
            <?php if (empty($booksArray)): ?>
            <div class="text-center py-12">
                <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Books.png" 
                     alt="Tidak Ada Buku" 
                     class="w-32 h-32 mx-auto mb-4 animasi-melayang">
                <h3 class="text-lg font-medium text-gray-900">Tidak Ada Buku</h3>
                <p class="text-gray-500 mt-2">
                    <?php if (!empty($search) || !empty($category)): ?>
                        Coba ubah kata kunci atau filter pencarian
                    <?php else: ?>
                        Belum ada buku yang tersedia
                    <?php endif; ?>
                </p>
                <?php if (!empty($search) || !empty($category)): ?>
                <a href="catalog.php" 
                   class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Lihat Semua Buku
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // JavaScript untuk toggle mobile menu
        const hamburger = document.getElementById('hamburger');
        const mobileMenu = document.getElementById('mobile-menu');
        const overlay = document.getElementById('overlay');

        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        overlay.addEventListener('click', () => {
            mobileMenu.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    </script>
</body>
</html>