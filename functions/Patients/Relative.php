<?php

function linkRelative ($conn, $patientId, $data){
    try {
        $sql = 'INSERT INTO patient_relative_info (patientId, fullname, relationship, phone_number)
        VALUES (:patientId, :fullname, :relationship, :phone_number)';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        $stmt->bindParam(':fullname', $data['fullname'], PDO::PARAM_STR);
        $stmt->bindParam(':relationship', $data['relationship'], PDO::PARAM_STR);
        $stmt->bindParam(':phone_number', $data['phone_number'], PDO::PARAM_STR);
        if($stmt->execute()){
            return true;
        } else {
            throw new Exception('Failed to link relative');
        }
    } catch (Exception $e) {
        throw new Exception('Failed to link relative: '. $e->getMessage());
    }
}

function linkRelativeUpdate($conn, $patientId, $data){
    try {
        $sql = 'UPDATE patient_relative_info SET 
        fullname = :fullname,
        relationship = :relationship,
        phone_number = :phone_number
        WHERE patientId = :patientId';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        $stmt->bindParam(':fullname', $data['fullname'], PDO::PARAM_STR);
        $stmt->bindParam(':relationship', $data['relationship'], PDO::PARAM_STR);
        $stmt->bindParam(':phone_number', $data['phone_number'], PDO::PARAM_STR);
        if($stmt->execute()){
            return true;
        } else {
            throw new Exception('Failed to link update relative');
        }
    } catch (Exception $e) {
        throw new Exception('Failed to link update relative: '. $e->getMessage());
    }
}