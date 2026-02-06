<?php
session_start();
require_once './config/database.php';

$errorMessage = '';
$successMessage = '';

// If already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ./index.php');
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $errorMessage = 'Please enter both email and password.';
    } else {
        try {
            // Fetch user information
            $stmt = $pdo->prepare("SELECT u.id, u.email, u.password, u.name FROM users u WHERE u.email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Login successful - store user info in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_type'] = 'admin'; // Set as admin user

                header('Location: ./index.php');
                exit;
            } else {
                $errorMessage = 'Invalid email or password. Please try again.';
            }
        } catch (Exception $e) {
            $errorMessage = 'Error during login: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - RRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f3460 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
        }
    </style>
</head>

<body>
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-md">
            <!-- Logo Card -->
            <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
                <!-- Header with Logo -->
                <div class="bg-gradient-to-r from-blue-900 to-blue-700 px-6 sm:px-8 py-8 sm:py-12 text-center">
                    <div class="flex justify-center mb-6">
                        <img src="./assets/img/indian-railway-logo.png" alt="Indian Railways Logo" 
                             class="h-24 sm:h-28 w-auto">
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-white mb-2">RRMS</h1>
                    <p class="text-blue-100 text-sm">Running Room Management System</p>
                </div>

                <!-- Form Section -->
                <div class="px-6 sm:px-8 py-8 sm:py-10">
                    <!-- Error Message -->
                    <?php if ($errorMessage): ?>
                        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-circle text-red-600 mt-0.5 mr-3 flex-shrink-0"></i>
                                <p class="text-red-800 text-sm"><?php echo htmlspecialchars($errorMessage); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Success Message -->
                    <?php if ($successMessage): ?>
                        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-600 mt-0.5 mr-3 flex-shrink-0"></i>
                                <p class="text-green-800 text-sm"><?php echo htmlspecialchars($successMessage); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Login Form -->
                    <form method="POST" class="space-y-5">
                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <i class="fas fa-envelope absolute left-3 top-3.5 text-gray-400"></i>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    placeholder="admin@rrms.com"
                                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    required>
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <i class="fas fa-lock absolute left-3 top-3.5 text-gray-400"></i>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Enter your password"
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    required>
                            </div>
                        </div>

                        <!-- Remember Me Checkbox -->
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="remember" 
                                name="remember"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-600">
                                Remember me
                            </label>
                        </div>

                        <!-- Login Button -->
                        <button 
                            type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-sign-in-alt"></i>
                            Sign In
                        </button>
                    </form>

                    <!-- Footer -->
                    <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                        <p class="text-gray-600 text-xs sm:text-sm">
                            For admin login assistance, please contact the system administrator
                        </p>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-8 text-center">
                <p class="text-blue-100 text-xs sm:text-sm">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Secure Admin Access
                </p>
            </div>
        </div>
    </div>

    <script>
        // Optional: Add remember me functionality
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const rememberCheckbox = document.getElementById('remember');

            // Check if remembered credentials exist
            const savedEmail = localStorage.getItem('rrms_admin_email');
            if (savedEmail) {
                emailInput.value = savedEmail;
                rememberCheckbox.checked = true;
            }

            // Save credentials if remember me is checked
            document.querySelector('form').addEventListener('submit', function(e) {
                if (rememberCheckbox.checked) {
                    localStorage.setItem('rrms_admin_email', emailInput.value);
                } else {
                    localStorage.removeItem('rrms_admin_email');
                }
            });
        });
    </script>
</body>

</html>
