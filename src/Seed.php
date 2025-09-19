<?php
require_once 'Database.php';

class Seed {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // CREATE - Insert new seed
    public function create($name, $variety, $category, $donor_name, $donor_email, $quantity, $season, $description) {
        $sql = "INSERT INTO seeds (name, variety, category, donor_name, donor_email, quantity_in_stock, planting_season, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->executeQuery($sql, "sssssiis", [
            $name, $variety, $category, $donor_name, $donor_email, $quantity, $season, $description
        ]);
        
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        return $success;
    }

    // READ - Get all seeds
    public function getAll() {
        $sql = "SELECT * FROM seeds ORDER BY name ASC";
        $stmt = $this->db->executeQuery($sql);
        
        $result = $stmt->get_result();
        $seeds = $result->fetch_all(MYSQLI_ASSOC);
        
        $stmt->close();
        return $seeds;
    }

    // READ - Get single seed by ID
    public function getById($id) {
        $sql = "SELECT * FROM seeds WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, "i", [$id]);
        
        $result = $stmt->get_result();
        $seed = $result->fetch_assoc();
        
        $stmt->close();
        return $seed;
    }

    // UPDATE - Update existing seed
    public function update($id, $name, $variety, $category, $donor_name, $donor_email, $quantity, $season, $description) {
        $sql = "UPDATE seeds SET name=?, variety=?, category=?, donor_name=?, donor_email=?, quantity_in_stock=?, planting_season=?, description=? WHERE id=?";
        
        $stmt = $this->db->executeQuery($sql, "sssssissi", [
            $name, $variety, $category, $donor_name, $donor_email, $quantity, $season, $description, $id
        ]);
        
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        return $success;
    }

    // DELETE - Remove seed
    public function delete($id) {
        $sql = "DELETE FROM seeds WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, "i", [$id]);
        
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        return $success;
    }

    // Update stock quantity
    public function updateStock($id, $new_quantity) {
        $sql = "UPDATE seeds SET quantity_in_stock = ? WHERE id = ?";
        $stmt = $this->db->executeQuery($sql, "ii", [$new_quantity, $id]);
        
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        return $success;
    }

    // Get transactions for a seed
    public function getTransactions($seed_id) {
        $sql = "SELECT * FROM seed_transactions WHERE seed_id = ? ORDER BY transaction_date DESC";
        $stmt = $this->db->executeQuery($sql, "i", [$seed_id]);
        
        $result = $stmt->get_result();
        $transactions = $result->fetch_all(MYSQLI_ASSOC);
        
        $stmt->close();
        return $transactions;
    }

    // Add transaction
    public function addTransaction($seed_id, $type, $quantity, $member_name, $notes = '') {
        $sql = "INSERT INTO seed_transactions (seed_id, transaction_type, quantity, member_name, notes) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->db->executeQuery($sql, "isiss", [
            $seed_id, $type, $quantity, $member_name, $notes
        ]);
        
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        return $success;
    }

    public function __destruct() {
        $this->db->close();
    }
}