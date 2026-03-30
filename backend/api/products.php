<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';

$database = new Database();
$db = $database->connect();
$product = new Product($db);
$auth = new Auth();
$method = $_SERVER['REQUEST_METHOD'];

// Validate authentication
$token_data = $auth->validateRequest();
if (!$token_data) {
    Response::unauthorized();
}

$user_id = $token_data['user_id'];

if ($method === 'GET') {
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if ($id) {
        // Get single product
        $result = $product->getById($id, $user_id);
        
        if (!$result) {
            Response::notFound('Product not found');
        }

        Response::success($result);
    } else {
        // Get all products
        $filters = [
            'status' => $_GET['status'] ?? null,
            'category' => $_GET['category'] ?? null,
            'search' => $_GET['search'] ?? null
        ];

        $filters = array_filter($filters);
        $results = $product->getAll($user_id, $filters);

        Response::success($results);
    }

} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Validation
    $validator = new Validator();
    $validator->required('name', $data['name'] ?? '', 'Product name');
    $validator->required('price', $data['price'] ?? '', 'Price');
    $validator->numeric('price', $data['price'] ?? '', 'Price');
    $validator->min('price', $data['price'] ?? 0, 0, 'Price');

    if ($validator->fails()) {
        Response::validationError($validator->getErrors());
    }

    $product_data = [
        'user_id' => $user_id,
        'name' => $data['name'],
        'description' => $data['description'] ?? null,
        'category' => $data['category'] ?? null,
        'price' => $data['price'],
        'cost' => $data['cost'] ?? 0,
        'stock_quantity' => $data['stock_quantity'] ?? 0,
        'sku' => $data['sku'] ?? null,
        'status' => $data['status'] ?? 'active'
    ];

    $product_id = $product->create($product_data);

    if ($product_id) {
        $new_product = $product->getById($product_id, $user_id);
        Response::success($new_product, 'Product created successfully', 201);
    } else {
        Response::serverError('Failed to create product');
    }

} elseif ($method === 'PUT') {
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if (!$id) {
        Response::error('Product ID is required', 400);
    }

    $data = json_decode(file_get_contents("php://input"), true);

    // Check if product exists
    $existing = $product->getById($id, $user_id);
    if (!$existing) {
        Response::notFound('Product not found');
    }

    // Validation
    if (isset($data['price'])) {
        $validator = new Validator();
        $validator->numeric('price', $data['price'], 'Price');
        $validator->min('price', $data['price'], 0, 'Price');

        if ($validator->fails()) {
            Response::validationError($validator->getErrors());
        }
    }

    $result = $product->update($id, $user_id, $data);

    if ($result) {
        $updated = $product->getById($id, $user_id);
        Response::success($updated, 'Product updated successfully');
    } else {
        Response::serverError('Failed to update product');
    }

} elseif ($method === 'DELETE') {
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if (!$id) {
        Response::error('Product ID is required', 400);
    }

    // Check if product exists
    $existing = $product->getById($id, $user_id);
    if (!$existing) {
        Response::notFound('Product not found');
    }

    $result = $product->delete($id, $user_id);

    if ($result) {
        Response::success(null, 'Product deleted successfully');
    } else {
        Response::serverError('Failed to delete product');
    }

} else {
    Response::error('Method not allowed', 405);
}
