<?php
require_once 'includes/db_connection.php';
require_once 'includes/header.php';

// Initialize variables
$student = [
    'id' => '',
    'first_name' => '',
    'last_name' => '',
    'grade_level' => 11,
    'strand_id' => '',
    'section_id' => '',
    'school_year_id' => '',
    'birthdate' => '',
    'address' => '',
    'contact_number' => '',
    'email' => ''
];

$isEdit = false;

// Check if editing existing student
if (isset($_GET['id'])) {
    $isEdit = true;
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$student) {
        header("Location: students.php?error=Student not found");
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $student = [
        'id' => $_POST['id'] ?? '',
        'first_name' => trim($_POST['first_name']),
        'last_name' => trim($_POST['last_name']),
        'grade_level' => (int)$_POST['grade_level'],
        'strand_id' => $_POST['strand_id'] ? (int)$_POST['strand_id'] : null,
        'section_id' => $_POST['section_id'] ? (int)$_POST['section_id'] : null,
        'school_year_id' => $_POST['school_year_id'] ? (int)$_POST['school_year_id'] : null,
        'birthdate' => $_POST['birthdate'],
        'address' => trim($_POST['address']),
        'contact_number' => trim($_POST['contact_number']),
        'email' => trim($_POST['email'])
    ];

    // Validate required fields
    $errors = [];
    if (empty($student['first_name'])) $errors[] = 'First name is required';
    if (empty($student['last_name'])) $errors[] = 'Last name is required';
    if (empty($student['grade_level'])) $errors[] = 'Grade level is required';

    if (empty($errors)) {
        try {
            if ($isEdit) {
                // Update existing student
                $sql = "UPDATE students SET 
                        first_name = :first_name,
                        last_name = :last_name,
                        grade_level = :grade_level,
                        strand_id = :strand_id,
                        section_id = :section_id,
                        school_year_id = :school_year_id,
                        birthdate = :birthdate,
                        address = :address,
                        contact_number = :contact_number,
                        email = :email
                        WHERE id = :id";
            } else {
                // Insert new student
                $sql = "INSERT INTO students (
                        first_name, last_name, grade_level, strand_id, 
                        section_id, school_year_id, birthdate, address, 
                        contact_number, email
                    ) VALUES (
                        :first_name, :last_name, :grade_level, :strand_id,
                        :section_id, :school_year_id, :birthdate, :address,
                        :contact_number, :email
                    )";
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($student);

            $redirectUrl = $isEdit 
                ? "students.php?grade={$student['grade_level']}&success=Student updated successfully"
                : "students.php?grade={$student['grade_level']}&success=Student added successfully";
            
            header("Location: $redirectUrl");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Get options for dropdowns
$strands = $pdo->query("SELECT * FROM strands")->fetchAll();
$sections = $pdo->query("SELECT * FROM sections WHERE grade_level = {$student['grade_level']}")->fetchAll();
$schoolYears = $pdo->query("SELECT * FROM school_years ORDER BY year_start DESC")->fetchAll();
?>

<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-semibold mb-6">
        <?= $isEdit ? 'Edit Student' : 'Add New Student' ?>
    </h2>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="id" value="<?= $student['id'] ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" 
                       class="w-full border rounded p-2" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars($student['last_name']) ?>" 
                       class="w-full border rounded p-2" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level *</label>
                <select name="grade_level" class="w-full border rounded p-2" required
                        onchange="updateSections(this.value)">
                    <option value="11" <?= $student['grade_level'] == 11 ? 'selected' : '' ?>>Grade 11</option>
                    <option value="12" <?= $student['grade_level'] == 12 ? 'selected' : '' ?>>Grade 12</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Strand</label>
                <select name="strand_id" class="w-full border rounded p-2">
                    <option value="">Select Strand</option>
                    <?php foreach ($strands as $strand): ?>
                    <option value="<?= $strand['id'] ?>" 
                        <?= $student['strand_id'] == $strand['id'] ? 'selected' : '' ?>>
                        <?= $strand['name'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                <select name="section_id" class="w-full border rounded p-2" id="section-select">
                    <option value="">Select Section</option>
                    <?php foreach ($sections as $section): ?>
                    <option value="<?= $section['id'] ?>" 
                        <?= $student['section_id'] == $section['id'] ? 'selected' : '' ?>>
                        <?= $section['name'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">School Year</label>
                <select name="school_year_id" class="w-full border rounded p-2">
                    <option value="">Select School Year</option>
                    <?php foreach ($schoolYears as $year): ?>
                    <option value="<?= $year['id'] ?>" 
                        <?= $student['school_year_id'] == $year['id'] ? 'selected' : '' ?>>
                        <?= $year['year_start'] ?>-<?= $year['year_end'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Birthdate</label>
                <input type="date" name="birthdate" value="<?= $student['birthdate'] ?>" 
                       class="w-full border rounded p-2">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                <input type="tel" name="contact_number" value="<?= htmlspecialchars($student['contact_number']) ?>" 
                       class="w-full border rounded p-2">
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
            <textarea name="address" class="w-full border rounded p-2"><?= htmlspecialchars($student['address']) ?></textarea>
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" 
                   class="w-full border rounded p-2">
        </div>
        
        <div class="flex justify-end">
            <a href="students.php?grade=<?= $student['grade_level'] ?>" 
               class="bg-gray-300 text-gray-800 px-4 py-2 rounded mr-3 hover:bg-gray-400 transition">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                <?= $isEdit ? 'Update Student' : 'Add Student' ?>
            </button>
        </div>
    </form>
</div>

<script>
function updateSections(gradeLevel) {
    fetch(`get_sections.php?grade_level=${gradeLevel}`)
        .then(response => response.json())
        .then(sections => {
            const select = document.getElementById('section-select');
            select.innerHTML = '<option value="">Select Section</option>';
            
            sections.forEach(section => {
                const option = document.createElement('option');
                option.value = section.id;
                option.textContent = section.name;
                select.appendChild(option);
            });
        });
}
</script>

<?php require_once 'includes/footer.php'; ?>