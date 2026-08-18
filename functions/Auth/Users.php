<?php

function getUsers($conn){
    try {
        $sql = 'SELECT users.id, users.username, users.displayName, users.administrator, roles.name as role_name FROM users
        LEFT JOIN roles ON users.roleId = roles.id
        WHERE users.administrator = 0';
        $stmt = $conn->prepare($sql);
        if ($stmt->execute()) {
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($users) {
                $userIds = [];
                $userDisplayNames = [];
            
                foreach ($users as $user) {
                    $userIds[] = $user['id'];
                    $userNames[] = $user['displayName'];
                }
            
                $logs = [
                    'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,  
                    'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
                    'action_type' => 'GET', 
                    'resource' => 'users', 
                    'resource_id' => implode(', ', $userIds),  
                    'status' => 'Success',  
                    'ip_address' => getIpAddress(), 
                    'request_data' => json_encode([]),  
                    'response_data' => json_encode([
                        'userIds' => $userIds, 
                    ]),  
                    'remarks' => 'Fetch all users', 
                    'user_agent' => getUserAgent(),
                ];
            
                createLogs($conn, $logs); 
            
                return $users; 
            } else {
                return [];
            }

        } else {
            throw new Exception('Failed to fetch users');
        }


    } catch (Exception $e) {

        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
            'action_type' => 'GET', 
            'resource' => 'users', 
            'resource_id' => null, 
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode([]),  
            'response_data' => json_encode(['error' => $e->getMessage()]),  
            'remarks' => 'Failed to fetch users',  
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);
        throw new Exception('Error fetch users: ' . $e->getMessage());
    }
}

function getUserForEdit($conn, $userId){
    try {
        $sql = 'SELECT users.id, users.username, users.displayname, users.roleId, users.administrator FROM users
        WHERE users.id = :userId AND users.administrator = 0';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                $logs = [
                    'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                    'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
                    'action_type' => 'GET', 
                    'resource' => 'users', 
                    'resource_id' => $userId,  
                    'status' => 'Success',  
                    'ip_address' => getIpAddress(), 
                    'request_data' => json_encode(['userId' => $userId]), 
                    'response_data' => json_encode(['userId' => $user['id'], 'displayname' => $user['displayname']]), 
                    'remarks' => 'fetch user data for edit',  
                    'user_agent' => getUserAgent(),
                ];
                createLogs($conn, $logs);
            
                return $user; 
            } else {
                return null;
            }

        } else {
            throw new Exception('Failed to fetch user');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
            'action_type' => 'GET', 
            'resource' => 'users', 
            'resource_id' => $userId, 
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode(['userId' => $userId]),  
            'response_data' => json_encode(['error' => $e->getMessage()]),  
            'remarks' => 'Failed to fetch user data for edit',  
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);
        throw new Exception('Error fetch user data for edit: ' . $e->getMessage());
    }
}

