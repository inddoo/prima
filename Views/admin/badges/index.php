<?php
session_start();
require_once '../../../config/database.php';

// Cek role admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Ambil semua badge
$badgesArray = $badges->find()->toArray();

// Array untuk icon badge
$badgeIcons = [
    'Pembaca Aktif' => 'fa-book-reader',
    'Petualang' => 'fa-hiking',
    'Suka Sains' => 'fa-flask',
    'Dongeng Master' => 'fa-hat-wizard',
    'Super Reader' => 'fa-star',
    'Sahabat Buku' => 'fa-book-open',
    'default' => 'fa-medal'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Badge - Admin MPUSBaru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-indigo-700 text-white">
        <div class="flex items-center justify-center h-16 bg-indigo-800">
            <a href="../dashboard.php" class="flex items-center">
                <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Books.png" 
                     alt="Logo" 
                     class="h-8 w-8 mr-2">
                <span class="text-xl font-bold">MPUSBaru</span>
            </a>
        </div>
        
        <nav class="mt-8">
            <a class="flex items-center px-6 py-3 text-white hover:bg-indigo-800" 
               href="../dashboard.php">
                <i class="fas fa-home mr-3"></i>
                Dashboard
            </a>
            <a class="flex items-center px-6 py-3 text-white hover:bg-indigo-800" 
               href="../books/index.php">
                <i class="fas fa-book mr-3"></i>
                Kelola Buku
            </a>
            <a class="flex items-center px-6 py-3 text-white hover:bg-indigo-800" 
               href="../users/index.php">
                <i class="fas fa-users mr-3"></i>
                Kelola Pengguna
            </a>
            <a class="flex items-center px-6 py-3 bg-indigo-800 text-white" 
               href="index.php">
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
                <h1 class="text-2xl font-bold text-gray-800">Kelola Badge</h1>
                <p class="text-gray-600">Kelola badge pencapaian untuk pengguna</p>
            </div>
            <a href="create.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i>
                Tambah Badge
            </a>
        </div>

        <!-- Badge Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($badgesArray as $badge): ?>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <i class="fas <?php 
                                echo isset($badgeIcons[$badge->name]) 
                                    ? $badgeIcons[$badge->name] 
                                    : $badgeIcons['default']; 
                                ?> text-2xl text-indigo-600"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <?php echo htmlspecialchars($badge->name); ?>
                            </h3>
                            <span class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                Aktif
                            </span>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="edit.php?id=<?php echo $badge->_id; ?>" 
                           class="text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="deleteBadge('<?php echo $badge->_id; ?>')" 
                                class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <p class="text-gray-600 mb-4">
                    <?php echo htmlspecialchars($badge->description); ?>
                </p>
                
                <div class="border-t pt-4">
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-trophy mr-2"></i>
                        <span>Syarat: <?php echo htmlspecialchars($badge->requirement ?? 'Membaca buku'); ?></span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500 mt-2">
                        <i class="fas fa-star mr-2"></i>
                        <span>Point: <?php echo number_format($badge->points ?? 0); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Hapus Badge</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus badge ini?
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="deleteButton"
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Hapus
                    </button>
                    <button onclick="closeDeleteModal()"
                            class="ml-3 px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let badgeIdToDelete = null;

        function deleteBadge(id) {
            badgeIdToDelete = id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            badgeIdToDelete = null;
        }

        document.getElementById('deleteButton').addEventListener('click', function() {
            if (badgeIdToDelete) {
                window.location.href = `delete.php?id=${badgeIdToDelete}`;
            }
        });
    </script>
</body>
</html>