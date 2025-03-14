<?php
require_once '../../includes/db_config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000'); // Allow requests from frontend
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

$response = ['success' => false, 'data' => [], 'message' => ''];

if (!$query) {
    $response['message'] = 'Search query is required';
    echo json_encode($response);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE title LIKE :query");
    $stmt->execute(['query' => "%$query%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['data'] = $results;
} catch (PDOException $e) {
    $response['message'] = 'Database Error: ' . $e->getMessage();
}

echo json_encode($response);
?>