function getHistoryUsers($conn, $userId, $page = 1, $perPage = 10, $activeInactivePage = 1, $historyPage = 1, $logsPage = 1, $logsPerPage = 5) {
    try {
        $sql = 'SELECT users.id, users.username, users.displayname, users.administrator, roles.name as role_name 
                FROM users
                LEFT JOIN roles ON users.roleId = roles.id
                WHERE users.id = :userId AND users.administrator = 0';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                return null;
            }

            $activeInactiveOffset = ($activeInactivePage - 1) * $perPage;
            $historyOffset = ($historyPage - 1) * $perPage;
            $logsOffset = ($logsPage - 1) * $logsPerPage;

            $sqlHistories = '
                SELECT login_history.id AS loginId, login_history.userId, login_history.login_time, login_history.logout_time, 
                       login_history.ip_address, login_history.user_agent, login_history.login_status,
                    CASE 
                        WHEN login_history.logout_time IS NULL THEN "active" 
                        ELSE "inactive" 
                    END AS status
                FROM login_history
                WHERE login_history.userId = :userId
                AND login_history.login_status = "success"
                ORDER BY status ASC, login_history.login_time DESC
                LIMIT :offset, :perPage';

            $stmtHistories = $conn->prepare($sqlHistories);
            $stmtHistories->bindParam(':userId', $user['id'], PDO::PARAM_INT);
            $stmtHistories->bindParam(':offset', $activeInactiveOffset, PDO::PARAM_INT);
            $stmtHistories->bindParam(':perPage', $perPage, PDO::PARAM_INT);

            if ($stmtHistories->execute()) {
                $histories = $stmtHistories->fetchAll(PDO::FETCH_ASSOC);

                $groupedHistories = ['active' => [], 'inactive' => []];

                foreach ($histories as $history) {
                    $sqlLogs = '
                        SELECT id, action_type, resource, resource_id, timestamp, status,
                               ip_address AS activity_log_ip_address, user_agent AS user_agent_activity_log,
                               request_data, response_data, remarks
                        FROM activity_logs
                        WHERE loginId = :loginId
                        ORDER BY timestamp DESC
                        LIMIT :offset, :perPage';

                    $stmtLogs = $conn->prepare($sqlLogs);
                    $stmtLogs->bindParam(':loginId', $history['loginId'], PDO::PARAM_INT);
                    $stmtLogs->bindParam(':offset', $logsOffset, PDO::PARAM_INT);
                    $stmtLogs->bindParam(':perPage', $logsPerPage, PDO::PARAM_INT);
                    $stmtLogs->execute();

                    $history['activity_logs'] = $stmtLogs->fetchAll(PDO::FETCH_ASSOC);

                    $sqlLogCount = 'SELECT COUNT(*) FROM activity_logs WHERE loginId = :loginId';
                    $stmtLogCount = $conn->prepare($sqlLogCount);
                    $stmtLogCount->bindParam(':loginId', $history['loginId'], PDO::PARAM_INT);
                    $stmtLogCount->execute();
                    $totalLogs = $stmtLogCount->fetchColumn();

                    $history['log_pagination'] = [
                        'currentPage' => $logsPage,
                        'perPage' => $logsPerPage,
                        'totalPages' => ceil($totalLogs / $logsPerPage),
                        'totalLogs' => $totalLogs
                    ];

                    if ($history['status'] === 'active') {
                        $groupedHistories['active'][] = $history;
                    } else {
                        $groupedHistories['inactive'][] = $history;
                    }
                }

                $sqlCount = 'SELECT COUNT(*) FROM login_history WHERE userId = :userId AND login_status = "success"';
                $stmtCount = $conn->prepare($sqlCount);
                $stmtCount->bindParam(':userId', $user['id'], PDO::PARAM_INT);
                $stmtCount->execute();
                $totalRecords = $stmtCount->fetchColumn();

                $totalPages = ceil($totalRecords / $perPage);

                return [
                    "user" => $user,
                    "histories" => $groupedHistories,
                    "pagination" => [
                        "currentPage" => $page,
                        "perPage" => $perPage,
                        "totalPages" => $totalPages,
                        "totalRecords" => $totalRecords
                    ]
                ];
            } else {
                throw new Exception('Failed to fetch history user');
            }
        } else {
            throw new Exception('Failed to fetch user');
        }

    } catch (Exception $e) {
        throw new Exception('Error fetching history users: ' . $e->getMessage());
    }
}

function generatePagination($currentPage, $totalPages, $pageQueryParam, $additionalParams = []){
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    $output = '<div class="hidden sm:flex sm:space-x-2 mt-6 justify-center items-center">';

    if ($start > 1) {
        $output .= '<span class="px-4 py-2 text-sm font-medium text-gray-700">...</span>';
    }

    for ($i = $start; $i <= $end; $i++) {
        $class = $i == $currentPage ? 'text-blue-600 bg-blue-50 border border-blue-500' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50';
        $params = array_merge([$pageQueryParam => $i], $additionalParams);
        $url = '&userId=' . $_GET['userId'] . '&' . http_build_query($params);
        $output .= "<a href=\"?page=user_activity_logs{$url}\" class=\"px-4 py-2 text-sm font-medium {$class} rounded-md\">{$i}</a>";
    }

    if ($end < $totalPages) {
        $output .= '<span class="px-4 py-2 text-sm font-medium text-gray-700">...</span>';
    }

    $output .= '</div>';
    return $output;
}
function generateLogPagination($currentPage, $totalPages, $pageQueryParam, $additionalParams = []){
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    $output = '<div class="hidden sm:flex sm:space-x-2 mt-6 justify-center items-center">';

    if ($start > 1) {
        $output .= '<span class="px-4 py-2 text-sm font-medium text-gray-700">...</span>';
    }

    for ($i = $start; $i <= $end; $i++) {
        $class = $i == $currentPage ? 'text-blue-600 bg-blue-50 border border-blue-500' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50';
        $params = array_merge([$pageQueryParam => $i], $additionalParams);
        $url = '&userId=' . $_GET['userId'] . '&' . http_build_query($params);
        $output .= "<a href=\"?page=user_activity_logs{$url}\" class=\"px-4 py-2 text-sm font-medium {$class} rounded-md\">{$i}</a>";
    }

    if ($end < $totalPages) {
        $output .= '<span class="px-4 py-2 text-sm font-medium text-gray-700">...</span>';
    }

    $output .= '</div>';
    return $output;
}

