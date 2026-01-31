<?php
/**
 * Database Configuration
 * Asisten Akademik Harian
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'akademik_harian');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get PDO Database Connection
 * @return PDO|null
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            return null;
        }
    }
    
    return $pdo;
}

/**
 * Execute a query and return results
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return array|false
 */
function dbQuery($sql, $params = []) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Query Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Execute a query and return single row
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return array|false
 */
function dbQueryOne($sql, $params = []) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Query Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Execute an insert/update/delete query
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return int|false Last insert ID or affected rows
 */
function dbExecute($sql, $params = []) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // Return last insert ID for INSERT, affected rows for UPDATE/DELETE
        if (stripos($sql, 'INSERT') === 0) {
            return $pdo->lastInsertId();
        }
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Execute Error: " . $e->getMessage());
        return false;
    }
}
