<?php
require_once 'includes/db_connection.php';
require_once 'includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $gradeLevel = (int)$_POST['grade_level'];
    
    if (!empty($name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO sections (name, grade_level) VALUES (?, ?)");
            $stmt->execute([$name, $gradeLevel]);
            $success = "Section added successfully!";
        } catch (PDOException $e) {
            $error = "Error adding section: " . $e->getMessage();
        }
    } else {
        $error = "Section name cannot be empty";
    }
}

// Handle section deletion
if (isset($_GET['delete'])) {
    $sectionId = (int)$_GET['delete'];
    try {
        // Check if section has students
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE section_id = ?");
        $stmt->execute([$sectionId]);
        $studentCount = $stmt->fetchColumn();
        
        if ($studentCount > 0) {
            $error = "Cannot delete section with assigned students";
        } else {
            $pdo->prepare("DELETE FROM sections WHERE id = ?")->execute([$sectionId]);
            $success = "Section deleted successfully!";
        }
    } catch (PDOException $e) {
        $error = "Error deleting section: " . $e->getMessage();
    }
}

// Get all sections
$sections = $pdo->query("
    SELECT s.id, s.name, s.grade_level, 
           COUNT(st.id) as student_count
    FROM sections s
    LEFT JOIN students st ON s.id = st.section_id
    GROUP BY s.id
    ORDER BY s.grade_level, s.name
")->fetchAll();
?>

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Manage Sections</h2>
    </div>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <!-- Add Section Form -->
    <div class="mb-8 p-4 border border-gray-200 rounded-lg">
        <h3 class="text-lg font-medium mb-4">Add New Section</h3>
        <form method="post">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section Name</label>
                    <input type="text" name="name" class="w-full border rounded p-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                    <select name="grade_level" class="w-full border rounded p-2" required>
                        <option value="11">Grade 11</option>
                        <option value="12">Grade 12</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Add Section
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Sections List -->
    <h3 class="text-lg font-medium mb-4">Existing Sections</h3>
    <?php if (count($sections) > 0): ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($sections as $section): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $section['name'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">Grade <?= $section['grade_level'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $section['student_count'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="sections.php?delete=<?= $section['id'] ?>" onclick="return confirmDelete()" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-gray-500">No sections found.</p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>