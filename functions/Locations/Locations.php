<?php
function getLocations($conn): array {
    try {
        $sql = 'SELECT * FROM locations';
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        throw new Exception('Failed to fetch locations: ' . $e->getMessage());
    }
}
