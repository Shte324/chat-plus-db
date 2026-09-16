<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if($_SERVER['REQUEST_METHOD'] !== 'GET'){
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'method not allowed']);
    exit;
}

$userId = isset($_GET['user_id']) ? trim((string) $_GET['user_id']) : '';

if($userId === '' || mb_strlen($userId) > 64 || !preg_match('/^[A-Za-z0-9_\-]+$/', $userId)){
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Invalid user_id']);
    exit;
}



try {
    $pdo = require __DIR__ . '/../../backend/db.php';
} catch (Throwable $e){
    error_log('get_messages: db connection failed: ' . $e->getMessage());
http_response_code(500);
echo json_encode(['success' => false, 'error' => 'Server error']);
exit;
}

try{
    $sql = "SELECT id, user_id, sender, text, created_at
            FROM messages 
            WHERE user_id = :user_id
            ORDER BY created_at ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':user_id' => $userId,
]);

 $rows = $stmt->fetchAll();

} catch (Throwable $e){
error_log('get message: select failed ' . $e->getMessage());
http_response_code(500);
echo json_encode(['success' => false, 'error' => 'Server error']);
exit;
}

//array_map применяет функцию к каждому элементу массива.
$messages = array_map(function($row){
    return [
        'id'         => (int) $row['id'],
        'user_id'    => $row['user_id'],
        'sender'     => $row['sender'],
        'text'       => $row['text'],
        'created_at' => $row['created_at'],
    ];
    }, $rows);

    echo json_encode([
    'success'  => true,
    'messages' => $messages,
]);

