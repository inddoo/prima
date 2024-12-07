<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="/mpusbaru/Views/home.php" class="text-xl font-bold text-blue-600">MPUSBaru</a>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="/mpusbaru/Views/home.php" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md">Home</a>
                    <a href="/mpusbaru/Views/catalog.php" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md">Katalog</a>
                    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a href="/mpusbaru/Views/admin/dashboard.php" class="text-gray-700 hover:text-gray-900 px-3 py-2 rounded-md">Dashboard Admin</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex items-center">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span class="text-gray-700 mr-4">Hai, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="/mpusbaru/Views/auth/logout.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Logout</a>
                <?php else: ?>
                    <a href="/mpusbaru/Views/auth/login.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav> 