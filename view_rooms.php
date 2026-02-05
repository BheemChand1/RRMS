<?php
require_once './config/database.php';

$locations = [];
$selectedLocationId = $_GET['location_id'] ?? null;
$roomsData = [];
$selectedLocationName = '';
$successMessage = '';
$errorMessage = '';

// Fetch all locations
try {
    $stmt = $pdo->query("SELECT id, name FROM locations ORDER BY name ASC");
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $errorMessage = "Error fetching locations: " . $e->getMessage();
}

// Handle delete room
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_room') {
    $roomId = $_POST['room_id'] ?? null;
    if ($roomId) {
        try {
            // Delete beds first (foreign key constraint)
            $deleteBeds = $pdo->prepare("DELETE FROM beds WHERE room_id = :room_id");
            $deleteBeds->execute([':room_id' => $roomId]);
            
            // Then delete room
            $deleteRoom = $pdo->prepare("DELETE FROM rooms WHERE id = :room_id");
            $deleteRoom->execute([':room_id' => $roomId]);
            
            $successMessage = "Room deleted successfully!";
        } catch (Exception $e) {
            $errorMessage = "Error deleting room: " . $e->getMessage();
        }
    }
}

// Handle update room name
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_room') {
    $roomId = $_POST['room_id'] ?? null;
    $roomName = $_POST['room_name'] ?? null;
    if ($roomId && $roomName) {
        try {
            $stmt = $pdo->prepare("UPDATE rooms SET room = :room WHERE id = :room_id");
            $stmt->execute([':room' => $roomName, ':room_id' => $roomId]);
            $successMessage = "Room name updated successfully!";
        } catch (Exception $e) {
            $errorMessage = "Error updating room: " . $e->getMessage();
        }
    }
}

// Handle delete bed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_bed') {
    $bedId = $_POST['bed_id'] ?? null;
    if ($bedId) {
        try {
            $deleteBed = $pdo->prepare("DELETE FROM beds WHERE id = :bed_id");
            $deleteBed->execute([':bed_id' => $bedId]);
            $successMessage = "Bed deleted successfully!";
        } catch (Exception $e) {
            $errorMessage = "Error deleting bed: " . $e->getMessage();
        }
    }
}

// Handle update bed name
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_bed') {
    $bedId = $_POST['bed_id'] ?? null;
    $bedName = $_POST['bed_name'] ?? null;
    if ($bedId && $bedName) {
        try {
            $stmt = $pdo->prepare("UPDATE beds SET bed_name = :bed_name WHERE id = :bed_id");
            $stmt->execute([':bed_name' => $bedName, ':bed_id' => $bedId]);
            $successMessage = "Bed name updated successfully!";
        } catch (Exception $e) {
            $errorMessage = "Error updating bed: " . $e->getMessage();
        }
    }
}

// Handle add bed to room
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_bed') {
    $roomId = $_POST['room_id'] ?? null;
    $bedName = $_POST['bed_name'] ?? null;
    if ($roomId && $bedName) {
        try {
            // Get room details
            $roomStmt = $pdo->prepare("SELECT division_id, location_id, gender FROM rooms WHERE id = :room_id");
            $roomStmt->execute([':room_id' => $roomId]);
            $roomData = $roomStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($roomData) {
                $stmt = $pdo->prepare("
                    INSERT INTO beds (room_id, division_id, location_id, gender, bed_name, status, created_at, updated_at)
                    VALUES (:room_id, :division_id, :location_id, :gender, :bed_name, 0, NOW(), NOW())
                ");
                $stmt->execute([
                    ':room_id' => $roomId,
                    ':division_id' => $roomData['division_id'],
                    ':location_id' => $roomData['location_id'],
                    ':gender' => $roomData['gender'],
                    ':bed_name' => $bedName
                ]);
                
                // Update room no_of_bed count
                $updateStmt = $pdo->prepare("UPDATE rooms SET no_of_bed = no_of_bed + 1 WHERE id = :room_id");
                $updateStmt->execute([':room_id' => $roomId]);
                
                $successMessage = "Bed added successfully!";
            }
        } catch (Exception $e) {
            $errorMessage = "Error adding bed: " . $e->getMessage();
        }
    }
}

