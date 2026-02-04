<?php
require_once './config/database.php';

$locations = [];
$divisions = [];
$successMessage = '';
$errorMessage = '';

// Fetch all locations
try {
    $stmt = $pdo->query("SELECT id, name FROM locations ORDER BY name ASC");
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $errorMessage = "Error fetching locations: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $locationId = $_POST['location_id'] ?? null;
    $roomsData = $_POST['rooms'] ?? [];
    
    // Get current user ID
    $userId = $_SESSION['user_id'] ?? 1;
    
    // Get division_id from locations table
    $divisionStmt = $pdo->prepare("SELECT division_id FROM locations WHERE id = :location_id");
    $divisionStmt->execute([':location_id' => $locationId]);
    $locationData = $divisionStmt->fetch(PDO::FETCH_ASSOC);
    $divisionId = $locationData['division_id'] ?? null;

    if (!$locationId || empty($roomsData)) {
        $errorMessage = "Please select a location and add at least one room.";
    } else {
        try {
            $pdo->beginTransaction();
            $roomsCreated = 0;
            $bedsCreated = 0;

            // Insert multiple rooms and beds
            foreach ($roomsData as $roomData) {
                $roomName = $roomData['name'] ?? null;
                $gender = $roomData['gender'] ?? null;
                $numberOfBeds = $roomData['no_of_bed'] ?? 0;
                $bedNames = $roomData['bed_names'] ?? [];

                if (!$roomName || !$gender || $numberOfBeds < 1) {
                    continue; // Skip invalid rooms
                }

                // Insert room
                $stmt = $pdo->prepare("INSERT INTO rooms (user_id, location_id, division_id, room, gender, no_of_bed, status, created_at, updated_at) 
                                       VALUES (:user_id, :location_id, :division_id, :room, :gender, :no_of_bed, 2, NOW(), NOW())");
                $stmt->execute([
                    ':user_id' => $userId,
                    ':location_id' => $locationId,
                    ':division_id' => $divisionId,
                    ':room' => $roomName,
                    ':gender' => $gender,
                    ':no_of_bed' => $numberOfBeds
                ]);

                $roomId = $pdo->lastInsertId();
                $roomsCreated++;

                // Insert beds for this room
                $bedStmt = $pdo->prepare("INSERT INTO beds (room_id, division_id, location_id, gender, bed_name, status, created_at, updated_at) 
                                          VALUES (:room_id, :division_id, :location_id, :gender, :bed_name, 0, NOW(), NOW())");

                for ($i = 0; $i < $numberOfBeds; $i++) {
                    $bedName = $bedNames[$i] ?? "Bed " . ($i + 1);
                    $bedStmt->execute([
                        ':room_id' => $roomId,
                        ':division_id' => $divisionId,
                        ':location_id' => $locationId,
                        ':gender' => $gender,
                        ':bed_name' => $bedName
                    ]);
                    $bedsCreated++;
                }
            }

            $pdo->commit();
            $successMessage = "Successfully created $roomsCreated room(s) with $bedsCreated bed(s)!";
            // Reset form
            $_POST = [];
        } catch (Exception $e) {
            $pdo->rollBack();
            $errorMessage = "Error creating rooms: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Room - RRMS Admin Dashboard</title>
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
            <main class="flex-1 overflow-auto p-3 sm:p-4 md:p-8 flex items-center justify-center">
                <div class="w-full max-w-2xl">
                    <!-- Breadcrumb -->
                    <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm text-gray-600 overflow-x-auto">
                        <a href="./index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                        <i class="fas fa-chevron-right"></i>
                        <a href="./view_rooms.php" class="text-blue-600 hover:text-blue-800">Room</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">Create Room</span>
                    </div>

                    <!-- Form Card -->
                    <div class="bg-white rounded-lg shadow p-6 sm:p-8">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Create New Room</h1>
                        <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">Add a new room with beds to a location</p>

                        <!-- Success Message -->
                        <?php if ($successMessage): ?>
                            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                                    <p class="text-green-800"><?php echo $successMessage; ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Error Message -->
                        <?php if ($errorMessage): ?>
                            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                                    <p class="text-red-800"><?php echo $errorMessage; ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="space-y-6">
                            <!-- Step 1: Location Selection -->
                            <div id="step1" class="space-y-6">
                                <div>
                                    <label for="location_id" class="block text-sm font-semibold text-gray-700 mb-3">
                                        Select Location <span class="text-red-600">*</span>
                                    </label>
                                    <select id="location_id" name="location_id"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">-- Select Location --</option>
                                        <?php foreach ($locations as $location): ?>
                                            <option value="<?php echo $location['id']; ?>" <?php echo (isset($_POST['location_id']) && $_POST['location_id'] == $location['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($location['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Step 2: Multiple Rooms -->
                            <div id="step2" class="space-y-6" style="display: none;">
                                <!-- Location Display -->
                                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-sm text-blue-800"><strong>Selected Location:</strong> <span id="selectedLocation">-</span></p>
                                </div>

                                <!-- Rooms Container -->
                                <div id="roomsContainer" class="space-y-6">
                                    <!-- Room entries will be added here dynamically -->
                                </div>

                                <!-- Add Room Button -->
                                <button type="button" onclick="addRoomEntry()" 
                                    class="w-full bg-blue-600 text-white py-2.5 px-6 rounded-lg hover:bg-blue-700 font-medium transition-colors">
                                    <i class="fas fa-plus mr-2"></i> Add Another Room
                                </button>
                            </div>

                            <!-- Buttons -->
                            <div class="flex gap-4 pt-8">
                                <button type="button" id="proceedBtn" onclick="proceedToStep2()"
                                    class="flex-1 bg-blue-600 text-white py-2.5 px-6 rounded-lg hover:bg-blue-700 font-medium transition-colors">
                                    <i class="fas fa-arrow-right mr-2"></i> Proceed
                                </button>
                                <a href="./view_rooms.php"
                                    class="flex-1 bg-gray-700 text-white py-2.5 px-6 rounded-lg hover:bg-gray-800 font-medium transition-colors text-center">
                                    <i class="fas fa-arrow-left mr-2"></i> Back
                                </a>
                            </div>

                            <!-- Submit Button (shown in step 2) -->
                            <div id="submitContainer" style="display: none;" class="flex gap-4 pt-8">
                                <button type="submit"
                                    class="flex-1 bg-green-600 text-white py-2.5 px-6 rounded-lg hover:bg-green-700 font-medium transition-colors">
                                    <i class="fas fa-save mr-2"></i> Create All Rooms & Beds
                                </button>
                                <button type="button" onclick="goBackToStep1()"
                                    class="flex-1 bg-gray-500 text-white py-2.5 px-6 rounded-lg hover:bg-gray-600 font-medium transition-colors">
                                    <i class="fas fa-arrow-left mr-2"></i> Back
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include './includes/scripts.php'; ?>

    <script>
        let roomCounter = 0;

        function proceedToStep2() {
            const locationId = document.getElementById('location_id').value;
            if (!locationId) {
                alert('Please select a location');
                return;
            }
            
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
            document.getElementById('proceedBtn').style.display = 'none';
            document.getElementById('submitContainer').style.display = 'flex';
            
            // Set selected location display
            const locationSelect = document.getElementById('location_id');
            const selectedOption = locationSelect.options[locationSelect.selectedIndex];
            document.getElementById('selectedLocation').textContent = selectedOption.text;
            
            // Add first room entry if container is empty
            if (document.getElementById('roomsContainer').children.length === 0) {
                addRoomEntry();
            }
        }

        function goBackToStep1() {
            document.getElementById('step2').style.display = 'none';
            document.getElementById('step1').style.display = 'block';
            document.getElementById('proceedBtn').style.display = 'block';
            document.getElementById('submitContainer').style.display = 'none';
        }

        // Add a new room entry
        function addRoomEntry() {
            const container = document.getElementById('roomsContainer');
            const roomIndex = roomCounter++;
            
            const roomBlock = document.createElement('div');
            roomBlock.id = `room-${roomIndex}`;
            roomBlock.className = 'p-6 bg-gray-50 border border-gray-300 rounded-lg space-y-4';
            
            roomBlock.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Room #${container.children.length + 1}</h3>
                    ${container.children.length > 0 ? `<button type="button" onclick="removeRoomEntry(${roomIndex})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i> Remove</button>` : ''}
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Room Name <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="rooms[${roomIndex}][name]" placeholder="e.g., Vande Bharat, Mahila Karmidal" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Gender <span class="text-red-600">*</span>
                    </label>
                    <select name="rooms[${roomIndex}][gender]" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select Gender --</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Number of Beds <span class="text-red-600">*</span>
                    </label>
                    <input type="number" name="rooms[${roomIndex}][no_of_bed]" min="1" max="20" placeholder="e.g., 4" required
                        oninput="generateRoomBedInputs(${roomIndex})"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div id="bedInputs-${roomIndex}" class="space-y-3 mt-4 pt-4 border-t border-gray-300">
                    <!-- Bed inputs will be generated here -->
                </div>
            `;
            
            container.appendChild(roomBlock);
        }

        // Generate bed inputs for a specific room
        function generateRoomBedInputs(roomIndex) {
            const numBeds = document.querySelector(`input[name="rooms[${roomIndex}][no_of_bed]"]`).value;
            const bedContainer = document.getElementById(`bedInputs-${roomIndex}`);
            
            if (!numBeds || numBeds < 1) {
                bedContainer.innerHTML = '';
                return;
            }
            
            let html = '<p class="text-sm font-semibold text-gray-700 mb-3">Enter Bed Names:</p>';
            for (let i = 0; i < parseInt(numBeds); i++) {
                html += `
                    <input type="text" name="rooms[${roomIndex}][bed_names][]" 
                        placeholder="Bed ${i + 1} name (e.g., Bed A, Window Side)" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                `;
            }
            bedContainer.innerHTML = html;
        }

        // Remove a room entry
        function removeRoomEntry(roomIndex) {
            const roomBlock = document.getElementById(`room-${roomIndex}`);
            if (roomBlock) {
                roomBlock.remove();
                updateRoomNumbers();
            }
        }

        // Update room numbers after removal
        function updateRoomNumbers() {
            const rooms = document.querySelectorAll('[id^="room-"]');
            rooms.forEach((room, index) => {
                const heading = room.querySelector('h3');
                if (heading) {
                    heading.textContent = `Room #${index + 1}`;
                }
                // Hide remove button for first room if only one room
                const removeBtn = room.querySelector('button[onclick*="removeRoomEntry"]');
                if (removeBtn) {
                    removeBtn.style.display = rooms.length > 1 ? 'block' : 'none';
                }
            });
        }

        // Initialize if location is pre-selected
        window.addEventListener('load', function() {
            const locationId = document.getElementById('location_id').value;
            if (locationId) {
                proceedToStep2();
            }
        });
    </script>
</body>

</html>
