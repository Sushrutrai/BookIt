<?php
session_start();
require_once __DIR__ . '/../bootstrap.php';

// Check if user is logged in
if (!isset($_SESSION['role'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Please log in to bookmark events']);
    exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$id = $data['id'];
$eid = $data['eid'];
$status = $data['liked'];

// Role-based access control
if ($_SESSION['role'] === 'user' && $_SESSION['id'] != $id) {
    // Users can only manage their own bookmarks
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: You can only manage your own bookmarks']);
    exit;
}
// Admins can manage any bookmarks

try {
    if ($status === false) {
        $query = $connection->prepare("DELETE FROM bookmarks WHERE eid=? AND id=?");
        $query->bind_param('ii', $eid, $id);
        $query->execute();
    } else {
        $sql = "INSERT INTO bookmarks(eid,id) VALUES(?,?)";
        $query = $connection->prepare($sql);
        $query->bind_param('ii', $eid, $id);
        $query->execute();
    }
    echo json_encode(['success' => true, 'message' => 'status updated']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>