function getDeviceType($userAgent) {
    if (strpos($userAgent, 'Mobile') !== false) {
        if (preg_match('/Android/i', $userAgent)) {
            return 'โทรศัพท์ (Android)';
        } elseif (preg_match('/iPhone/i', $userAgent)) {
            return 'โทรศัพท์ (iPhone)';
        } else {
            return 'โทรศัพท์ (อื่น ๆ)';
        }
    } elseif (strpos($userAgent, 'Tablet') !== false) {
        if (preg_match('/Android/i', $userAgent)) {
            return 'แท็บเล็ต (Android)';
        } elseif (preg_match('/iPad/i', $userAgent)) {
            return 'แท็บเล็ต (iPad)';
        } else {
            return 'แท็บเล็ต (อื่น ๆ)';
        }
    } else {
        $details = '';

        if (strpos($userAgent, 'Windows') !== false) {
            if (preg_match('/Windows NT 10.0/i', $userAgent)) {
                $details = 'Windows 10';
            } elseif (preg_match('/Windows NT 6.3/i', $userAgent)) {
                $details = 'Windows 8.1';
            } elseif (preg_match('/Windows NT 6.2/i', $userAgent)) {
                $details = 'Windows 8';
            } elseif (preg_match('/Windows NT 6.1/i', $userAgent)) {
                $details = 'Windows 7';
            } elseif (preg_match('/Windows NT 5.1/i', $userAgent)) {
                $details = 'Windows XP';
            } else {
                $details = 'Windows (เวอร์ชันไม่รู้จัก)';
            }
            return 'คอมพิวเตอร์ (' . $details . ')';
        } elseif (strpos($userAgent, 'Macintosh') !== false) {
            return 'คอมพิวเตอร์ (Mac)';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            return 'คอมพิวเตอร์ (Linux)';
        } else {
            return 'คอมพิวเตอร์ (อื่น ๆ)';
        }
    }
}

function getUserData($conn, $userId){
    try {
        $sql = 'SELECT id, username, displayname, roleId, administrator FROM users
                WHERE id = :userId';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if($data){
                return $data;
            } else {
                return null;
            }
        } else {
            throw new Exception('Failed to fetch user data');
        }


    } catch (Exception $e) {
        throw new Exception('Error fetch user data: ' . $e->getMessage());
    }
}

function updateUser($conn, $data, $userId) {
    try {
        $sql = 'UPDATE users SET username = :username, displayname = :displayname, roleId = :roleId';

        if (!is_null($data['password'])) {
            $sql .= ', password = :password';
            $updatePassword = true;
        } else {
            $updatePassword = false;
        }
        $sql .= ' WHERE id = :userId AND users.administrator = 0';

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':username', $data['username'], PDO::PARAM_STR);
        $stmt->bindParam(':displayname', $data['displayname'], PDO::PARAM_STR);
        $stmt->bindParam(':roleId', $data['role'], PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        if ($updatePassword) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        }

        if ($stmt->execute()) {
            $logs = [
                'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
                'action_type' => 'UPDATE', 
                'resource' => 'users', 
                'resource_id' => $userId,  
                'status' => 'Success',  
                'ip_address' => getIpAddress(), 
                'request_data' => json_encode($data),  
                'response_data' => json_encode(['updatedUserId' => $userId]), 
                'remarks' => 'Updated user data',  
                'user_agent' => getUserAgent(),
            ];
            createLogs($conn, $logs);
            return true;
        } else {
            throw new Exception('Failed to update user');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
            'action_type' => 'UPDATE', 
            'resource' => 'users', 
            'resource_id' => $userId, 
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode($data),  
            'response_data' => json_encode(['error' => $e->getMessage()]),  
            'remarks' => 'Failed to update user',  
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);
        throw new Exception('Error updating user: ' . $e->getMessage());
    }
}

