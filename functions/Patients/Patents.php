<?php

function linkPatent($conn, $patientId, $data){
    try {
        $sql = 'INSERT INTO patent_for_patient (patientId, patentId, patent_other)
        VALUES (:patientId, :patentId, :patent_other)';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        $stmt->bindParam(':patentId', $data['patentId'], PDO::PARAM_INT);
        $stmt->bindParam(':patent_other', $data['patent_other'], PDO::PARAM_STR);
        if($stmt->execute()){
            return true;
        } else {
            throw new Exception('Failed to link patent');
        }
    } catch (Exception $e) {
        throw new Exception('Failed to link patent: '. $e->getMessage());
    }
}

function updatePatentLink($conn, $patientId, $data) {
    try {
        $sql = 'UPDATE patent_for_patient SET 
                patentId = :patentId, 
                patent_other = :patent_other 
                WHERE patientId = :patientId';
                
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patentId', $data['patentId'], PDO::PARAM_INT);
        $stmt->bindParam(':patent_other', $data['patent_other'], PDO::PARAM_STR);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception('Failed to update patent link');
        }
    } catch (Exception $e) {
        throw new Exception('Failed to update patent link: ' . $e->getMessage());
    }
}
