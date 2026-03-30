<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';

$database = new Database();
$db = $database->connect();
$sale = new Sale($db);
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
        // Get single sale
        $result = $sale->getById($id, $user_id);
        
        if (!$result) {
            Response::notFound('Sale not found');
        }

        Response::success($result);
    } else {
        // Get all sales
        $filters = [
            'status' => $_GET['status'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null
        ];

        $filters = array_filter($filters);
        $results = $sale->getAll($user_id, $filters);

        Response::success($results);
    }

} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Validation
    $validator = new Validator();
    $validator->required('sale_date', $data['sale_date'] ?? '', 'Sale date');
    $validator->date('sale_date', $data['sale_date'] ?? '', 'Sale date');
    $validator->required('total_amount', $data['total_amount'] ?? '', 'Total amount');
    $validator->numeric('total_amount', $data['total_amount'] ?? '', 'Total amount');
    $validator->min('total_amount', $data['total_amount'] ?? 0, 0, 'Total amount');

    if (!isset($data['items']) || !is_array($data['items']) || empty($data['items'])) {
        $validator->required('items', '', 'Sale items');
    }

    if ($validator->fails()) {
        Response::validationError($validator->getErrors());
    }

    $sale_data = [
        'user_id' => $user_id,
        'customer_id' => $data['customer_id'] ?? null,
        'sale_date' => $data['sale_date'],
        'total_amount' => $data['total_amount'],
        'discount' => $data['discount'] ?? 0,
        'tax' => $data['tax'] ?? 0,
        'payment_method' => $data['payment_method'] ?? 'cash',
        'status' => $data['status'] ?? 'completed',
        'notes' => $data['notes'] ?? null
    ];

    $sale_id = $sale->create($sale_data, $data['items']);

    if ($sale_id) {
        // Update product stock quantities
        foreach ($data['items'] as $item) {
            if (isset($item['product_id']) && $item['product_id']) {
                $product->updateStock($item['product_id'], -$item['quantity']);
            }
        }

        $new_sale = $sale->getById($sale_id, $user_id);
        Response::success($new_sale, 'Sale created successfully', 201);
    } else {
        Response::serverError('Failed to create sale');
    }

} elseif ($method === 'PUT') {
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if (!$id) {
        Response::error('Sale ID is required', 400);
    }

    $data = json_decode(file_get_contents("php://input"), true);

    // Check if sale exists
    $existing = $sale->getById($id, $user_id);
    if (!$existing) {
        Response::notFound('Sale not found');
    }

    // Only allow status updates
    if (!isset($data['status'])) {
        Response::error('Status is required', 400);
    }

    $result = $sale->updateStatus($id, $user_id, $data['status']);

    if ($result) {
        $updated = $sale->getById($id, $user_id);
        Response::success($updated, 'Sale updated successfully');
    } else {
        Response::serverError('Failed to update sale');
    }

} elseif ($method === 'DELETE') {
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if (!$id) {
        Response::error('Sale ID is required', 400);
    }

    // Check if sale exists
    $existing = $sale->getById($id, $user_id);
    if (!$existing) {
        Response::notFound('Sale not found');
    }

    $result = $sale->delete($id, $user_id);

    if ($result) {
        Response::success(null, 'Sale deleted successfully');
    } else {
        Response::serverError('Failed to delete sale');
    }

} else {
    Response::error('Method not allowed', 405);
}
