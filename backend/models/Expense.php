<?php
class Expense {
    private $conn;
    private $table = 'expenses';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all expenses for a user
    public function getAll($user_id, $filters = []) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id";

        if (isset($filters['category'])) {
            $query .= " AND category = :category";
        }

        if (isset($filters['date_from'])) {
            $query .= " AND expense_date >= :date_from";
        }

        if (isset($filters['date_to'])) {
            $query .= " AND expense_date <= :date_to";
        }

        if (isset($filters['search'])) {
            $query .= " AND (description LIKE :search OR category LIKE :search)";
        }

        $query .= " ORDER BY expense_date DESC, created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);

        if (isset($filters['category'])) {
            $stmt->bindParam(':category', $filters['category']);
        }

        if (isset($filters['date_from'])) {
            $stmt->bindParam(':date_from', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $stmt->bindParam(':date_to', $filters['date_to']);
        }

        if (isset($filters['search'])) {
            $search = "%{$filters['search']}%";
            $stmt->bindParam(':search', $search);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get expense by ID
    public function getById($id, $user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id AND user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Create expense
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, category, description, amount, expense_date, payment_method, receipt_url, notes) 
                  VALUES (:user_id, :category, :description, :amount, :expense_date, :payment_method, :receipt_url, :notes)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':amount', $data['amount']);
        $stmt->bindParam(':expense_date', $data['expense_date']);
        $stmt->bindParam(':payment_method', $data['payment_method']);
        $stmt->bindParam(':receipt_url', $data['receipt_url']);
        $stmt->bindParam(':notes', $data['notes']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    // Update expense
    public function update($id, $user_id, $data) {
        $fields = [];
        $params = [':id' => $id, ':user_id' => $user_id];

        $allowed = ['category', 'description', 'amount', 'expense_date', 'payment_method', 'receipt_url', 'notes'];

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

    // Delete expense
    public function delete($id, $user_id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }

    // Get expense categories for a user
    public function getCategories($user_id) {
        $query = "SELECT DISTINCT category FROM " . $this->table . " WHERE user_id = :user_id ORDER BY category";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
