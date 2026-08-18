<?php

function getPatents($conn) {
    try {
        $sql = 'SELECT * FROM patents';
        $stmt = $conn->prepare($sql);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Exception $e) {
        throw new Exception('Failed to fetch patents: ' . $e->getMessage());
    }
}
