<?php
session_start();
require_once '../config/database.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

try {
    // Ambil statistik dengan error handling
    $totalBooksRead = (int) $reading_progress->countDocuments([
        'user_id' => $_SESSION['user_id'],
        'status' => 'completed'
    ]);

    $booksInProgress = (int) $reading_progress->countDocuments([
        'user_id' => $_SESSION['user_id'],
        'status' => 'in_progress'
    ]);

    $totalBooks = (int) $books->countDocuments([]);

    // Hitung persentase dengan pengecekan
    $readPercentage = ($totalBooks > 0) ? round(($totalBooksRead / $totalBooks) * 100) : 0;

    // Debug info
    error_log("Total Books Read: " . $totalBooksRead);
    error_log("Books in Progress: " . $booksInProgress);
    error_log("Total Books: " . $totalBooks);
    error_log("Read Percentage: " . $readPercentage);

} catch (Exception $e) {
    // Set nilai default jika terjadi error
    error_log("Error fetching statistics: " . $e->getMessage());
    $totalBooksRead = 0;
    $booksInProgress = 0;
    $totalBooks = 0;
    $readPercentage = 0;
}

// Pastikan semua variabel memiliki nilai default
$totalBooksRead = $totalBooksRead ?? 0;
$booksInProgress = $booksInProgress ?? 0;
$totalBooks = $totalBooks ?? 0;
$readPercentage = $readPercentage ?? 0;

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

// Ambil 6 buku terbaru untuk ditampilkan di home
$booksArray = $books->find($filter, [
    'sort' => ['created_at' => -1],
    'limit' => 4
])->toArray();

// Ambil daftar kategori
$categories = $books->distinct('category');

