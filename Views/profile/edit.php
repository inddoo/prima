<?php
require_once __DIR__ . '/../../middleware/auth.php';
require_once __DIR__ . '/../../config/database.php';

checkLogin();

// Ambil data user yang sedang login
$user = $users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updateData = [
        'full_name' => $_POST['full_name'] ?? $user->full_name,
        'email' => $_POST['email'] ?? $user->email,
        'age' => intval($_POST['age'] ?? $user->age),
        'school' => $_POST['school'] ?? $user->school,
        'grade' => $_POST['grade'] ?? $user->grade
    ];

    // Jika ada password baru
    if (!empty($_POST['new_password'])) {
        if (password_verify($_POST['current_password'], $user->password)) {
            $updateData['password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        } else {
            $error = "Password saat ini tidak sesuai!";
        }
    }

    // Update profil jika tidak ada error
    if (!isset($error)) {
        try {
            $result = $users->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])],
                ['$set' => $updateData]
            );
            
            if ($result->getModifiedCount() > 0) {
                $success = "Profil berhasil diperbarui!";
                // Refresh user data
                $user = $users->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);
            }
        } catch (Exception $e) {
            $error = "Terjadi kesalahan saat memperbarui profil.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - MPUSBaru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-custom {
            background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
        }
        .profile-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
        }
        .floating {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .input-custom:focus {
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.3);
        }
    </style>
    <script>
    let saveTimeout;

    function autoSave(field, value) {
        // Clear timeout sebelumnya
        if (saveTimeout) {
            clearTimeout(saveTimeout);
        }

        // Tampilkan indikator saving
        const indicator = document.getElementById('save-indicator');
        indicator.textContent = 'Menyimpan...';
        indicator.classList.remove('hidden');

        // Set timeout baru
        saveTimeout = setTimeout(() => {
            fetch('../api/update_profile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    field: field,
                    value: value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    indicator.textContent = 'Tersimpan!';
                    setTimeout(() => {
                        indicator.classList.add('hidden');
                    }, 2000);
                } else {
                    indicator.textContent = 'Gagal menyimpan: ' + data.message;
                    indicator.classList.add('text-red-500');
                    setTimeout(() => {
                        indicator.classList.add('hidden');
                        indicator.classList.remove('text-red-500');
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                indicator.textContent = 'Gagal menyimpan!';
                indicator.classList.add('text-red-500');
            });
        }, 1000); // Delay 1 detik sebelum save
    }
    </script>
</head>
<body class="gradient-custom min-h-screen">
    <!-- Floating Images -->
    <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Books.png" 
         class="floating fixed top-10 right-10 w-20 h-20 opacity-50">
    <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Objects/Pencil.png" 
         class="floating fixed bottom-10 left-10 w-16 h-16 opacity-50" style="animation-delay: 1s">

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="max-w-4xl mx-auto mb-8 text-center">
            <a href="../home.php" class="inline-block mb-4">
                <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Hand%20gestures/Waving%20Hand.png" 
                     class="w-16 h-16 mx-auto mb-2">
                <span class="text-indigo-600 hover:text-indigo-800">Kembali ke Dashboard</span>
            </a>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Edit Profilmu</h1>
            <p class="text-gray-600">Perbarui informasi profilmu agar tetap up-to-date!</p>
        </div>

        <!-- Profile Form -->
        <div class="max-w-4xl mx-auto">
            <?php if (isset($success)): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg profile-card">
                <div class="flex items-center">
                    <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Smilies/Star-Struck.png" 
                         class="w-6 h-6 mr-2">
                    <?php echo $success; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg profile-card">
                <div class="flex items-center">
                    <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Smilies/Confounded%20Face.png" 
                         class="w-6 h-6 mr-2">
                    <?php echo $error; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden profile-card">
                <div class="p-8">
                    <div class="flex items-center justify-center mb-8">
                        <div class="relative">
                            <div class="w-32 h-32 rounded-full bg-indigo-100 flex items-center justify-center">
                                <img src="https://raw.githubusercontent.com/Tarikul-Islam-Anik/Animated-Fluent-Emojis/master/Emojis/Smilies/Grinning%20Face%20with%20Big%20Eyes.png" 
                                     class="w-20 h-20">
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Nama Lengkap -->
                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2">
                                    <i class="fas fa-user text-indigo-600 mr-2"></i>
                                    Nama Lengkap
                                </label>
                                <input type="text" 
                                       name="full_name" 
                                       value="<?php echo htmlspecialchars($user->full_name ?? ''); ?>"
                                       onchange="autoSave('full_name', this.value)"
                                       class="input-custom w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 transition-all duration-300">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2">
                                    <i class="fas fa-envelope text-indigo-600 mr-2"></i>
                                    Email
                                </label>
                                <input type="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($user->email ?? ''); ?>"
                                       onchange="autoSave('email', this.value)"
                                       class="input-custom w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 transition-all duration-300">
                            </div>

                            <!-- Usia -->
                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2">
                                    <i class="fas fa-birthday-cake text-indigo-600 mr-2"></i>
                                    Usia
                                </label>
                                <input type="number" 
                                       name="age" 
                                       value="<?php echo htmlspecialchars($user->age ?? ''); ?>"
                                       onchange="autoSave('age', this.value)"
                                       class="input-custom w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 transition-all duration-300">
                            </div>

                            <!-- Sekolah -->
                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2">
                                    <i class="fas fa-school text-indigo-600 mr-2"></i>
                                    Sekolah
                                </label>
                                <input type="text" 
                                       name="school" 
                                       value="<?php echo htmlspecialchars($user->school ?? ''); ?>"
                                       onchange="autoSave('school', this.value)"
                                       class="input-custom w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 transition-all duration-300">
                            </div>

                            <!-- Grade -->
                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2">
                                    <i class="fas fa-graduation-cap text-indigo-600 mr-2"></i>
                                    Grade
                                </label>
                                <input type="text" 
                                       name="grade" 
                                       value="<?php echo htmlspecialchars($user->grade ?? ''); ?>"
                                       onchange="autoSave('grade', this.value)"
                                       class="input-custom w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 transition-all duration-300">
                            </div>
                        </div>

                        <!-- Password Section -->
                        <div class="border-t pt-6 mt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                <i class="fas fa-lock text-indigo-600 mr-2"></i>
                                Ganti Password
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">
                                        Password Saat Ini
                                    </label>
                                    <input type="password" 
                                           name="current_password"
                                           class="input-custom w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 transition-all duration-300">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">
                                        Password Baru
                                    </label>
                                    <input type="password" 
                                           name="new_password"
                                           class="input-custom w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 transition-all duration-300">
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-center pt-6">
                            <button type="submit"
                                    class="px-8 py-3 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transform hover:scale-105 transition-all duration-300">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambahkan indikator saving di bawah header -->
    <div id="save-indicator" 
         class="fixed top-4 right-4 bg-green-100 text-green-700 px-4 py-2 rounded-lg shadow-lg hidden">
    </div>
</body>
</html> 