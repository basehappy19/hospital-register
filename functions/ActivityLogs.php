<?php
function getIpAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'];
}

function createLogs($conn, $logs) {
    $sql = 'INSERT INTO activity_logs (userId, loginId, action_type, resource, resource_id, status, ip_address, user_agent, request_data, response_data, remarks)
            VALUES (:userId, :loginId, :action_type, :resource, :resource_id, :status, :ip_address, :user_agent, :request_data, :response_data, :remarks)';
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':userId', $logs['userId'], PDO::PARAM_INT);
    $stmt->bindParam(':loginId', $logs['loginId'], PDO::PARAM_INT);
    $stmt->bindParam(':action_type', $logs['action_type'], PDO::PARAM_STR);
    $stmt->bindParam(':resource', $logs['resource'], PDO::PARAM_STR);
    $stmt->bindParam(':resource_id', $logs['resource_id'], PDO::PARAM_INT);
    $stmt->bindParam(':status', $logs['status'], PDO::PARAM_STR);
    $stmt->bindParam(':ip_address', $logs['ip_address'], PDO::PARAM_STR);
    $stmt->bindParam(':user_agent', $logs['user_agent'], PDO::PARAM_STR);
    $stmt->bindParam(':request_data', $logs['request_data'], PDO::PARAM_STR);
    $stmt->bindParam(':response_data', $logs['response_data'], PDO::PARAM_STR); 
    $stmt->bindParam(':remarks', $logs['remarks'], PDO::PARAM_STR);
    
    return $stmt->execute();
}
