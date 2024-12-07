<?php
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../config/database.php';

checkLogin();

// Ambil ID buku dari URL
$book_id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$book_id) {
    header("Location: ../dashboard.php");
    exit;
}

try {
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
    <style>
        .sidebar {
            height: 100vh;
            width: 280px;
            position: fixed;
            top: 0;
            left: 0;
            background: white;
            z-index: 1000;
            transition: 0.3s;
        }
        
        .main-content {
            margin-left: 0;
            transition: 0.3s;
            width: 100%;
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

        .page-content {
            font-size: 1.1rem;
            line-height: 1.8;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Overlay untuk mobile -->
    <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-40 hidden md:hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar shadow-lg overflow-y-auto">
        <div class="p-4">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-800">Menu Baca</h2>
                <button onclick="toggleSidebar()" class="md:hidden text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Reading Progress -->
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-600 mb-2">Progress Membaca</h3>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div id="progress-indicator" class="bg-indigo-600 h-2.5 rounded-full" style="width: 0%"></div>
                </div>
                <p class="text-sm text-gray-600 mt-2">
                    <span id="progress-text">0%</span> selesai
                </p>
            </div>

            <!-- Reading Settings -->
            <div class="space-y-4">
                <!-- Font Size -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Ukuran Teks</h3>
                    <div class="flex space-x-2">
                        <button onclick="changeFontSize('decrease')" class="p-2 bg-gray-100 rounded hover:bg-gray-200">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button onclick="changeFontSize('increase')" class="p-2 bg-gray-100 rounded hover:bg-gray-200">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>

                <!-- Theme Toggle -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Tema</h3>
                    <button onclick="toggleTheme()" class="w-full p-2 bg-gray-100 rounded hover:bg-gray-200 text-left">
                        <i class="fas fa-moon mr-2"></i>
                        <span id="theme-text">Mode Gelap</span>
                    </button>
                </div>

                <!-- Bookmark -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-600 mb-2">Bookmark</h3>
                    <button onclick="toggleBookmark()" class="w-full p-2 bg-gray-100 rounded hover:bg-gray-200 text-left">
                        <i class="fas fa-bookmark mr-2"></i>
                        Tandai Halaman
                    </button>
                </div>
            </div>
        </div>

        <!-- Bottom Navigation -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-white">
            <a href="katalog.php" class="block w-full p-2 text-center text-indigo-600 hover:bg-indigo-50 rounded">
                <i class="fas fa-home mr-2"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navigation -->
        <nav class="bg-white shadow-sm sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between items-center h-16">
                    <button onclick="toggleSidebar()" class="md:hidden text-gray-600 hover:text-gray-800">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-800 truncate">
                        <?php echo htmlspecialchars($book->title); ?>
                    </h1>
                    <div class="flex items-center space-x-4">
                        <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
                                class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-arrow-up"></i>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Reading Content -->
        <div class="max-w-3xl mx-auto px-4 py-8">
            <div class="bg-white rounded-lg shadow-sm p-6 md:p-8">
                <div class="page-content prose max-w-none">
                    <?php 
                    if (isset($book->content) && !empty($book->content)) {
                        $paragraphs = explode("\n\n", trim($book->content));
                        foreach ($paragraphs as $paragraph) {
                            if (!empty(trim($paragraph))) {
                                echo "<p class='mb-4'>" . nl2br(htmlspecialchars(trim($paragraph))) . "</p>";
                            }
                        }
                    } else {
                        echo "<div class='text-center text-gray-600'>";
                        echo "<i class='fas fa-book-open text-4xl mb-4'></i>";
                        echo "<p>Konten buku belum tersedia.</p>";
                        echo "</div>";
                    }
                    ?>
                </div>
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

        // Reading Progress
        function updateReadingProgress() {
            const winScroll = document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            
            document.getElementById('progress-indicator').style.width = scrolled + '%';
            document.getElementById('progress-text').textContent = Math.round(scrolled) + '%';
        }

        // Font Size Control
        let currentFontSize = 1.1;
        function changeFontSize(action) {
            const content = document.querySelector('.page-content');
            if (action === 'increase' && currentFontSize < 1.5) {
                currentFontSize += 0.1;
            } else if (action === 'decrease' && currentFontSize > 0.8) {
                currentFontSize -= 0.1;
            }
            content.style.fontSize = currentFontSize + 'rem';
        }

        // Theme Toggle
        let isDarkMode = false;
        function toggleTheme() {
            const body = document.body;
            const content = document.querySelector('.page-content');
            const themeText = document.getElementById('theme-text');
            
            isDarkMode = !isDarkMode;
            if (isDarkMode) {
                body.classList.add('bg-gray-900');
                content.classList.add('text-gray-100');
                themeText.textContent = 'Mode Terang';
            } else {
                body.classList.remove('bg-gray-900');
                content.classList.remove('text-gray-100');
                themeText.textContent = 'Mode Gelap';
            }
        }

        // Bookmark Function
        function toggleBookmark() {
            const scrollPosition = window.scrollY;
            localStorage.setItem(`bookmark_${<?php echo json_encode($book_id); ?>}`, scrollPosition);
            alert('Halaman ditandai!');
        }

        // Load bookmark if exists
        window.addEventListener('load', () => {
            const bookmark = localStorage.getItem(`bookmark_${<?php echo json_encode($book_id); ?>}`);
            if (bookmark) {
                window.scrollTo(0, parseInt(bookmark));
            }
        });

        // Update progress on scroll
        window.addEventListener('scroll', updateReadingProgress);
    </script>
</body>
</html> 
</html> 