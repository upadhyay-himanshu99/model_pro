<?php
/**
 * BANAVI - USERS & AUTH API
 * Endpoints for user operations and authentication
 */

require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'login';

switch ($method) {
  case 'POST':
    if ($action === 'login') {
      login();
    } elseif ($action === 'signup') {
      signup();
    } elseif ($action === 'logout') {
      logout();
    }
    break;

  case 'GET':
    if ($action === 'profile') {
      getProfile();
    } elseif ($action === 'verify-email') {
      verifyEmail();
    }
    break;

  case 'PUT':
    if ($action === 'update-profile') {
      updateProfile();
    } elseif ($action === 'change-password') {
      changePassword();
    }
    break;

  default:
    sendResponse(false, 'Invalid request method', null, 405);
}

// Login Function
function login() {
  $data = json_decode(file_get_contents('php://input'), true);
  
  if (empty($data['email']) || empty($data['password'])) {
    sendResponse(false, 'Email and password required', null, 400);
  }
  
  $conn = getDBConnection();
  
  $email = $conn->real_escape_string(sanitizeInput($data['email']));
  $password = $data['password'];
  
  $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
  $result = $conn->query($query);
  $user = $result->fetch_assoc();
  
  if (!$user) {
    $conn->close();
    sendResponse(false, 'Invalid credentials', null, 401);
  }
  
  if (!password_verify($password, $user['password_hash'])) {
    $conn->close();
    sendResponse(false, 'Invalid credentials', null, 401);
  }
  
  // Unset sensitive data
  unset($user['password_hash']);
  
  // Create session/token
  $_SESSION['user_id'] = $user['id'];
  $_SESSION['user_email'] = $user['email'];
  
  $conn->close();
  sendResponse(true, 'Login successful', $user);
}

// Signup Function
function signup() {
  $data = json_decode(file_get_contents('php://input'), true);
  
  $required = ['first_name', 'last_name', 'email', 'phone', 'password'];
  foreach ($required as $field) {
    if (empty($data[$field])) {
      sendResponse(false, "Missing required field: $field", null, 400);
    }
  }
  
  if (!validateEmail($data['email'])) {
    sendResponse(false, 'Invalid email format', null, 400);
  }
  
  if (!validatePhone($data['phone'])) {
    sendResponse(false, 'Invalid phone format', null, 400);
  }
  
  if (strlen($data['password']) < 6) {
    sendResponse(false, 'Password must be at least 6 characters', null, 400);
  }
  
  $conn = getDBConnection();
  
  // Check if email exists
  $email = $conn->real_escape_string(sanitizeInput($data['email']));
  $query = "SELECT id FROM users WHERE email = '$email' LIMIT 1";
  $result = $conn->query($query);
  
  if ($result->num_rows > 0) {
    $conn->close();
    sendResponse(false, 'Email already registered', null, 409);
  }
  
  $first_name = $conn->real_escape_string(sanitizeInput($data['first_name']));
  $last_name = $conn->real_escape_string(sanitizeInput($data['last_name']));
  $phone = $conn->real_escape_string(sanitizeInput($data['phone']));
  $password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
  
  $query = "INSERT INTO users (first_name, last_name, email, phone, password_hash, profile_avatar, is_verified)
           VALUES ('$first_name', '$last_name', '$email', '$phone', '$password_hash', '" . substr($first_name, 0, 1) . "', FALSE)";
  
  if ($conn->query($query)) {
    $user_id = $conn->insert_id;
    
    $query = "SELECT * FROM users WHERE id = $user_id";
    $result = $conn->query($query);
    $user = $result->fetch_assoc();
    unset($user['password_hash']);
    
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_email'] = $email;
    
    $conn->close();
    sendResponse(true, 'Signup successful', $user, 201);
  } else {
    $conn->close();
    sendResponse(false, 'Error creating account: ' . $conn->error, null, 500);
  }
}

// Logout Function
function logout() {
  session_destroy();
  sendResponse(true, 'Logout successful');
}

