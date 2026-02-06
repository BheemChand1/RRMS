<?php
require_once 'config/database.php';

$division = null;
$zones = [];
$message = '';
$messageType = '';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: view_divisions.php');
    exit;
}

$id = (int)$_GET['id'];

// Fetch division details
try {
    $stmt = $pdo->prepare("SELECT d.*, z.name as zone_name FROM divisions d LEFT JOIN zones z ON d.zone_id = z.id WHERE d.id = ?");
    $stmt->execute([$id]);
    $division = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$division) {
        header('Location: view_divisions.php?msg=not_found');
        exit;
    }
    
    // Fetch all zones for dropdown
    $stmt = $pdo->query("SELECT * FROM zones ORDER BY name ASC");
    $zones = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = "Error fetching division: " . $e->getMessage();
    $messageType = "error";
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['divisionName'] ?? '');
    $zone_id = (int)($_POST['zone_id'] ?? 0);
    
    if (!empty($name) && $zone_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE divisions SET name = ?, zone_id = ? WHERE id = ?");
            $stmt->execute([$name, $zone_id, $id]);
            $message = "Division updated successfully!";
            $messageType = "success";
            
            // Refresh division data
            $stmt = $pdo->prepare("SELECT d.*, z.name as zone_name FROM divisions d LEFT JOIN zones z ON d.zone_id = z.id WHERE d.id = ?");
            $stmt->execute([$id]);
            $division = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $message = "Error updating division: " . $e->getMessage();
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
    <title>Edit Division - RRMS Admin Dashboard</title>
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
                    <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm text-gray-600 overflow-x-auto">
                        <a href="index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                        <i class="fas fa-chevron-right"></i>
                        <a href="view_divisions.php" class="text-blue-600 hover:text-blue-800">Division</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">Edit Division</span>
                    </div>

                    <!-- Page Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                            <i class="fas fa-edit text-blue-600"></i>
                            Edit Division
                        </h1>
                        <p class="text-gray-600 mt-2">Update division details for <span class="font-semibold text-gray-900"><?php echo $division ? htmlspecialchars($division['name']) : ''; ?></span></p>
                    </div>

                    <!-- Alert Message -->
                    <?php if ($message): ?>
                        <div class="mb-6 p-4 bg-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-50 border border-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-200 text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700 rounded-lg flex items-center gap-3">
                            <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                            <span><?php echo htmlspecialchars($message); ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Content Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Form Section -->
                        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6 md:p-8">
                            <?php if ($division): ?>
                                <form method="POST" class="space-y-6">
                                    <!-- Zone Selection -->
                                    <div>
                                        <label for="zone_id" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-map-pin"></i> Zone
                                        </label>
                                        <select id="zone_id" name="zone_id"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <option value="">Select a Zone</option>
                                            <?php foreach ($zones as $zone): ?>
                                                <option value="<?php echo $zone['id']; ?>" <?php echo $division['zone_id'] == $zone['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($zone['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Select the zone this division belongs to</p>
                                    </div>

                                    <!-- Division Name -->
                                    <div>
                                        <label for="divisionName" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-tag"></i> Division Name
                                        </label>
                                        <input type="text" id="divisionName" name="divisionName" placeholder="e.g., Ahmedabad Division, Mumbai Division"
                                            value="<?php echo htmlspecialchars($division['name']); ?>"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                        <p class="text-xs text-gray-500 mt-1">Enter a unique name for the division</p>
                                    </div>

                                    <!-- Form Actions -->
                                    <div class="flex gap-3 pt-6 border-t border-gray-200">
                                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                                            <i class="fas fa-save"></i> Save Changes
                                        </button>
                                        <a href="view_divisions.php" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center gap-2">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>

                        <!-- Info Sidebar -->
                        <div class="lg:col-span-1">
                            <!-- Division Information Card -->
                            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                    Division Information
                                </h3>
                                <div class="space-y-4 text-sm">
                                    <div>
                                        <p class="text-gray-600 mb-1"><i class="fas fa-id-badge mr-2"></i> Division ID</p>
                                        <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg"><?php echo $division ? $division['id'] : 'N/A'; ?></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 mb-1"><i class="fas fa-building mr-2"></i> Zone</p>
                                        <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg"><?php echo $division ? htmlspecialchars($division['zone_name'] ?? 'N/A') : 'N/A'; ?></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 mb-1"><i class="fas fa-clock mr-2"></i> Created</p>
                                        <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg"><?php echo $division ? date('M d, Y H:i', strtotime($division['created_at'])) : 'N/A'; ?></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 mb-1"><i class="fas fa-sync-alt mr-2"></i> Last Updated</p>
                                        <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg"><?php echo $division ? date('M d, Y H:i', strtotime($division['updated_at'])) : 'N/A'; ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Tips Card -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                                <h3 class="text-lg font-bold text-blue-900 mb-3 flex items-center gap-2">
                                    <i class="fas fa-lightbulb"></i>
                                    Tips
                                </h3>
                                <ul class="text-sm text-blue-800 space-y-2">
                                    <li class="flex gap-2">
                                        <i class="fas fa-check-circle flex-shrink-0 mt-0.5"></i>
                                        <span>Ensure the zone is correctly assigned</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <i class="fas fa-check-circle flex-shrink-0 mt-0.5"></i>
                                        <span>Division names should be unique and descriptive</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <i class="fas fa-check-circle flex-shrink-0 mt-0.5"></i>
                                        <span>Use consistent naming conventions across divisions</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>
