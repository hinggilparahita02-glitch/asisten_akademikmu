<?php
/**
 * Timer API
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

// Get JSON input for POST
$input = json_decode(file_get_contents('php://input'), true) ?? [];

switch ($method) {
    case 'GET':
        handleGet($userId);
        break;
    case 'POST':
        handlePost($userId, $input);
        break;
    default:
        jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
}

function handleGet($userId)
{
    $period = $_GET['period'] ?? 'today';

    $today = date('Y-m-d');

    switch ($period) {
        case 'today':
            $startDate = $today;
            break;
        case 'week':
            $startDate = date('Y-m-d', strtotime('monday this week'));
            break;
        case 'month':
            $startDate = date('Y-m-01');
            break;
        case 'all':
            $startDate = '2000-01-01';
            break;
        default:
            $startDate = $today;
    }

    // Get total study time
    $totalMinutes = dbQueryOne(
        "SELECT COALESCE(SUM(duration_minutes), 0) as total 
         FROM study_sessions 
         WHERE user_id = ? AND DATE(started_at) >= ?",
        [$userId, $startDate]
    )['total'] ?? 0;

    // Get session count
    $sessionCount = dbQueryOne(
        "SELECT COUNT(*) as count 
         FROM study_sessions 
         WHERE user_id = ? AND DATE(started_at) >= ? AND session_type = 'pomodoro'",
        [$userId, $startDate]
    )['count'] ?? 0;

    // Get today's sessions
    $todaySessions = dbQuery(
        "SELECT ss.*, s.name as subject_name 
         FROM study_sessions ss 
         LEFT JOIN subjects s ON ss.subject_id = s.id 
         WHERE ss.user_id = ? AND DATE(ss.started_at) = ?
         ORDER BY ss.started_at DESC",
        [$userId, $today]
    );

    // Get today's stats
    $todayMinutes = dbQueryOne(
        "SELECT COALESCE(SUM(duration_minutes), 0) as total 
         FROM study_sessions 
         WHERE user_id = ? AND DATE(started_at) = ?",
        [$userId, $today]
    )['total'] ?? 0;

    $todayPomodoroCount = dbQueryOne(
        "SELECT COUNT(*) as count 
         FROM study_sessions 
         WHERE user_id = ? AND DATE(started_at) = ? AND session_type = 'pomodoro'",
        [$userId, $today]
    )['count'] ?? 0;

    jsonResponse([
        'success' => true,
        'data' => [
            'total_minutes' => (int) $totalMinutes,
            'session_count' => (int) $sessionCount,
            'today_minutes' => (int) $todayMinutes,
            'today_pomodoro_count' => (int) $todayPomodoroCount,
            'sessions' => $todaySessions
        ]
    ]);
}

function handlePost($userId, $input)
{
    $durationMinutes = (int) ($input['duration_minutes'] ?? 0);
    $sessionType = $input['session_type'] ?? 'pomodoro';
    $subjectId = $input['subject_id'] ?? null;
    $startedAt = $input['started_at'] ?? date('Y-m-d H:i:s');

    if ($durationMinutes <= 0) {
        jsonResponse(['success' => false, 'error' => 'Duration must be greater than 0'], 400);
    }

    // Calculate ended_at
    $endedAt = date('Y-m-d H:i:s', strtotime($startedAt) + ($durationMinutes * 60));

    $sessionId = dbExecute(
        "INSERT INTO study_sessions (user_id, subject_id, duration_minutes, session_type, started_at, ended_at) 
         VALUES (?, ?, ?, ?, ?, ?)",
        [$userId, $subjectId ?: null, $durationMinutes, $sessionType, $startedAt, $endedAt]
    );

    if ($sessionId) {
        // Update user's total study time
        dbExecute(
            "UPDATE users SET total_study_minutes = total_study_minutes + ? WHERE id = ?",
            [$durationMinutes, $userId]
        );

        // Update streak
        updateStreak($userId);

        jsonResponse(['success' => true, 'id' => $sessionId, 'message' => 'Session saved successfully']);
    } else {
        jsonResponse(['success' => false, 'error' => 'Failed to save session'], 500);
    }
}
