<?php
class Product {
    private $conn;
    private $table = 'products';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all products for a user
    public function getAll($user_id, $filters = []) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id";

        if (isset($filters['status'])) {
            $query .= " AND status = :status";
        }

        if (isset($filters['category'])) {
            $query .= " AND category = :category";
        }

        if (isset($filters['search'])) {
            $query .= " AND (name LIKE :search OR description LIKE :search OR sku LIKE :search)";
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);

        if (isset($filters['status'])) {
            $stmt->bindParam(':status', $filters['status']);
        }

        if (isset($filters['category'])) {
            $stmt->bindParam(':category', $filters['category']);
        }

        if (isset($filters['search'])) {
            $search = "%{$filters['search']}%";
            $stmt->bindParam(':search', $search);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get product by ID
    public function getById($id, $user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id AND user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Create product
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, name, description, category, price, cost, stock_quantity, sku, status) 
                  VALUES (:user_id, :name, :description, :category, :price, :cost, :stock_quantity, :sku, :status)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':cost', $data['cost']);
        $stmt->bindParam(':stock_quantity', $data['stock_quantity']);
        $stmt->bindParam(':sku', $data['sku']);
        $stmt->bindParam(':status', $data['status']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    // Update product
    public function update($id, $user_id, $data) {
        $fields = [];
        $params = [':id' => $id, ':user_id' => $user_id];

        $allowed = ['name', 'description', 'category', 'price', 'cost', 'stock_quantity', 'sku', 'status'];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $fields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }

        if (empty($fields)) {
            return false;
        }

        $query = "UPDATE " . $this->table . " SET " . implode(', ', $fields) . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute($params);
    }

    // Delete product
    public function delete($id, $user_id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }

    // Update stock quantity
    public function updateStock($id, $quantity) {
        $query = "UPDATE " . $this->table . " SET stock_quantity = stock_quantity + :quantity WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':quantity', $quantity);

        return $stmt->execute();
    }
}
