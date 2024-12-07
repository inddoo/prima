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
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];
    $age = (int)$_POST['age'];

    // Cek username sudah ada atau belum
    $existingUser = $users->findOne(['username' => $username]);
    if ($existingUser) {
        $error = "Username sudah digunakan!";
    } else {
        // Insert user baru
        $result = $users->insertOne([
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'full_name' => $full_name,
            'role' => $role,
            'age' => $age,
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]);

        if ($result->getInsertedCount() > 0) {
            header("Location: index.php?success=created");
            exit;
        } else {
            $error = "Gagal menambahkan pengguna!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna - Admin MPUSBaru</title>
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
            <a class="flex items-center px-6 py-3 text-white hover:bg-indigo-800" href="#">
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
                    <h1 class="text-2xl font-bold text-gray-800">Tambah Pengguna Baru</h1>
                    <p class="text-gray-600 mt-1">Tambahkan pengguna baru ke sistem</p>
                </div>
                <a href="index.php" class="flex items-center text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm p-6 max-w-2xl">
            <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" 
                               name="username" 
                               required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                               placeholder="Masukkan username">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" 
                               name="password" 
                               required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                               placeholder="Masukkan password">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" 
                               name="email" 
                               required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                               placeholder="Masukkan email">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" 
                               name="full_name" 
                               required
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                               placeholder="Masukkan nama lengkap">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select name="role" 
                                required
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-indigo-500">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Usia</label>
                        <input type="number" 
                               name="age" 
                               required
                               min="1"
                               class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-indigo-500"
                               placeholder="Masukkan usia">
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="index.php" 
                       class="px-6 py-2 border rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200">
                        <i class="fas fa-user-plus mr-2"></i>
                        Tambah Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>