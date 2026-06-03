<?php
/**
 * BANAVI - CONFIGURATION FILE
 * Database and application settings
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'banavi_db');
define('DB_PORT', 3306);

// Application Settings
define('APP_NAME', 'Banavi Fashion Store');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/banavi_project');
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// Payment Settings
define('PAYMENT_MODE', 'mock'); // 'mock', 'razorpay', 'stripe'
define('CURRENCY', 'INR');

// Security
define('JWT_SECRET', 'your_jwt_secret_key_here_change_in_production');
define('SESSION_TIMEOUT', 3600); // 1 hour

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable error display
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Start session for API authentication
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// CORS Headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  exit;
}

// Database Connection Function
function getDBConnection() {
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
  
  if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
  }
  
  $conn->set_charset("utf8");
  return $conn;
}

// Response Helper Function
function sendResponse($success, $message, $data = null, $statusCode = 200) {
  http_response_code($statusCode);
  echo json_encode([
    'success' => $success,
    'message' => $message,
    'data' => $data
  ]);
  exit;
}

// Validation Helpers
function validateEmail($email) {
  return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
  return preg_match('/^[0-9]{10}$/', str_replace(['-', ' ', '+91'], '', $phone));
}

function sanitizeInput($data) {
  return htmlspecialchars(strip_tags(trim($data)));
}
?>
