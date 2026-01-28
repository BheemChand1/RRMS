<?php
require_once 'config/database.php';

$message = '';
$messageType = '';

// Fetch all zones for dropdown
$stmt = $pdo->query("SELECT * FROM zones ORDER BY name ASC");
$zones = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zone_id = (int)($_POST['zone_id'] ?? 0);
    $name = trim($_POST['divisionName'] ?? '');
    
    if (!empty($name) && $zone_id > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO divisions (zone_id, name) VALUES (?, ?)");
            $stmt->execute([$zone_id, $name]);
            $message = "Division created successfully!";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Error creating division: " . $e->getMessage();
            $messageType = "error";
        }
    } else {
        $message = "Please fill all required fields.";
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Division - RRMS Admin Dashboard</title>
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
                        <a href="view_divisions.php" class="text-blue-600 hover:text-blue-800">Division</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">Create Division</span>
                    </div>

                    <!-- Alert Message -->
                    <?php if ($message): ?>
                        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-red-100 text-red-700 border border-red-400'; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form Card -->
                    <div class="bg-white rounded-lg shadow p-6 sm:p-8">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Create New Division</h1>
                        <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">Add a new division to manage your operations</p>

                        <form method="POST" class="space-y-6">
                            <!-- Zone Selection -->
                            <div>
                                <label for="zone_id" class="block text-sm font-semibold text-gray-700 mb-3">Zone
                                    <span class="text-red-600">*</span></label>
                                <select id="zone_id" name="zone_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    <option value="">Select a Zone</option>
                                    <?php foreach ($zones as $zone): ?>
                                        <option value="<?php echo $zone['id']; ?>"><?php echo htmlspecialchars($zone['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Division Name -->
                            <div>
                                <label for="divisionName" class="block text-sm font-semibold text-gray-700 mb-3">Division Name
                                    <span class="text-red-600">*</span></label>
                                <input type="text" id="divisionName" name="divisionName" placeholder="e.g., Ahmedabad Division, Mumbai Division"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-4 pt-8">
                                <button type="submit"
                                    class="flex-1 bg-blue-600 text-white py-2.5 px-6 rounded-lg hover:bg-blue-700 font-medium transition-colors">
                                    <i class="fas fa-save mr-2"></i> Create Division
                                </button>
                                <button type="reset"
                                    class="flex-1 bg-gray-500 text-white py-2.5 px-6 rounded-lg hover:bg-gray-600 font-medium transition-colors">
                                    <i class="fas fa-redo mr-2"></i> Clear
                                </button>
                                <a href="view_divisions.php"
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
