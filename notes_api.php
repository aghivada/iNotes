<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        // Ambil semua catatan milik user aktif (Pinned ditaruh paling atas)
        $stmt = $pdo->prepare("SELECT * FROM notes WHERE user_id = ? ORDER BY is_pinned DESC, updated_at DESC");
        $stmt->execute([$userId]);
        echo json_encode($stmt->fetchAll());
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($action === 'create') {
            $title = trim($input['title'] ?? 'Tanpa Judul');
            $content = trim($input['content'] ?? '');
            $color = $input['color'] ?? '#ffffff';

            $stmt = $pdo->prepare("INSERT INTO notes (user_id, title, content, color) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $title, $content, $color]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } 
        
        elseif ($action === 'update') {
            $id = $input['id'] ?? null;
            $title = trim($input['title'] ?? 'Tanpa Judul');
            $content = trim($input['content'] ?? '');
            $color = $input['color'] ?? '#ffffff';
            $is_pinned = $input['is_pinned'] ?? 0;

            $stmt = $pdo->prepare("UPDATE notes SET title = ?, content = ?, color = ?, is_pinned = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$title, $content, $color, $is_pinned, $id, $userId]);
            echo json_encode(['success' => true]);
        } 
        
        elseif ($action === 'delete') {
            $id = $input['id'] ?? null;
            $stmt = $pdo->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            echo json_encode(['success' => true]);
        }
        break;
}