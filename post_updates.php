<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$successMessage = '';
$errorMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? 'General');
    
    // Validation
    if (empty($title)) {
        $errorMessage = 'Title is required.';
    } elseif (empty($content)) {
        $errorMessage = 'Content is required.';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO updates (title, content, category, posted_by, posted_date)
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $title,
                $content,
                $category,
                $_SESSION['user_id']
            ]);
            
            $successMessage = 'Update posted successfully!';
            // Clear form
            $_POST = [];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errorMessage = 'Failed to post update. Please try again.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Updates - RRMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 md:ml-64 flex flex-col overflow-hidden">
            <?php include 'includes/navbar.php'; ?>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto p-3 sm:p-4 md:p-8">
                <div class="w-full">
                    <!-- Breadcrumb -->
                    <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm text-gray-600">
                        <a href="index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">Post Updates</span>
                    </div>

                    <!-- Page Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                            <i class="fas fa-newspaper text-blue-600"></i>
                            Post News & Updates
                        </h1>
                        <p class="text-gray-600 mt-2">Share important news and updates with all users</p>
                    </div>

                    <!-- Success Message -->
                    <?php if (!empty($successMessage)): ?>
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-3">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo htmlspecialchars($successMessage); ?></span>
                    </div>
                    <?php endif; ?>

                    <!-- Error Message -->
                    <?php if (!empty($errorMessage)): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center gap-3">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($errorMessage); ?></span>
                    </div>
                    <?php endif; ?>

                    <!-- Form Container -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Form Section -->
                        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6 md:p-8">
                            <form method="POST" class="space-y-6">
                                <!-- Title -->
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-heading"></i> Title
                                    </label>
                                    <input type="text" 
                                        id="title"
                                        name="title"
                                        placeholder="Enter update title"
                                        value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required>
                                    <p class="text-xs text-gray-500 mt-1">Make it clear and concise</p>
                                </div>

                                <!-- Category -->
                                <div>
                                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-tag"></i> Category
                                    </label>
                                    <select id="category"
                                        name="category"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="General" <?php echo ($_POST['category'] ?? '') === 'General' ? 'selected' : ''; ?>>General</option>
                                        <option value="Maintenance" <?php echo ($_POST['category'] ?? '') === 'Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                        <option value="Emergency" <?php echo ($_POST['category'] ?? '') === 'Emergency' ? 'selected' : ''; ?>>Emergency</option>
                                        <option value="Event" <?php echo ($_POST['category'] ?? '') === 'Event' ? 'selected' : ''; ?>>Event</option>
                                        <option value="Announcement" <?php echo ($_POST['category'] ?? '') === 'Announcement' ? 'selected' : ''; ?>>Announcement</option>
                                    </select>
                                </div>

                                <!-- Content -->
                                <div>
                                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-pen-fancy"></i> Content
                                    </label>
                                    <textarea id="content"
                                        name="content"
                                        placeholder="Write your update message here..."
                                        rows="10"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-vertical"
                                        required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Provide detailed information about the update</p>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex gap-3 pt-4 border-t border-gray-200">
                                    <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                                        <i class="fas fa-paper-plane"></i> Post Update
                                    </button>
                                    <button type="reset" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center gap-2">
                                        <i class="fas fa-redo"></i> Clear
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Info Sidebar -->
                        <div class="space-y-6">
                            <!-- Tips Card -->
                            <div class="bg-blue-50 rounded-lg shadow-sm p-6 border border-blue-200">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="p-3 bg-blue-600 text-white rounded-lg">
                                        <i class="fas fa-lightbulb text-lg"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">Tips</h3>
                                </div>
                                <ul class="space-y-2 text-sm text-gray-700">
                                    <li class="flex gap-2">
                                        <i class="fas fa-check text-green-600 flex-shrink-0 mt-0.5"></i>
                                        <span>Keep titles short and descriptive</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <i class="fas fa-check text-green-600 flex-shrink-0 mt-0.5"></i>
                                        <span>Use appropriate categories</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <i class="fas fa-check text-green-600 flex-shrink-0 mt-0.5"></i>
                                        <span>Be clear and concise</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <i class="fas fa-check text-green-600 flex-shrink-0 mt-0.5"></i>
                                        <span>Include all relevant details</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Categories Card -->
                            <div class="bg-purple-50 rounded-lg shadow-sm p-6 border border-purple-200">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="p-3 bg-purple-600 text-white rounded-lg">
                                        <i class="fas fa-tags text-lg"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900">Categories</h3>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block px-3 py-1 bg-blue-200 text-blue-800 rounded-full text-xs font-semibold">General</span>
                                        <span class="text-xs text-gray-600">Regular updates</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block px-3 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs font-semibold">Maintenance</span>
                                        <span class="text-xs text-gray-600">System maintenance</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block px-3 py-1 bg-red-200 text-red-800 rounded-full text-xs font-semibold">Emergency</span>
                                        <span class="text-xs text-gray-600">Urgent matters</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block px-3 py-1 bg-green-200 text-green-800 rounded-full text-xs font-semibold">Event</span>
                                        <span class="text-xs text-gray-600">Special events</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block px-3 py-1 bg-indigo-200 text-indigo-800 rounded-full text-xs font-semibold">Announcement</span>
                                        <span class="text-xs text-gray-600">Important announcements</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
