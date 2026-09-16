<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => '// Error: Unauthorized session.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db.php';
    
    $alias = trim($_POST['alias'] ?? '');
    $status_text = trim($_POST['status_text'] ?? '');
    $msg = trim($_POST['message'] ?? '');

    if (!empty($alias) && !empty($msg)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO inquiries (alias, status, message) VALUES (:alias, :status, :message)");
            $stmt->execute([
                'alias' => $alias,
                'status' => $status_text,
                'message' => $msg
            ]);
            echo json_encode(['status' => 'success', 'message' => '// Log transmitted successfully to the server!']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => '// Transmission error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => '// Error: Please fill in your handle and message.']);
    }
}
exit;