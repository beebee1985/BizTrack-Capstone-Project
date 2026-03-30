<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';

$database = new Database();
$db = $database->connect();
$customer = new Customer($db);
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
        // Get single customer
        $result = $customer->getById($id, $user_id);
        
        if (!$result) {
            Response::notFound('Customer not found');
        }

        Response::success($result);
    } else {
        // Get all customers
        $filters = [
            'search' => $_GET['search'] ?? null
        ];

        $filters = array_filter($filters);
        $results = $customer->getAll($user_id, $filters);

        Response::success($results);
    }

} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Validation
    $validator = new Validator();
    $validator->required('name', $data['name'] ?? '', 'Customer name');
    
    if (isset($data['email']) && !empty($data['email'])) {
        $validator->email('email', $data['email'], 'Email');
    }

    if ($validator->fails()) {
        Response::validationError($validator->getErrors());
    }

    $customer_data = [
        'user_id' => $user_id,
        'name' => $data['name'],
        'email' => $data['email'] ?? null,
        'phone' => $data['phone'] ?? null,
        'address' => $data['address'] ?? null,
        'city' => $data['city'] ?? null,
        'state' => $data['state'] ?? null,
        'zip_code' => $data['zip_code'] ?? null,
        'notes' => $data['notes'] ?? null
    ];

    $customer_id = $customer->create($customer_data);

    if ($customer_id) {
        $new_customer = $customer->getById($customer_id, $user_id);
        Response::success($new_customer, 'Customer created successfully', 201);
    } else {
        Response::serverError('Failed to create customer');
    }

} elseif ($method === 'PUT') {
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if (!$id) {
        Response::error('Customer ID is required', 400);
    }

    $data = json_decode(file_get_contents("php://input"), true);

    // Check if customer exists
    $existing = $customer->getById($id, $user_id);
    if (!$existing) {
        Response::notFound('Customer not found');
    }

    // Validation
    if (isset($data['email']) && !empty($data['email'])) {
        $validator = new Validator();
        $validator->email('email', $data['email'], 'Email');

        if ($validator->fails()) {
            Response::validationError($validator->getErrors());
        }
    }

    $result = $customer->update($id, $user_id, $data);

    if ($result) {
        $updated = $customer->getById($id, $user_id);
        Response::success($updated, 'Customer updated successfully');
    } else {
        Response::serverError('Failed to update customer');
    }

} elseif ($method === 'DELETE') {
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if (!$id) {
        Response::error('Customer ID is required', 400);
    }

    // Check if customer exists
    $existing = $customer->getById($id, $user_id);
    if (!$existing) {
        Response::notFound('Customer not found');
    }

    $result = $customer->delete($id, $user_id);

    if ($result) {
        Response::success(null, 'Customer deleted successfully');
    } else {
        Response::serverError('Failed to delete customer');
    }

} else {
    Response::error('Method not allowed', 405);
}
