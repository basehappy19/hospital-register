<?php

function getDepartmentsForEdit($conn){
    try {
        $sql = 'SELECT departments.id, departments.name FROM departments';
        $stmt = $conn->prepare($sql);
        if ($stmt->execute()) {
            $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($departments) {
                $departmentIds = [];
                $departmentNames = [];
            
                foreach ($departments as $department) {
                    $departmentIds[] = $department['id'];
                    $departmentNames[] = $department['name'];
                }
                $logs = [
                    'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                    'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
                    'action_type' => 'GET', 
                    'resource' => 'departments', 
                    'resource_id' => implode(', ', $departmentIds),  
                    'status' => 'Success',  
                    'ip_address' => getIpAddress(), 
                    'request_data' => json_encode([]), 
                    'response_data' => json_encode([
                        'departmentIds' => $departmentIds, 
                    ]),  
                    'remarks' => 'fetch departments data for edit',  
                    'user_agent' => getUserAgent(),
                ];
                createLogs($conn, $logs);
            
                return $departments; 
            } else {
                return null;
            }

        } else {
            throw new Exception('Failed to fetch departments for edit');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
            'action_type' => 'GET', 
            'resource' => 'departments', 
            'resource_id' => null, 
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode([]),  
            'response_data' => json_encode(['error' => $e->getMessage()]),  
            'remarks' => 'Failed to fetch department data for edit',  
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);
        throw new Exception('Error fetch department data for edit: ' . $e->getMessage());
    }
}

function getDepartmentForEdit($conn, $departmentId){
    try {
        $sql = 'SELECT departments.id, departments.name FROM departments
        WHERE departments.id = :departmentId';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":departmentId", $departmentId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $department = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($department) {
                $logs = [
                    'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                    'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
                    'action_type' => 'GET', 
                    'resource' => 'departments', 
                    'resource_id' => $departmentId,  
                    'status' => 'Success',  
                    'ip_address' => getIpAddress(), 
                    'request_data' => json_encode(['departmentId' => $departmentId]), 
                    'response_data' => json_encode(['departmentId' => $department['id'], 'name' => $department['name']]), 
                    'remarks' => 'fetch department data for edit',  
                    'user_agent' => getUserAgent(),
                ];
                createLogs($conn, $logs);
            
                return $department; 
            } else {
                return null;
            }

        } else {
            throw new Exception('Failed to fetch department');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
            'action_type' => 'GET', 
            'resource' => 'departments', 
            'resource_id' => $departmentId, 
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode(['departmentId' => $departmentId]),  
            'response_data' => json_encode(['error' => $e->getMessage()]),  
            'remarks' => 'Failed to fetch department data for edit',  
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);
        throw new Exception('Error fetch department data for edit: ' . $e->getMessage());
    }
}

function checkDepartmentExists($conn, $name) {
    try {
        $sql = 'SELECT COUNT(*) FROM departments WHERE name = :name';
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $count = $stmt->fetchColumn();
            
            return $count > 0;
        } else {
            throw new Exception('Failed to execute check query');
        }
    } catch (Exception $e) {
        throw new Exception('Error checking department existence: ' . $e->getMessage());
    }
}

function createDepartment($conn, $department) {
    try {

        $sql = 'INSERT INTO departments (name) 
                VALUES (:name)'; 
        
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':name', $department['name'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            $departmentId = $conn->lastInsertId(); 
            $logs = [
                'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
                'action_type' => 'POST', 
                'resource' => 'departments', 
                'resource_id' => $departmentId,  
                'status' => 'Success',  
                'ip_address' => getIpAddress(), 
                'request_data' => json_encode($department), 
                'response_data' => json_encode(['departmentId' => $departmentId, 'name' => $department['name']]), 
                'remarks' => 'Created new department',  
                'user_agent' => getUserAgent(),
            ];
            createLogs($conn, $logs);
            
            return $departmentId; 
        } else {
            throw new Exception('Failed to create department');
        }

    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
            'action_type' => 'POST', 
            'resource' => 'departments', 
            'resource_id' => null,  
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode($department),  
            'response_data' => json_encode(['error' => $e->getMessage()]),  
            'remarks' => 'Failed to create department',  
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        throw new Exception('Error creating department: ' . $e->getMessage());
    }
}

function updateDepartment($conn, $data, $departmentId) {
    try {
        $sql = 'UPDATE departments SET name = :name WHERE departments.id = :departmentId';
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':departmentId', $departmentId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $logs = [
                'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
                'action_type' => 'UPDATE', 
                'resource' => 'departments', 
                'resource_id' => $departmentId,  
                'status' => 'Success',  
                'ip_address' => getIpAddress(), 
                'request_data' => json_encode($data),  
                'response_data' => json_encode(['updatedDepartmentId' => $departmentId]), 
                'remarks' => 'Updated department data',  
                'user_agent' => getUserAgent(),
            ];
            createLogs($conn, $logs);
            return true;
        } else {
            throw new Exception('Failed to update department');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
            'action_type' => 'UPDATE', 
            'resource' => 'departments', 
            'resource_id' => $departmentId, 
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode($data),  
            'response_data' => json_encode(['error' => $e->getMessage()]),  
            'remarks' => 'Failed to update department',  
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);
        throw new Exception('Error updating department: ' . $e->getMessage());
    }
}

function getDepartments($conn): array {
    try {
        $sql = 'SELECT * FROM departments';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        throw new Exception('Failed to fetch departments: ' . $e->getMessage());
    }
}

function getCountPatientsInDepartment($conn): array {
    try {
        $sql = 'SELECT departmentId, COUNT(*) as patientCount 
                FROM patient_visits 
                GROUP BY departmentId';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        throw new Exception('Failed to fetch department counts: ' . $e->getMessage());
    }
}

