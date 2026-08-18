<?php

function addSymptom($conn, $patientId, $symptom) {
    try {
        $sql = 'INSERT INTO patient_symptoms (patientId,description)
        VALUES (:patientId, :description)';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        $stmt->bindParam(':description', $symptom, PDO::PARAM_STR);
        if($stmt->execute()){
            return true;
        } else {
            throw new Exception('Failed to add sympton');
        }
    } catch (Exception $e) {
        throw new Exception('Failed to add sympton: ' . $e->getMessage());
    }
}

function updateSymptom($conn, $patientId, $symptom) {
    try {
        $sql = 'UPDATE patient_symptoms SET description = :description 
                WHERE patientId = :patientId';
                
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':description', $symptom, PDO::PARAM_STR);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception('Failed to update symptom');
        }
    } catch (Exception $e) {
        throw new Exception('Failed to update symptom: ' . $e->getMessage());
    }
}
