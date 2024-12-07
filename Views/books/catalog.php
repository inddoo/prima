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

// Ambil semua buku dengan filter tanpa batasan
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
        /* Animasi dasar */
        .bayangan-kartun {
            box-shadow: 4px 4px 0px #6366f1;
        }
        .garis-kartun {
            border: 3px solid #6366f1;
        }
        
        /* Animasi melayang */
        @keyframes melayang {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animasi-melayang {
            animation: melayang 3s ease-in-out infinite;
        }
        
        /* Animasi berputar untuk badge */
        @keyframes berputar {
            0% { transform: rotate(-2deg); }
            50% { transform: rotate(2deg); }
            100% { transform: rotate(-2deg); }
        }
        .animasi-berputar {
            animation: berputar 2s ease-in-out infinite;
        }
        
        /* Animasi bersinar untuk tombol */
        @keyframes bersinar {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); box-shadow: 0 0 15px rgba(99, 102, 241, 0.5); }
            100% { transform: scale(1); }
        }
        .animasi-bersinar:hover {
            animation: bersinar 1s ease-in-out infinite;
        }
        
        /* Animasi muncul untuk card */
        @keyframes muncul {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animasi-muncul {
            animation: muncul 0.5s ease-out forwards;
        }
        
        /* Efek hover untuk card */
        .card-buku {
            transition: all 0.3s ease;
        }
        .card-buku:hover {
            transform: translateY(-10px) rotate(1deg);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2);
        }
        
        /* Animasi untuk icon */
        .icon-berputar:hover {
            animation: berputar 0.5s ease;
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
<body class="bg-gray-50">
    <!-- Karakter animasi -->
    <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Animals/Cat.png" 
         alt="Running Cat" 
         class="running-cat">
         
    <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Animals/Bird.png" 
         alt="Flying Bird" 
         class="flying-bird">

    <!-- Awan melayang -->
    <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Travel%20and%20places/Cloud.png" 
         alt="Cloud" 
         class="floating-cloud"
         style="top: 50px; right: 10%;">

    <!-- Pelangi bergerak -->
    <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Travel%20and%20places/Rainbow.png" 
         alt="Rainbow" 
         class="moving-rainbow"
         style="right: 30%;">

    <!-- Bintang-bintang berkedip dengan posisi random -->
    <script>
        function addRandomElement(type) {
            const element = document.createElement('img');
            const isRainbow = type === 'rainbow';
            const isStar = type === 'star';
            const isCloud = type === 'cloud';
            
            element.src = isRainbow 
                ? 'https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Travel%20and%20places/Rainbow.png'
                : isStar
                    ? 'https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Travel%20and%20places/Star.png'
                    : 'https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Travel%20and%20places/Cloud.png';
            
            element.className = isRainbow 
                ? 'moving-rainbow' 
                : isStar
                    ? 'twinkling-star'
                    : 'floating-cloud';
            
            element.style.position = 'fixed';
            element.style.top = Math.random() * window.innerHeight + 'px';
            element.style.left = Math.random() * window.innerWidth + 'px';
            element.style.width = isRainbow ? '100px' : isStar ? '20px' : '80px';
            element.style.height = element.style.width;
            element.style.zIndex = isRainbow ? '20' : isStar ? '40' : '30';
            
            document.body.appendChild(element);

            setTimeout(() => {
                element.remove();
            }, isRainbow ? 15000 : isStar ? 3000 : 6000);
        }

        // Tambahkan elemen secara periodik
        setInterval(() => addRandomElement('star'), 2000);
        setInterval(() => addRandomElement('rainbow'), 8000);
        setInterval(() => addRandomElement('cloud'), 5000);
    </script>

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
                <div class="card-buku bg-white rounded-3xl bayangan-kartun garis-kartun overflow-hidden animasi-muncul hover:scale-[1.02] transition-all duration-300">
                    <div class="relative h-64 group">
                        <img src="<?php 
                            if (!empty($book->cover_image)) {
                                echo '../../public/uploads/covers/' . basename($book->cover_image);
                            } else {
                                echo 'https://via.placeholder.com/400x300?text=No+Cover';
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
                                         animasi-berputar">
                                <i class="fas fa-tag mr-1.5 text-indigo-600 icon-berputar"></i>
                                <?php echo htmlspecialchars($book->category); ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2 hover:text-indigo-600 transition-colors">
                            <?php echo htmlspecialchars($book->title); ?>
                        </h3>
                        <p class="text-gray-600 text-sm mb-2">
                            <i class="fas fa-user mr-2 icon-berputar"></i>
                            <?php echo htmlspecialchars($book->author); ?>
                        </p>
                        <p class="text-gray-600 text-sm mb-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full 
                                         border-2 border-dashed border-yellow-400
                                         bg-gradient-to-r from-yellow-100 to-orange-100 
                                         text-yellow-800 text-xs font-bold
                                         animasi-berputar">
                                <i class="fas fa-users mr-1.5 text-yellow-600 icon-berputar"></i>
                                Usia <?php echo htmlspecialchars($book->age_range); ?> tahun
                            </span>
                        </p>
                        <div class="grid grid-cols-2 gap-2">
                            <a href="detail.php?id=<?php echo $book->_id; ?>" 
                               class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors transform hover:scale-105">
                                <i class="fas fa-info-circle mr-2"></i>
                                Detail
                            </a>
                            <a href="read.php?id=<?php echo $book->_id; ?>" 
                               class="inline-flex items-center justify-center px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition-colors transform hover:scale-105 shine-effect">
                                <i class="fas fa-book-reader mr-2"></i>
                                Baca Sekarang
                            </a>
                        </div>
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

        // Script untuk menambahkan bintang secara random
        function addRandomStar() {
            const star = document.createElement('img');
            star.src = 'https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Travel%20and%20places/Star.png';
            star.className = 'twinkling-star';
            star.style.position = 'fixed';
            star.style.top = Math.random() * window.innerHeight + 'px';
            star.style.left = Math.random() * window.innerWidth + 'px';
            star.style.width = '20px';
            star.style.height = '20px';
            document.body.appendChild(star);

            // Hapus bintang setelah beberapa waktu
            setTimeout(() => {
                star.remove();
            }, 5000);
        }

        // Tambahkan bintang secara periodik
        setInterval(addRandomStar, 3000);
    </script>
</body>
</html>
