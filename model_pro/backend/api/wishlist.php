<?php
/**
 * BANAVI - WISHLIST API
 * Endpoints for wishlist operations
 */

require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
  case 'GET':
    if ($action === 'list') {
      getWishlist();
    } elseif ($action === 'check') {
      checkWishlist();
    }
    break;

  case 'POST':
    if ($action === 'add') {
      addToWishlist();
    }
    break;

  case 'DELETE':
    if ($action === 'remove') {
      removeFromWishlist();
    }
    break;

  default:
    sendResponse(false, 'Invalid request method', null, 405);
}

// Get Wishlist for User
function getWishlist() {
  if (empty($_SESSION['user_id'])) {
    sendResponse(false, 'Not authenticated', null, 401);
  }
  
  $user_id = intval($_SESSION['user_id']);
  $conn = getDBConnection();
  
  $query = "SELECT p.* FROM products p
           INNER JOIN wishlist w ON p.id = w.product_id
           WHERE w.user_id = $user_id AND p.is_active = TRUE
           ORDER BY w.added_at DESC";
  
  $result = $conn->query($query);
  $wishlist = [];
  
  while ($row = $result->fetch_assoc()) {
    $wishlist[] = $row;
  }
  
  $conn->close();
  sendResponse(true, 'Wishlist retrieved', $wishlist);
}

// Check if Product in Wishlist
function checkWishlist() {
  if (empty($_SESSION['user_id']) || empty($_GET['product_id'])) {
    sendResponse(false, 'Invalid request', null, 400);
  }
  
  $user_id = intval($_SESSION['user_id']);
  $product_id = intval($_GET['product_id']);
  $conn = getDBConnection();
  
  $query = "SELECT id FROM wishlist WHERE user_id = $user_id AND product_id = $product_id LIMIT 1";
  $result = $conn->query($query);
  
  $in_wishlist = $result->num_rows > 0;
  
  $conn->close();
  sendResponse(true, 'Wishlist check completed', ['in_wishlist' => $in_wishlist]);
}

// Add to Wishlist
function addToWishlist() {
  if (empty($_SESSION['user_id'])) {
    sendResponse(false, 'Not authenticated', null, 401);
  }
  
  $data = json_decode(file_get_contents('php://input'), true);
  
  if (empty($data['product_id'])) {
    sendResponse(false, 'Product ID required', null, 400);
  }
  
  $user_id = intval($_SESSION['user_id']);
  $product_id = intval($data['product_id']);
  $conn = getDBConnection();
  
  // Check if already in wishlist
  $query = "SELECT id FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
  $result = $conn->query($query);
  
  if ($result->num_rows > 0) {
    $conn->close();
    sendResponse(false, 'Product already in wishlist', null, 409);
  }
  
  // Add to wishlist
  $query = "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)";
  
  if ($conn->query($query)) {
    $conn->close();
    sendResponse(true, 'Added to wishlist', null, 201);
  } else {
    $conn->close();
    sendResponse(false, 'Error adding to wishlist', null, 500);
  }
}

// Remove from Wishlist
function removeFromWishlist() {
  if (empty($_SESSION['user_id']) || empty($_GET['product_id'])) {
    sendResponse(false, 'Invalid request', null, 400);
  }
  
  $user_id = intval($_SESSION['user_id']);
  $product_id = intval($_GET['product_id']);
  $conn = getDBConnection();
  
  $query = "DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id";
  
  if ($conn->query($query)) {
    $conn->close();
    sendResponse(true, 'Removed from wishlist');
  } else {
    $conn->close();
    sendResponse(false, 'Error removing from wishlist', null, 500);
  }
}
?>
