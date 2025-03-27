<?php
require_once 'includes/db_connection.php';
require_once 'includes/header.php';

// Get query parameters
$grade = isset($_GET['grade']) ? (int)$_GET['grade'] : 11;
$strand = isset($_GET['strand']) ? (int)$_GET['strand'] : null;
$section = isset($_GET['section']) ? (int)$_GET['section'] : null;
$schoolYear = isset($_GET['school_year']) ? (int)$_GET['school_year'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build base query
$query = "SELECT s.id, s.first_name, s.last_name, s.grade_level, 
                 st.name as strand_name, sec.name as section_name,
                 CONCAT(sy.year_start, '-', sy.year_end) as school_year
          FROM students s
          LEFT JOIN strands st ON s.strand_id = st.id
          LEFT JOIN sections sec ON s.section_id = sec.id
          LEFT JOIN school_years sy ON s.school_year_id = sy.id
          WHERE s.grade_level = :grade_level";

$params = [':grade_level' => $grade];

// Add filters
if ($strand) {
    $query .= " AND s.strand_id = :strand_id";
    $params[':strand_id'] = $strand;
}

if ($section) {
    $query .= " AND s.section_id = :section_id";
    $params[':section_id'] = $section;
}

if ($schoolYear) {
    $query .= " AND s.school_year_id = :school_year_id";
    $params[':school_year_id'] = $schoolYear;
}

if ($search) {
    $query .= " AND (s.first_name LIKE :search OR s.last_name LIKE :search)";
    $params[':search'] = "%$search%";
}

// Get filter options
$strands = $pdo->query("SELECT * FROM strands")->fetchAll();
$sections = $pdo->query("SELECT * FROM sections WHERE grade_level = $grade")->fetchAll();
$schoolYears = $pdo->query("SELECT * FROM school_years ORDER BY year_start DESC")->fetchAll();

// Execute query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll();
?>

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Grade <?= $grade ?> Students</h2>
        <a href="student_form.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            <i class="fas fa-user-plus mr-2"></i> Add Student
        </a>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Strand</label>
            <select onchange="updateFilter('strand', this.value)" class="w-full border rounded p-2">
                <option value="">All Strands</option>
                <?php foreach ($strands as $s): ?>
                <option value="<?= $s['id'] ?>" <?= $strand == $s['id'] ? 'selected' : '' ?>>
                    <?= $s['name'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
            <select onchange="updateFilter('section', this.value)" class="w-full border rounded p-2">
                <option value="">All Sections</option>
                <?php foreach ($sections as $sec): ?>
                <option value="<?= $sec['id'] ?>" <?= $section == $sec['id'] ? 'selected' : '' ?>>
                    <?= $sec['name'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">School Year</label>
            <select onchange="updateFilter('school_year', this.value)" class="w-full border rounded p-2">
                <option value="">All Years</option>
                <?php foreach ($schoolYears as $sy): ?>
                <option value="<?= $sy['id'] ?>" <?= $schoolYear == $sy['id'] ? 'selected' : '' ?>>
                    <?= $sy['year_start'] ?>-<?= $sy['year_end'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <form method="get" class="flex">
                <input type="hidden" name="grade" value="<?= $grade ?>">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                       placeholder="Search students..." class="flex-1 border rounded-l p-2">
                <button type="submit" class="bg-blue-600 text-white px-4 rounded-r hover:bg-blue-700 transition">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Strand</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School Year</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (count($students) > 0): ?>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $student['id'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $student['first_name'] ?> <?= $student['last_name'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $student['strand_name'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $student['section_name'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= $student['school_year'] ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="student_form.php?id=<?= $student['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="documents.php?student_id=<?= $student['id'] ?>" class="text-green-600 hover:text-green-900 mr-3">
                                <i class="fas fa-file-alt"></i>
                            </a>
                            <a href="delete_student.php?id=<?= $student['id'] ?>" onclick="return confirmDelete()" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No students found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function updateFilter(param, value) {
    const url = new URL(window.location.href);
    if (value) {
        url.searchParams.set(param, value);
    } else {
        url.searchParams.delete(param);
    }
    window.location.href = url.toString();
}
</script>

<?php require_once 'includes/footer.php'; ?>