<!-- <?php
require_once __DIR__ . '/../middleware/auth.php';
checkLogin();
require_once __DIR__ . '/../config/database.php';

// Ambil data user yang login
$user = $users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);

// Ambil buku terbaru
$recentBooks = $books->find([], ['limit' => 8, 'sort' => ['created_at' => -1]])->toArray();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MPUSBaru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Nunito', sans-serif;
        }
        
        .sidebar {
            height: 100vh;
            width: 280px;
            position: fixed;
            top: 0;
            left: 0;
            background: #FFF5F7;
            z-index: 1000;
            transition: 0.3s;
            border-right: 4px solid #FED7E2;
        }
        
        .main-content {
            margin-left: 0;
            transition: 0.3s;
            width: 100%;
            background: #FAFBFF;
        }

        @media (min-width: 768px) {
            .main-content {
                margin-left: 280px;
                width: calc(100% - 280px);
            }
            .sidebar {
                transform: translateX(0);
            }
        }

        @media (max-width: 767px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
        }

        .book-card {
            transition: all 0.3s ease;
            border-radius: 20px;
            overflow: hidden;
            background: white;
            border: 3px solid #E2E8F0;
        }
        
        .book-card:hover {
            transform: translateY(-10px) rotate(2deg);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            border-color: #FED7E2;
        }

        .menu-item {
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .menu-item:hover {
            transform: scale(1.05);
        }

        .hero-gradient {
            background: linear-gradient(135deg, #FF6B6B 0%, #FFE66D 100%);
        }

        .achievement-badge {
            position: relative;
            overflow: hidden;
        }

        .achievement-badge::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .progress-bar {
            height: 10px;
            border-radius: 5px;
            background: #E2E8F0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4F46E5 0%, #7C3AED 100%);
            transition: width 0.5s ease;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Overlay untuk mobile -->
    <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden md:hidden"></div>

    <!-- Sidebar yang lebih menarik -->
    <aside id="sidebar" class="sidebar shadow-lg overflow-y-auto">
        <div class="p-6">
            <!-- Profile Section yang lebih menarik -->
            <div class="flex flex-col items-center space-y-4 mb-8">
                <div class="relative">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user->full_name ?? 'User'); ?>&background=FF6B6B&color=fff" 
                         alt="Profile" 
                         class="w-20 h-20 rounded-full border-4 border-pink-200">
                    <div class="absolute -bottom-2 -right-2 bg-green-400 rounded-full p-2">
                        <i class="fas fa-book-reader text-white"></i>
                    </div>
                </div>
                <div class="text-center">
                    <h2 class="font-bold text-gray-800 text-xl">
                        <?php echo htmlspecialchars($user->full_name ?? 'Pembaca Hebat'); ?>
                    </h2>
                    <p class="text-pink-500 font-semibold">Level 3 Reader</p>
                </div>
                <!-- Progress Bar -->
                <div class="w-full">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 75%"></div>
                    </div>
                    <p class="text-sm text-gray-600 text-center mt-2">75/100 XP to Level 4</p>
                </div>
            </div>

            <!-- Navigation Menu yang lebih menarik -->
            <nav class="space-y-3">
                <a href="/mpusbaru/Views/dashboard.php" 
                   class="menu-item flex items-center space-x-3 p-4 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-xl">
                    <i class="fas fa-home text-xl"></i>
                    <span class="font-semibold">Beranda</span>
                </a>
                <a href="/mpusbaru/Views/profile/edit.php" 
                   class="menu-item flex items-center space-x-3 p-4 bg-white hover:bg-gradient-to-r hover:from-pink-500 hover:to-red-500 hover:text-white text-gray-700 rounded-xl shadow-sm">
                    <i class="fas fa-user text-xl"></i>
                    <span class="font-semibold">Profil Saya</span>
                </a>
                <a href="#" class="menu-item flex items-center space-x-3 p-4 bg-white hover:bg-gradient-to-r hover:from-blue-500 hover:to-cyan-500 hover:text-white text-gray-700 rounded-xl shadow-sm">
                    <i class="fas fa-book text-xl"></i>
                    <span class="font-semibold">Koleksi Buku</span>
                </a>
                <a href="#" class="menu-item flex items-center space-x-3 p-4 bg-white hover:bg-gradient-to-r hover:from-yellow-500 hover:to-orange-500 hover:text-white text-gray-700 rounded-xl shadow-sm">
                    <i class="fas fa-star text-xl"></i>
                    <span class="font-semibold">Favorit</span>
                </a>
                <a href="#" class="menu-item flex items-center space-x-3 p-4 bg-white hover:bg-gradient-to-r hover:from-green-500 hover:to-teal-500 hover:text-white text-gray-700 rounded-xl shadow-sm">
                    <i class="fas fa-trophy text-xl"></i>
                    <span class="font-semibold">Pencapaian</span>
                </a>
            </nav>

            <!-- Logout Button yang lebih menarik -->
            <div class="mt-8 pt-6 border-t border-pink-200">
                <a href="/mpusbaru/Views/auth/logout.php" 
                   class="menu-item flex items-center space-x-3 p-4 bg-white hover:bg-red-500 hover:text-white text-red-500 rounded-xl shadow-sm">
                    <i class="fas fa-sign-out-alt text-xl"></i>
                    <span class="font-semibold">Keluar</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content"></main>
        <!-- Top Navigation yang lebih menarik -->
        <nav class="bg-white shadow-lg sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <button onclick="toggleSidebar()" class="md:hidden text-gray-600 hover:text-gray-800">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                        <img src="assets/images/logo.png" alt="Logo" class="h-8 w-auto ml-3">
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Search Bar with Dropdown -->
                        <div class="relative group">
                            <div class="hidden md:flex items-center bg-gray-100 rounded-full px-4 py-2 min-w-[300px]">
                                <i class="fas fa-search text-gray-400"></i>
                                <input type="text" 
                                       id="searchInput" 
                                       placeholder="Cari buku kesukaanmu..." 
                                       class="bg-transparent border-none focus:outline-none ml-2 w-full"
                                       onkeyup="searchBooks(this.value)">
                                <button class="text-gray-400 hover:text-gray-600" onclick="clearSearch()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <!-- Search Results Dropdown -->
                            <div id="searchResults" 
                                 class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl overflow-hidden hidden
                                        max-h-[400px] overflow-y-auto z-50 border-2 border-pink-100">
                                <!-- Loading State -->
                                <div id="searchLoading" class="hidden p-4 text-center text-gray-500">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    Mencari buku...
                                </div>
                                
                                <!-- Results Container -->
                                <div id="resultsContainer"></div>
                                
                                <!-- No Results State -->
                                <div id="noResults" class="hidden p-8 text-center">
                                    <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Smilies/Pleading%20Face.png" 
                                         alt="No Results" 
                                         class="w-16 h-16 mx-auto mb-4">
                                    <p class="text-gray-500 font-medium">Ups! Buku tidak ditemukan</p>
                                    <p class="text-gray-400 text-sm">Coba kata kunci lain ya!</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-600 hover:text-gray-800">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-4 h-4 text-xs flex items-center justify-center">
                                3
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section yang lebih menarik -->
        <div class="hero-gradient text-white py-16">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="md:w-1/2 mb-8 md:mb-0">
                        <h1 class="text-5xl font-bold mb-6 leading-tight">
                            Selamat Datang di<br>Dunia Buku Digital! 
                            <span class="text-yellow-300">✨</span>
                        </h1>
                        <p class="text-xl mb-8 opacity-90">
                            Mari jelajahi petualangan seru bersama karakter-karakter menarik dalam buku!
                        </p>
                        <div class="flex space-x-4">
                            <a href="#books" 
                               class="bg-white text-pink-500 px-8 py-4 rounded-full font-bold hover:bg-pink-100 transition-colors transform hover:scale-105">
                                Mulai Membaca
                            </a>
                            <a href="#achievements" 
                               class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold hover:bg-white hover:text-pink-500 transition-colors">
                                Lihat Pencapaian
                            </a>
                        </div>
                    </div>
                    <div class="md:w-1/2 flex justify-center">
                        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Books.png" 
                             alt="Books" 
                             class="w-64 h-64 floating">
                    </div>
                </div>
            </div>
        </div>

        <!-- Achievement Section -->
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
                <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-lg transition-shadow">
                    <div class="achievement-badge bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-book-reader text-2xl text-blue-500"></i>
                    </div>
                    <h3 class="font-bold text-xl text-gray-800">12</h3>
                    <p class="text-gray-600">Buku Dibaca</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-lg transition-shadow">
                    <div class="achievement-badge bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-clock text-2xl text-green-500"></i>
                    </div>
                    <h3 class="font-bold text-xl text-gray-800">48 Jam</h3>
                    <p class="text-gray-600">Waktu Membaca</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-lg transition-shadow">
                    <div class="achievement-badge bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-star text-2xl text-purple-500"></i>
                    </div>
                    <h3 class="font-bold text-xl text-gray-800">5</h3>
                    <p class="text-gray-600">Pencapaian</p>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm hover:shadow-lg transition-shadow">
                    <div class="achievement-badge bg-pink-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-heart text-2xl text-pink-500"></i>
                    </div>
                    <h3 class="font-bold text-xl text-gray-800">8</h3>
                    <p class="text-gray-600">Buku Favorit</p>
                </div>
            </div>

            <!-- Books Section yang lebih menarik -->
            <h2 class="text-3xl font-bold text-gray-800 mb-8 flex items-center">
                <i class="fas fa-books text-pink-500 mr-3"></i>
                Buku Terbaru
                <span class="text-sm font-normal text-gray-500 ml-4">Diperbarui hari ini</span>
            </h2>

            <!-- Books Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($recentBooks as $book): ?>
                <div class="book-card">
                    <div class="relative">
                        <img src="<?php echo htmlspecialchars($book->cover_url ?? 'assets/images/default-book.png'); ?>" 
                             alt="<?php echo htmlspecialchars($book->title); ?>"
                             class="w-full h-48 object-cover">
                        <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black to-transparent">
                            <h3 class="text-white font-bold text-lg">
                                <?php echo htmlspecialchars($book->title); ?>
                            </h3>
                            <p class="text-gray-300">
                                <?php echo htmlspecialchars($book->author); ?>
                            </p>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-4">
                            <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm font-semibold">
                                <i class="fas fa-clock mr-1"></i>
                                <?php echo htmlspecialchars($book->reading_time ?? '10-15'); ?> menit
                            </span>
                            <span class="bg-purple-100 text-purple-600 px-3 py-1 rounded-full text-sm font-semibold">
                                <i class="fas fa-user-friends mr-1"></i>
                                Usia <?php echo htmlspecialchars($book->age_range ?? '7-12'); ?>
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="/mpusbaru/Views/books/detail.php?id=<?php echo $book->_id; ?>" 
                               class="flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors transform hover:scale-105">
                                <i class="fas fa-info-circle mr-2"></i>
                                Detail
                            </a>
                            <a href="/mpusbaru/Views/books/read.php?id=<?php echo $book->_id; ?>" 
                               class="flex items-center justify-center px-4 py-2 bg-pink-500 text-white rounded-xl hover:bg-pink-600 transition-colors transform hover:scale-105">
                                <i class="fas fa-book-reader mr-2"></i>
                                Baca
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            
            sidebar.classList.toggle('show');
            if (sidebar.classList.contains('show')) {
                overlay.classList.remove('hidden');
            } else {
                overlay.classList.add('hidden');
            }
        }

        // Close sidebar when clicking overlay
        document.getElementById('overlay').addEventListener('click', toggleSidebar);

        // Initialize sidebar for desktop
        function initSidebar() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth >= 768) {
                sidebar.classList.add('show');
            } else {
                sidebar.classList.remove('show');
            }
        }

        // Run on load and resize
        window.addEventListener('load', initSidebar);
        window.addEventListener('resize', initSidebar);

        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        const searchLoading = document.getElementById('searchLoading');
        const resultsContainer = document.getElementById('resultsContainer');
        const noResults = document.getElementById('noResults');

        function searchBooks(query) {
            clearTimeout(searchTimeout);
            
            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }

            // Show loading state
            searchResults.classList.remove('hidden');
            searchLoading.classList.remove('hidden');
            resultsContainer.classList.add('hidden');
            noResults.classList.add('hidden');

            // Debounce search
            searchTimeout = setTimeout(() => {
                // Simulasi API call (ganti dengan actual API call)
                fetch(`/mpusbaru/api/search.php?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        searchLoading.classList.add('hidden');
                        
                        if (data.length === 0) {
                            noResults.classList.remove('hidden');
                            resultsContainer.classList.add('hidden');
                            return;
                        }

                        resultsContainer.classList.remove('hidden');
                        displayResults(data);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchLoading.classList.add('hidden');
                        noResults.classList.remove('hidden');
                    });
            }, 300);
        }

        function displayResults(books) {
            resultsContainer.innerHTML = books.map(book => `
                <a href="/mpusbaru/Views/books/detail.php?id=${book._id}" 
                   class="flex items-center p-4 hover:bg-pink-50 transition-colors border-b border-gray-100 last:border-0">
                    <img src="${book.cover_url || 'assets/images/default-book.png'}" 
                         alt="${book.title}"
                         class="w-12 h-16 object-cover rounded-lg shadow-sm">
                    <div class="ml-4 flex-1">
                        <h4 class="font-semibold text-gray-800">${book.title}</h4>
                        <p class="text-sm text-gray-600">${book.author}</p>
                        <div class="flex items-center mt-1 space-x-2">
                            <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full">
                                ${book.reading_time || '10-15'} menit
                            </span>
                            <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded-full">
                                Usia ${book.age_range || '7-12'}
                            </span>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
            `).join('');
        }

        function clearSearch() {
            searchInput.value = '';
            searchResults.classList.add('hidden');
        }

        // Close search results when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });

        // Prevent search results from closing when clicking inside
        searchResults.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Keyboard navigation
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                clearSearch();
            }
        });
    </script>
</body>
</html> -->