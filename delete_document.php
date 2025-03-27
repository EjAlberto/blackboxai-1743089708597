<?php
require_once 'includes/db_connection.php';

if (isset($_GET['id'])) {
    $documentId = (int)$_GET['id'];
    
    try {
        // Get document info first
        $stmt = $pdo->prepare("SELECT file_path, student_id FROM documents WHERE id = ?");
        $stmt->execute([$documentId]);
        $document = $stmt->fetch();
        
        if ($document) {
            // Delete the file
            if (file_exists($document['file_path'])) {
                unlink($document['file_path']);
            }
            
            // Delete the database record
            $pdo->prepare("DELETE FROM documents WHERE id = ?")->execute([$documentId]);
            
            header("Location: documents.php?student_id={$document['student_id']}&success=Document deleted successfully");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: documents.php?student_id={$document['student_id']}&error=Error deleting document");
        exit();
    }
}

header("Location: documents.php");
exit();