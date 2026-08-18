<?php

function link_Call_Status($conn, $patientId){
    try {
        $sql = 'INSERT INTO patient_call_status (patientId) VALUES (:patientId)';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        if($stmt->execute()){
            return true;
        } else {
            throw new Exception('Failed to link call status');
        };
    } catch (Exception $e) {
        throw new Exception('Failed to link call status: ' . $e->getMessage());
    }
}

function FailedCallStatus($conn, $patientId, $failure_note) {
    try {
        $sql = 'UPDATE patient_call_status SET call_statusId = 5, failure_note = :failure_note
                WHERE patientId = :patientId';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':failure_note', $failure_note, PDO::PARAM_STR);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $logs = [
                'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null, 
                'action_type' => 'UPDATE', 
                'resource' => 'patient_call_status', 
                'resource_id' => $patientId, 
                'status' => 'Success',  
                'ip_address' => getIpAddress(), 
                'request_data' => json_encode(['patientId' => $patientId, 'failure_note' => $failure_note]), 
                'response_data' => json_encode(['call_statusId' => 5]), 
                'remarks' => 'Update failed call status', 
                'user_agent' => getUserAgent(),
            ];
            createLogs($conn, $logs);
            return true;
        } else {
            throw new Exception('Failed to update call status');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null, 
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
            'action_type' => 'UPDATE', 
            'resource' => 'patient_call_status', 
            'resource_id' => $patientId, 
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode(['patientId' => $patientId, 'failure_note' => $failure_note]), 
            'response_data' => json_encode(['error' => $e->getMessage()]), 
            'remarks' => 'Failed to update failed call status', 
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);
        throw new Exception('Failed to update call status: ' . $e->getMessage());
    }
}


function CalledStatus($conn, $patientId) {
    try {
        $sql = 'UPDATE patient_call_status SET call_statusId = 4, failure_note = null
                WHERE patientId = :patientId';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $logs = [
                'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null, 
                'action_type' => 'UPDATE', 
                'resource' => 'patient_call_status', 
                'resource_id' => $patientId, 
                'status' => 'Success',  
                'ip_address' => getIpAddress(), 
                'request_data' => json_encode(['patientId' => $patientId, 'call_statusId' => 4]), 
                'response_data' => json_encode(['call_statusId' => 4]), 
                'remarks' => 'Update called status', 
                'user_agent' => getUserAgent(),
            ];
            createLogs($conn, $logs);
            return true;
        } else {
            throw new Exception('Failed to update call status');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null, 
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
            'action_type' => 'UPDATE', 
            'resource' => 'patient_call_status', 
            'resource_id' => $patientId, 
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode(['patientId' => $patientId]), 
            'response_data' => json_encode(['error' => $e->getMessage()]), 
            'remarks' => 'Failed to update called status', 
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);
        throw new Exception('Failed to update call status: ' . $e->getMessage());
    }
}
