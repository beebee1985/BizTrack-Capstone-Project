<?php
class Sale {
    private $conn;
    private $table = 'sales';
    private $items_table = 'sale_items';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all sales for a user
    public function getAll($user_id, $filters = []) {
        $query = "SELECT s.*, c.name as customer_name 
                  FROM " . $this->table . " s
                  LEFT JOIN customers c ON s.customer_id = c.id
                  WHERE s.user_id = :user_id";

        if (isset($filters['status'])) {
            $query .= " AND s.status = :status";
        }

        if (isset($filters['date_from'])) {
            $query .= " AND s.sale_date >= :date_from";
        }

        if (isset($filters['date_to'])) {
            $query .= " AND s.sale_date <= :date_to";
        }

        $query .= " ORDER BY s.sale_date DESC, s.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);

        if (isset($filters['status'])) {
            $stmt->bindParam(':status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $stmt->bindParam(':date_from', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $stmt->bindParam(':date_to', $filters['date_to']);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get sale by ID with items
    public function getById($id, $user_id) {
        $query = "SELECT s.*, c.name as customer_name 
                  FROM " . $this->table . " s
                  LEFT JOIN customers c ON s.customer_id = c.id
                  WHERE s.id = :id AND s.user_id = :user_id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        $sale = $stmt->fetch();

        if ($sale) {
            $sale['items'] = $this->getSaleItems($id);
        }

        return $sale;
    }

    // Get sale items
    public function getSaleItems($sale_id) {
        $query = "SELECT * FROM " . $this->items_table . " WHERE sale_id = :sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $sale_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Create sale
    public function create($data, $items) {
        try {
            $this->conn->beginTransaction();

            // Insert sale
            $query = "INSERT INTO " . $this->table . " 
                      (user_id, customer_id, sale_date, total_amount, discount, tax, payment_method, status, notes) 
                      VALUES (:user_id, :customer_id, :sale_date, :total_amount, :discount, :tax, :payment_method, :status, :notes)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':customer_id', $data['customer_id']);
            $stmt->bindParam(':sale_date', $data['sale_date']);
            $stmt->bindParam(':total_amount', $data['total_amount']);
            $stmt->bindParam(':discount', $data['discount']);
            $stmt->bindParam(':tax', $data['tax']);
            $stmt->bindParam(':payment_method', $data['payment_method']);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':notes', $data['notes']);

            $stmt->execute();
            $sale_id = $this->conn->lastInsertId();

            // Insert sale items
            foreach ($items as $item) {
                $this->createSaleItem($sale_id, $item);
            }

            $this->conn->commit();
            return $sale_id;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Create sale item
    private function createSaleItem($sale_id, $item) {
        $query = "INSERT INTO " . $this->items_table . " 
                  (sale_id, product_id, product_name, quantity, unit_price, subtotal) 
                  VALUES (:sale_id, :product_id, :product_name, :quantity, :unit_price, :subtotal)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $sale_id);
        $stmt->bindParam(':product_id', $item['product_id']);
        $stmt->bindParam(':product_name', $item['product_name']);
        $stmt->bindParam(':quantity', $item['quantity']);
        $stmt->bindParam(':unit_price', $item['unit_price']);
        $stmt->bindParam(':subtotal', $item['subtotal']);

        return $stmt->execute();
    }

    // Update sale status
    public function updateStatus($id, $user_id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':status', $status);

        return $stmt->execute();
    }

    // Delete sale
    public function delete($id, $user_id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }
}
