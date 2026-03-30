<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../models/Expense.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';

$database = new Database();
$db = $database->connect();
$expense = new Expense($db);
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
    $action = isset($_GET['action']) ? $_GET['action'] : null;

    if ($action === 'categories') {
        // Get expense categories
        $categories = $expense->getCategories($user_id);
        Response::success($categories);
    }
    elseif ($id) {
        // Get single expense
        $result = $expense->getById($id, $user_id);
        
        if (!$result) {
            Response::notFound('Expense not found');
        }

        Response::success($result);
    } else {
        // Get all expenses
        $filters = [
            'category' => $_GET['category'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
            'search' => $_GET['search'] ?? null
        ];

        $filters = array_filter($filters);
        $results = $expense->getAll($user_id, $filters);

        Response::success($results);
    }

} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Validation
    $validator = new Validator();
    $validator->required('category', $data['category'] ?? '', 'Category');
    $validator->required('description', $data['description'] ?? '', 'Description');
    $validator->required('amount', $data['amount'] ?? '', 'Amount');
    $validator->numeric('amount', $data['amount'] ?? '', 'Amount');
    $validator->min('amount', $data['amount'] ?? 0, 0, 'Amount');
    $validator->required('expense_date', $data['expense_date'] ?? '', 'Expense date');
    $validator->date('expense_date', $data['expense_date'] ?? '', 'Expense date');

    if ($validator->fails()) {
        Response::validationError($validator->getErrors());
    }

    $expense_data = [
        'user_id' => $user_id,
        'category' => $data['category'],
        'description' => $data['description'],
        'amount' => $data['amount'],
        'expense_date' => $data['expense_date'],
        'payment_method' => $data['payment_method'] ?? 'cash',
        'receipt_url' => $data['receipt_url'] ?? null,
        'notes' => $data['notes'] ?? null
    ];

    $expense_id = $expense->create($expense_data);

    if ($expense_id) {
        $new_expense = $expense->getById($expense_id, $user_id);
        Response::success($new_expense, 'Expense created successfully', 201);
    } else {
        Response::serverError('Failed to create expense');
    }

} elseif ($method === 'PUT') {
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if (!$id) {
        Response::error('Expense ID is required', 400);
    }

    $data = json_decode(file_get_contents("php://input"), true);

    // Check if expense exists
    $existing = $expense->getById($id, $user_id);
    if (!$existing) {
        Response::notFound('Expense not found');
    }

    // Validation
    if (isset($data['amount'])) {
        $validator = new Validator();
        $validator->numeric('amount', $data['amount'], 'Amount');
        $validator->min('amount', $data['amount'], 0, 'Amount');

        if ($validator->fails()) {
            Response::validationError($validator->getErrors());
        }
    }

    if (isset($data['expense_date'])) {
        $validator = new Validator();
        $validator->date('expense_date', $data['expense_date'], 'Expense date');

        if ($validator->fails()) {
            Response::validationError($validator->getErrors());
        }
    }

    $result = $expense->update($id, $user_id, $data);

    if ($result) {
        $updated = $expense->getById($id, $user_id);
        Response::success($updated, 'Expense updated successfully');
    } else {
        Response::serverError('Failed to update expense');
    }

} elseif ($method === 'DELETE') {
    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if (!$id) {
        Response::error('Expense ID is required', 400);
    }

    // Check if expense exists
    $existing = $expense->getById($id, $user_id);
    if (!$existing) {
        Response::notFound('Expense not found');
    }

    $result = $expense->delete($id, $user_id);

    if ($result) {
        Response::success(null, 'Expense deleted successfully');
    } else {
        Response::serverError('Failed to delete expense');
    }

} else {
    Response::error('Method not allowed', 405);
}
