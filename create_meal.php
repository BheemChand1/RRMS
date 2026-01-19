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
            <main class="flex-1 overflow-auto p-3 sm:p-4 md:p-8 flex items-center justify-center">
                <div class="w-full max-w-6xl">
                    <!-- Breadcrumb -->
                    <div class="mb-6 flex items-center gap-2 text-xs sm:text-sm text-gray-600 overflow-x-auto">
                        <a href="index.php" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                        <i class="fas fa-chevron-right"></i>
                        <a href="#" class="text-blue-600 hover:text-blue-800">Meal</a>
                        <i class="fas fa-chevron-right"></i>
                        <span class="text-gray-900 font-medium">Create Meal</span>
                    </div>

                    <!-- Form Card -->
                    <div class="bg-white rounded-lg shadow p-6 sm:p-8">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">Create New Meal</h1>
                        <p class="text-sm sm:text-base text-gray-600 mb-6 sm:mb-8">Add meal options with pricing for different locations</p>

                        <form class="space-y-6">
                            <!-- Meal Type -->
                            <div>
                                <label for="mealType" class="block text-sm font-semibold text-gray-700 mb-3">Meal Type <span class="text-red-500">*</span></label>
                                <select id="mealType"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none text-gray-700"
                                    required>
                                    <option value="">Select Meal Type</option>
                                    <option value="Breakfast">Breakfast</option>
                                    <option value="Lunch">Lunch</option>
                                    <option value="Dinner">Dinner</option>
                                    <option value="Parcel">Parcel</option>
                                </select>
                            </div>

                            <!-- Location -->
                            <div>
                                <label for="location" class="block text-sm font-semibold text-gray-700 mb-3">Location <span class="text-red-500">*</span></label>
                                <select id="location"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none text-gray-700"
                                    required>
                                    <option value="">Select Location</option>
                                    <option value="Ahmedabad">Ahmedabad</option>
                                    <option value="Bangalore">Bangalore</option>
                                    <option value="Mumbai">Mumbai</option>
                                    <option value="Delhi">Delhi</option>
                                    <option value="Pune">Pune</option>
                                </select>
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-semibold text-gray-700 mb-3">Price (₹) <span class="text-red-500">*</span></label>
                                <input type="number" id="price" step="0.01" min="0"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none text-gray-700"
                                    placeholder="Enter price"
                                    required>
                            </div>

                            <!-- Meals Table -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <label class="block text-sm font-semibold text-gray-700">Meal Details</label>
                                    <button type="button" id="addRowBtn"
                                        class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 font-medium transition-colors text-sm flex items-center gap-2">
                                        <i class="fas fa-plus"></i> Add Row
                                    </button>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse">
                                        <thead>
                                            <tr class="bg-gray-50 border-b border-gray-300">
                                                <th class="px-3 sm:px-4 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 w-1/4">Meal Type</th>
                                                <th class="px-3 sm:px-4 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 w-1/4">Location</th>
                                                <th class="px-3 sm:px-4 py-3 text-left text-xs sm:text-sm font-semibold text-gray-700 w-1/4">Price (₹)</th>
                                                <th class="px-3 sm:px-4 py-3 text-center text-xs sm:text-sm font-semibold text-gray-700 w-1/4">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="mealsTableBody">
                                            <!-- Rows will be added here dynamically -->
                                        </tbody>
                                    </table>
                                    <div id="emptyMessage" class="text-center py-8 text-gray-500">
                                        <p class="text-sm">No meals added yet. Click "Add Row" to add meals.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex gap-4 pt-6 border-t border-gray-200">
                                <button type="submit" id="submitBtn"
                                    class="bg-blue-600 text-white py-2 sm:py-3 px-6 rounded-lg hover:bg-blue-700 font-medium transition-colors text-sm sm:text-base">
                                    <i class="fas fa-save mr-2"></i> Submit Meals
                                </button>
                                <a href="view_meal.php"
                                    class="bg-gray-300 text-gray-800 py-2 sm:py-3 px-6 rounded-lg hover:bg-gray-400 font-medium transition-colors text-sm sm:text-base text-center">
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
        let rowCount = 0;

        // Add Row Button Click Event
        document.getElementById('addRowBtn').addEventListener('click', function() {
            const mealType = document.getElementById('mealType').value;
            const location = document.getElementById('location').value;
            const price = document.getElementById('price').value;

            if (!mealType || !location || !price) {
                alert('Please fill all fields (Meal Type, Location, and Price) before adding a row');
                return;
            }

            addMealRow(mealType, location, price);
            // Clear input fields
            document.getElementById('mealType').value = '';
            document.getElementById('location').value = '';
            document.getElementById('price').value = '';
        });

        // Add Meal Row Function
        function addMealRow(mealType, location, price) {
            const tableBody = document.getElementById('mealsTableBody');
            const emptyMessage = document.getElementById('emptyMessage');

            // Hide empty message
            if (emptyMessage) {
                emptyMessage.style.display = 'none';
            }

            const row = document.createElement('tr');
            row.id = 'row-' + rowCount;
            row.classList.add('border-b', 'border-gray-200', 'hover:bg-gray-50');
            row.innerHTML = `
                <td class="px-3 sm:px-4 py-3 text-sm text-gray-700">${mealType}</td>
                <td class="px-3 sm:px-4 py-3 text-sm text-gray-700">${location}</td>
                <td class="px-3 sm:px-4 py-3 text-sm text-gray-700">₹${parseFloat(price).toFixed(2)}</td>
                <td class="px-3 sm:px-4 py-3 text-center">
                    <button type="button" class="text-red-600 hover:text-red-800 font-medium text-sm" onclick="deleteRow(${rowCount})">
                        <i class="fas fa-trash mr-1"></i> Delete
                    </button>
                </td>
            `;

            tableBody.appendChild(row);
            rowCount++;
        }

        // Delete Row Function
        function deleteRow(id) {
            const row = document.getElementById('row-' + id);
            if (row) {
                row.remove();
            }

            // Show empty message if no rows
            const tableBody = document.getElementById('mealsTableBody');
            if (tableBody.children.length === 0) {
                const emptyMessage = document.getElementById('emptyMessage');
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
