<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../utils/Auth.php';
require_once __DIR__ . '/../utils/Response.php';

$database = new Database();
$db = $database->connect();
$auth = new Auth();

// Validate authentication
$token_data = $auth->validateRequest();
if (!$token_data) {
    Response::unauthorized();
}

$user_id = $token_data['user_id'];

// Get date range from query params, default to current month
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-t');

// Get total sales
$sales_query = "SELECT 
    COUNT(*) as total_sales,
    COALESCE(SUM(total_amount), 0) as total_revenue,
    COALESCE(AVG(total_amount), 0) as average_sale
FROM sales 
WHERE user_id = :user_id 
AND status = 'completed'
AND sale_date BETWEEN :date_from AND :date_to";

$stmt = $db->prepare($sales_query);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':date_from', $date_from);
$stmt->bindParam(':date_to', $date_to);
$stmt->execute();
$sales_stats = $stmt->fetch();

// Get total expenses
$expenses_query = "SELECT 
    COUNT(*) as total_expenses,
    COALESCE(SUM(amount), 0) as total_amount
FROM expenses 
WHERE user_id = :user_id 
AND expense_date BETWEEN :date_from AND :date_to";

$stmt = $db->prepare($expenses_query);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':date_from', $date_from);
$stmt->bindParam(':date_to', $date_to);
$stmt->execute();
$expense_stats = $stmt->fetch();

// Calculate profit
$profit = $sales_stats['total_revenue'] - $expense_stats['total_amount'];

// Get sales by date for chart
$sales_by_date_query = "SELECT 
    sale_date as date,
    COUNT(*) as count,
    SUM(total_amount) as total
FROM sales 
WHERE user_id = :user_id 
AND status = 'completed'
AND sale_date BETWEEN :date_from AND :date_to
GROUP BY sale_date
ORDER BY sale_date ASC";

$stmt = $db->prepare($sales_by_date_query);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':date_from', $date_from);
$stmt->bindParam(':date_to', $date_to);
$stmt->execute();
$sales_by_date = $stmt->fetchAll();

// Get expenses by category
$expenses_by_category_query = "SELECT 
    category,
    COUNT(*) as count,
    SUM(amount) as total
FROM expenses 
WHERE user_id = :user_id 
AND expense_date BETWEEN :date_from AND :date_to
GROUP BY category
ORDER BY total DESC";

$stmt = $db->prepare($expenses_by_category_query);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':date_from', $date_from);
$stmt->bindParam(':date_to', $date_to);
$stmt->execute();
$expenses_by_category = $stmt->fetchAll();

// Get top selling products
$top_products_query = "SELECT 
    p.name,
    SUM(si.quantity) as total_quantity,
    SUM(si.subtotal) as total_revenue
FROM sale_items si
JOIN products p ON si.product_id = p.id
JOIN sales s ON si.sale_id = s.id
WHERE s.user_id = :user_id 
AND s.status = 'completed'
AND s.sale_date BETWEEN :date_from AND :date_to
GROUP BY si.product_id
ORDER BY total_revenue DESC
LIMIT 5";

$stmt = $db->prepare($top_products_query);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':date_from', $date_from);
$stmt->bindParam(':date_to', $date_to);
$stmt->execute();
$top_products = $stmt->fetchAll();

// Get customer count
$customer_count_query = "SELECT COUNT(*) as total FROM customers WHERE user_id = :user_id";
$stmt = $db->prepare($customer_count_query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$customer_count = $stmt->fetch();

// Get product count
$product_count_query = "SELECT COUNT(*) as total FROM products WHERE user_id = :user_id AND status = 'active'";
$stmt = $db->prepare($product_count_query);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$product_count = $stmt->fetch();

$dashboard_data = [
    'summary' => [
        'total_revenue' => floatval($sales_stats['total_revenue']),
        'total_expenses' => floatval($expense_stats['total_amount']),
        'profit' => floatval($profit),
        'total_sales' => intval($sales_stats['total_sales']),
        'average_sale' => floatval($sales_stats['average_sale']),
        'total_customers' => intval($customer_count['total']),
        'total_products' => intval($product_count['total'])
    ],
    'charts' => [
        'sales_by_date' => $sales_by_date,
        'expenses_by_category' => $expenses_by_category,
        'top_products' => $top_products
    ],
    'period' => [
        'from' => $date_from,
        'to' => $date_to
    ]
];

Response::success($dashboard_data);
