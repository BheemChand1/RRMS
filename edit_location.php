<?php
require_once 'config/database.php';

$location = null;
$zones = [];
$divisions = [];
$message = '';
$messageType = '';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: view_locations.php');
    exit;
}

$id = (int)$_GET['id'];

// Fetch location details
try {
    $stmt = $pdo->prepare("SELECT l.*, z.name as zone_name, d.name as division_name FROM locations l LEFT JOIN zones z ON l.zone_id = z.id LEFT JOIN divisions d ON l.division_id = d.id WHERE l.id = ?");
    $stmt->execute([$id]);
    $location = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$location) {
        header('Location: view_locations.php?msg=not_found');
        exit;
    }
    
    // Fetch all zones for dropdown
    $stmt = $pdo->query("SELECT * FROM zones ORDER BY name ASC");
    $zones = $stmt->fetchAll();
    
    // Fetch all divisions for dropdown
    $stmt = $pdo->query("SELECT * FROM divisions ORDER BY name ASC");
    $divisions = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = "Error fetching location: " . $e->getMessage();
    $messageType = "error";
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['locationName'] ?? '');
    $short_name = trim($_POST['shortName'] ?? '');
    $zone_id = (int)($_POST['zone_id'] ?? 0);
    $division_id = (int)($_POST['division_id'] ?? 0);
    $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
    $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
    $is_subscribed = isset($_POST['is_subscribed']) ? 1 : 0;
    $subscription_start = !empty($_POST['subscription_start']) ? $_POST['subscription_start'] : null;
    $subscription_end = !empty($_POST['subscription_end']) ? $_POST['subscription_end'] : null;
    
    if (!empty($name) && !empty($short_name) && $zone_id > 0 && $division_id > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE locations SET name = ?, short_name = ?, zone_id = ?, division_id = ?, latitude = ?, longitude = ?, is_subscribed = ?, subscription_start = ?, subscription_end = ? WHERE id = ?");
            $stmt->execute([$name, $short_name, $zone_id, $division_id, $latitude, $longitude, $is_subscribed, $subscription_start, $subscription_end, $id]);
            $message = "Location updated successfully!";
            $messageType = "success";
            
            // Refresh location data
            $stmt = $pdo->prepare("SELECT l.*, z.name as zone_name, d.name as division_name FROM locations l LEFT JOIN zones z ON l.zone_id = z.id LEFT JOIN divisions d ON l.division_id = d.id WHERE l.id = ?");
            $stmt->execute([$id]);
            $location = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $message = "Error updating location: " . $e->getMessage();
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
    <title>Edit Location - RRMS Admin Dashboard</title>
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
                        <a href="view_locations.php" class="text-blue-600 hover:text-blue-800">Location</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">Edit Location</span>
                    </div>

                    <!-- Page Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                            <i class="fas fa-edit text-blue-600"></i>
                            Edit Location
                        </h1>
                        <p class="text-gray-600 mt-2">Update location details for <span class="font-semibold text-gray-900"><?php echo $location ? htmlspecialchars($location['name']) : ''; ?></span></p>
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
                            <?php if ($location): ?>
                                <form method="POST" class="space-y-6">
                                    <!-- Location Name -->
                                    <div>
                                        <label for="locationName" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-map-marker-alt"></i> Location Name
                                        </label>
                                        <input type="text" id="locationName" name="locationName" placeholder="e.g., Ahmedabad Central, Mumbai Main"
                                            value="<?php echo htmlspecialchars($location['name']); ?>"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                        <p class="text-xs text-gray-500 mt-1">Full location name</p>
                                    </div>

                                    <!-- Short Name -->
                                    <div>
                                        <label for="shortName" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-tag"></i> Short Name
                                        </label>
                                        <input type="text" id="shortName" name="shortName" placeholder="e.g., AMD, MUM"
                                            value="<?php echo htmlspecialchars($location['short_name']); ?>"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                        <p class="text-xs text-gray-500 mt-1">Short abbreviation</p>
                                    </div>

                                    <!-- Zone Selection -->
                                    <div>
                                        <label for="zone_id" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-map-pin"></i> Zone
                                        </label>
                                        <select id="zone_id" name="zone_id"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <option value="">Select a Zone</option>
                                            <?php foreach ($zones as $zone): ?>
                                                <option value="<?php echo $zone['id']; ?>" <?php echo $location['zone_id'] == $zone['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($zone['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Select the zone for this location</p>
                                    </div>

                                    <!-- Division Selection -->
                                    <div>
                                        <label for="division_id" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-building"></i> Division
                                        </label>
                                        <select id="division_id" name="division_id"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                            <option value="">Select a Division</option>
                                            <?php foreach ($divisions as $division): ?>
                                                <option value="<?php echo $division['id']; ?>" <?php echo $location['division_id'] == $division['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($division['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Select the division for this location</p>
                                    </div>

                                    <!-- Latitude & Longitude -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-compass"></i> Latitude
                                            </label>
                                            <input type="number" id="latitude" name="latitude" placeholder="e.g., 23.1815" step="0.0001"
                                                value="<?php echo $location['latitude'] ?? ''; ?>"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <p class="text-xs text-gray-500 mt-1">Optional</p>
                                        </div>
                                        <div>
                                            <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-compass"></i> Longitude
                                            </label>
                                            <input type="number" id="longitude" name="longitude" placeholder="e.g., 72.6311" step="0.0001"
                                                value="<?php echo $location['longitude'] ?? ''; ?>"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <p class="text-xs text-gray-500 mt-1">Optional</p>
                                        </div>
                                    </div>

                                    <!-- Subscription Status -->
                                    <div>
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input type="checkbox" 
                                                id="is_subscribed"
                                                name="is_subscribed"
                                                value="1"
                                                <?php echo $location['is_subscribed'] == 1 ? 'checked' : ''; ?>
                                                class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                                            <span class="text-sm font-medium text-gray-700">
                                                <i class="fas fa-check-circle"></i> Active Subscription
                                            </span>
                                        </label>
                                        <p class="text-xs text-gray-500 mt-2 ml-8">Check this box if location has an active subscription</p>
                                    </div>

                                    <!-- Subscription Dates -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="subscription_start" class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-calendar-check"></i> Start Date
                                            </label>
                                            <input type="date" 
                                                id="subscription_start"
                                                name="subscription_start"
                                                value="<?php echo $location['subscription_start'] ? date('Y-m-d', strtotime($location['subscription_start'])) : ''; ?>"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <p class="text-xs text-gray-500 mt-1">Optional</p>
                                        </div>
                                        <div>
                                            <label for="subscription_end" class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-calendar-times"></i> End Date
                                            </label>
                                            <input type="date" 
                                                id="subscription_end"
                                                name="subscription_end"
                                                value="<?php echo $location['subscription_end'] ? date('Y-m-d', strtotime($location['subscription_end'])) : ''; ?>"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <p class="text-xs text-gray-500 mt-1">Optional</p>
                                        </div>
                                    </div>

                                    <!-- Form Actions -->
                                    <div class="flex gap-3 pt-6 border-t border-gray-200">
                                        <button type="submit" class="flex-1 px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                                            <i class="fas fa-save"></i> Save Changes
                                        </button>
                                        <a href="view_locations.php" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center gap-2">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>

                        <!-- Info Sidebar -->
                        <div class="lg:col-span-1">
                            <!-- Location Information Card -->
                            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                    Location Information
                                </h3>
                                <div class="space-y-4 text-sm">
                                    <div>
                                        <p class="text-gray-600 mb-1"><i class="fas fa-id-badge mr-2"></i> Location ID</p>
                                        <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg"><?php echo $location ? $location['id'] : 'N/A'; ?></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 mb-1"><i class="fas fa-map-pin mr-2"></i> Zone</p>
                                        <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg"><?php echo $location ? htmlspecialchars($location['zone_name'] ?? 'N/A') : 'N/A'; ?></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 mb-1"><i class="fas fa-building mr-2"></i> Division</p>
                                        <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg"><?php echo $location ? htmlspecialchars($location['division_name'] ?? 'N/A') : 'N/A'; ?></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 mb-1"><i class="fas fa-clock mr-2"></i> Created</p>
                                        <p class="font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg"><?php echo $location ? date('M d, Y H:i', strtotime($location['created_at'])) : 'N/A'; ?></p>
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
                                        <span>Ensure zone and division are correctly assigned</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <i class="fas fa-check-circle flex-shrink-0 mt-0.5"></i>
                                        <span>Short name should be a unique abbreviation</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <i class="fas fa-check-circle flex-shrink-0 mt-0.5"></i>
                                        <span>Coordinates are optional but useful for mapping</span>
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