// Fetch rooms and beds for selected location
if ($selectedLocationId) {
    try {
        // Get location name
        $locStmt = $pdo->prepare("SELECT name FROM locations WHERE id = :location_id");
        $locStmt->execute([':location_id' => $selectedLocationId]);
        $locData = $locStmt->fetch(PDO::FETCH_ASSOC);
        $selectedLocationName = $locData['name'] ?? 'Unknown';
        
        $stmt = $pdo->prepare("
            SELECT r.id as room_id, r.room, r.gender, r.no_of_bed, r.status as room_status
            FROM rooms r
            WHERE r.location_id = :location_id
            ORDER BY r.room ASC
        ");
        $stmt->execute([':location_id' => $selectedLocationId]);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // For each room, fetch beds
        foreach ($rooms as &$room) {
            $bedStmt = $pdo->prepare("
                SELECT id, bed_name, status
                FROM beds
                WHERE room_id = :room_id
                ORDER BY bed_name ASC
            ");
            $bedStmt->execute([':room_id' => $room['room_id']]);
            $room['beds'] = $bedStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        $roomsData = $rooms;
    } catch (Exception $e) {
        $errorMessage = "Error fetching rooms: " . $e->getMessage();
    }
}

// Get status color based on bed status
function getStatusColor($status) {
    $statusMap = [
        0 => ['color' => 'bg-green-500', 'text' => 'Vacant'],
        1 => ['color' => 'bg-red-500', 'text' => 'Occupied'],
        2 => ['color' => 'bg-yellow-500', 'text' => 'Block'],
        3 => ['color' => 'bg-orange-500', 'text' => 'Checking out']
    ];
    return $statusMap[$status] ?? ['color' => 'bg-green-500', 'text' => 'Vacant'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Rooms - RRMS Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <?php include './includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 md:ml-64 flex flex-col overflow-hidden">
            <?php include './includes/navbar.php'; ?>

            <!-- Page Content -->
            <main class="flex-1 overflow-auto p-3 sm:p-4 md:p-8">
                <!-- Breadcrumb -->
                <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm text-gray-600">
                    <a href="./index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                    <i class="fas fa-chevron-right"></i>
                    <span class="text-gray-900 font-medium">View Rooms</span>
                </div>

                <!-- Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 mb-6">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Room & Bed Layout</h1>
                    <a href="./create_room.php"
                        class="w-full sm:w-auto bg-blue-600 text-white py-2.5 px-4 rounded-lg hover:bg-blue-700 font-medium transition-colors text-center text-sm sm:text-base">
                        <i class="fas fa-plus mr-2"></i> Create Room
                    </a>
                </div>

                <!-- Location Selector -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <label for="locationSelect" class="block text-sm font-semibold text-gray-700 mb-3">
                        Select Location to View Rooms & Beds
                    </label>
                    <div class="flex gap-3">
                        <select id="locationSelect" onchange="filterByLocation()" 
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Select Location --</option>
                            <?php foreach ($locations as $location): ?>
                                <option value="<?php echo $location['id']; ?>" <?php echo ($selectedLocationId == $location['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($location['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Success Message -->
                <?php if ($successMessage): ?>
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-3"></i>
                        <p class="text-green-800"><?php echo $successMessage; ?></p>
                    </div>
                <?php endif; ?>

                <!-- Error Message -->
                <?php if ($errorMessage): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center">
                        <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                        <p class="text-red-800"><?php echo $errorMessage; ?></p>
                    </div>
                <?php endif; ?>

                <!-- Rooms Display -->
                <?php if ($selectedLocationId && !empty($roomsData)): ?>
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6"><?php echo htmlspecialchars($selectedLocationName); ?></h2>

                        <!-- Room Sections -->
                        <div class="space-y-8">
                            <?php foreach ($roomsData as $room): ?>
                                <div class="border-t border-gray-200 pt-8 first:border-t-0 first:pt-0">
                                    <!-- Room Header -->
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($room['room']); ?></h3>
                                            <span class="text-sm font-medium text-gray-600">
                                                <?php echo ucfirst($room['gender']); ?> • <?php echo $room['no_of_bed']; ?> Beds
                                            </span>
                                        </div>
                                        <div class="flex gap-2">
                                            <button onclick="openEditRoomModal(<?php echo $room['room_id']; ?>, '<?php echo htmlspecialchars($room['room'], ENT_QUOTES); ?>')" 
                                                class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded text-sm flex items-center gap-2">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button onclick="deleteRoom(<?php echo $room['room_id']; ?>)" 
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded text-sm flex items-center gap-2">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Bed Tiles Grid -->
                                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4 mb-4">
                                        <?php foreach ($room['beds'] as $bed): ?>
                                            <?php $statusInfo = getStatusColor($bed['status']); ?>
                                            <div class="flex flex-col items-center group">
                                                <!-- Bed Tile -->
                                                <div class="w-14 h-14 sm:w-16 sm:h-16 <?php echo $statusInfo['color']; ?> rounded flex items-center justify-center cursor-pointer hover:shadow-lg transition-all relative mb-2 border-2 border-white">
                                                    <!-- Bed Icon -->
                                                    <div class="text-white text-xl sm:text-2xl">
                                                        <i class="fas fa-bed"></i>
                                                    </div>
                                                    
                                                    <!-- Hover Action Buttons -->
                                                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <button onclick="openEditBedModal(<?php echo $bed['id']; ?>, '<?php echo htmlspecialchars($bed['bed_name'], ENT_QUOTES); ?>')" 
                                                            class="bg-blue-500 hover:bg-blue-600 text-white p-1.5 rounded text-xs" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button onclick="deleteBed(<?php echo $bed['id']; ?>)" 
                                                            class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded text-xs" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                    
                                                    <!-- Hover Tooltip -->
                                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                                                        <?php echo htmlspecialchars($bed['bed_name']); ?>
                                                    </div>
                                                </div>
                                                <!-- Bed Label -->
                                                <p class="text-xs font-medium text-gray-700 text-center">
                                                    <?php echo htmlspecialchars($bed['bed_name']); ?>
                                                </p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Add Bed Button -->
                                    <button onclick="openAddBedModal(<?php echo $room['room_id']; ?>)" 
                                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm flex items-center gap-2">
                                        <i class="fas fa-plus"></i> Add Bed
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Legend -->
                        <div class="border-t border-gray-200 mt-8 pt-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-4">Status Legend</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-green-500 rounded"></div>
                                    <span class="text-sm text-gray-600">Vacant</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-red-500 rounded"></div>
                                    <span class="text-sm text-gray-600">Occupied</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-yellow-500 rounded"></div>
                                    <span class="text-sm text-gray-600">Block</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-orange-500 rounded"></div>
                                    <span class="text-sm text-gray-600">Checking out</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif ($selectedLocationId): ?>
                    <!-- No Rooms Message -->
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                        <i class="fas fa-inbox text-gray-400 text-5xl mb-4"></i>
                        <p class="text-lg text-gray-600 mb-4">No rooms found for this location</p>
                        <a href="./create_room.php" class="inline-block bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 font-medium">
                            <i class="fas fa-plus mr-2"></i> Create Room
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Select Location Message -->
                    <div class="bg-white rounded-lg shadow p-12 text-center">
                        <i class="fas fa-map-marker-alt text-gray-400 text-5xl mb-4"></i>
                        <p class="text-lg text-gray-600">Please select a location to view rooms and beds</p>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php include './includes/scripts.php'; ?>

    <!-- Edit Room Modal -->
    <div id="editRoomModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Room Name</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update_room">
                <input type="hidden" name="room_id" id="editRoomId">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Room Name</label>
                    <input type="text" id="editRoomName" name="room_name" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-medium">
                        Save
                    </button>
                    <button type="button" onclick="closeEditRoomModal()" class="flex-1 bg-gray-400 text-white py-2 rounded-lg hover:bg-gray-500 font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Bed Modal -->
    <div id="editBedModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Bed Name</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update_bed">
                <input type="hidden" name="bed_id" id="editBedId">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bed Name</label>
                    <input type="text" id="editBedName" name="bed_name" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 font-medium">
                        Save
                    </button>
                    <button type="button" onclick="closeEditBedModal()" class="flex-1 bg-gray-400 text-white py-2 rounded-lg hover:bg-gray-500 font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Bed Modal -->
    <div id="addBedModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-96">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Add New Bed</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="add_bed">
                <input type="hidden" name="room_id" id="addBedRoomId">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Bed Name</label>
                    <input type="text" id="addBedName" name="bed_name" placeholder="e.g., Bed A, Window Side" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-medium">
                        Add Bed
                    </button>
                    <button type="button" onclick="closeAddBedModal()" class="flex-1 bg-gray-400 text-white py-2 rounded-lg hover:bg-gray-500 font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Edit Room Modal Functions
        function openEditRoomModal(roomId, roomName) {
            document.getElementById('editRoomId').value = roomId;
            document.getElementById('editRoomName').value = roomName;
            document.getElementById('editRoomModal').classList.remove('hidden');
        }

        function closeEditRoomModal() {
            document.getElementById('editRoomModal').classList.add('hidden');
        }

        // Edit Bed Modal Functions
        function openEditBedModal(bedId, bedName) {
            document.getElementById('editBedId').value = bedId;
            document.getElementById('editBedName').value = bedName;
            document.getElementById('editBedModal').classList.remove('hidden');
        }

        function closeEditBedModal() {
            document.getElementById('editBedModal').classList.add('hidden');
        }

        // Add Bed Modal Functions
        function openAddBedModal(roomId) {
            document.getElementById('addBedRoomId').value = roomId;
            document.getElementById('addBedName').value = '';
            document.getElementById('addBedModal').classList.remove('hidden');
        }

        function closeAddBedModal() {
            document.getElementById('addBedModal').classList.add('hidden');
        }

        // Delete Room Function
        function deleteRoom(roomId) {
            if (confirm('Are you sure you want to delete this room and all its beds?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_room">
                    <input type="hidden" name="room_id" value="${roomId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Delete Bed Function
        function deleteBed(bedId) {
            if (confirm('Are you sure you want to delete this bed?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_bed">
                    <input type="hidden" name="bed_id" value="${bedId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function filterByLocation() {
            const locationId = document.getElementById('locationSelect').value;
            if (locationId) {
                window.location.href = `?location_id=${locationId}`;
            } else {
                window.location.href = '?';
            }
        }
    </script>
</body>

</html>
