<?php
class Response {
    
    // Send JSON response
    public static function json($data, $status_code = 200) {
        http_response_code($status_code);
        echo json_encode($data);
        exit();
    }

    // Send success response
    public static function success($data = null, $message = "Success", $status_code = 200) {
        self::json([
            "success" => true,
            "message" => $message,
            "data" => $data
        ], $status_code);
    }

    // Send error response
    public static function error($message = "Error", $status_code = 400, $errors = null) {
        self::json([
            "success" => false,
            "message" => $message,
            "errors" => $errors
        ], $status_code);
    }

    // Unauthorized
    public static function unauthorized($message = "Unauthorized") {
        self::error($message, 401);
    }

    // Forbidden
    public static function forbidden($message = "Forbidden") {
        self::error($message, 403);
    }

    // Not found
    public static function notFound($message = "Resource not found") {
        self::error($message, 404);
    }

    // Validation error
    public static function validationError($errors, $message = "Validation failed") {
        self::error($message, 422, $errors);
    }

    // Server error
    public static function serverError($message = "Internal server error") {
        self::error($message, 500);
    }
}
