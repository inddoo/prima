<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user = $users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);

// Hitung statistik
$totalBooksRead = $reading_progress->countDocuments([
    'user_id' => $_SESSION['user_id'],
    'status' => 'completed'
]) ?? 0;

$booksInProgress = $reading_progress->countDocuments([
    'user_id' => $_SESSION['user_id'],
    'status' => 'in_progress'
]) ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - MPUSBaru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .animated-bg {
            background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .shine {
            position: relative;
            overflow: hidden;
        }

        .shine::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to right,
                rgba(255,255,255,0) 0%,
                rgba(255,255,255,.3) 50%,
                rgba(255,255,255,0) 100%
            );
            transform: rotate(30deg);
            animation: shine 6s ease-in-out infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) rotate(30deg); }
            100% { transform: translateX(100%) rotate(30deg); }
        }
    </style>
</head>
<body class="animated-bg min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Books.png" 
                         alt="Logo" 
                         class="h-8 w-8">
                    <span class="text-xl font-bold text-indigo-600 ml-2">MPUSBaru</span>
                </div>
                <div class="hidden md:flex md:items-center md:space-x-4">
                    <a href="home.php" class="px-3 py-2 rounded-md text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50">
                        Beranda
                    </a>
                    <a href="profile.php" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        Profil
                    </a>
                    <a href="auth/logout.php" class="ml-4 px-4 py-2 rounded-md text-sm font-medium text-white bg-red-500 hover:bg-red-600">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Konten Profil -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl p-8 relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-purple-200 rounded-full opacity-50 blur-2xl"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-200 rounded-full opacity-50 blur-2xl"></div>

            <!-- Profile Header -->
            <div class="flex flex-col md:flex-row items-center mb-8 relative">
                <div class="mb-6 md:mb-0 md:mr-8">
                    <div class="relative">
                        <div class="w-32 h-32 rounded-full overflow-hidden shine shadow-xl border-4 border-white">
                            <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Smilies/Grinning%20Face%20with%20Big%20Eyes.png" 
                                 alt="Avatar" 
                                 class="w-full h-full object-cover float-animation">
                        </div>
                    </div>
                </div>
                <div class="text-center md:text-left">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($user->name); ?></h1>
                    <p class="text-gray-600"><?php echo htmlspecialchars($user->email); ?></p>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                            <?php echo htmlspecialchars($user->role); ?>
                        </span>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            <?php echo $totalBooksRead; ?> Buku Selesai
                        </span>
                    </div>
                </div>
            </div>

            <!-- Statistik Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Reading Stats -->
                <div class="bg-gradient-to-br from-purple-50 to-indigo-50 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Books.png" 
                             alt="Books" 
                             class="w-12 h-12 float-animation">
                        <div class="ml-4">
                            <h3 class="text-2xl font-bold text-gray-800"><?php echo $totalBooksRead; ?></h3>
                            <p class="text-gray-600">Buku Selesai</p>
                        </div>
                    </div>
                </div>

                <!-- In Progress -->
                <div class="bg-gradient-to-br from-orange-50 to-yellow-50 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Bookmark%20Tabs.png" 
                             alt="Reading" 
                             class="w-12 h-12 float-animation">
                        <div class="ml-4">
                            <h3 class="text-2xl font-bold text-gray-800"><?php echo $booksInProgress; ?></h3>
                            <p class="text-gray-600">Sedang Dibaca</p>
                        </div>
                    </div>
                </div>

                <!-- Badges -->
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Trophy.png" 
                             alt="Badges" 
                             class="w-12 h-12 float-animation">
                        <div class="ml-4">
                            <h3 class="text-2xl font-bold text-gray-800">3</h3>
                            <p class="text-gray-600">Badge Diraih</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="home.php" class="px-6 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors flex items-center">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Beranda
                </a>
                <a href="./profile/edit.php" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors flex items-center">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Profil
                </a>
            </div>
        </div>
    </div>
</body>
</html> 