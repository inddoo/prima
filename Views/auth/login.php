<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/mpusbaru/config/database.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Cari user berdasarkan email
    $user = $users->findOne(['email' => $email]);

    if ($user && password_verify($password, $user->password)) {
        $_SESSION['user_id'] = (string) $user->_id;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_role'] = $user->role;

        // Redirect berdasarkan role dengan alert
        if ($user->role === 'admin') {
            echo "<script>
                alert('Selamat datang Admin!');
                window.location.href = '../admin/dashboard.php';
            </script>";
        } else {
            echo "<script>
                alert('Selamat datang " . htmlspecialchars($user->name) . "!');
                window.location.href = '../home.php';
            </script>";
        }
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MPUSBaru</title>
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
        <!-- Left Side - Form -->
        <div class="w-full lg:w-1/2">
            <div class="bg-white/90 backdrop-blur-md rounded-xl shadow-2xl p-8 animate-fade-in">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang!</h2>
                    <p class="text-gray-600">Silakan login untuk melanjutkan</p>
                </div>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded animate-fade-in">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded animate-fade-in">
                        <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" class="space-y-6">
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

                    <button type="submit" 
                            class="w-full bg-blue-600 text-white rounded-lg px-4 py-3 font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                        Login
                    </button>
                </form>

                <script>
                    function redirectToHome(event) {
                        // Mencegah form submit default
                        event.preventDefault();
                        
                        // Submit form menggunakan AJAX
                        const form = event.target.closest('form');
                        const formData = new FormData(form);
                        
                        fetch('', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.text())
                        .then(data => {
                            if(data.includes('admin')) {
                                window.location.href = '../admin/dashboard.php';
                            } else {
                                window.location.href = '../home.php';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat login');
                        });
                    }
                </script>

                <!-- <!-- <p class="text-center mt-8 text-gray-600"> -->
                    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <?php endif; ?>
                    Belum punya akun? 
                    <a href="register.php" class="text-blue-600 hover:text-blue-800 font-semibold transition duration-200">
                        Daftar di sini
                    </a>
                </p>
            </div>
        </div>

        <!-- Right Side - Illustration -->
        <div class="hidden lg:flex lg:w-1/2 items-center justify-center p-8">
            <div class="animate-float">
                <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Animals/Cat.png" 
                     alt="Cat Illustration" 
                     class="w-full max-w-md object-contain"
                     style="max-height: 400px;">
            </div>
        </div>
    </div>
</body>
</html>
