<?php
/**
 * Helper Functions
 * Asisten Akademik Harian
 */

/**
 * Get greeting based on current time
 * @return array ['greeting' => string, 'icon' => string]
 */
function getTimeGreeting()
{
    $hour = (int) date('G');

    if ($hour >= 5 && $hour < 11) {
        return ['greeting' => 'Selamat Pagi', 'icon' => '☀️'];
    } elseif ($hour >= 11 && $hour < 15) {
        return ['greeting' => 'Selamat Siang', 'icon' => '🌤️'];
    } elseif ($hour >= 15 && $hour < 18) {
        return ['greeting' => 'Selamat Sore', 'icon' => '🌅'];
    } else {
        return ['greeting' => 'Selamat Malam', 'icon' => '🌙'];
    }
}

/**
 * Get random motivational quote
 * @param string $category Optional category filter
 * @return string
 */
function getRandomQuote($category = 'general')
{
    require_once __DIR__ . '/../config/database.php';

    $sql = "SELECT quote FROM motivational_quotes WHERE category = ? OR category = 'general' ORDER BY RAND() LIMIT 1";
    $result = dbQueryOne($sql, [$category]);

    if ($result) {
        return $result['quote'];
    }

    // Fallback quotes
    $fallbackQuotes = [
        'Jangan menyerah! Perjuanganmu akan membuahkan hasil! 🚀',
        'Yuk dicicil satu per satu! Kamu pasti bisa! ✨',
        'Terus pertahankan semangatmu! 🔥'
    ];

    return $fallbackQuotes[array_rand($fallbackQuotes)];
}

/**
 * Format date to Indonesian format
 * @param string $date Date string
 * @param bool $includeTime Include time
 * @return string
 */
function formatDateIndonesian($date, $includeTime = false)
{
    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $months = [
        '',
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    $timestamp = strtotime($date);
    $dayName = $days[date('w', $timestamp)];
    $day = date('j', $timestamp);
    $month = $months[(int) date('n', $timestamp)];
    $year = date('Y', $timestamp);

    $formatted = "$dayName, $day $month $year";

    if ($includeTime) {
        $formatted .= ' ' . date('H:i', $timestamp);
    }

    return $formatted;
}

/**
 * Format duration in minutes to readable string
 * @param int $minutes Total minutes
 * @return string
 */
function formatDuration($minutes)
{
    if ($minutes < 60) {
        return $minutes . ' menit';
    }

    $hours = floor($minutes / 60);
    $mins = $minutes % 60;

    if ($mins === 0) {
        return $hours . 'h';
    }

    return $hours . 'h ' . $mins . 'm';
}

/**
 * Sanitize input string
 * @param string $input Input string
 * @return string
 */
function sanitize($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Calculate days remaining until deadline
 * @param string $deadline Deadline datetime
 * @return int Days remaining (negative if overdue)
 */
function getDaysRemaining($deadline)
{
    $now = new DateTime();
    $deadlineDate = new DateTime($deadline);
    $diff = $now->diff($deadlineDate);

    $days = $diff->days;
    if ($diff->invert) {
        $days = -$days;
    }

    return $days;
}

/**
 * Get urgency class based on deadline
 * @param string $deadline Deadline datetime
 * @return string CSS class
 */
function getUrgencyClass($deadline)
{
    $days = getDaysRemaining($deadline);

    if ($days < 0)
        return 'overdue';
    if ($days === 0)
        return 'today';
    if ($days <= 3)
        return 'urgent';
    if ($days <= 7)
        return 'soon';
    return 'normal';
}

/**
 * Send JSON response
 * @param mixed $data Response data
 * @param int $statusCode HTTP status code
 */
function jsonResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Get user statistics
 * @param int $userId User ID
 * @return array
 */
function getUserStats($userId)
{
    require_once __DIR__ . '/../config/database.php';

    // Count subjects
    $subjectCount = dbQueryOne(
        "SELECT COUNT(*) as count FROM subjects WHERE user_id = ?",
        [$userId]
    )['count'] ?? 0;

    // Count active tasks
    $activeTaskCount = dbQueryOne(
        "SELECT COUNT(*) as count FROM tasks WHERE user_id = ? AND is_completed = 0",
        [$userId]
    )['count'] ?? 0;

    // Get streak and study time from user
    $user = dbQueryOne(
        "SELECT streak_days, total_study_minutes FROM users WHERE id = ?",
        [$userId]
    );

    return [
        'subjects' => $subjectCount,
        'active_tasks' => $activeTaskCount,
        'streak_days' => $user['streak_days'] ?? 0,
        'total_study_minutes' => $user['total_study_minutes'] ?? 0
    ];
}

/**
 * Update user streak
 * @param int $userId User ID
 */
function updateStreak($userId)
{
    require_once __DIR__ . '/../config/database.php';

    $user = dbQueryOne("SELECT last_activity, streak_days FROM users WHERE id = ?", [$userId]);
    $today = date('Y-m-d');
    $lastActivity = $user['last_activity'];

    if ($lastActivity === $today) {
        // Already updated today
        return;
    }

    $yesterday = date('Y-m-d', strtotime('-1 day'));

    if ($lastActivity === $yesterday) {
        // Continue streak
        dbExecute(
            "UPDATE users SET streak_days = streak_days + 1, last_activity = ? WHERE id = ?",
            [$today, $userId]
        );
    } elseif ($lastActivity !== $today) {
        // Reset streak
        dbExecute(
            "UPDATE users SET streak_days = 1, last_activity = ? WHERE id = ?",
            [$today, $userId]
        );
    }
}

/**
 * Initialize default achievements for new user
 * @param int $userId User ID
 */
function initializeAchievements($userId)
{
    require_once __DIR__ . '/../config/database.php';

    $achievements = [
        ['streak', 'Pemula Konsisten', 'Raih streak 3 hari', '🔥', 3],
        ['streak', 'Pejuang Tangguh', 'Raih streak 7 hari', '🔥', 7],
        ['streak', 'Master Konsistensi', 'Raih streak 30 hari', '🔥', 30],
        ['study_time', 'Mulai Belajar', 'Belajar total 1 jam', '⏰', 60],
        ['study_time', 'Rajin Belajar', 'Belajar total 10 jam', '⏰', 600],
        ['study_time', 'Kutu Buku', 'Belajar total 50 jam', '📚', 3000],
        ['tasks', 'Produktif', 'Selesaikan 5 tugas', '✅', 5],
        ['tasks', 'Super Produktif', 'Selesaikan 20 tugas', '✅', 20],
        ['tasks', 'Legenda Tugas', 'Selesaikan 100 tugas', '🏆', 100],
        ['gpa', 'Target Tercapai', 'Raih IPK 3.0+', '🎓', 300],
        ['gpa', 'Cumlaude', 'Raih IPK 3.5+', '🎓', 350],
        ['gpa', 'Summa Cumlaude', 'Raih IPK 3.75+', '👑', 375],
    ];

    foreach ($achievements as $a) {
        dbExecute(
            "INSERT INTO achievements (user_id, category, name, description, icon, target) VALUES (?, ?, ?, ?, ?, ?)",
            [$userId, $a[0], $a[1], $a[2], $a[3], $a[4]]
        );
    }
}
