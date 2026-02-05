<?php
session_start();
require_once 'config/database.php';

// Allow logged-in users to view the portal (no redirect)

// Get all locations
$locations = [];
try {
    $stmt = $pdo->query("SELECT id, name, short_name FROM locations ORDER BY name ASC");
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
}

// Handle AJAX request for users by location
if (isset($_GET['action']) && $_GET['action'] === 'get_users' && isset($_GET['location_id'])) {
    header('Content-Type: application/json');
    $locationId = intval($_GET['location_id']);
    $userTypeFilter = isset($_GET['user_type']) ? trim($_GET['user_type']) : null;
    
    try {
        // Build query with optional user type filter
        $query = "
            SELECT u.id, u.name, u.email, u.mobile, ut.type_name
            FROM users u
            LEFT JOIN user_types ut ON u.user_type_id = ut.id
            WHERE u.location_id = ?
        ";
        
        $params = [$locationId];
        
        if ($userTypeFilter) {
            $query .= " AND ut.type_name = ?";
            $params[] = $userTypeFilter;
        }
        
        $query .= " ORDER BY ut.id, u.name ASC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Group users by type
        $usersByType = [];
        foreach ($users as $user) {
            $typeId = $user['type_name'] ?? 'Unknown';
            if (!isset($usersByType[$typeId])) {
                $usersByType[$typeId] = [];
            }
            $usersByType[$typeId][] = $user;
        }
        
        echo json_encode([
            'success' => true,
            'users' => $usersByType
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal - RRMS</title>
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
                        <span class="text-gray-900 font-medium">Login Portal</span>
                    </div>

                    <!-- Page Header -->
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                            <i class="fas fa-building text-blue-600"></i>
                            Login Portal
                        </h1>
                        <p class="text-gray-600 mt-2">Select a location to view available users</p>
                    </div>

                    <!-- Search Section -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-search text-gray-400 text-xl"></i>
                            <input type="text" id="searchInput" placeholder="Search locations by name..."
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Location Cards Grid -->
                    <div id="locationsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($locations as $location): ?>
                        <div class="location-card group bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1" data-location-name="<?php echo htmlspecialchars(strtolower($location['name'])); ?>">
                            <!-- Card Header -->
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white text-center">
                                <i class="fas fa-map-marker-alt text-4xl mb-3 block opacity-90 group-hover:scale-110 transition-transform duration-300"></i>
                                <h2 class="text-xl font-bold"><?php echo htmlspecialchars($location['name']); ?></h2>
                            </div>

                            <!-- Card Body -->
                            <div class="p-5">
                                <p class="text-gray-600 text-center text-sm mb-4 pb-4 border-b border-gray-200">
                                    <i class="fas fa-barcode text-blue-600 mr-1"></i>
                                    <span class="font-semibold"><?php echo htmlspecialchars($location['short_name'] ?? 'N/A'); ?></span>
                                </p>

                            <!-- User Type Buttons -->
                            <div class="space-y-2">
                                <button type="button"
                                    class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 text-sm">
                                    <i class="fas fa-user-tie"></i>
                                    <span>Reception Manager</span>
                                </button>

                                <button type="button"
                                    class="w-full bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2 text-sm">
                                    <i class="fas fa-user-check"></i>
                                    <span>Location Manager</span>
                                </button>

                                <button type="button"
                                    class="w-full bg-purple-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-purple-700 transition-colors flex items-center justify-center gap-2 text-sm">
                                    <i class="fas fa-user"></i>
                                    <span>Division Manager</span>
                                </button>
                            </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (empty($locations)): ?>
                    <div class="text-center bg-white rounded-lg shadow-sm p-12">
                        <i class="fas fa-inbox text-5xl mb-4 opacity-30 block text-gray-400"></i>
                        <p class="text-xl text-gray-600">No locations found in the system</p>
                    </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const locationCards = document.querySelectorAll('.location-card');

        locationCards.forEach(card => {
            const locationName = card.getAttribute('data-location-name');
            if (locationName.includes(searchTerm)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Clear search on input clear
    document.getElementById('searchInput').addEventListener('change', function() {
        if (this.value === '') {
            const locationCards = document.querySelectorAll('.location-card');
            locationCards.forEach(card => {
                card.style.display = '';
            });
        }
    });
    </script>
</body>
</html>
