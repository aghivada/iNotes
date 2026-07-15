<?php
require_once 'config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // 1. GOOGLE SIGN-IN HANDLER
    if ($action === 'google-login') {
        $credential = $input['credential'] ?? '';
        
        if (empty($credential)) {
            echo json_encode(['success' => false, 'message' => 'Token Google tidak ditemukan.']);
            exit;
        }

        // Verifikasi token via Google API
        $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($credential);
        $response = file_get_contents($url);
        $payload = json_decode($response, true);

        if (isset($payload['error_description']) || $payload['aud'] !== GOOGLE_CLIENT_ID) {
            echo json_encode(['success' => false, 'message' => 'Token Google tidak valid.']);
            exit;
        }

        $googleId = $payload['sub'];
        $email = $payload['email'];
        $name = $payload['name'];
        $avatar = $payload['picture'] ?? '';

        // Cek apakah user sudah ada
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Update google_id jika sebelumnya daftar manual dengan email yang sama
            if (!$user['google_id']) {
                $pdo->prepare("UPDATE users SET google_id = ?, avatar = ? WHERE id = ?")
                    ->execute([$googleId, $avatar, $user['id']]);
            }
            $userId = $user['id'];
        } else {
            // Buat user baru
            $stmt = $pdo->prepare("INSERT INTO users (google_id, name, email, avatar) VALUES (?, ?, ?, ?)");
            $stmt->execute([$googleId, $name, $email, $avatar]);
            $userId = $pdo->lastInsertId();
        }

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        
        echo json_encode(['success' => true]);
        exit;
    }

    // 2. REGISTRASI MANUAL
    if ($action === 'register') {
        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Semua data harus diisi.']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email sudah terdaftar.']);
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword]);

        echo json_encode(['success' => true, 'message' => 'Registrasi berhasil! Silakan login.']);
        exit;
    }

    // 3. LOGIN MANUAL
    if ($action === 'login') {
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && $user['password'] && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Email atau password salah.']);
        }
        exit;
    }
}