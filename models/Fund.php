<?php
require_once 'Database.php';

class Fund {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    // Create new fund
    public function createFund(int $userId, array $data): bool {
        $query = "INSERT INTO funds (user_id, type, source, amount, description, created_at) 
                  VALUES (:user_id, :type, :source, :amount, :description, NOW())";
        $statement = $this->db->prepare($query);
        return $statement->execute([
            ':user_id' => $userId,
            ':type' => $data['type'],
            ':source' => $data['source'],
            ':amount' => $data['amount'],
            ':description' => $data['description']
        ]);
    }

    // get funds

    public function getFunds(int $userId): array {
        $query = "SELECT 
            f.id,
            f.source,
            f.type,
            f.amount,
            f.description,
            f.created_at,
            f.amount - COALESCE(SUM(e.amount), 0) as remaining,
            COALESCE(SUM(e.amount), 0) as spent
        FROM funds f
        LEFT JOIN expenses e ON f.id = e.fund_id
        WHERE f.user_id = :user_id
        GROUP BY f.id, f.source, f.type, f.amount, f.description, f.created_at
        ORDER BY f.created_at DESC";
        
        $statement = $this->db->prepare($query);
        $statement->execute([':user_id' => $userId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single fund details
    public function getFundDetails(int $userId, int $fundId): ?array {
        $query = "SELECT 
            f.*,
            f.amount - COALESCE(SUM(e.amount), 0) as remaining,
            COALESCE(SUM(e.amount), 0) as spent
        FROM funds f
        LEFT JOIN expenses e ON f.id = e.fund_id
        WHERE f.id = :id AND f.user_id = :user_id
        GROUP BY f.id";
        
        $statement = $this->db->prepare($query);
        $statement->execute([':id' => $fundId, ':user_id' => $userId]);
        return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
    }


    // Update existing fund (only one instance)
    public function updateFund(int $userId, int $fundId, array $data): bool {
        $query = "UPDATE funds SET 
            type = :type,
            source = :source,
            amount = :amount,
            description = :description,
            updated_at = NOW()
            WHERE id = :id
            AND user_id = :user_id"; // Add user_id check
    
        $statement = $this->db->prepare($query);
        return $statement->execute([
            ':type' => $data['type'],
            ':source' => $data['source'],
            ':amount' => $data['amount'],
            ':description' => $data['description'],
            ':id' => $fundId,
            ':user_id' => $userId
        ]);
    }

    // Delete fund (optional)
    public function deleteFund(int $id): bool {
        $query = "DELETE FROM funds WHERE id = :id";
        $statement = $this->db->prepare($query);
        return $statement->execute([':id' => $id]);
    }
}