<?php
require_once 'includes/db_connection.php';

if (isset($_GET['id'])) {
    $studentId = (int)$_GET['id'];
    
    try {
        // First get grade level for redirect
        $stmt = $pdo->prepare("SELECT grade_level FROM students WHERE id = ?");
        $stmt->execute([$studentId]);
        $student = $stmt->fetch();
        
        if ($student) {
            // Delete student's documents first
            $pdo->prepare("DELETE FROM documents WHERE student_id = ?")->execute([$studentId]);
            
            // Then delete the student
            $pdo->prepare("DELETE FROM students WHERE id = ?")->execute([$studentId]);
            
            header("Location: students.php?grade={$student['grade_level']}&success=Student deleted successfully");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: students.php?error=Error deleting student");
        exit();
    }
}

header("Location: students.php");
exit();