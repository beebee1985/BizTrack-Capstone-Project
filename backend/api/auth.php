<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);
$auth = new Auth();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $action = isset($_GET['action']) ? $_GET['action'] : 'login';

    if ($action === 'register') {
        // Registration
        $validator = new Validator();
        
        $validator->required('username', $data['username'] ?? '', 'Username');
        $validator->minLength('username', $data['username'] ?? '', 3, 'Username');
        
        $validator->required('email', $data['email'] ?? '', 'Email');
        $validator->email('email', $data['email'] ?? '', 'Email');
        
        $validator->required('password', $data['password'] ?? '', 'Password');
        $validator->minLength('password', $data['password'] ?? '', 6, 'Password');
        
        $validator->required('full_name', $data['full_name'] ?? '', 'Full name');

        if ($validator->fails()) {
            Response::validationError($validator->getErrors());
        }

        // Check if email already exists
        if ($user->emailExists($data['email'])) {
            Response::error('Email already exists', 400);
        }

        // Check if username already exists
        if ($user->usernameExists($data['username'])) {
            Response::error('Username already exists', 400);
        }

        // Hash password
        $hashed_password = $auth->hashPassword($data['password']);
        
        // Create user
        $user_id = $user->create(
            $data['username'],
            $data['email'],
            $hashed_password,
            $data['full_name'],
            $data['role'] ?? 'owner'
        );

        if ($user_id) {
            $token = $auth->generateToken($user_id, $data['username'], $data['role'] ?? 'owner');
            
            Response::success([
                'token' => $token,
                'user' => [
                    'id' => $user_id,
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'full_name' => $data['full_name'],
                    'role' => $data['role'] ?? 'owner'
                ]
            ], 'Registration successful', 201);
        } else {
            Response::serverError('Failed to create user');
        }

    } else {
        // Login
        $validator = new Validator();
        
        $validator->required('email', $data['email'] ?? '', 'Email');
        $validator->required('password', $data['password'] ?? '', 'Password');

        if ($validator->fails()) {
            Response::validationError($validator->getErrors());
        }

        $existing_user = $user->getByEmail($data['email']);

        if (!$existing_user) {
            Response::error('Invalid credentials', 401);
        }

        if (!$auth->verifyPassword($data['password'], $existing_user['password'])) {
            Response::error('Invalid credentials', 401);
        }

        $token = $auth->generateToken(
            $existing_user['id'],
            $existing_user['username'],
            $existing_user['role']
        );

        Response::success([
            'token' => $token,
            'user' => [
                'id' => $existing_user['id'],
                'username' => $existing_user['username'],
                'email' => $existing_user['email'],
                'full_name' => $existing_user['full_name'],
                'role' => $existing_user['role']
            ]
        ], 'Login successful');
    }

} elseif ($method === 'GET') {
    // Get current user
    $token_data = $auth->validateRequest();

    if (!$token_data) {
        Response::unauthorized();
    }

    $current_user = $user->getById($token_data['user_id']);

    if (!$current_user) {
        Response::notFound('User not found');
    }

    Response::success($current_user);

} else {
    Response::error('Method not allowed', 405);
}
