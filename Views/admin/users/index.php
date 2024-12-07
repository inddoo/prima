<?php
session_start();
require_once '../../../config/database.php';

// Cek role admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil semua pengguna
$usersArray = $users->find([], ['sort' => ['created_at' => -1]])->toArray();
?>

<!DOCTYPE html>
<html lang="id">
<head></head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Admin MPUSBaru</title>
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
            <a class="flex items-center px-6 py-3 text-white hover:bg-indigo-800" href="../books/index.php">
                <i class="fas fa-book mr-3"></i>
                Kelola Buku
            </a>
            <a class="flex items-center px-6 py-3 bg-indigo-800 text-white" href="index.php">
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
                <h1 class="text-2xl font-bold text-gray-800">Kelola Pengguna</h1>
                <p class="text-gray-600 mt-1">Kelola data pengguna perpustakaan digital</p>
            </div>
            <a href="create.php" 
               class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition duration-200 flex items-center">
                <i class="fas fa-user-plus mr-2"></i>
                Tambah Pengguna
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
                               placeholder="Cari username atau nama..." 
                               class="pl-10 pr-4 py-2 w-full border rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>
                </div>
                <select name="role" 
                        class="border rounded-lg px-4 py-2 focus:outline-none focus:border-indigo-500">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
                <button type="submit" 
                        class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition duration-200">
                    <i class="fas fa-search mr-2"></i>
                    Cari
                </button>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Username
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama Lengkap
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bergabung
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($usersArray as $user): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img class="h-10 w-10 rounded-full" 
                                     src="https://ui-avatars.com/api/?name=<?php echo urlencode($user->username ?? 'User'); ?>&background=6366f1&color=fff" 
                                     alt="<?php echo htmlspecialchars($user->username ?? 'User'); ?>">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($user->username ?? 'User'); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($user->email ?? '-'); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <?php echo htmlspecialchars($user->full_name ?? '-'); ?>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                       <?php echo $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'; ?>">
                                <?php echo ucfirst($user->role); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Aktif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php 
                            $date = $user->created_at->toDateTime();
                            echo $date->format('d M Y'); 
                            ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="edit.php?id=<?php echo $user->_id; ?>" 
                               class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="delete.php?id=<?php echo $user->_id; ?>" 
                               class="text-red-600 hover:text-red-900"
                               onclick="return confirm('Yakin ingin menghapus pengguna ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <?php if (empty($usersArray)): ?>
        <div class="text-center py-12">
            <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/People/Person%20Raising%20Hand.png" 
                 alt="No Users" 
                 class="w-24 h-24 mx-auto mb-4">
            <h3 class="text-lg font-medium text-gray-900">Belum Ada Pengguna</h3>
            <p class="text-gray-500 mt-2">Mulai tambahkan pengguna ke sistem</p>
            <a href="create.php" 
               class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-user-plus mr-2"></i>
                Tambah Pengguna Pertama
            </a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
