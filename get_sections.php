<?php
require_once 'includes/db_connection.php';

header('Content-Type: application/json');

$gradeLevel = isset($_GET['grade_level']) ? (int)$_GET['grade_level'] : 11;

$stmt = $pdo->prepare("SELECT id, name FROM sections WHERE grade_level = ?");
$stmt->execute([$gradeLevel]);
$sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($sections);