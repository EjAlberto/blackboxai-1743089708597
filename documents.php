<?php
require_once 'includes/db_connection.php';
require_once 'includes/header.php';

$studentId = isset($_GET['student_id']) ? (int)$_GET['student_id'] : null;

// Get student info if specific student is selected
$student = null;
if ($studentId) {
    $stmt = $pdo->prepare("
        SELECT s.id, s.first_name, s.last_name, 
               st.name as strand_name, sec.name as section_name
        FROM students s
        LEFT JOIN strands st ON s.strand_id = st.id
        LEFT JOIN sections sec ON s.section_id = sec.id
        WHERE s.id = ?
    ");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch();
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $studentId) {
    $documentType = $_POST['document_type'];
    $uploadDir = 'assets/uploads/';
    
    // Create upload directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($_FILES['document']['name']);
    $filePath = $uploadDir . uniqid() . '_' . $fileName;
    $fileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    // Validate file
    $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    if (!in_array($fileType, $allowedTypes)) {
        $error = "Only PDF, DOC, DOCX, JPG, JPEG, PNG files are allowed.";
    } elseif ($_FILES['document']['size'] > 5000000) {
        $error = "File size must be less than 5MB.";
    } elseif (move_uploaded_file($_FILES['document']['tmp_name'], $filePath)) {
        // Save to database
        $stmt = $pdo->prepare("
            INSERT INTO documents (student_id, document_type, file_path)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$studentId, $documentType, $filePath]);
        $success = "Document uploaded successfully!";
    } else {
        $error = "There was an error uploading your file.";
    }
}

// Get documents for selected student
$documents = [];
if ($studentId) {
    $stmt = $pdo->prepare("
        SELECT id, document_type, file_path, upload_date 
        FROM documents 
        WHERE student_id = ?
        ORDER BY upload_date DESC
    ");
    $stmt->execute([$studentId]);
    $documents = $stmt->fetchAll();
}
?>

<div class="bg-white rounded-lg shadow p-6">
    <?php if ($student): ?>
        <h2 class="text-xl font-semibold mb-4">
            Documents for <?= $student['first_name'] ?> <?= $student['last_name'] ?>
        </h2>
        <p class="text-gray-600 mb-6">
            <?= $student['strand_name'] ?> - <?= $student['section_name'] ?>
        </p>
    <?php else: ?>
        <h2 class="text-xl font-semibold mb-6">Document Management</h2>
        <p class="text-gray-600 mb-6">Select a student to view or upload documents</p>
    <?php endif; ?>

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

    <?php if ($studentId): ?>
        <!-- Upload Form -->
        <div class="mb-8 p-4 border border-gray-200 rounded-lg">
            <h3 class="text-lg font-medium mb-4">Upload New Document</h3>
            <form method="post" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Document Type</label>
                        <select name="document_type" class="w-full border rounded p-2" required>
                            <option value="">Select Type</option>
                            <option value="Report Card">Report Card</option>
                            <option value="Birth Certificate">Birth Certificate</option>
                            <option value="Good Moral">Good Moral Certificate</option>
                            <option value="Form 137">Form 137</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Document File</label>
                        <input type="file" name="document" class="w-full" required>
                    </div>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Upload Document
                </button>
            </form>
        </div>

        <!-- Documents List -->
        <h3 class="text-lg font-medium mb-4">Student Documents</h3>
        <?php if (count($documents) > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">File Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Uploaded</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $doc['document_type'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= basename($doc['file_path']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= date('M d, Y h:i A', strtotime($doc['upload_date'])) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="<?= $doc['file_path'] ?>" target="_blank" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="delete_document.php?id=<?= $doc['id'] ?>" onclick="return confirmDelete()" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-500">No documents found for this student.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>