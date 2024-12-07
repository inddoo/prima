<?php
session_start();
require_once '../../../config/database.php';

// Cek role admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $badge = [
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'icon' => $_POST['icon'],
        'color' => $_POST['color'],
        'text_color' => $_POST['text_color'],
        'requirement' => $_POST['requirement'],
        'points_required' => (int)$_POST['points_required'],
        'status' => $_POST['status'],
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ];

    $result = $badges->insertOne($badge);
    if ($result->getInsertedCount() > 0) {
        header("Location: index.php?success=created");
        exit;
    } else {
        $error = "Gagal menambahkan badge!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Badge - Admin MPUSBaru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Sidebar (sama seperti sebelumnya) -->
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
            <a class="flex items-center px-6 py-3 text-white hover:bg-indigo-800" href="../users/index.php">
                <i class="fas fa-users mr-3"></i>
                Kelola Pengguna
            </a>
            <a class="flex items-center px-6 py-3 bg-indigo-800 text-white" href="index.php">
                <i class="fas fa-medal mr-3"></i>
                Kelola Badge
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="ml-64 p-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Tambah Badge Baru</h1>
                    <p class="text-gray-600 mt-1">Buat badge pencapaian baru untuk pengguna</p>
                </div>
                <a href="index.php" class="flex items-center text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm p-6 max-w-2xl">
            <!-- Preview Badge -->
            <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-4">Preview Badge</h3>
                <div class="flex items-center">
                    <div id="previewIcon" class="w-12 h-12 flex items-center justify-center rounded-full bg-indigo-100">
                        <i class="fas fa-medal text-2xl text-indigo-600"></i>
                    </div>
                    <div class="ml-4">
                        <h4 id="previewName" class="text-lg font-semibold text-gray-800">Nama Badge</h4>
                        <p id="previewDesc" class="text-sm text-gray-500">Deskripsi badge akan muncul di sini</p>
                    </div>
                </div>
            </div>

            <form method="POST" class="space-y-6">
                <!-- Nama dan Deskripsi -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Badge</label>
                        <input type="text" 
                               name="name" 
                               required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                               placeholder="Contoh: Super Reader"
                               oninput="document.getElementById('previewName').textContent = this.value">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <input type="text" 
                               name="description" 
                               required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                               placeholder="Contoh: Membaca 10 buku"
                               oninput="document.getElementById('previewDesc').textContent = this.value">
                    </div>
                </div>

                <!-- Icon dan Warna -->
                <div class="grid grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Icon</label>
                        <select name="icon" 
                                required
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                                onchange="updatePreviewIcon(this.value)">
                            <option value="fas fa-medal">🏅 Medal</option>
                            <option value="fas fa-star">⭐ Star</option>
                            <option value="fas fa-trophy">🏆 Trophy</option>
                            <option value="fas fa-crown">👑 Crown</option>
                            <option value="fas fa-book-reader">📚 Reader</option>
                            <option value="fas fa-graduation-cap">🎓 Graduate</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Background</label>
                        <select name="color" 
                                required
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                                onchange="updatePreviewColor(this.value)">
                            <option value="bg-indigo-100">Indigo</option>
                            <option value="bg-blue-100">Blue</option>
                            <option value="bg-green-100">Green</option>
                            <option value="bg-yellow-100">Yellow</option>
                            <option value="bg-red-100">Red</option>
                            <option value="bg-purple-100">Purple</option>
                        </select>
                    </div>
                    <div></div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Warna Icon</label>
                        <select name="text_color" 
                                required
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                                onchange="updatePreviewTextColor(this.value)">
                            <option value="text-indigo-600">Indigo</option>
                            <option value="text-blue-600">Blue</option>
                            <option value="text-green-600">Green</option>
                            <option value="text-yellow-600">Yellow</option>
                            <option value="text-red-600">Red</option>
                            <option value="text-purple-600">Purple</option>
                        </select>
                    </div>
                </div>

                <!-- Syarat dan Point -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Syarat Badge</label>
                        <input type="text" 
                               name="requirement" 
                               required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                               placeholder="Contoh: Baca 10 buku">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Point yang Dibutuhkan</label>
                        <input type="number" 
                               name="points_required" 
                               required
                               min="0"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                               placeholder="Contoh: 100">
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" 
                            required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-indigo-500">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="index.php" 
                       class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Badge
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updatePreviewIcon(iconClass) {
            const iconElement = document.querySelector('#previewIcon i');
            iconElement.className = iconClass + ' text-2xl';
        }

        function updatePreviewColor(colorClass) {
            const iconContainer = document.getElementById('previewIcon');
            iconContainer.className = `w-12 h-12 flex items-center justify-center rounded-full ${colorClass}`;
        }

        function updatePreviewTextColor(colorClass) {
            const iconElement = document.querySelector('#previewIcon i');
            iconElement.className = iconElement.className.replace(/text-\w+-600/, colorClass);
        }
    </script>
</body>
</html> 