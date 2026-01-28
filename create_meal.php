<?php
require_once 'config/database.php';

$message = '';
$messageType = '';

// Fetch all locations for dropdown
$stmt = $pdo->query("SELECT * FROM locations ORDER BY name ASC");
$locations = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle bulk meal insertion
    if (isset($_POST['meals']) && is_array($_POST['meals']) && count($_POST['meals']) > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO meals (meal_type, price, location_id) VALUES (?, ?, ?)");
            $insertedCount = 0;
            
            foreach ($_POST['meals'] as $meal) {
                $meal_type = trim($meal['meal_type'] ?? '');
                $price = (float)($meal['price'] ?? 0);
                $location_id = (int)($meal['location_id'] ?? 0);
                
                if (!empty($meal_type) && $price > 0 && $location_id > 0) {
                    $stmt->execute([$meal_type, $price, $location_id]);
                    $insertedCount++;
                }
            }
            
            if ($insertedCount > 0) {
                $message = "Successfully created $insertedCount meal(s)!";
                $messageType = "success";
            } else {
                $message = "No valid meals to insert.";
                $messageType = "error";
            }
        } catch (PDOException $e) {
            $message = "Error creating meals: " . $e->getMessage();
            $messageType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Meal - RRMS Admin Dashboard</title>
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
                <div class="w-full max-w-6xl mx-auto">
                    <!-- Breadcrumb -->
                    <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm text-gray-600 overflow-x-auto">
                        <a href="index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                        <i class="fas fa-chevron-right"></i>
                        <a href="view_meal.php" class="text-blue-600 hover:text-blue-800">Meal</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">Create Meal</span>
                    </div>

                    <!-- Alert Message -->
                    <?php if ($message): ?>
                        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-red-100 text-red-700 border border-red-400'; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Form Card -->
                    <div class="bg-white rounded-lg shadow p-6 sm:p-8">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Create New Meal</h1>
                        <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">Add multiple meals at once</p>

                        <form method="POST" class="space-y-6">
                            <!-- Input Fields -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Meal Type -->
                                <div>
                                    <label for="meal_type" class="block text-sm font-semibold text-gray-700 mb-3">Meal Type <span class="text-red-600">*</span></label>
                                    <select id="meal_type"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Select Meal Type</option>
                                        <option value="Breakfast">Breakfast</option>
                                        <option value="Lunch">Lunch</option>
                                        <option value="Dinner">Dinner</option>
                                        <option value="Parcel">Parcel</option>
                                    </select>
                                </div>

                                <!-- Location -->
                                <div>
                                    <label for="location_id" class="block text-sm font-semibold text-gray-700 mb-3">Location <span class="text-red-600">*</span></label>
                                    <select id="location_id"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Select Location</option>
                                        <?php foreach ($locations as $location): ?>
                                            <option value="<?php echo $location['id']; ?>"><?php echo htmlspecialchars($location['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Price -->
                                <div>
                                    <label for="price" class="block text-sm font-semibold text-gray-700 mb-3">Price (₹) <span class="text-red-600">*</span></label>
                                    <input type="number" id="price" step="0.01" min="0" placeholder="Enter price"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <!-- Add Row Button -->
                            <div class="flex justify-end">
                                <button type="button" id="addRowBtn"
                                    class="bg-green-600 text-white py-2.5 px-6 rounded-lg hover:bg-green-700 font-medium transition-colors flex items-center gap-2">
                                    <i class="fas fa-plus"></i> Add Row
                                </button>
                            </div>

                            <!-- Meals Table -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-4">Added Meals</label>
                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse">
                                        <thead>
                                            <tr class="bg-gray-50 border-b border-gray-300">
                                                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Meal Type</th>
                                                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Location</th>
                                                <th class="px-4 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700">Price (₹)</th>
                                                <th class="px-4 py-3 text-center text-xs sm:text-sm font-semibold text-gray-700">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="mealsTableBody">
                                            <!-- Rows will be added here -->
                                        </tbody>
                                    </table>
                                    <div id="emptyMessage" class="text-center py-8 text-gray-500">
                                        <p class="text-sm">No meals added yet. Click "Add Row" to add meals.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex gap-4 pt-8 border-t border-gray-200">
                                <button type="submit"
                                    class="flex-1 bg-blue-600 text-white py-2.5 px-6 rounded-lg hover:bg-blue-700 font-medium transition-colors">
                                    <i class="fas fa-save mr-2"></i> Submit All Meals
                                </button>
                                <a href="view_meal.php"
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

    <script>
        let mealRowCount = 0;
        const locationOptions = <?php echo json_encode(array_map(function($l) { return ['id' => $l['id'], 'name' => $l['name']]; }, $locations)); ?>;

        document.getElementById('addRowBtn').addEventListener('click', function(e) {
            e.preventDefault();
            const mealType = document.getElementById('meal_type').value;
            const locationId = document.getElementById('location_id').value;
            const locationName = document.getElementById('location_id').options[document.getElementById('location_id').selectedIndex].text;
            const price = document.getElementById('price').value;

            if (!mealType || !locationId || !price) {
                alert('Please fill all fields before adding a row');
                return;
            }

            addMealRow(mealType, locationId, locationName, price);
            document.getElementById('meal_type').value = '';
            document.getElementById('location_id').value = '';
            document.getElementById('price').value = '';
        });

        function addMealRow(mealType, locationId, locationName, price) {
            const tableBody = document.getElementById('mealsTableBody');
            const emptyMessage = document.getElementById('emptyMessage');

            if (emptyMessage && emptyMessage.style.display !== 'none') {
                emptyMessage.style.display = 'none';
            }

            const row = document.createElement('tr');
            row.id = 'meal-row-' + mealRowCount;
            row.classList.add('border-b', 'border-gray-200', 'hover:bg-gray-50');
            row.innerHTML = `
                <td class="px-4 py-3 text-sm text-gray-700">${mealType}</td>
                <td class="px-4 py-3 text-sm text-gray-700">${locationName}</td>
                <td class="px-4 py-3 text-sm text-gray-700">₹${parseFloat(price).toFixed(2)}</td>
                <td class="px-4 py-3 text-center">
                    <button type="button" onclick="deleteMealRow(${mealRowCount})" class="text-red-600 hover:text-red-800 text-sm font-medium">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                </td>
                <input type="hidden" name="meals[${mealRowCount}][meal_type]" value="${mealType}">
                <input type="hidden" name="meals[${mealRowCount}][location_id]" value="${locationId}">
                <input type="hidden" name="meals[${mealRowCount}][price]" value="${price}">
            `;
            tableBody.appendChild(row);
            mealRowCount++;
        }

        function deleteMealRow(id) {
            const row = document.getElementById('meal-row-' + id);
            if (row) row.remove();
            
            const tableBody = document.getElementById('mealsTableBody');
            if (tableBody.children.length === 0) {
                document.getElementById('emptyMessage').style.display = 'block';
            }
        }

        document.querySelector('form').addEventListener('submit', function(e) {
            const tableBody = document.getElementById('mealsTableBody');
            if (tableBody.children.length === 0) {
                e.preventDefault();
                alert('Please add at least one meal before submitting');
            }
        });
    </script>
</body>

</html>
                if (emptyMessage) {
                    emptyMessage.style.display = 'block';
                }
            }
        }

        // Form Submit Handler
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();

            const tableBody = document.getElementById('mealsTableBody');
            if (tableBody.children.length === 0) {
                alert('Please add at least one meal before submitting');
                return;
            }

            // Collect all meal data
            const mealsData = [];
            const rows = tableBody.querySelectorAll('tr');
            
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                mealsData.push({
                    mealType: cells[0].textContent,
                    location: cells[1].textContent,
                    price: cells[2].textContent.replace('₹', '').trim()
                });
            });

            console.log('Meals Data:', mealsData);
            
            // Here you would send the data to your backend
            alert('Meals submitted successfully!\n\nTotal meals: ' + mealsData.length + '\n\nYou can now implement backend processing.');

            // Reset form
            document.getElementById('mealsTableBody').innerHTML = '';
            document.getElementById('emptyMessage').style.display = 'block';
        });

        // Allow Enter key to add row
        document.getElementById('price').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('addRowBtn').click();
            }
        });
    </script>
</body>

</html>
