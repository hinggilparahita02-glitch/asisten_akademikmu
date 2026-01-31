<?php
/**
 * Notes API
 * Asisten Akademik Harian
 */

require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Require login
if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
}

$userId = getCurrentUserId();
$method = $_SERVER['REQUEST_METHOD'];

// Get JSON input for POST/PUT
$input = json_decode(file_get_contents('php://input'), true) ?? [];

switch ($method) {
    case 'GET':
        handleGet($userId);
        break;
    case 'POST':
        handlePost($userId, $input);
        break;
    case 'PUT':
        handlePut($userId, $input);
        break;
    case 'DELETE':
        handleDelete($userId);
        break;
    default:
        jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}

function handleGet($userId)
{
    $id = $_GET['id'] ?? null;
    $search = $_GET['search'] ?? '';

    if ($id) {
        $note = dbQueryOne(
            "SELECT * FROM notes WHERE id = ? AND user_id = ?",
            [$id, $userId]
        );

        if ($note) {
            jsonResponse(['success' => true, 'data' => $note]);
        } else {
            jsonResponse(['success' => false, 'error' => 'Note not found'], 404);
        }
    } else {
        $sql = "SELECT * FROM notes WHERE user_id = ?";
        $params = [$userId];

        if ($search) {
            $sql .= " AND (title LIKE ? OR content LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY is_pinned DESC, updated_at DESC";

        $notes = dbQuery($sql, $params);
        jsonResponse(['success' => true, 'data' => $notes]);
    }
}

function handlePost($userId, $input)
{
    $title = trim($input['title'] ?? '');
    $content = trim($input['content'] ?? '');
    $color = $input['color'] ?? 'yellow';

    if (empty($title)) {
        jsonResponse(['success' => false, 'error' => 'Title is required'], 400);
    }

    $noteId = dbExecute(
        "INSERT INTO notes (user_id, title, content, color) VALUES (?, ?, ?, ?)",
        [$userId, $title, $content, $color]
    );

    if ($noteId) {
        jsonResponse(['success' => true, 'id' => $noteId, 'message' => 'Note created successfully']);
    } else {
        jsonResponse(['success' => false, 'error' => 'Failed to create note'], 500);
    }
}

function handlePut($userId, $input)
{
    $id = $input['id'] ?? null;

    if (!$id) {
        jsonResponse(['success' => false, 'error' => 'Note ID is required'], 400);
    }

    // Check if note belongs to user
    $note = dbQueryOne("SELECT * FROM notes WHERE id = ? AND user_id = ?", [$id, $userId]);
    if (!$note) {
        jsonResponse(['success' => false, 'error' => 'Note not found'], 404);
    }

    // Build update query
    $updates = [];
    $params = [];

    if (isset($input['title'])) {
        $updates[] = "title = ?";
        $params[] = trim($input['title']);
    }
    if (isset($input['content'])) {
        $updates[] = "content = ?";
        $params[] = trim($input['content']);
    }
    if (isset($input['color'])) {
        $updates[] = "color = ?";
        $params[] = $input['color'];
    }
    if (isset($input['is_pinned'])) {
        $updates[] = "is_pinned = ?";
        $params[] = $input['is_pinned'] ? 1 : 0;
    }

    if (empty($updates)) {
        jsonResponse(['success' => false, 'error' => 'No fields to update'], 400);
    }

    $params[] = $id;
    $params[] = $userId;

    $result = dbExecute(
        "UPDATE notes SET " . implode(', ', $updates) . " WHERE id = ? AND user_id = ?",
        $params
    );

    if ($result !== false) {
        jsonResponse(['success' => true, 'message' => 'Note updated successfully']);
    } else {
        jsonResponse(['success' => false, 'error' => 'Failed to update note'], 500);
    }
}

function handleDelete($userId)
{
    $id = $_GET['id'] ?? null;

    if (!$id) {
        jsonResponse(['success' => false, 'error' => 'Note ID is required'], 400);
    }

    $result = dbExecute(
        "DELETE FROM notes WHERE id = ? AND user_id = ?",
        [$id, $userId]
    );

    if ($result) {
        jsonResponse(['success' => true, 'message' => 'Note deleted successfully']);
    } else {
        jsonResponse(['success' => false, 'error' => 'Failed to delete note'], 500);
    }
}
