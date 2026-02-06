<?php
require_once 'config/database.php';

$parameter = null;
$locations = [];
$message = '';
$messageType = '';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: view_feedback.php');
    exit;
}

$id = (int)$_GET['id'];

// Fetch feedback parameter details
try {
    $stmt = $pdo->prepare("SELECT fp.*, l.name as location_name FROM feedback_parameters fp LEFT JOIN locations l ON fp.location_id = l.id WHERE fp.id = ?");
    $stmt->execute([$id]);
    $parameter = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$parameter) {
        header('Location: view_feedback.php?msg=not_found');
        exit;
    }
    
    // Fetch all locations for dropdown
    $stmt = $pdo->query("SELECT * FROM locations ORDER BY name ASC");
    $locations = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = "Error fetching parameter: " . $e->getMessage();
    $messageType = "error";
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['parameterName'] ?? '');
    $location_id = (int)($_POST['location_id'] ?? 0);
    
    if (!empty($name) && $location_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE feedback_parameters SET name = ?, location_id = ? WHERE id = ?");
            $stmt->execute([$name, $location_id, $id]);
            $message = "Feedback parameter updated successfully!";
            $messageType = "success";
            
            // Refresh parameter data
            $stmt = $pdo->prepare("SELECT fp.*, l.name as location_name FROM feedback_parameters fp LEFT JOIN locations l ON fp.location_id = l.id WHERE fp.id = ?");
            $stmt->execute([$id]);
            $parameter = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $message = "Error updating parameter: " . $e->getMessage();
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
    <title>Edit Feedback Parameter - RRMS Admin Dashboard</title>
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
                        <a href="view_feedback.php" class="text-blue-600 hover:text-blue-800">Feedback</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">Edit Parameter</span>
                    </div>

                    <!-- Page Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                            <i class="fas fa-edit text-blue-600"></i>
                            Edit Feedback Parameter
                        </h1>
                        <p class="text-gray-600 mt-2">Update feedback parameter details for <span class="font-semibold text-gray-900"><?php echo $parameter ? htmlspecialchars($parameter['name']) : ''; ?></span></p>
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
                            <?php if ($parameter): ?>
                                <form method="POST" class="space-y-6">
                                    <!-- Parameter Name -->
                                    <div>
                                        <label for="parameterName" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-tag"></i> Parameter Name
                                        </label>
                                        <input type="text" id="parameterName" name="parameterName" placeholder="e.g., Cleanliness, Food Quality, Staff Behavior"
                                            value="<?php echo htmlspecialchars($parameter['name']); ?>"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                        <p class="text-xs text-gray-500 mt-1">Enter a descriptive parameter name</p>
                                    </div>

                                    <!-- Location Selection -->
                                    <div>
                                        <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-map-marker-alt"></i> Location
                                        </label>
                                        <select id="location_id" name="location_id"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <option value="">Select a Location</option>
                                            <?php foreach ($locations as $location): ?>
                                                <option value="<?php echo $location['id']; ?>" <?php echo $parameter['location_id'] == $location['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($location['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Select the location this parameter belongs to</p>
                                    </div>

                                    <!-- Form Actions -->
                                    <div class="flex gap-3 pt-6 border-t border-gray-200">
                                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                                            <i class="fas fa-save"></i> Save Changes
                                        </button>
                                        <a href="view_feedback.php" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center gap-2">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>

                        <!-- Info Sidebar -->
                        <div class="lg:col-span-1">
                            <!-- Parameter Information Card -->
                            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                    Parameter Information
                                </h3>
                                <div class="space-y-4 text-sm">
                                    <div>
                                        <p class="text-gray-600 mb-1"><i class="fas fa-id-badge mr-2"></i> Parameter ID</p>
                                        <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg"><?php echo $parameter ? $parameter['id'] : 'N/A'; ?></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 mb-1"><i class="fas fa-map-marker-alt mr-2"></i> Location</p>
                                        <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg"><?php echo $parameter ? htmlspecialchars($parameter['location_name'] ?? 'N/A') : 'N/A'; ?></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 mb-1"><i class="fas fa-clock mr-2"></i> Created</p>
                                        <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg"><?php echo $parameter ? date('M d, Y H:i', strtotime($parameter['created_at'])) : 'N/A'; ?></p>
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
                                        <span>Parameter names should be clear and measurable</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <i class="fas fa-check-circle flex-shrink-0 mt-0.5"></i>
                                        <span>Ensure the location is correctly assigned</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <i class="fas fa-check-circle flex-shrink-0 mt-0.5"></i>
                                        <span>Use consistent naming conventions across parameters</span>
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