function logLoginHistory($conn, $loginData) {
    try {
        $sql = 'INSERT INTO login_history (userId, login_time, ip_address, user_agent, login_status)
                VALUES (:userId, :login_time, :ip_address, :user_agent, :login_status)';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userId', $loginData['userId'], PDO::PARAM_INT);
        $stmt->bindParam(':login_time', $loginData['login_time'], PDO::PARAM_STR);
        $stmt->bindParam(':ip_address', $loginData['ip_address'], PDO::PARAM_STR);
        $stmt->bindParam(':user_agent', $loginData['user_agent'], PDO::PARAM_STR);
        $stmt->bindParam(':login_status', $loginData['login_status'], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return $conn->lastInsertId(); 
        } else {
            throw new Exception('Failed to log login history');
        }

    } catch (Exception $e) {
        throw new Exception('Error logging login history: ' . $e->getMessage());
    }
}

function removeUser($conn, $userId) {
    try {
        $sqlCheck = 'SELECT * FROM users WHERE id = :userId AND users.administrator = 0';
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmtCheck->execute();
        $user = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            $sqlDeleteLogs = 'DELETE FROM activity_logs WHERE userId = :userId';
            $stmtDeleteLogs = $conn->prepare($sqlDeleteLogs);
            $stmtDeleteLogs->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtDeleteLogs->execute();

            $sqlDeleteHistories = 'DELETE FROM login_history WHERE userId = :userId';
            $stmtDeleteHistories = $conn->prepare($sqlDeleteHistories);
            $stmtDeleteHistories->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmtDeleteHistories->execute();

            $sqlDeleted = 'DELETE FROM users WHERE id = :userId AND users.administrator = 0';
            $stmtDeleted = $conn->prepare($sqlDeleted);
            $stmtDeleted->bindParam(':userId', $userId, PDO::PARAM_INT);
            if ($stmtDeleted->execute()) {

                $logs = [
                    'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                    'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
                    'action_type' => 'DELETE',
                    'resource' => 'users',
                    'resource_id' => $userId,
                    'status' => 'Success',
                    'ip_address' => getIpAddress(),
                    'request_data' => json_encode(['userId' => $userId]),
                    'response_data' => json_encode(['userId' => $userId, 'displayname' => $user['displayname']]),
                    'remarks' => 'delete user',
                    'user_agent' => getUserAgent(),
                ];
                createLogs($conn, $logs);

                return true; 
            } else {
                throw new Exception('Failed to delete user');
            }
        } else {
            throw new Exception('User not found or already deleted');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
            'action_type' => 'DELETE',
            'resource' => 'users',
            'resource_id' => $userId,
            'status' => 'Failed',
            'ip_address' => getIpAddress(),
            'request_data' => json_encode(['userId' => $userId]),
            'response_data' => json_encode(['error' => $e->getMessage()]),
            'remarks' => 'Failed to delete user',
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        throw new Exception('Failed to delete user: ' . $e->getMessage());
    }
}

function checkUserExists($conn, $username) {
    try {
        $sql = 'SELECT COUNT(*) FROM users WHERE username = :username';
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $count = $stmt->fetchColumn();
            
            return $count > 0;
        } else {
            throw new Exception('Failed to execute check query');
        }
    } catch (Exception $e) {
        throw new Exception('Error checking user existence: ' . $e->getMessage());
    }
}

function createUser($conn, $user) {
    try {

        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);

        $sql = 'INSERT INTO users (username, displayname, password, roleId) 
                VALUES (:username, :displayname, :password, :roleId)'; 
        
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':username', $user['username'], PDO::PARAM_STR);
        $stmt->bindParam(':displayname', $user['displayname'], PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR); 
        $stmt->bindParam(':roleId', $user['role'], PDO::PARAM_INT);

        if ($stmt->execute()) {
            $userId = $conn->lastInsertId(); 
            $logs = [
                'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
                'action_type' => 'POST', 
                'resource' => 'users', 
                'resource_id' => $userId,  
                'status' => 'Success',  
                'ip_address' => getIpAddress(), 
                'request_data' => json_encode($user), 
                'response_data' => json_encode(['userId' => $userId, 'username' => $user['username']]), 
                'remarks' => 'Created new user',  
                'user_agent' => getUserAgent(),
            ];
            createLogs($conn, $logs);
            
            return $userId; 
        } else {
            throw new Exception('Failed to create user');
        }

    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
            'action_type' => 'POST', 
            'resource' => 'users', 
            'resource_id' => null,  
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode($user),  
            'response_data' => json_encode(['error' => $e->getMessage()]),  
            'remarks' => 'Failed to create user',  
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        throw new Exception('Error creating user: ' . $e->getMessage());
    }
}
