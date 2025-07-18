<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailOrUserId = trim($_POST['email_or_userid'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = [];

    if (empty($emailOrUserId)) {
        $errors[] = "Email or User ID is required.";
    }
    if (empty($password) || strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if (empty($errors)) {
        // Fetch user by email or user_id
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR user_id = :user_id");
        $stmt->execute(['email' => $emailOrUserId, 'user_id' => $emailOrUserId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Authentication successful
            $_SESSION['user_id'] = $user['user_id'];
            echo json_encode(['success' => true, 'message' => 'Login successful']);
            exit;
        } else {
            $errors[] = "Invalid credentials.";
        }
    }

    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}
?>
