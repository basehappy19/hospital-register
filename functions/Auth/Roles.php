<?php 

function getRoles($conn): array {
    try {
        $sql = 'SELECT * FROM roles';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        throw new Exception('Failed to fetch roles: ' . $e->getMessage());
    }
}