<?php

function addVisits($conn, $patientId, $departmentId, $locationId){
    try {
        $sql = 'INSERT INTO patient_visits (patientId, departmentId, locationId)
        VALUES (:patientId, :departmentId, :locationId)';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        $stmt->bindParam(':departmentId', $departmentId, PDO::PARAM_INT);
        $stmt->bindParam(':locationId', $locationId, PDO::PARAM_INT);
        if($stmt->execute()){
            return true;
        } else {
            throw new Exception('Failed to add visits');
        }
    } catch (Exception $e) {
        throw new Exception('Failed to add visits: ' . $e->getMessage());
    }
}

function updateVisits($conn, $patientId, $departmentId, $locationId) {
    try {
        $sql = 'UPDATE patient_visits SET 
                departmentId = :departmentId, 
                locationId = :locationId 
                WHERE patientId = :patientId';
                
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':departmentId', $departmentId, PDO::PARAM_INT);
        $stmt->bindParam(':locationId', $locationId, PDO::PARAM_INT);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception('Failed to update visits');
        }
    } catch (Exception $e) {
        throw new Exception('Failed to update visits: ' . $e->getMessage());
    }
}