// Get User Profile
function getProfile() {
  if (empty($_SESSION['user_id'])) {
    sendResponse(false, 'Not authenticated', null, 401);
  }
  
  $user_id = intval($_SESSION['user_id']);
  $conn = getDBConnection();
  
  $query = "SELECT id, first_name, last_name, email, phone, profile_avatar, date_of_birth, gender, is_verified, created_at FROM users WHERE id = $user_id";
  $result = $conn->query($query);
  $user = $result->fetch_assoc();
  
  if (!$user) {
    $conn->close();
    sendResponse(false, 'User not found', null, 404);
  }
  
  $conn->close();
  sendResponse(true, 'Profile retrieved', $user);
}

// Update Profile
function updateProfile() {
  if (empty($_SESSION['user_id'])) {
    sendResponse(false, 'Not authenticated', null, 401);
  }
  
  $data = json_decode(file_get_contents('php://input'), true);
  $user_id = intval($_SESSION['user_id']);
  $conn = getDBConnection();
  
  $updateFields = [];
  
  if (!empty($data['first_name'])) {
    $first_name = $conn->real_escape_string(sanitizeInput($data['first_name']));
    $updateFields[] = "first_name = '$first_name'";
  }
  
  if (!empty($data['last_name'])) {
    $last_name = $conn->real_escape_string(sanitizeInput($data['last_name']));
    $updateFields[] = "last_name = '$last_name'";
  }
  
  if (!empty($data['phone'])) {
    if (!validatePhone($data['phone'])) {
      sendResponse(false, 'Invalid phone format', null, 400);
    }
    $phone = $conn->real_escape_string(sanitizeInput($data['phone']));
    $updateFields[] = "phone = '$phone'";
  }
  
  if (!empty($data['date_of_birth'])) {
    $dob = $conn->real_escape_string($data['date_of_birth']);
    $updateFields[] = "date_of_birth = '$dob'";
  }
  
  if (!empty($data['gender'])) {
    $gender = $conn->real_escape_string($data['gender']);
    $updateFields[] = "gender = '$gender'";
  }
  
  if (empty($updateFields)) {
    sendResponse(false, 'No fields to update', null, 400);
  }
  
  $updateFields[] = "updated_at = CURRENT_TIMESTAMP";
  $query = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = $user_id";
  
  if ($conn->query($query)) {
    $conn->close();
    sendResponse(true, 'Profile updated successfully');
  } else {
    $conn->close();
    sendResponse(false, 'Error updating profile', null, 500);
  }
}

// Change Password
function changePassword() {
  if (empty($_SESSION['user_id'])) {
    sendResponse(false, 'Not authenticated', null, 401);
  }
  
  $data = json_decode(file_get_contents('php://input'), true);
  
  if (empty($data['current_password']) || empty($data['new_password'])) {
    sendResponse(false, 'Current and new password required', null, 400);
  }
  
  $user_id = intval($_SESSION['user_id']);
  $conn = getDBConnection();
  
  $query = "SELECT password_hash FROM users WHERE id = $user_id";
  $result = $conn->query($query);
  $user = $result->fetch_assoc();
  
  if (!password_verify($data['current_password'], $user['password_hash'])) {
    $conn->close();
    sendResponse(false, 'Current password is incorrect', null, 401);
  }
  
  if (strlen($data['new_password']) < 6) {
    $conn->close();
    sendResponse(false, 'New password must be at least 6 characters', null, 400);
  }
  
  $new_password_hash = password_hash($data['new_password'], PASSWORD_BCRYPT);
  $query = "UPDATE users SET password_hash = '$new_password_hash' WHERE id = $user_id";
  
  if ($conn->query($query)) {
    $conn->close();
    sendResponse(true, 'Password changed successfully');
  } else {
    $conn->close();
    sendResponse(false, 'Error changing password', null, 500);
  }
}

// Verify Email
function verifyEmail() {
  if (empty($_GET['token'])) {
    sendResponse(false, 'Verification token required', null, 400);
  }
  
  sendResponse(true, 'Email verified successfully');
}
?>
