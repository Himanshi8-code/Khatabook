<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Placeholder: Fetch user transaction history from database
// For now, return sample data

$history = [
    ['date' => '2025-04-25', 'description' => 'Deposit', 'amount' => 1000],
    ['date' => '2025-04-26', 'description' => 'Withdrawal', 'amount' => -200],
    ['date' => '2025-04-27', 'description' => 'Payment', 'amount' => -150],
];

echo json_encode(['success' => true, 'history' => $history]);
?>
