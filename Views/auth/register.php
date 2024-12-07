<?php
require_once __DIR__ . '/../../config/database.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validasi
        if ($password !== $confirm_password) {
            $error = "Password tidak cocok!";
        } else {
            // Cek email sudah terdaftar
            $existingUser = $users->findOne(['email' => $email]);
            if ($existingUser) {
                $error = "Email sudah terdaftar!";
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user baru
                $result = $users->insertOne([
                    'name' => $name,
                    'email' => $email,
                    'password' => $hashedPassword,
                    'role' => 'user',
                    'created_at' => new MongoDB\BSON\UTCDateTime(),
                    'total_books_read' => 0,
                    'badges' => []
                ]);

                if ($result->getInsertedCount()) {
                    // Set session untuk login otomatis
                    $_SESSION['user_id'] = (string) $result->getInsertedId();
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_role'] = 'user';

                    // Redirect ke home
                    header("Location: ../home.php");
                    exit;
                } else {
                    $error = "Gagal melakukan registrasi!";
                }
            }
        }
    } catch (Exception $e) {
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MPUSBaru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
            100% {
                transform: translateY(0px);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bg-rgb {
            background: linear-gradient(
                45deg,
                #ff0000,
                #00ff00,
                #0000ff,
                #ff0000
            );
            background-size: 400% 400%;
            animation: rgb 15s ease infinite;
        }

        @keyframes rgb {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
    </style>
</head>
<body class="bg-rgb min-h-screen flex items-center justify-center p-4">
    <div class="flex w-full max-w-5xl">
        <!-- Left Side - Illustration -->
        <div class="hidden lg:flex lg:w-1/2 items-center justify-center p-8">
            <div class="animate-float">
                <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Animals/Baby%20Chick.png" 
                     alt="Chick Illustration" 
                     class="w-full max-w-md object-contain"
                     style="max-height: 400px;">
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="w-full lg:w-1/2">
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-2xl p-8 animate-fade-in">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Daftar Akun</h2>
                    <p class="text-gray-600">Mari bergabung dengan komunitas membaca kami!</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded animate-fade-in">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="name">
                            Nama Lengkap
                        </label>
                        <input class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-blue-500 focus:bg-white focus:outline-none transition duration-200"
                               id="name" 
                               type="text" 
                               name="name" 
                               required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="email">
                            Email
                        </label>
                        <input class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-blue-500 focus:bg-white focus:outline-none transition duration-200"
                               id="email" 
                               type="email" 
                               name="email" 
                               required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">
                            Password
                        </label>
                        <input class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-blue-500 focus:bg-white focus:outline-none transition duration-200"
                               id="password" 
                               type="password" 
                               name="password" 
                               required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="confirm_password">
                            Konfirmasi Password
                        </label>
                        <input class="w-full px-4 py-3 rounded-lg bg-gray-50 border border-gray-300 focus:border-blue-500 focus:bg-white focus:outline-none transition duration-200"
                               id="confirm_password" 
                               type="password" 
                               name="confirm_password" 
                               required>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 text-white rounded-lg px-4 py-3 font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                        Daftar Sekarang
                    </button>
                </form>

                <p class="text-center mt-8 text-gray-600">
                    Sudah punya akun? 
                    <a href="login.php" class="text-blue-600 hover:text-blue-800 font-semibold transition duration-200">
                        Login di sini
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
