<?php
require_once 'config/database.php';

$message = '';
$messageType = '';

// Fetch all zones for dropdown
$stmt = $pdo->query("SELECT * FROM zones ORDER BY name ASC");
$zones = $stmt->fetchAll();

// Fetch all divisions for dropdown
$stmt = $pdo->query("SELECT * FROM divisions ORDER BY name ASC");
$divisions = $stmt->fetchAll();

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
            $stmt = $pdo->prepare("INSERT INTO locations (name, short_name, zone_id, division_id, latitude, longitude, is_subscribed, subscription_start, subscription_end) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $short_name, $zone_id, $division_id, $latitude, $longitude, $is_subscribed, $subscription_start, $subscription_end]);
            $message = "Location created successfully!";
            $messageType = "success";
        } catch (PDOException $e) {
            $message = "Error creating location: " . $e->getMessage();
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
    <title>Create Location - RRMS Admin Dashboard</title>
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
                        <a href="view_locations.php" class="text-blue-600 hover:text-blue-800">Location</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">Create Location</span>
                    </div>

                    <!-- Alert Message -->
                    <?php if ($message): ?>
                        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-red-100 text-red-700 border border-red-400'; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form Card -->
                    <div class="bg-white rounded-lg shadow p-6 sm:p-8">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Create New Location</h1>
                        <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">Add a new property location to the system</p>

                        <form method="POST" class="space-y-6">
                            <!-- Location Name -->
                            <div>
                                <label for="locationName" class="block text-sm font-semibold text-gray-700 mb-3">Location Name
                                    <span class="text-red-600">*</span></label>
                                <input type="text" id="locationName" name="locationName" placeholder="e.g., Ahmedabad Central"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            </div>

                            <!-- Short Name -->
                            <div>
                                <label for="shortName" class="block text-sm font-semibold text-gray-700 mb-3">Short Name
                                    <span class="text-red-600">*</span></label>
                                <input type="text" id="shortName" name="shortName" placeholder="e.g., ADI, BCT"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                <p class="text-xs text-gray-500 mt-2">Short code for the location</p>
                            </div>

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

                            <!-- Division Selection -->
                            <div>
                                <label for="division_id" class="block text-sm font-semibold text-gray-700 mb-3">Division
                                    <span class="text-red-600">*</span></label>
                                <select id="division_id" name="division_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                                    <option value="">Select a Division</option>
                                    <?php foreach ($divisions as $division): ?>
                                        <option value="<?php echo $division['id']; ?>"><?php echo htmlspecialchars($division['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Latitude and Longitude -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-3">Latitude
                                        <span class="text-gray-400 font-normal">(Optional)</span></label>
                                    <input type="number" id="latitude" name="latitude" step="any" placeholder="e.g., 23.0225"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <p class="text-xs text-gray-500 mt-2">Latitude coordinate (-90 to 90)</p>
                                </div>
                                <div>
                                    <label for="longitude" class="block text-sm font-semibold text-gray-700 mb-3">Longitude
                                        <span class="text-gray-400 font-normal">(Optional)</span></label>
                                    <input type="number" id="longitude" name="longitude" step="any" placeholder="e.g., 72.5714"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <p class="text-xs text-gray-500 mt-2">Longitude coordinate (-180 to 180)</p>
                                </div>
                            </div>

                            <!-- Subscription Settings -->
                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4"><i class="fas fa-crown text-yellow-500 mr-2"></i>Subscription Settings</h3>
                                
                                <!-- Is Subscribed Toggle -->
                                <div class="mb-6">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" id="is_subscribed" name="is_subscribed" value="1" checked
                                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-3 text-sm font-semibold text-gray-700">Location is Subscribed</span>
                                    </label>
                                    <p class="text-xs text-gray-500 mt-2 ml-8">Enable subscription for this location (default: enabled)</p>
                                </div>

                                <!-- Subscription Period -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="subscription_start" class="block text-sm font-semibold text-gray-700 mb-3">Subscription Start Date
                                            <span class="text-gray-400 font-normal">(Optional)</span></label>
                                        <input type="date" id="subscription_start" name="subscription_start"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                    <div>
                                        <label for="subscription_end" class="block text-sm font-semibold text-gray-700 mb-3">Subscription End Date
                                            <span class="text-gray-400 font-normal">(Optional)</span></label>
                                        <input type="date" id="subscription_end" name="subscription_end"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-4 pt-8">
                                <button type="submit"
                                    class="flex-1 bg-blue-600 text-white py-2.5 px-6 rounded-lg hover:bg-blue-700 font-medium transition-colors">
                                    <i class="fas fa-save mr-2"></i> Create Location
                                </button>
                                <button type="reset"
                                    class="flex-1 bg-gray-500 text-white py-2.5 px-6 rounded-lg hover:bg-gray-600 font-medium transition-colors">
                                    <i class="fas fa-redo mr-2"></i> Clear
                                </button>
                                <a href="view_locations.php"
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
