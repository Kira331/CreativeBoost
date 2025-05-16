<?php
session_start();
require 'config.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo json_encode([
                'status' => 'success',
                'message' => 'Вхід успішний',
                'redirect' => 'dashboard.php'
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Невірний email або пароль']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Помилка: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Невірний метод запиту.']);
}
?>
