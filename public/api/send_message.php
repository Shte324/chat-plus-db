<?php
/**
 * API: вернуть сообщение от посетителя сайта.
 * Метод: GET (JSON)
 * Возвращает: JSON с созданным сообщением или ошибкой.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'method not alowed']);
    exit;
}

$raw = file_get_contents('php://input'); //Content-Type: application/json
$data = json_decode($raw, true);

if(!is_array($data)){
http_response_code(400);
echo json_encode(['success' => false, 'error' => 'Invalid JSON body']);
exit;
}

$userId = isset($data['user_id']) ? trim((string) $data['user_id']) : '';
$text   = isset($data['text'])    ? trim((string) $data['text'])    : '';

if($userId === '' || mb_strlen($userId) > 64 || !preg_match('/^[A-Za-z0-9_\-]+$/', $userId)){
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid user ID']);
    exit;
}
if($text === ''){
     http_response_code(400);
     echo json_encode(['success' => false, 'error' => 'Message is empty']);
     exit;
}

if(mb_strlen($text) > 4000){
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Text is to long (must be 4000)']);
    exit;
}

try{
    $pdo = require __DIR__ . '/../../backend/db.php';
} catch (Throwable $e){
    error_log('send_message: db connectin failed: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Server error']);
        exit;
}

try{
    $sql = "INSERT INTO messages (user_id, sender, text)
    VALUES(:user_id,:sender,:text)
    RETURNING id, user_id, sender, text, created_at";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $userId,
        ':sender'  => 'user',
        ':text'    => $text,
    ]);

    $row = $stmt->fetch();

} catch (Throwable $e){
    error_log('send_message: insert failed' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Server error']);
        exit;
}

echo json_encode([
    'success' => true,
    'message' => [
        'id'         => (int) $row['id'],
        'user_id'    => $row['user_id'],
        'sender'     => $row['sender'],
        'text'       => $row['text'],
        'created_at' => $row['created_at'],
    ],
]);