// Ganti query buku di home.php
$books = $books->find([], ['limit' => 6]); // Membatasi hanya 6 buku
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - MPUSBaru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Theme colors */
        :root {
            --theme-1: linear-gradient(120deg, #e0f7fa 0%, #fff1ff 50%, #e8f5e9 100%);
            --theme-2: linear-gradient(120deg, #fff8e1 0%, #ffecb3 50%, #ffe0b2 100%);
            --theme-3: linear-gradient(120deg, #e3f2fd 0%, #bbdefb 50%, #90caf9 100%);
            --theme-4: linear-gradient(120deg, #f3e5f5 0%, #e1bee7 50%, #ce93d8 100%);
            --theme-5: linear-gradient(120deg, #e8f5e9 0%, #c8e6c9 50%, #a5d6a7 100%);
        }

        body {
            background: var(--theme-1);
            min-height: 100vh;
            transition: background 0.5s ease;
        }

        /* Theme switcher style */
        .theme-switcher {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: flex;
            gap: 8px;
        }

        .theme-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid black;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .theme-btn:hover {
            transform: scale(1.1);
        }

        .theme-btn.active {
            border: 2px solid #6366f1;
        }

        #theme1 { background: #e0f7fa; }
        #theme2 { background: #fff8e1; }
        #theme3 { background: #e3f2fd; }
        #theme4 { background: #f3e5f5; }
        #theme5 { background: #e8f5e9; }

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

        /* Animasi kucing berlari dengan bayangan dan efek lompat */
        @keyframes runningCat {
            0% { transform: translateX(-100%) translateY(0); }
            25% { transform: translateX(25vw) translateY(-20px) rotate(5deg); }
            50% { transform: translateX(50vw) translateY(0) rotate(-5deg); }
            75% { transform: translateX(75vw) translateY(-20px) rotate(5deg); }
            100% { transform: translateX(100vw) translateY(0); }
        }
        .running-cat {
            position: fixed;
            bottom: 20px;
            left: 0;
            width: 60px;
            height: 60px;
            animation: runningCat 8s linear infinite;
            z-index: 50;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }

        /* Animasi burung terbang dengan efek mengepak */
        @keyframes flyingBird {
            0% { transform: translate(-100%, 50px) rotate(0deg) scale(1); }
            25% { transform: translate(25vw, 0) rotate(5deg) scale(1.1); }
            50% { transform: translate(50vw, -50px) rotate(-5deg) scale(1); }
            75% { transform: translate(75vw, 0) rotate(5deg) scale(1.1); }
            100% { transform: translate(100vw, 50px) rotate(0deg) scale(1); }
        }
        .flying-bird {
            position: fixed;
            top: 80px;
            left: 0;
            width: 40px;
            height: 40px;
            animation: flyingBird 12s ease-in-out infinite;
            z-index: 50;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
        }

        /* Animasi bintang berkedip yang lebih dinamis */
        @keyframes twinkle {
            0% { transform: scale(1) rotate(0deg); opacity: 1; }
            50% { transform: scale(1.2) rotate(180deg); opacity: 0.5; }
            100% { transform: scale(1) rotate(360deg); opacity: 1; }
        }
        .twinkling-star {
            position: fixed;
            width: 30px;
            height: 30px;
            animation: twinkle 3s ease-in-out infinite;
            z-index: 40;
            filter: drop-shadow(0 0 10px rgba(255,215,0,0.5));
        }

        /* Animasi awan melayang */
        @keyframes floatingCloud {
            0% { transform: translateX(0) translateY(0); }
            50% { transform: translateX(20px) translateY(-10px); }
            100% { transform: translateX(0) translateY(0); }
        }
        .floating-cloud {
            position: fixed;
            width: 80px;
            height: 80px;
            animation: floatingCloud 6s ease-in-out infinite;
            z-index: 30;
            opacity: 0.8;
        }

        /* Animasi pelangi bergerak */
        @keyframes movingRainbow {
            0% { transform: translateY(100%) scale(0.5); opacity: 0; }
            50% { transform: translateY(0) scale(1); opacity: 1; }
            100% { transform: translateY(-100%) scale(0.5); opacity: 0; }
        }
        .moving-rainbow {
            position: fixed;
            width: 100px;
            height: 100px;
            animation: movingRainbow 15s ease-in-out infinite;
            z-index: 20;
        }

        /* Efek hover yang lebih menarik untuk card buku */
        .card-buku {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
        }
        .card-buku:hover {
            transform: translateY(-15px) rotate(2deg) scale(1.02);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.3);
        }
    </style>
</head>
<body>
    <!-- Theme Switcher -->
    <div class="theme-switcher">
        <button id="theme1" class="theme-btn active" onclick="changeTheme('--theme-1')" title="Tema Biru-Pink"></button>
        <button id="theme2" class="theme-btn" onclick="changeTheme('--theme-2')" title="Tema Kuning"></button>
        <button id="theme3" class="theme-btn" onclick="changeTheme('--theme-3')" title="Tema Biru"></button>
        <button id="theme4" class="theme-btn" onclick="changeTheme('--theme-4')" title="Tema Ungu"></button>
        <button id="theme5" class="theme-btn" onclick="changeTheme('--theme-5')" title="Tema Hijau"></button>
    </div>

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
                    <a href="home.php" class="flex items-center">
                        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Books.png" 
                             alt="Logo" 
                             class="h-8 w-8 ml-2 md:ml-0">
                        <span class="text-xl font-bold text-indigo-600 ml-2">MPUSBaru</span>
                    </a>
                </div>

                <!-- Menu Desktop -->
                <div class="hidden md:flex md:items-center md:space-x-4">
                    <a href="home.php" class="px-3 py-2 rounded-md text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50">
                        Beranda
                    </a>
                    <a href="books/catalog.php" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        Katalog
                    </a>
                    <a href="profile.php" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        Profil
                    </a>
                    <a href="auth/logout.php" class="ml-4 px-4 py-2 rounded-md text-sm font-medium text-white bg-red-500 hover:bg-red-600">
                        Logout
                    </a>
                </div>

                <!-- User Info Desktop -->
                <div class="hidden md:flex md:items-center">
                    <a href="profile.php" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900">
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
                    <a href="profile.php" class="flex items-center space-x-2">
                        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Smilies/Grinning%20Face%20with%20Big%20Eyes.png" 
                             alt="Avatar" 
                             class="w-10 h-10 rounded-full">
                        <span class="text-sm font-medium text-gray-600">
                            <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                        </span>
                    </a>
                </div>
                <nav class="flex-1 px-4 py-4 space-y-2">
                    <a href="home.php" class="block px-3 py-2 rounded-md text-base font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50">
                        Beranda
                    </a>
                    <a href="books/catalog.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        Katalog
                    </a>
                    <a href="profile.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        Profil
                    </a>
                </nav>
                <div class="p-4 border-t">
                    <a href="auth/logout.php" class="block w-full px-4 py-2 text-center rounded-md text-sm font-medium text-white bg-red-500 hover:bg-red-600">
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="bg-white rounded-3xl bayangan-kartun garis-kartun p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Badge Kamu</h2>
                <div class="flex flex-wrap gap-3">
                    <?php if (!empty($userBadges)): ?>
                        <?php foreach ($userBadges as $badge): ?>
                            <div class="inline-flex items-center px-4 py-2 rounded-full 
                                        border-2 border-dashed border-yellow-400
                                        bg-gradient-to-r from-yellow-100 to-orange-100 
                                        text-yellow-800 text-sm font-bold
                                        transform hover:scale-105 transition-all duration-200
                                        shadow-sm group">
                                <i class="fas fa-medal mr-2 text-yellow-600 group-hover:rotate-12 transition-transform"></i>
                                <span><?php echo htmlspecialchars($badge->name); ?></span>
                                <?php if (!empty($badge->description)): ?>
                                    <span class="ml-2 text-xs text-yellow-600">
                                        (<?php echo htmlspecialchars($badge->description); ?>)
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center w-full py-4">
                            <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Trophy.png" 
                                 alt="Belum Ada Badge" 
                                 class="w-16 h-16 mx-auto mb-2 animasi-melayang">
                            <p class="text-gray-500">Belum ada badge yang kamu dapatkan</p>
                            <p class="text-sm text-gray-400">Terus baca buku untuk mendapatkan badge!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

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
                                echo '../public/uploads/covers/' . basename($book->cover_image);
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
                        <a href="books/read.php?id=<?php echo $book->_id; ?>" 
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
                <a href="home.php" 
                   class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Lihat Semua Buku
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Statistik Pembaca -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="bg-white rounded-3xl bayangan-kartun garis-kartun p-6 mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Statistik Pembacamu</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Buku Dibaca -->
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-4 rounded-2xl border-2 border-dashed border-indigo-400">
                        <div class="flex items-center">
                            <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Open%20Book.png" 
                                 alt="Books Read" 
                                 class="w-12 h-12 mr-4 animasi-melayang">
                            <div>
                                <h3 class="text-2xl font-bold text-indigo-800"><?php echo $totalBooksRead; ?></h3>
                                <p class="text-sm text-indigo-600">Buku Selesai Dibaca</p>
                            </div>
                        </div>
                    </div>

                    <!-- Buku Sedang Dibaca -->
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 p-4 rounded-2xl border-2 border-dashed border-yellow-400">
                        <div class="flex items-center">
                            <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Bookmark%20Tabs.png" 
                                 alt="In Progress" 
                                 class="w-12 h-12 mr-4 animasi-melayang">
                            <div>
                                <h3 class="text-2xl font-bold text-yellow-800"><?php echo $booksInProgress; ?></h3>
                                <p class="text-sm text-yellow-600">Sedang Dibaca</p>
                            </div>
                        </div>
                    </div>

                    <!-- Persentase Pembacaan -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-2xl border-2 border-dashed border-green-400">
                        <div class="flex items-center">
                            <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Chart%20Increasing.png" 
                                 alt="Progress" 
                                 class="w-12 h-12 mr-4 animasi-melayang">
                            <div>
                                <h3 class="text-2xl font-bold text-green-800"><?php echo $readPercentage; ?>%</h3>
                                <p class="text-sm text-green-600">Koleksi Terbaca</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-6">
                    <div class="w-full bg-gray-200 rounded-full h-4 garis-kartun overflow-hidden">
                        <div class="bg-indigo-600 h-full rounded-full transition-all duration-500"
                             style="width: <?php echo $readPercentage; ?>%">
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2 text-center">
                        Kamu sudah membaca <?php echo $totalBooksRead; ?> dari <?php echo $totalBooks; ?> buku yang tersedia
                    </p>
                </div>
            </div>
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

        function changeTheme(theme) {
            // Update background
            document.body.style.background = `var(${theme})`;
            
            // Update active button
            document.querySelectorAll('.theme-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.getElementById(theme.replace('--', '')).classList.add('active');
            
            // Save preference to localStorage
            localStorage.setItem('selectedTheme', theme);
        }

        // Load saved theme
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('selectedTheme');
            if (savedTheme) {
                changeTheme(savedTheme);
            }
        });
    </script>
</body>
</html>
