<?php
// Pastikan session user tersedia
$user = isset($user) ? $user : null;
?>

<aside id="sidebar" class="sidebar shadow-lg overflow-y-auto">
    <div class="p-6">
        <!-- Profile Section -->
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
        </div>

        <!-- Navigation Menu -->
        <nav class="space-y-3">
            <a href="/mpusbaru/Views/dashboard.php" 
               class="menu-item flex items-center space-x-3 p-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-indigo-50 text-indigo-600' : 'hover:bg-gray-100 text-gray-700'; ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="/mpusbaru/Views/books/catalog.php" 
               class="menu-item flex items-center space-x-3 p-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'catalog.php' ? 'bg-indigo-50 text-indigo-600' : 'hover:bg-gray-100 text-gray-700'; ?>">
                <i class="fas fa-books"></i>
                <span>Katalog Buku</span>
            </a>
            <a href="/mpusbaru/Views/profile/favorites.php" 
               class="menu-item flex items-center space-x-3 p-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'favorites.php' ? 'bg-indigo-50 text-indigo-600' : 'hover:bg-gray-100 text-gray-700'; ?>">
                <i class="fas fa-heart"></i>
                <span>Favorit Saya</span>
            </a>
            <a href="/mpusbaru/Views/profile/history.php" 
               class="menu-item flex items-center space-x-3 p-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'history.php' ? 'bg-indigo-50 text-indigo-600' : 'hover:bg-gray-100 text-gray-700'; ?>">
                <i class="fas fa-history"></i>
                <span>Riwayat Baca</span>
            </a>
            <a href="/mpusbaru/Views/profile/achievements.php" 
               class="menu-item flex items-center space-x-3 p-3 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'achievements.php' ? 'bg-indigo-50 text-indigo-600' : 'hover:bg-gray-100 text-gray-700'; ?>">
                <i class="fas fa-medal"></i>
                <span>Pencapaian</span>
            </a>
        </nav>

        <!-- Logout Button -->
        <div class="mt-6 pt-6 border-t">
            <a href="/mpusbaru/Views/auth/logout.php" 
               class="menu-item flex items-center space-x-3 p-3 rounded-lg hover:bg-red-50 text-red-600">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar</span>
            </a>
        </div>
    </div>
</aside> 