<?php
session_start();
require_once '../../config/database.php';

// Cek role admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data admin yang login
$adminUser = $users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);

// Ambil statistik
$totalUsers = $users->count();
$totalBooks = $books->count();
$totalBadges = $badges->count();

// Ambil buku terbaru
$recentBooks = $books->find([], [
    'sort' => ['created_at' => -1],
    'limit' => 5
])->toArray();

// Ambil user terbaru
$recentUsers = $users->find([], [
    'sort' => ['created_at' => -1],
    'limit' => 5
])->toArray();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - MPUSBaru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <!-- Perbaikan bagian sidebar/navigasi -->
<div class="fixed inset-y-0 left-0 w-64 bg-indigo-700 text-white">
    <div class="flex items-center justify-center h-16 bg-indigo-800">
        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Books.png" 
             alt="Logo" 
             class="h-8 w-8 mr-2">
        <span class="text-xl font-bold">MPUSBaru</span>
    </div>
    
    <nav class="mt-8">
        <a class="flex items-center px-6 py-3 bg-indigo-800 text-white" href="dashboard.php">
            <i class="fas fa-home mr-3"></i>
            Dashboard
        </a>
        <a class="flex items-center px-6 py-3 text-white hover:bg-indigo-800" href="./books/index.php">
            <i class="fas fa-book mr-3"></i>
            Kelola Buku
        </a>
        <a class="flex items-center px-6 py-3 text-white hover:bg-indigo-800" href="./users/index.php">
            <i class="fas fa-users mr-3"></i>
            Kelola Pengguna
        </a>
        <a class="flex items-center px-6 py-3 text-white hover:bg-indigo-800" href="./badges/index.php">
            <i class="fas fa-medal mr-3"></i>
            Kelola Badge
        </a>
    </nav>
</div>

<!-- Perbaikan link di bagian tabel -->
<div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-bold text-gray-800">Pengguna Terbaru</h2>
</div>

    <!-- Main Content -->
    <div class="ml-64 p-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
                <p class="text-gray-600">Selamat datang kembali, <?php echo htmlspecialchars($adminUser->full_name ?? 'Admin'); ?></p>
            </div>
            <a href="../auth/logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                <i class="fas fa-sign-out-alt mr-2"></i>
                Logout
            </a>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Users Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 mr-4">
                        <i class="fas fa-users text-indigo-500 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Pengguna</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo number_format($totalUsers); ?></p>
                    </div>
                </div>
            </div>

            <!-- Books Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 mr-4">
                        <i class="fas fa-book text-green-500 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Buku</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo number_format($totalBooks); ?></p>
                    </div>
                </div>
            </div>

            <!-- Badges Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 mr-4">
                        <i class="fas fa-medal text-yellow-500 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500">Total Badge</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo number_format($totalBadges); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Books -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Buku Terbaru</h2>
                <a href="books/index.php" class="text-indigo-600 hover:text-indigo-800">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penulis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usia</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($recentBooks as $book): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($book->title ?? '-'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($book->author ?? '-'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                    <?php echo htmlspecialchars($book->category ?? '-'); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($book->age_range ?? '-'); ?> tahun
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Pengguna Terbaru</h2>
                <a href="users/index.php" class="text-indigo-600 hover:text-indigo-800">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($recentUsers as $user): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($user->full_name ?? '-'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($user->email ?? '-'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-full">
                                    <?php echo htmlspecialchars($user->role ?? 'user'); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                    Aktif
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>