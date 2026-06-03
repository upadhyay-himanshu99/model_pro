<?php
/**
 * BANAVI - ORDERS API
 * Endpoints for order operations
 */

require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));

$action = $_GET['action'] ?? $request[0] ?? '';
$id = $request[1] ?? $_GET['id'] ?? null;

switch ($method) {
  case 'GET':
    if ($action === 'list') {
      getOrderList();
    } elseif ($action === 'single' && $id) {
      getOrderDetails($id);
    } elseif ($action === 'user') {
      getUserOrders();
    } else {
      getOrderList();
    }
    break;

  case 'POST':
    if ($action === 'create') {
      createOrder();
    }
    break;

  case 'PUT':
    if ($action === 'update' && $id) {
      updateOrder($id);
    }
    break;

  default:
    sendResponse(false, 'Invalid request method', null, 405);
}

// Get Order List
function getOrderList() {
  $conn = getDBConnection();
  $limit = intval($_GET['limit'] ?? 20);
  $offset = intval($_GET['offset'] ?? 0);
  
  $query = "SELECT * FROM orders ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
  $result = $conn->query($query);
  
  $orders = [];
  while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
  }
  
  $conn->close();
  sendResponse(true, 'Orders retrieved', $orders);
}

// Get User Orders
function getUserOrders() {
  $user_id = intval($_GET['user_id'] ?? 0);
  
  if ($user_id <= 0) {
    sendResponse(false, 'Invalid user ID', null, 400);
  }
  
  $conn = getDBConnection();
  $query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC";
  $result = $conn->query($query);
  
  $orders = [];
  while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
  }
  
  $conn->close();
  sendResponse(true, 'User orders retrieved', $orders);
}

// Get Order Details
function getOrderDetails($order_id) {
  $order_id = intval($order_id);
  $conn = getDBConnection();
  
  // Get order info
  $query = "SELECT * FROM orders WHERE id = $order_id";
  $result = $conn->query($query);
  $order = $result->fetch_assoc();
  
  if (!$order) {
    sendResponse(false, 'Order not found', null, 404);
  }
  
  // Get order items
  $query = "SELECT * FROM order_items WHERE order_id = $order_id";
  $result = $conn->query($query);
  $items = [];
  while ($row = $result->fetch_assoc()) {
    $items[] = $row;
  }
  
  $order['items'] = $items;
  
  $conn->close();
  sendResponse(true, 'Order details retrieved', $order);
}

// Create Order
function createOrder() {
  $data = json_decode(file_get_contents('php://input'), true);
  
  $required = ['customer_name', 'customer_email', 'customer_phone', 'shipping_address', 'city', 'pincode', 'items', 'total_amount', 'payment_method'];
  foreach ($required as $field) {
    if (empty($data[$field])) {
      sendResponse(false, "Missing required field: $field", null, 400);
    }
  }
  
  if (empty($data['items']) || !is_array($data['items'])) {
    sendResponse(false, 'Invalid items data', null, 400);
  }
  
  $conn = getDBConnection();
  
  // Generate order number
  $order_number = 'BNV-' . date('YmdHis') . rand(1000, 9999);
  
  $customer_name = $conn->real_escape_string($data['customer_name']);
  $customer_email = $conn->real_escape_string($data['customer_email']);
  $customer_phone = $conn->real_escape_string($data['customer_phone']);
  $shipping_address = $conn->real_escape_string($data['shipping_address']);
  $city = $conn->real_escape_string($data['city']);
  $state = $conn->real_escape_string($data['state'] ?? '');
  $pincode = $conn->real_escape_string($data['pincode']);
  $total_amount = floatval($data['total_amount']);
  $discount_amount = floatval($data['discount_amount'] ?? 0);
  $final_amount = floatval($data['final_amount'] ?? $total_amount);
  $payment_method = $conn->real_escape_string($data['payment_method']);
  $user_id = !empty($data['user_id']) ? intval($data['user_id']) : null;
  
  $query = "INSERT INTO orders (order_number, user_id, customer_name, customer_email, customer_phone, shipping_address, city, state, pincode, total_amount, discount_amount, final_amount, payment_method, payment_status, order_status)
           VALUES ('$order_number', " . ($user_id ? $user_id : 'NULL') . ", '$customer_name', '$customer_email', '$customer_phone', '$shipping_address', '$city', '$state', '$pincode', $total_amount, $discount_amount, $final_amount, '$payment_method', 'Pending', 'Processing')";
  
  if ($conn->query($query)) {
    $order_id = $conn->insert_id;
    
    // Insert order items
    foreach ($data['items'] as $item) {
      $product_id = intval($item['id'] ?? 0);
      $product_name = $conn->real_escape_string($item['name'] ?? '');
      $quantity = intval($item['qty'] ?? 1);
      $size = $conn->real_escape_string($item['size'] ?? 'Free');
      $price = floatval($item['price'] ?? 0);
      $item_total = $price * $quantity;
      
      $conn->query("INSERT INTO order_items (order_id, product_id, product_name, quantity, size, price, total) 
                  VALUES ($order_id, $product_id, '$product_name', $quantity, '$size', $price, $item_total)");
    }
    
    $conn->close();
    sendResponse(true, 'Order created successfully', ['order_id' => $order_id, 'order_number' => $order_number], 201);
  } else {
    $conn->close();
    sendResponse(false, 'Error creating order: ' . $conn->error, null, 500);
  }
}

// Update Order Status
function updateOrder($order_id) {
  $order_id = intval($order_id);
  $data = json_decode(file_get_contents('php://input'), true);
  $conn = getDBConnection();
  
  $updateFields = [];
  
  if (!empty($data['order_status'])) {
    $status = $conn->real_escape_string($data['order_status']);
    $updateFields[] = "order_status = '$status'";
  }
  
  if (!empty($data['payment_status'])) {
    $status = $conn->real_escape_string($data['payment_status']);
    $updateFields[] = "payment_status = '$status'";
  }
  
  if (!empty($data['notes'])) {
    $notes = $conn->real_escape_string($data['notes']);
    $updateFields[] = "notes = '$notes'";
  }
  
  if (empty($updateFields)) {
    sendResponse(false, 'No fields to update', null, 400);
  }
  
  $updateFields[] = "updated_at = CURRENT_TIMESTAMP";
  $query = "UPDATE orders SET " . implode(', ', $updateFields) . " WHERE id = $order_id";
  
  if ($conn->query($query)) {
    $conn->close();
    sendResponse(true, 'Order updated successfully');
  } else {
    $conn->close();
    sendResponse(false, 'Error updating order', null, 500);
  }
}
?>
