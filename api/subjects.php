<?php
/**
 * Subjects API
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

    if ($id) {
        $subject = dbQueryOne(
            "SELECT * FROM subjects WHERE id = ? AND user_id = ?",
            [$id, $userId]
        );

        if ($subject) {
            jsonResponse(['success' => true, 'data' => $subject]);
        } else {
            jsonResponse(['success' => false, 'error' => 'Subject not found'], 404);
        }
    } else {
        $subjects = dbQuery(
            "SELECT * FROM subjects WHERE user_id = ? ORDER BY name",
            [$userId]
        );
        jsonResponse(['success' => true, 'data' => $subjects]);
    }
}

function handlePost($userId, $input)
{
    $name = trim($input['name'] ?? '');
    $credits = (int) ($input['credits'] ?? 3);
    $grade = $input['grade'] ?? null;
    $numericScore = isset($input['numeric_score']) ? (int) $input['numeric_score'] : null;

    if (empty($name)) {
        jsonResponse(['success' => false, 'error' => 'Name is required'], 400);
    }

    // Check if subject already exists
    $existing = dbQueryOne(
        "SELECT id FROM subjects WHERE user_id = ? AND name = ?",
        [$userId, $name]
    );

    if ($existing) {
        jsonResponse(['success' => false, 'error' => 'Mata kuliah sudah ada'], 400);
    }

    $gradeValue = getGradeValue($grade);

    $subjectId = dbExecute(
        "INSERT INTO subjects (user_id, name, credits, grade, grade_value, numeric_score) VALUES (?, ?, ?, ?, ?, ?)",
        [$userId, $name, $credits, $grade, $gradeValue, $numericScore]
    );

    if ($subjectId) {
        updateUserGPA($userId);
        jsonResponse(['success' => true, 'id' => $subjectId, 'message' => 'Subject added successfully']);
    } else {
        jsonResponse(['success' => false, 'error' => 'Failed to add subject'], 500);
    }
}

function handlePut($userId, $input)
{
    $id = $input['id'] ?? null;

    if (!$id) {
        jsonResponse(['success' => false, 'error' => 'Subject ID is required'], 400);
    }

    // Check if subject belongs to user
    $subject = dbQueryOne("SELECT * FROM subjects WHERE id = ? AND user_id = ?", [$id, $userId]);
    if (!$subject) {
        jsonResponse(['success' => false, 'error' => 'Subject not found'], 404);
    }

    // Build update query
    $updates = [];
    $params = [];

    if (isset($input['name'])) {
        $updates[] = "name = ?";
        $params[] = trim($input['name']);
    }
    if (isset($input['credits'])) {
        $updates[] = "credits = ?";
        $params[] = (int) $input['credits'];
    }
    if (array_key_exists('grade', $input)) {
        $updates[] = "grade = ?";
        $params[] = $input['grade'];
        $updates[] = "grade_value = ?";
        $params[] = getGradeValue($input['grade']);
    }
    if (array_key_exists('numeric_score', $input)) {
        $updates[] = "numeric_score = ?";
        $params[] = $input['numeric_score'] ? (int) $input['numeric_score'] : null;
    }

    if (empty($updates)) {
        jsonResponse(['success' => false, 'error' => 'No fields to update'], 400);
    }

    $params[] = $id;
    $params[] = $userId;

    $result = dbExecute(
        "UPDATE subjects SET " . implode(', ', $updates) . " WHERE id = ? AND user_id = ?",
        $params
    );

    if ($result !== false) {
        updateUserGPA($userId);
        jsonResponse(['success' => true, 'message' => 'Subject updated successfully']);
    } else {
        jsonResponse(['success' => false, 'error' => 'Failed to update subject'], 500);
    }
}

function handleDelete($userId)
{
    $id = $_GET['id'] ?? null;

    if (!$id) {
        jsonResponse(['success' => false, 'error' => 'Subject ID is required'], 400);
    }

    $result = dbExecute(
        "DELETE FROM subjects WHERE id = ? AND user_id = ?",
        [$id, $userId]
    );

    if ($result) {
        updateUserGPA($userId);
        jsonResponse(['success' => true, 'message' => 'Subject deleted successfully']);
    } else {
        jsonResponse(['success' => false, 'error' => 'Failed to delete subject'], 500);
    }
}

function getGradeValue($grade)
{
    $gradePoints = [
        'A' => 4.0,
        'A-' => 3.7,
        'B+' => 3.3,
        'B' => 3.0,
        'B-' => 2.7,
        'C+' => 2.3,
        'C' => 2.0,
        'C-' => 1.7,
        'D+' => 1.3,
        'D' => 1.0,
        'E' => 0.0
    ];

    return $gradePoints[$grade] ?? null;
}

function updateUserGPA($userId)
{
    $subjects = dbQuery(
        "SELECT credits, grade_value FROM subjects WHERE user_id = ? AND grade_value IS NOT NULL",
        [$userId]
    );

    $totalCredits = 0;
    $totalPoints = 0;

    foreach ($subjects as $subject) {
        $totalCredits += $subject['credits'];
        $totalPoints += $subject['credits'] * $subject['grade_value'];
    }

    $gpa = $totalCredits > 0 ? $totalPoints / $totalCredits : 0;

    dbExecute("UPDATE users SET current_gpa = ? WHERE id = ?", [$gpa, $userId]);
}
