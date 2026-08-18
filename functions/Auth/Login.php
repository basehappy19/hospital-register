<?php

function authLogin($conn, $username, $password) {
    try {
        $sql = 'SELECT id, password FROM users WHERE username = :username';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $loginData = [
                    'userId' => $user['id'],
                    'ip_address' => getIpAddress(),
                    'user_agent' => getUserAgent(),
                    'login_time' => date('Y-m-d H:i:s'),
                    'login_status' => 'success',
                ];
                $loginId = logLoginHistory($conn, $loginData);

                $logs = [
                    'userId' => $user['id'],
                    'loginId' => $loginId,
                    'action_type' => 'LOGIN',
                    'resource' => 'users',
                    'resource_id' => $user['id'],
                    'status' => 'Success',
                    'ip_address' => getIpAddress(),
                    'request_data' => json_encode(['username' => $username]),
                    'response_data' => json_encode(['login' => 'success']),
                    'remarks' => 'User login successful',
                    'user_agent' => getUserAgent(),
                ];
                createLogs($conn, $logs);

                $_SESSION['userId'] = $user['id'];
                $_SESSION['loginId'] = $loginId;

                return true;
            } else {
                $loginData = [
                    'userId' => $user ? $user['id'] : null,
                    'ip_address' => getIpAddress(),
                    'user_agent' => getUserAgent(),
                    'login_time' => date('Y-m-d H:i:s'),
                    'login_status' => 'failure',
                    'login_method' => 'password'
                ];
                logLoginHistory($conn, $loginData);

                $logs = [
                    'userId' => $user ? $user['id'] : null,
                    'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
                    'action_type' => 'LOGIN',
                    'resource' => 'users',
                    'resource_id' => $user ? $user['id'] : null,
                    'status' => 'Failed',
                    'ip_address' => getIpAddress(),
                    'request_data' => json_encode(['username' => $username]),
                    'response_data' => json_encode(['login' => 'failed']),
                    'remarks' => 'User login failed',
                    'user_agent' => getUserAgent(),
                ];
                createLogs($conn, $logs);

                return false;
            }
        } else {
            throw new Exception('Failed to execute login query');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => null,
            'loginId' => null,
            'action_type' => 'LOGIN',
            'resource' => 'users',
            'resource_id' => null,
            'status' => 'Failed',
            'ip_address' => getIpAddress(),
            'request_data' => json_encode(['username' => $username]),
            'response_data' => json_encode(['error' => $e->getMessage()]),
            'remarks' => 'Exception during login',
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        throw new Exception('Error logging in: ' . $e->getMessage());
    }
}

function authLogout($conn) {
    try {
        if (isset($_SESSION['userId'])) {
            $userId = $_SESSION['userId'];
            $loginId = $_SESSION['loginId'];

            $sql = 'SELECT id FROM login_history WHERE userId = :userId AND id = :loginId AND logout_time IS NULL ORDER BY login_time DESC LIMIT 1';
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':loginId', $loginId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $loginHistory = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($loginHistory) {
                    $sql = 'UPDATE login_history SET logout_time = :logout_time WHERE id = :id';
                    $stmt = $conn->prepare($sql);
                    $logout_time = date('Y-m-d H:i:s');
                    $stmt->bindParam(':logout_time', $logout_time, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $loginHistory['id'], PDO::PARAM_INT);

                    if (!$stmt->execute()) {
                        throw new Exception('Failed to update logout time');
                    }

                    $logs = [
                        'userId' => $userId,
                        'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
                        'action_type' => 'LOGOUT',
                        'resource' => 'users',
                        'resource_id' => $userId,
                        'status' => 'Success',
                        'ip_address' => getIpAddress(),
                        'request_data' => json_encode([]),  // ไม่มีข้อมูล request
                        'response_data' => json_encode(['logout' => 'success']),
                        'remarks' => 'User logout successful',
                        'user_agent' => getUserAgent(),
                    ];
                    createLogs($conn, $logs);

                    session_destroy();
                    return true;
                } else {
                    $logs = [
                        'userId' => $userId,
                        'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
                        'action_type' => 'LOGOUT',
                        'resource' => 'users',
                        'resource_id' => $userId,
                        'status' => 'NotFound',
                        'ip_address' => getIpAddress(),
                        'request_data' => json_encode([]),
                        'response_data' => json_encode(['logout' => 'not found']),
                        'remarks' => 'No active login session found for user',
                        'user_agent' => getUserAgent(),
                    ];
                    createLogs($conn, $logs);

                    session_destroy();
                    return true;
                }
            } else {
                throw new Exception('Failed to fetch login history for logout');
            }
        } else {
            throw new Exception('User is not logged in.');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($userId) ? $userId : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
            'action_type' => 'LOGOUT',
            'resource' => 'users',
            'resource_id' => isset($userId) ? $userId : null,
            'status' => 'Failed',
            'ip_address' => getIpAddress(),
            'request_data' => json_encode([]),
            'response_data' => json_encode(['error' => $e->getMessage()]),
            'remarks' => 'User logout failed',
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        throw new Exception('Error logging out: ' . $e->getMessage());
    }
}

function checkSession($conn, $loginId, $userId) {
    try {
        $sql = 'SELECT COUNT(*) AS session_count
                FROM login_history 
                WHERE id = :loginId 
                AND userId = :userId 
                AND logout_time IS NULL';  

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':loginId', $loginId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if($result['session_count'] > 0){
                return true;
            } else {
                return false;
            }
        } else {
            throw new Exception('Failed to execute query for checking session');
        }

    } catch (Exception $e) {
        throw new Exception('Error checking session: ' . $e->getMessage());
    }
}

function logoutSession($conn, $loginId, $userId) {
    try {
        $sql = 'SELECT * FROM login_history WHERE login_history.id = :loginId AND login_history.userId = :userId';  
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':loginId', $loginId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $login = $stmt->fetch(PDO::FETCH_ASSOC);

            if($login){
                $sqlSession = 'UPDATE login_history SET login_history.logout_time = NOW() WHERE login_history.id = :loginId AND login_history.userId = :userId';  
                $stmtSession = $conn->prepare($sqlSession);
                $stmtSession->bindParam(':loginId', $login['id'], PDO::PARAM_INT);
                $stmtSession->bindParam(':userId', $login['userId'], PDO::PARAM_INT);
                if($stmtSession->execute()){
                    return true;
                } else {
                    throw new Exception('Failed to logout session');
                }
            } else {
                return false;
            }
        } else {
            throw new Exception('Failed to query login');
        }

    } catch (Exception $e) {
        throw new Exception('Error logout session: ' . $e->getMessage());
    }
}

function logoutSessionAll($conn, $loginId, $userId) {
    try {
        $sql = 'SELECT * FROM login_history WHERE login_history.userId = :userId';  
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $login = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if($login){
                $sqlSession = 'UPDATE login_history SET login_history.logout_time = NOW() WHERE login_history.userId = :userId AND login_history.id != :loginId';  
                $stmtSession = $conn->prepare($sqlSession);
                $stmtSession->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmtSession->bindParam(':loginId', $loginId, PDO::PARAM_INT);
                if($stmtSession->execute()){
                    return true;
                } else {
                    throw new Exception('Failed to logout all session');
                }
            } else {
                return false;
            }
        } else {
            throw new Exception('Failed to query all login');
        }

    } catch (Exception $e) {
        throw new Exception('Error logout all session: ' . $e->getMessage());
    }
}


