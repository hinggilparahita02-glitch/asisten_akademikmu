<?php
/**
 * Checklist API
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
    $type = $_GET['type'] ?? null;
    $today = date('Y-m-d');

    // Reset daily items for new day
    resetDailyItems($userId, $today);

    $sql = "SELECT * FROM checklist_items WHERE user_id = ?";
    $params = [$userId];

    if ($type) {
        $sql .= " AND type = ?";
        $params[] = $type;
    }

    // For daily items, only show today's
    if ($type === 'daily') {
        $sql .= " AND (check_date = ? OR check_date IS NULL)";
        $params[] = $today;
    }

    $sql .= " ORDER BY created_at ASC";

    $items = dbQuery($sql, $params);

    // Calculate progress
    $totalDaily = 0;
    $completedDaily = 0;
    $totalGoals = 0;
    $completedGoals = 0;

    foreach ($items as $item) {
        if ($item['type'] === 'daily') {
            $totalDaily++;
            if ($item['is_completed'])
                $completedDaily++;
        } else {
            $totalGoals++;
            if ($item['is_completed'])
                $completedGoals++;
        }
    }

    jsonResponse([
        'success' => true,
        'data' => $items,
        'stats' => [
            'daily_total' => $totalDaily,
            'daily_completed' => $completedDaily,
            'goals_total' => $totalGoals,
            'goals_completed' => $completedGoals
        ]
    ]);
}

function resetDailyItems($userId, $today)
{
    // Check if we need to reset (if check_date is not today)
    $needsReset = dbQueryOne(
        "SELECT COUNT(*) as count FROM checklist_items 
         WHERE user_id = ? AND type = 'daily' AND check_date IS NOT NULL AND check_date < ?",
        [$userId, $today]
    )['count'] ?? 0;

    if ($needsReset > 0) {
        // Reset all daily items
        dbExecute(
            "UPDATE checklist_items SET is_completed = 0, check_date = ? 
             WHERE user_id = ? AND type = 'daily'",
            [$today, $userId]
        );
    }
}

function handlePost($userId, $input)
{
    $title = trim($input['title'] ?? '');
    $type = $input['type'] ?? 'daily';
    $today = date('Y-m-d');

    if (empty($title)) {
        jsonResponse(['success' => false, 'error' => 'Title is required'], 400);
    }

    $checkDate = $type === 'daily' ? $today : null;

    $itemId = dbExecute(
        "INSERT INTO checklist_items (user_id, title, type, check_date) VALUES (?, ?, ?, ?)",
        [$userId, $title, $type, $checkDate]
    );

    if ($itemId) {
        jsonResponse(['success' => true, 'id' => $itemId, 'message' => 'Item added successfully']);
    } else {
        jsonResponse(['success' => false, 'error' => 'Failed to add item'], 500);
    }
}

function handlePut($userId, $input)
{
    $id = $input['id'] ?? null;

    if (!$id) {
        jsonResponse(['success' => false, 'error' => 'Item ID is required'], 400);
    }

    // Check if item belongs to user
    $item = dbQueryOne("SELECT * FROM checklist_items WHERE id = ? AND user_id = ?", [$id, $userId]);
    if (!$item) {
        jsonResponse(['success' => false, 'error' => 'Item not found'], 404);
    }

    // Build update query
    $updates = [];
    $params = [];

    if (isset($input['title'])) {
        $updates[] = "title = ?";
        $params[] = trim($input['title']);
    }
    if (isset($input['is_completed'])) {
        $updates[] = "is_completed = ?";
        $params[] = $input['is_completed'] ? 1 : 0;

        if ($input['is_completed'] && $item['type'] === 'daily') {
            $updates[] = "check_date = ?";
            $params[] = date('Y-m-d');
        }
    }

    if (empty($updates)) {
        jsonResponse(['success' => false, 'error' => 'No fields to update'], 400);
    }

    $params[] = $id;
    $params[] = $userId;

    $result = dbExecute(
        "UPDATE checklist_items SET " . implode(', ', $updates) . " WHERE id = ? AND user_id = ?",
        $params
    );

    if ($result !== false) {
        $message = isset($input['is_completed']) && $input['is_completed']
            ? 'Hebat! Satu langkah lebih dekat ke tujuanmu! 🎯'
            : 'Item updated successfully';
        jsonResponse(['success' => true, 'message' => $message]);
    } else {
        jsonResponse(['success' => false, 'error' => 'Failed to update item'], 500);
    }
}

function handleDelete($userId)
{
    $id = $_GET['id'] ?? null;

    if (!$id) {
        jsonResponse(['success' => false, 'error' => 'Item ID is required'], 400);
    }

    $result = dbExecute(
        "DELETE FROM checklist_items WHERE id = ? AND user_id = ?",
        [$id, $userId]
    );

    if ($result) {
        jsonResponse(['success' => true, 'message' => 'Item deleted successfully']);
    } else {
        jsonResponse(['success' => false, 'error' => 'Failed to delete item'], 500);
    }
}
