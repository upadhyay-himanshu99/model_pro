<?php
/**
 * BANAVI - PRODUCTS API
 * Endpoints for product operations
 */

require_once '../includes/config.php';

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));

// Get action from query string or request path
$action = $_GET['action'] ?? $request[0] ?? '';
$id = $request[1] ?? $_GET['id'] ?? null;

switch ($method) {
  case 'GET':
    if ($action === 'all') {
      getAllProducts();
    } elseif ($action === 'single' && $id) {
      getProductById($id);
    } elseif ($action === 'category') {
      getProductsByCategory($_GET['category'] ?? '');
    } elseif ($action === 'featured') {
      getFeaturedProducts();
    } else {
      getAllProducts();
    }
    break;

  case 'POST':
    if ($action === 'create') {
      createProduct();
    }
    break;

  case 'PUT':
    if ($action === 'update' && $id) {
      updateProduct($id);
    }
    break;

  case 'DELETE':
    if ($action === 'delete' && $id) {
      deleteProduct($id);
    }
    break;

  default:
    sendResponse(false, 'Invalid request method', null, 405);
}

// Get All Products
function getAllProducts() {
  $conn = getDBConnection();
  $category = $_GET['category'] ?? '';
  $limit = intval($_GET['limit'] ?? 12);
  $offset = intval($_GET['offset'] ?? 0);
  
  $query = "SELECT * FROM products WHERE is_active = TRUE";
  
  if (!empty($category)) {
    $category = $conn->real_escape_string($category);
    $query .= " AND category = '$category'";
  }
  
  $query .= " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
  
  $result = $conn->query($query);
  
  if (!$result) {
    sendResponse(false, 'Query failed: ' . $conn->error, null, 500);
  }
  
  $products = [];
  while ($row = $result->fetch_assoc()) {
    $products[] = $row;
  }
  
  $conn->close();
  sendResponse(true, 'Products retrieved successfully', $products);
}

// Get Single Product
function getProductById($id) {
  $id = intval($id);
  $conn = getDBConnection();
  
  $query = "SELECT p.*, 
           GROUP_CONCAT(ps.size ORDER BY ps.size) as sizes
           FROM products p
           LEFT JOIN product_sizes ps ON p.id = ps.product_id
           WHERE p.id = $id AND p.is_active = TRUE
           GROUP BY p.id";
  
  $result = $conn->query($query);
  
  if (!$result) {
    sendResponse(false, 'Query failed', null, 500);
  }
  
  $product = $result->fetch_assoc();
  
  if (!$product) {
    sendResponse(false, 'Product not found', null, 404);
  }
  
  if ($product['sizes']) {
    $product['sizes'] = explode(',', $product['sizes']);
  } else {
    $product['sizes'] = ['Free'];
  }
  
  $conn->close();
  sendResponse(true, 'Product retrieved successfully', $product);
}

// Get Products by Category
function getProductsByCategory($category) {
  $category = !empty($category) ? $_GET['category'] : 'Kurti';
  $conn = getDBConnection();
  $category = $conn->real_escape_string($category);
  
  $query = "SELECT * FROM products WHERE category = '$category' AND is_active = TRUE ORDER BY created_at DESC";
  $result = $conn->query($query);
  
  $products = [];
  while ($row = $result->fetch_assoc()) {
    $products[] = $row;
  }
  
  $conn->close();
  sendResponse(true, 'Category products retrieved', $products);
}

// Get Featured Products
function getFeaturedProducts() {
  $conn = getDBConnection();
  $query = "SELECT * FROM products WHERE is_active = TRUE ORDER BY RAND() LIMIT 8";
  $result = $conn->query($query);
  
  $products = [];
  while ($row = $result->fetch_assoc()) {
    $products[] = $row;
  }
  
  $conn->close();
  sendResponse(true, 'Featured products retrieved', $products);
}

// Create Product (Admin)
function createProduct() {
  $data = json_decode(file_get_contents('php://input'), true);
  
  $required = ['name', 'category', 'price'];
  foreach ($required as $field) {
    if (empty($data[$field])) {
      sendResponse(false, "Missing required field: $field", null, 400);
    }
  }
  
  $conn = getDBConnection();
  
  $name = $conn->real_escape_string($data['name']);
  $category = $conn->real_escape_string($data['category']);
  $price = floatval($data['price']);
  $original_price = !empty($data['original_price']) ? floatval($data['original_price']) : null;
  $description = $conn->real_escape_string($data['description'] ?? '');
  $emoji = $conn->real_escape_string($data['emoji'] ?? '👗');
  $badge = $data['badge'] ?? null;
  $stock = intval($data['stock'] ?? 100);
  
  $query = "INSERT INTO products (name, category, price, original_price, description, emoji, badge, stock) 
           VALUES ('$name', '$category', $price, " . ($original_price ? $original_price : 'NULL') . ", '$description', '$emoji', " . ($badge ? "'$badge'" : 'NULL') . ", $stock)";
  
  if ($conn->query($query)) {
    $product_id = $conn->insert_id;
    
    // Insert sizes if provided
    if (!empty($data['sizes'])) {
      foreach ($data['sizes'] as $size) {
        $size = $conn->real_escape_string($size);
        $conn->query("INSERT INTO product_sizes (product_id, size, stock) VALUES ($product_id, '$size', 50)");
      }
    }
    
    $conn->close();
    sendResponse(true, 'Product created successfully', ['id' => $product_id], 201);
  } else {
    $conn->close();
    sendResponse(false, 'Error creating product: ' . $conn->error, null, 500);
  }
}

// Update Product
function updateProduct($id) {
  $id = intval($id);
  $data = json_decode(file_get_contents('php://input'), true);
  $conn = getDBConnection();
  
  $updateFields = [];
  if (!empty($data['name'])) $updateFields[] = "name = '" . $conn->real_escape_string($data['name']) . "'";
  if (!empty($data['price'])) $updateFields[] = "price = " . floatval($data['price']);
  if (!empty($data['stock'])) $updateFields[] = "stock = " . intval($data['stock']);
  
  if (empty($updateFields)) {
    sendResponse(false, 'No fields to update', null, 400);
  }
  
  $query = "UPDATE products SET " . implode(', ', $updateFields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = $id";
  
  if ($conn->query($query)) {
    $conn->close();
    sendResponse(true, 'Product updated successfully');
  } else {
    $conn->close();
    sendResponse(false, 'Error updating product', null, 500);
  }
}

// Delete Product
function deleteProduct($id) {
  $id = intval($id);
  $conn = getDBConnection();
  
  $query = "DELETE FROM products WHERE id = $id";
  
  if ($conn->query($query)) {
    $conn->close();
    sendResponse(true, 'Product deleted successfully');
  } else {
    $conn->close();
    sendResponse(false, 'Error deleting product', null, 500);
  }
}
?>
