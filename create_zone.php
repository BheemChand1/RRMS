<?php
require_once 'config/database.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['zoneName'] ?? '');
    
    if (!empty($name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO zones (name) VALUES (?)");
            $stmt->execute([$name]);
            $message = "Zone created successfully!";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Error creating zone: " . $e->getMessage();
            $messageType = "error";
        }
    } else {
        $message = "Please enter a zone name.";
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Zone - RRMS Admin Dashboard</title>
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
            <main class="flex-1 overflow-auto p-3 sm:p-4 md:p-8 flex items-center justify-center">
                <div class="w-full max-w-6xl">
                    <!-- Breadcrumb -->
                    <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm text-gray-600 overflow-x-auto">
                        <a href="index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                        <i class="fas fa-chevron-right"></i>
                        <a href="view_zones.php" class="text-blue-600 hover:text-blue-800">Zone</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">Create Zone</span>
                    </div>

                    <!-- Alert Message -->
                    <?php if ($message): ?>
                        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-red-100 text-red-700 border border-red-400'; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form Card -->
                    <div class="bg-white rounded-lg shadow p-6 sm:p-8">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Create New Zone</h1>
                        <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">Add a new zone to manage your regions and properties</p>

                        <form method="POST" class="space-y-6">
                            <!-- Zone Name -->
                            <div>
                                <label for="zoneName" class="block text-sm font-semibold text-gray-700 mb-3">Zone Name
                                    <span class="text-red-600">*</span></label>
                                <input type="text" id="zoneName" name="zoneName" placeholder="e.g., Western Railways, Northern Railways"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <p class="text-xs text-gray-500 mt-2">Enter a unique name for the zone</p>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-4 pt-8">
                                <button type="submit"
                                    class="flex-1 bg-blue-600 text-white py-2.5 px-6 rounded-lg hover:bg-blue-700 font-medium transition-colors">
                                    <i class="fas fa-save mr-2"></i> Create Zone
                                </button>
                                <button type="reset"
                                    class="flex-1 bg-gray-500 text-white py-2.5 px-6 rounded-lg hover:bg-gray-600 font-medium transition-colors">
                                    <i class="fas fa-redo mr-2"></i> Clear
                                </button>
                                <a href="view_zones.php"
                                    class="flex-1 bg-gray-700 text-white py-2.5 px-6 rounded-lg hover:bg-gray-800 font-medium transition-colors text-center">
                                    <i class="fas fa-arrow-left mr-2"></i> Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>
