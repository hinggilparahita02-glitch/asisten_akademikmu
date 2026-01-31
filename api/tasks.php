<?php
/**
 * Tasks API
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
    $date = $_GET['date'] ?? null;
    $month = $_GET['month'] ?? null;
    $year = $_GET['year'] ?? null;

    if ($id) {
        // Get single task
        $task = dbQueryOne(
            "SELECT t.*, s.name as subject_name 
             FROM tasks t 
             LEFT JOIN subjects s ON t.subject_id = s.id 
             WHERE t.id = ? AND t.user_id = ?",
            [$id, $userId]
        );

        if ($task) {
            jsonResponse(['success' => true, 'data' => $task]);
        } else {
            jsonResponse(['success' => false, 'error' => 'Task not found'], 404);
        }
    } elseif ($date) {
        // Get tasks by date
        $tasks = dbQuery(
            "SELECT t.*, s.name as subject_name 
             FROM tasks t 
             LEFT JOIN subjects s ON t.subject_id = s.id 
             WHERE t.user_id = ? AND DATE(t.deadline) = ?
             ORDER BY t.deadline ASC",
            [$userId, $date]
        );
        jsonResponse(['success' => true, 'data' => $tasks]);
    } elseif ($month && $year) {
        // Get tasks by month for calendar
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $tasks = dbQuery(
            "SELECT t.*, s.name as subject_name, DATE(t.deadline) as deadline_date
             FROM tasks t 
             LEFT JOIN subjects s ON t.subject_id = s.id 
             WHERE t.user_id = ? AND DATE(t.deadline) BETWEEN ? AND ?
             ORDER BY t.deadline ASC",
            [$userId, $startDate, $endDate]
        );
        jsonResponse(['success' => true, 'data' => $tasks]);
    } else {
        // Get all tasks
        $tasks = dbQuery(
            "SELECT t.*, s.name as subject_name 
             FROM tasks t 
             LEFT JOIN subjects s ON t.subject_id = s.id 
             WHERE t.user_id = ? AND t.is_completed = 0
             ORDER BY t.deadline ASC",
            [$userId]
        );
        jsonResponse(['success' => true, 'data' => $tasks]);
    }
}

function handlePost($userId, $input)
{
    $title = trim($input['title'] ?? '');
    $description = trim($input['description'] ?? '');
    $deadline = $input['deadline'] ?? '';
    $subjectId = $input['subject_id'] ?? null;
    $priority = $input['priority'] ?? 'medium';

    if (empty($title) || empty($deadline)) {
        jsonResponse(['success' => false, 'error' => 'Title and deadline are required'], 400);
    }

    $taskId = dbExecute(
        "INSERT INTO tasks (user_id, subject_id, title, description, deadline, priority) VALUES (?, ?, ?, ?, ?, ?)",
        [$userId, $subjectId ?: null, $title, $description, $deadline, $priority]
    );

    if ($taskId) {
        jsonResponse(['success' => true, 'id' => $taskId, 'message' => 'Task created successfully']);
    } else {
        jsonResponse(['success' => false, 'error' => 'Failed to create task'], 500);
    }
}

function handlePut($userId, $input)
{
    $id = $input['id'] ?? null;

    if (!$id) {
        jsonResponse(['success' => false, 'error' => 'Task ID is required'], 400);
    }

    // Check if task belongs to user
    $task = dbQueryOne("SELECT * FROM tasks WHERE id = ? AND user_id = ?", [$id, $userId]);
    if (!$task) {
        jsonResponse(['success' => false, 'error' => 'Task not found'], 404);
    }

    // Build update query
    $updates = [];
    $params = [];

    if (isset($input['title'])) {
        $updates[] = "title = ?";
        $params[] = trim($input['title']);
    }
    if (isset($input['description'])) {
        $updates[] = "description = ?";
        $params[] = trim($input['description']);
    }
    if (isset($input['deadline'])) {
        $updates[] = "deadline = ?";
        $params[] = $input['deadline'];
    }
    if (isset($input['subject_id'])) {
        $updates[] = "subject_id = ?";
        $params[] = $input['subject_id'] ?: null;
    }
    if (isset($input['priority'])) {
        $updates[] = "priority = ?";
        $params[] = $input['priority'];
    }
    if (isset($input['is_completed'])) {
        $updates[] = "is_completed = ?";
        $params[] = $input['is_completed'];
        if ($input['is_completed']) {
            $updates[] = "completed_at = NOW()";
        }
    }

    if (empty($updates)) {
        jsonResponse(['success' => false, 'error' => 'No fields to update'], 400);
    }

    $params[] = $id;
    $params[] = $userId;

    $result = dbExecute(
        "UPDATE tasks SET " . implode(', ', $updates) . " WHERE id = ? AND user_id = ?",
        $params
    );

    if ($result !== false) {
        jsonResponse(['success' => true, 'message' => 'Task updated successfully']);
    } else {
        jsonResponse(['success' => false, 'error' => 'Failed to update task'], 500);
    }
}

function handleDelete($userId)
{
    $id = $_GET['id'] ?? null;

    if (!$id) {
        jsonResponse(['success' => false, 'error' => 'Task ID is required'], 400);
    }

    $result = dbExecute(
        "DELETE FROM tasks WHERE id = ? AND user_id = ?",
        [$id, $userId]
    );

    if ($result) {
        jsonResponse(['success' => true, 'message' => 'Task deleted successfully']);
    } else {
        jsonResponse(['success' => false, 'error' => 'Failed to delete task'], 500);
    }
}
