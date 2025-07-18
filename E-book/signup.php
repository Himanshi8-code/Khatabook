<?php
session_start();
require_once 'db.php';

// Function to validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate user ID length
function isValidUserId($userId) {
    $len = strlen($userId);
    return ($len >= 6 && $len <= 20);
}

// Function to validate password strength
function isValidPassword($password) {
    return (strlen($password) >= 8 && preg_match('/\d/', $password));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $userId = trim($_POST['user_id'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $errors = [];

    // Validate inputs
    if (empty($fullName)) {
        $errors[] = "Full name is required.";
    }
    if (!isValidEmail($email)) {
        $errors[] = "Invalid email format.";
    }
    if (!isValidUserId($userId)) {
        $errors[] = "User ID must be 6-20 characters.";
    }
    if (!isValidPassword($password)) {
        $errors[] = "Password must be at least 8 characters and contain a number.";
    }
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Check if email or user ID already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR user_id = :user_id");
        $stmt->execute(['email' => $email, 'user_id' => $userId]);
        if ($stmt->fetch()) {
            $errors[] = "Email or User ID already exists.";
        } else {
            // Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, user_id, password_hash) VALUES (:full_name, :email, :user_id, :password_hash)");
            $result = $stmt->execute([
                'full_name' => $fullName,
                'email' => $email,
                'user_id' => $userId,
                'password_hash' => $passwordHash
            ]);

            if ($result) {
                $_SESSION['user_id'] = $userId;
                echo json_encode(['success' => true, 'message' => 'Account created successfully']);
                exit;
            } else {
                $errors[] = "Failed to create account. Please try again.";
            }
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
