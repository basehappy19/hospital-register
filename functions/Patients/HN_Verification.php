<?php

function link_HN_Verification_Status($conn, $patientId){
    try {
        $sql = 'INSERT INTO hn_verification_status (patientId) VALUES (:patientId)';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        if($stmt->execute()){
            return true;
        } else {
            throw new Exception('Failed to link hn verification status');
        }
    } catch (Exception $e) {
        throw new Exception('Failed to link hn verification status: ' . $e->getMessage());
    }
}

