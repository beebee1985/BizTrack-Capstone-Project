<?php
class Customer {
    private $conn;
    private $table = 'customers';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all customers for a user
    public function getAll($user_id, $filters = []) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id";

        if (isset($filters['search'])) {
            $query .= " AND (name LIKE :search OR email LIKE :search OR phone LIKE :search)";
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);

        if (isset($filters['search'])) {
            $search = "%{$filters['search']}%";
            $stmt->bindParam(':search', $search);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get customer by ID
    public function getById($id, $user_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id AND user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetch();
    }

    // Create customer
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, name, email, phone, address, city, state, zip_code, notes) 
                  VALUES (:user_id, :name, :email, :phone, :address, :city, :state, :zip_code, :notes)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':state', $data['state']);
        $stmt->bindParam(':zip_code', $data['zip_code']);
        $stmt->bindParam(':notes', $data['notes']);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        return false;
    }

    // Update customer
    public function update($id, $user_id, $data) {
        $fields = [];
        $params = [':id' => $id, ':user_id' => $user_id];

        $allowed = ['name', 'email', 'phone', 'address', 'city', 'state', 'zip_code', 'notes'];

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

    // Delete customer
    public function delete($id, $user_id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);

        return $stmt->execute();
    }
}
