<?php

function countPatients($conn, $hn_status = null, $registerAt = null, $departmentId = null, $call_status = null, $deleted = null, $cid = null) {
    $sql = 'SELECT COUNT(*) as total FROM patients
    LEFT JOIN patient_visits as visits ON patients.id = visits.patientId
    LEFT JOIN departments as department ON visits.departmentId = department.id
    LEFT JOIN hn_verification_status as hn ON patients.id = hn.patientId
    LEFT JOIN status as hn_status ON hn.statusId = hn_status.id
    LEFT JOIN patient_call_status ON patients.id = patient_call_status.patientId';
    
    $filters = [];

    if ($hn_status !== null) {
        $filters[] = 'hn.statusId = :hn_status';
    }
    if ($registerAt !== null) {
        $filters[] = 'DATE(patients.registered_at) = :registerAt';
    }
    if ($departmentId !== null) {
        $filters[] = 'visits.departmentId = :departmentId';
    }
    if ($call_status !== null) {
        $filters[] = 'patient_call_status.call_statusId = :call_status';
    }
    if ($deleted !== null) {
        $filters[] = 'patients.deleted = :deleted';
    }
    if ($cid !== null) {
        $filters[] = 'patients.cid = :cid';
    }

    if (count($filters) > 0) {
        $sql .= ' WHERE ' . implode(' AND ', $filters);
    }

    $stmt = $conn->prepare($sql);

    if ($hn_status !== null) {
        $stmt->bindParam(':hn_status', $hn_status, PDO::PARAM_INT);
    }
    if ($registerAt !== null) {
        $stmt->bindParam(':registerAt', $registerAt, PDO::PARAM_STR);
    }
    if ($departmentId !== null) {
        $stmt->bindParam(':departmentId', $departmentId, PDO::PARAM_INT);
    }
    if ($call_status !== null) {
        $stmt->bindParam(':call_status', $call_status, PDO::PARAM_INT);
    }
    if ($deleted !== null) {
        $stmt->bindParam(':deleted', $deleted, PDO::PARAM_BOOL);
    }
    if ($cid !== null) {
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
    }

    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['total'];
}

function getAndSearchPatients($conn, $hn_status = null, $registerAt = null, $departmentId = null, $call_status = null, $deleted = null, $cid = null, $limit = 9, $page = 1) {
    try {

        $offset = ($page - 1) * $limit;
        $sql = 'SELECT 
        patients.id, 
        patients.cid, 
        patients.fullname, 
        patients.bday, 
        patients.age, 
        patients.gender, 
        patients.job, 
        patients.phone_number as phone_number, 
        patients.marital_status, 
        patients.dms, 
        patients.deleted, 
        patients.registered_at,
        department.name as department_name,
        location.name as location_name,
        patents.patent_other as patent_other,
        patent.name as patent_name,
        symptom.description as symptom_description,
        relative_info.fullname as relative_fullname,
        relative_info.relationship,
        relative_info.phone_number as relative_phone_number,
        patient_call_status.failure_note,
        call_status.name as call_status,
        patient_call_status.call_timestamp as call_timestamp,
        hn_status.name as hn_status,
        address_own.house_number as address_own_house_number,
        address_own.village as address_own_village,
        address_own.no as address_own_no,
        address_own.building as address_own_building,
        address_own.floor as address_own_floor,
        address_own.road as address_own_road,
        address_own.alley as address_own_alley,
        address_own.subdistrict as address_own_subdistrict,
        address_own.district as address_own_district,
        address_own.province as address_own_province,
        address_own.zipcode as address_own_zipcode,
        address_relative.house_number as address_relative_house_number,
        address_relative.village as address_relative_village,
        address_relative.no as address_relative_no,
        address_relative.building as address_relative_building,
        address_relative.floor as address_relative_floor,
        address_relative.road as address_relative_road,
        address_relative.alley as address_relative_alley,
        address_relative.subdistrict as address_relative_subdistrict,
        address_relative.district as address_relative_district,
        address_relative.province as address_relative_province,
        address_relative.zipcode as address_relative_zipcode
        FROM patients
        LEFT JOIN patent_for_patient as patents ON patients.id = patents.patientId
        LEFT JOIN patents as patent ON patents.patentId = patent.id
        LEFT JOIN patient_visits as visits ON patients.id = visits.patientId
        LEFT JOIN departments as department ON visits.departmentId = department.id
        LEFT JOIN locations as location ON visits.locationId = location.id
        LEFT JOIN patient_symptoms as symptom ON patients.id = symptom.patientId
        LEFT JOIN patient_relative_info as relative_info ON patients.id = relative_info.patientId
        LEFT JOIN patient_call_status ON patients.id = patient_call_status.patientId
        LEFT JOIN hn_verification_status as hn ON patients.id = hn.patientId
        LEFT JOIN status as call_status ON patient_call_status.call_statusId = call_status.id
        LEFT JOIN status as hn_status ON hn.statusId = hn_status.id
        LEFT JOIN address_for_patient ON address_for_patient.patientId = patients.id
        LEFT JOIN address as address_own ON address_for_patient.addressId = address_own.id
        LEFT JOIN address as address_relative ON address_for_patient.relativeAddressId = address_relative.id';

        $filters = [];
        if ($hn_status !== null) {
            $filters[] = 'hn.statusId = :hn_status';
        }
        if ($registerAt !== null) {
            $filters[] = 'DATE(patients.registered_at) = :registerAt';
        }
        if ($departmentId !== null) {
            $filters[] = 'visits.departmentId = :departmentId';
        }
        if ($call_status !== null) {
            $filters[] = 'patient_call_status.call_statusId = :call_status';
        }
        if ($deleted !== null) {
            $filters[] = 'patients.deleted = :deleted';
        }
        if ($cid !== null) {
            $filters[] = 'patients.cid = :cid';
        }

        if (count($filters) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $filters);
        }
        $sql .= ' ORDER BY 
            patients.registered_at DESC, 
            CASE 
                WHEN hn.statusId = 1 THEN 1
                WHEN call_status.id = 3 THEN 1 
                WHEN call_status.id = 5 THEN 2 
                WHEN hn.statusId = 2 THEN 3
                WHEN call_status.id = 4 THEN 3
                ELSE 4
            END ASC
        ';
        $sql .= ' LIMIT :limit OFFSET :offset';

        $stmt = $conn->prepare($sql);

        if ($hn_status !== null) {
            $stmt->bindParam(':hn_status', $hn_status, PDO::PARAM_INT);
        }
        if ($registerAt !== null) {
            $stmt->bindParam(':registerAt', $registerAt, PDO::PARAM_STR);
        }
        if ($departmentId !== null) {
            $stmt->bindParam(':departmentId', $departmentId, PDO::PARAM_INT);
        }

        if ($call_status !== null) {
            $stmt->bindParam(':call_status', $call_status, PDO::PARAM_INT);
        }

        if ($deleted !== null) {
            $stmt->bindParam(':deleted', $deleted, PDO::PARAM_BOOL);
        }

        if ($cid !== null) {
            $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($data) {
                $patientIds = [];
                $patientNames = [];
            
                foreach ($data as $patient) {
                    $patientIds[] = $patient['id'];
                    $patientNames[] = $patient['fullname'];
                }
            
                $logs = [
                    'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                    'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
                    'action_type' => 'GET', 
                    'resource' => 'patients', 
                    'resource_id' => implode(', ', $patientIds),  
                    'status' => 'Success',  
                    'ip_address' => getIpAddress(), 
                    'request_data' => json_encode(['hn_status' => $hn_status, 'registerAt' => $registerAt, 'departmentId' => $departmentId, 'call_status' => $call_status, 'deleted' => $deleted, 'cid' => $cid]),  
                    'response_data' => json_encode([
                        'patientIds' => $patientIds, 
                    ]),  
                    'remarks' => 'Found multiple patients', 
                    'user_agent' => getUserAgent(),
                ];
            
                createLogs($conn, $logs); 
            
                return $data; 
            } else {
                return [];
            }
            
        } else {
            throw new Exception('Failed to fetch patients');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null, 
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null, 
            'action_type' => 'GET', 
            'resource' => 'patients', 
            'resource_id' => null, 
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode(['hn_status' => $hn_status, 'registerAt' => $registerAt, 'departmentId' => $departmentId, 'call_status' => $call_status, 'deleted' => $deleted, 'cid' => $cid]),  
            'response_data' => json_encode(['error' => $e->getMessage()]),  
            'remarks' => 'Failed to get patients',  
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        throw new Exception('Failed to fetch patients: ' . $e->getMessage());
    }
}

function getPatientHNStatistics($conn) {
    try {
        $sqlTotalPatients = 'SELECT COUNT(*) AS total_patients FROM patients WHERE deleted = 0';
        $stmtTotal = $conn->prepare($sqlTotalPatients);
        $stmtTotal->execute();
        $totalPatients = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total_patients'];

        $sqlConfirmedHN = 'SELECT COUNT(*) AS confirmed_hn FROM hn_verification_status
                   JOIN patients ON patients.id = hn_verification_status.patientId 
                   WHERE hn_verification_status.statusId = 2 AND patients.deleted = 0';
        $stmtConfirmedHN = $conn->prepare($sqlConfirmedHN);
        $stmtConfirmedHN->execute();
        $confirmedHN = $stmtConfirmedHN->fetch(PDO::FETCH_ASSOC)['confirmed_hn'];

        $sqlNotConfirmedHN = 'SELECT COUNT(*) AS not_confirmed_hn FROM hn_verification_status
                      JOIN patients ON patients.id = hn_verification_status.patientId 
                      WHERE hn_verification_status.statusId = 1 AND patients.deleted = 0';
        $stmtNotConfirmedHN = $conn->prepare($sqlNotConfirmedHN);
        $stmtNotConfirmedHN->execute();
        $notConfirmedHN = $stmtNotConfirmedHN->fetch(PDO::FETCH_ASSOC)['not_confirmed_hn'];
        return [
            'total_patients' => $totalPatients,
            'confirmed_hn' => $confirmedHN,
            'not_confirmed_hn' => $notConfirmedHN,
        ];

    } catch (Exception $e) {

        throw new Exception('Failed to fetch patient hn statistics: ' . $e->getMessage());
    }
}

function getPatientCallStatistics($conn) {
    try {

        $sqlCalled = 'SELECT COUNT(*) AS Called FROM patient_call_status
                   JOIN patients ON patients.id = patient_call_status.patientId 
                   WHERE patient_call_status.call_statusId = 4 AND patients.deleted = 0';
        $stmtCalled = $conn->prepare($sqlCalled);
        $stmtCalled->execute();
        $Called = $stmtCalled->fetch(PDO::FETCH_ASSOC)['Called'];
        
        $sqlNotcalled = 'SELECT COUNT(*) AS Notcalled FROM patient_call_status
                   JOIN patients ON patients.id = patient_call_status.patientId 
                   WHERE patient_call_status.call_statusId = 3 AND patients.deleted = 0';
        $stmtNotcalled = $conn->prepare($sqlNotcalled);
        $stmtNotcalled->execute();
        $Notcalled = $stmtNotcalled->fetch(PDO::FETCH_ASSOC)['Notcalled'];

        $sqlFailed = 'SELECT COUNT(*) AS Failed FROM patient_call_status
                   JOIN patients ON patients.id = patient_call_status.patientId 
                   WHERE patient_call_status.call_statusId = 5 AND patients.deleted = 0';
        $stmtFailed = $conn->prepare($sqlFailed);
        $stmtFailed->execute();
        $failed = $stmtFailed->fetch(PDO::FETCH_ASSOC)['Failed'];

        return [
            'called' => $Called,
            'not_called' => $Notcalled,
            'failed' => $failed,
        ];

    } catch (Exception $e) {
        throw new Exception('Failed to fetch patient call statistics: ' . $e->getMessage());
    }
}

function getPatient($conn, $cid) {
    try {
        $sql = 'SELECT 
        patients.id, 
        patients.cid, 
        patients.fullname, 
        patients.bday, 
        patients.age, 
        patients.gender, 
        patients.job, 
        patients.phone_number as phone_number, 
        patients.marital_status, 
        patients.dms, 
        patients.registered_at,
        department.name as department_name,
        location.name as location_name,
        patents.patent_other as patent_other,
        patent.name as patent_name,
        symptom.description as symptom_description,
        relative_info.fullname as relative_fullname,
        relative_info.relationship,
        relative_info.phone_number as relative_phone_number,
        patient_call_status.failure_note,
        call_status.name as call_status,
        patient_call_status.call_timestamp as call_timestamp,
        hn_status.name as hn_status,
        address_own.house_number as address_own_house_number,
        address_own.village as address_own_village,
        address_own.no as address_own_no,
        address_own.building as address_own_building,
        address_own.floor as address_own_floor,
        address_own.road as address_own_road,
        address_own.alley as address_own_alley,
        address_own.subdistrict as address_own_subdistrict,
        address_own.district as address_own_district,
        address_own.province as address_own_province,
        address_own.zipcode as address_own_zipcode,
        address_relative.house_number as address_relative_house_number,
        address_relative.village as address_relative_village,
        address_relative.no as address_relative_no,
        address_relative.building as address_relative_building,
        address_relative.floor as address_relative_floor,
        address_relative.road as address_relative_road,
        address_relative.alley as address_relative_alley,
        address_relative.subdistrict as address_relative_subdistrict,
        address_relative.district as address_relative_district,
        address_relative.province as address_relative_province,
        address_relative.zipcode as address_relative_zipcode
        FROM patients
        LEFT JOIN patent_for_patient as patents ON patients.id = patents.patientId
        LEFT JOIN patents as patent ON patents.patentId = patent.id
        LEFT JOIN patient_visits as visits ON patients.id = visits.patientId
        LEFT JOIN departments as department ON visits.departmentId = department.id
        LEFT JOIN locations as location ON visits.locationId = location.id
        LEFT JOIN patient_symptoms as symptom ON patients.id = symptom.patientId
        LEFT JOIN patient_relative_info as relative_info ON patients.id = relative_info.patientId
        LEFT JOIN patient_call_status ON patients.id = patient_call_status.patientId
        LEFT JOIN hn_verification_status as hn ON patients.id = hn.patientId
        LEFT JOIN status as call_status ON patient_call_status.call_statusId = call_status.id
        LEFT JOIN status as hn_status ON hn.statusId = hn_status.id
        LEFT JOIN address_for_patient ON address_for_patient.patientId = patients.id
        LEFT JOIN address as address_own ON address_for_patient.addressId = address_own.id
        LEFT JOIN address as address_relative ON address_for_patient.relativeAddressId = address_relative.id
        WHERE patients.cid = :cid AND patients.deleted = 0';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $logs = [
                    'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                    'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
                    'action_type' => 'GET', 
                    'resource' => 'patients', 
                    'resource_id' => $data['id'],  
                    'status' => 'Success',  
                    'ip_address' => getIpAddress(), 
                    'request_data' => json_encode(['cid' => $cid]), 
                    'response_data' => json_encode(['patientId' => $data['id'], 'fullname' => $data['fullname']]),  // บันทึกข้อมูล response
                    'remarks' => 'Find patient',  
                    'user_agent' => getUserAgent(),
                ];
                createLogs($conn, $logs);

                return $data; 
            } else {
                return null;
            }
        } else {
            throw new Exception('Failed to fetch patient');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,  
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
            'action_type' => 'GET', 
            'resource' => 'patients', 
            'resource_id' => null, 
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode(['cid' => $cid]),  
            'response_data' => json_encode(['error' => $e->getMessage()]),  
            'remarks' => 'Failed to find patient',  
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        throw new Exception('Failed to fetch patient: ' . $e->getMessage());
    }
}

function getPatientForExportPDF($conn, $patientId) {
    try {
        $sql = 'SELECT 
        patients.id, 
        patients.cid, 
        patients.fullname, 
        patients.bday, 
        patients.age, 
        patients.gender, 
        patients.job, 
        patients.phone_number as phone_number, 
        patients.marital_status, 
        patients.dms, 
        patients.registered_at,
        department.name as department_name,
        location.name as location_name,
        patents.patent_other as patent_other,
        patent.name as patent_name,
        symptom.description as symptom_description,
        relative_info.fullname as relative_fullname,
        relative_info.relationship,
        relative_info.phone_number as relative_phone_number,
        patient_call_status.failure_note,
        call_status.name as call_status,
        patient_call_status.call_timestamp as call_timestamp,
        hn_status.name as hn_status,
        address_own.house_number as address_own_house_number,
        address_own.village as address_own_village,
        address_own.no as address_own_no,
        address_own.building as address_own_building,
        address_own.floor as address_own_floor,
        address_own.road as address_own_road,
        address_own.alley as address_own_alley,
        address_own.subdistrict as address_own_subdistrict,
        address_own.district as address_own_district,
        address_own.province as address_own_province,
        address_own.zipcode as address_own_zipcode,
        address_relative.house_number as address_relative_house_number,
        address_relative.village as address_relative_village,
        address_relative.no as address_relative_no,
        address_relative.building as address_relative_building,
        address_relative.floor as address_relative_floor,
        address_relative.road as address_relative_road,
        address_relative.alley as address_relative_alley,
        address_relative.subdistrict as address_relative_subdistrict,
        address_relative.district as address_relative_district,
        address_relative.province as address_relative_province,
        address_relative.zipcode as address_relative_zipcode
        FROM patients
        LEFT JOIN patent_for_patient as patents ON patients.id = patents.patientId
        LEFT JOIN patents as patent ON patents.patentId = patent.id
        LEFT JOIN patient_visits as visits ON patients.id = visits.patientId
        LEFT JOIN departments as department ON visits.departmentId = department.id
        LEFT JOIN locations as location ON visits.locationId = location.id
        LEFT JOIN patient_symptoms as symptom ON patients.id = symptom.patientId
        LEFT JOIN patient_relative_info as relative_info ON patients.id = relative_info.patientId
        LEFT JOIN patient_call_status ON patients.id = patient_call_status.patientId
        LEFT JOIN hn_verification_status as hn ON patients.id = hn.patientId
        LEFT JOIN status as call_status ON patient_call_status.call_statusId = call_status.id
        LEFT JOIN status as hn_status ON hn.statusId = hn_status.id
        LEFT JOIN address_for_patient ON address_for_patient.patientId = patients.id
        LEFT JOIN address as address_own ON address_for_patient.addressId = address_own.id
        LEFT JOIN address as address_relative ON address_for_patient.relativeAddressId = address_relative.id
        WHERE patients.id = :patientId AND patients.deleted = 0';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $logs = [
                    'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null, 
                    'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null, 
                    'action_type' => 'GET', 
                    'resource' => 'patients', 
                    'resource_id' => $data['id'],  
                    'status' => 'Success',  
                    'ip_address' => getIpAddress(), 
                    'request_data' => json_encode(['patientId' => $patientId]), 
                    'response_data' => json_encode(['patientId' => $data['id'], 'fullname' => $data['fullname']]),  
                    'remarks' => 'Export patient to pdf',  
                    'user_agent' => getUserAgent(),
                ];
                createLogs($conn, $logs);

                return $data; 
            } else {
                return null;
            }
        } else {
            throw new Exception('Failed to export patient pdf');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,  
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
            'action_type' => 'GET', 
            'resource' => 'patients', 
            'resource_id' => null, 
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode(['patientId' => $patientId]),  
            'response_data' => json_encode(['error' => $e->getMessage()]),  
            'remarks' => 'Failed to export patient pdf',  
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        throw new Exception('Failed to export patient pdf: ' . $e->getMessage());
    }
}

function getPatientForEdit($conn, $patientId) {
    try {
        $sql = 'SELECT 
        patients.id, 
        patients.cid, 
        patients.fullname, 
        patients.bday, 
        patients.age, 
        patients.gender, 
        patients.job, 
        patients.phone_number as phone_number, 
        patients.marital_status, 
        patients.dms, 
        patients.registered_at,
        department.name as department_name,
        location.name as location_name,
        patents.patent_other as patent_other,
        patent.name as patent_name,
        symptom.description as symptom_description,
        relative_info.fullname as relative_fullname,
        relative_info.relationship,
        relative_info.phone_number as relative_phone_number,
        patient_call_status.failure_note,
        call_status.name as call_status,
        patient_call_status.call_timestamp as call_timestamp,
        hn_status.name as hn_status,
        address_own.house_number as address_own_house_number,
        address_own.village as address_own_village,
        address_own.no as address_own_no,
        address_own.building as address_own_building,
        address_own.floor as address_own_floor,
        address_own.road as address_own_road,
        address_own.alley as address_own_alley,
        address_own.subdistrict as address_own_subdistrict,
        address_own.district as address_own_district,
        address_own.province as address_own_province,
        address_own.zipcode as address_own_zipcode,
        address_relative.house_number as address_relative_house_number,
        address_relative.village as address_relative_village,
        address_relative.no as address_relative_no,
        address_relative.building as address_relative_building,
        address_relative.floor as address_relative_floor,
        address_relative.road as address_relative_road,
        address_relative.alley as address_relative_alley,
        address_relative.subdistrict as address_relative_subdistrict,
        address_relative.district as address_relative_district,
        address_relative.province as address_relative_province,
        address_relative.zipcode as address_relative_zipcode
        FROM patients
        LEFT JOIN patent_for_patient as patents ON patients.id = patents.patientId
        LEFT JOIN patents as patent ON patents.patentId = patent.id
        LEFT JOIN patient_visits as visits ON patients.id = visits.patientId
        LEFT JOIN departments as department ON visits.departmentId = department.id
        LEFT JOIN locations as location ON visits.locationId = location.id
        LEFT JOIN patient_symptoms as symptom ON patients.id = symptom.patientId
        LEFT JOIN patient_relative_info as relative_info ON patients.id = relative_info.patientId
        LEFT JOIN patient_call_status ON patients.id = patient_call_status.patientId
        LEFT JOIN hn_verification_status as hn ON patients.id = hn.patientId
        LEFT JOIN status as call_status ON patient_call_status.call_statusId = call_status.id
        LEFT JOIN status as hn_status ON hn.statusId = hn_status.id
        LEFT JOIN address_for_patient ON address_for_patient.patientId = patients.id
        LEFT JOIN address as address_own ON address_for_patient.addressId = address_own.id
        LEFT JOIN address as address_relative ON address_for_patient.relativeAddressId = address_relative.id
        WHERE patients.id = :patientId AND patients.deleted = 0';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $logs = [
                    'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                    'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
                    'action_type' => 'GET', 
                    'resource' => 'patients', 
                    'resource_id' => $data['id'],  
                    'status' => 'Success',  
                    'ip_address' => getIpAddress(), 
                    'request_data' => json_encode(['patientId' => $patientId]), 
                    'response_data' => json_encode(['patientId' => $data['id'], 'fullname' => $data['fullname']]),  
                    'remarks' => 'Find patient for edit',  
                    'user_agent' => getUserAgent(),
                ];
                createLogs($conn, $logs);

                return $data; 
            } else {
                return null;
            }
        } else {
            throw new Exception('Failed to fetch patient');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
            'action_type' => 'GET', 
            'resource' => 'patients', 
            'resource_id' => null, 
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode(['patientId' => $patientId]),  
            'response_data' => json_encode(['error' => $e->getMessage()]),  
            'remarks' => 'Failed to find patient for edit',  
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        throw new Exception('Failed to fetch patient: ' . $e->getMessage());
    }
}

function checkPatientExists($conn, $cid) {
    try {
        $sql = 'SELECT COUNT(*) FROM patients WHERE cid = :cid AND deleted = 0';
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $count = $stmt->fetchColumn();
            
            return $count > 0;
        } else {
            throw new Exception('Failed to execute check query');
        }
    } catch (Exception $e) {
        throw new Exception('Error checking patient existence: ' . $e->getMessage());
    }
}

function patientRegister($conn, $data){
    try {
        $sql = 'INSERT INTO patients (cid, fullname, bday, age, gender, job, phone_number, marital_status, dms, registered_at)
                VALUES (:cid, :fullname, :bday, :age, :gender, :job, :phone_number, :marital_status, :dms, NOW())';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cid', $data['state-1']['patient']['cid'], PDO::PARAM_STR);
        $stmt->bindParam(':fullname', $data['state-1']['patient']['fullname'], PDO::PARAM_STR);
        $stmt->bindParam(':bday', $data['state-1']['patient']['bday'], PDO::PARAM_STR);
        $stmt->bindParam(':age', $data['state-1']['patient']['age'], PDO::PARAM_INT);
        $stmt->bindParam(':gender', $data['state-1']['patient']['gender'], PDO::PARAM_STR);
        $stmt->bindParam(':job', $data['state-1']['patient']['job'], PDO::PARAM_STR);
        $stmt->bindParam(':phone_number', $data['state-1']['patient']['phone_number'], PDO::PARAM_STR);
        $stmt->bindParam(':marital_status', $data['state-1']['patient']['marital_status'], PDO::PARAM_STR);
        $stmt->bindParam(':dms', $data['state-2']['patient']['dms'], PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new Exception('Failed to register patient');
        }

        $patientId = $conn->lastInsertId(); 

        if (!linkPatent($conn, $patientId, $data['state-2']['patent'])) {
            throw new Exception('Failed to link patent');
        }

        if (!addVisits($conn, $patientId, $data['state-2']['department'], $data['state-2']['location'])) {
            throw new Exception('Failed to add visits');
        }

        if (!addSymptom($conn, $patientId, $data['state-2']['patient-symptoms']['description'])) {
            throw new Exception('Failed to add symptoms');
        }

        if (!addAddress($conn, $patientId, $data['state-1']['address']['own'], $data['state-3']['address']['relative'])) {
            throw new Exception('Failed to add addresses');
        }

        if (!linkRelative($conn, $patientId, $data['state-3']['patient_relative_info'])) {
            throw new Exception('Failed to link relative info');
        }

        if (!link_Call_Status($conn, $patientId)) {
            throw new Exception('Failed to link call status');
        }

        if (!link_HN_Verification_Status($conn, $patientId)) {
            throw new Exception('Failed to link HN verification status');
        }

        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null, 
            'action_type' => 'POST', 
            'resource' => 'patients', 
            'resource_id' => $patientId, 
            'status' => 'Success',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode($data), 
            'response_data' => json_encode(['patientId' => $patientId]),
            'remarks' => 'Create patient',  
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        return true;

    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
            'action_type' => 'POST', 
            'resource' => 'patients', 
            'resource_id' => null, 
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode($data),
            'response_data' => json_encode(['error' => $e->getMessage()]), 
            'remarks' => 'Failed to create patient',  
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        return false;
    }
}

function patient_hn_confirm($conn, $patientId){
    try {
        $sql = 'UPDATE hn_verification_status SET statusId = 2 WHERE patientId = :patientId';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);

        if($stmt->execute()){
            $sql = 'SELECT id, fullname FROM patients WHERE id = :patientId';
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
            $stmt->execute();
            $patientData = $stmt->fetch(PDO::FETCH_ASSOC);

            $logs = [
                'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
                'action_type' => 'UPDATE', 
                'resource' => 'patients', 
                'resource_id' => $patientId,  
                'status' => 'Success',  
                'ip_address' => getIpAddress(), 
                'request_data' => json_encode(['patientId' => $patientId]), 
                'response_data' => json_encode(['patientId' => $patientData['id'], 'fullname' => $patientData['fullname']]), 
                'remarks' => 'Confirm HN status for patient',  
                'user_agent' => getUserAgent(),
            ];
            createLogs($conn, $logs);

            return true;
        } else {
            throw new Exception('Failed to confirm HN status');
        }

    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
            'action_type' => 'UPDATE', 
            'resource' => 'patients', 
            'resource_id' => $patientId, 
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode(['patientId' => $patientId]),  
            'response_data' => json_encode(['error' => $e->getMessage()]),  
            'remarks' => 'Failed to update HN status for patient',  
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        throw new Exception('Failed to confirm HN status: '. $e->getMessage());
    }
}

function patientUpdate($conn, $patientId, $data){
    try {
        $sql = 'UPDATE patients SET 
                cid = :cid, 
                fullname = :fullname, 
                bday = :bday, 
                age = :age, 
                gender = :gender, 
                job = :job, 
                phone_number = :phone_number, 
                marital_status = :marital_status, 
                dms = :dms
                WHERE id = :patientId';
                
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cid', $data['patient']['cid'], PDO::PARAM_STR);
        $stmt->bindParam(':fullname', $data['patient']['fullname'], PDO::PARAM_STR);
        $stmt->bindParam(':bday', $data['patient']['bday'], PDO::PARAM_STR);
        $stmt->bindParam(':age', $data['patient']['age'], PDO::PARAM_INT);
        $stmt->bindParam(':gender', $data['patient']['gender'], PDO::PARAM_STR);
        $stmt->bindParam(':job', $data['patient']['job'], PDO::PARAM_STR);
        $stmt->bindParam(':phone_number', $data['patient']['phone_number'], PDO::PARAM_STR);
        $stmt->bindParam(':marital_status', $data['patient']['marital_status'], PDO::PARAM_STR);
        $stmt->bindParam(':dms', $data['patient']['dms'], PDO::PARAM_STR);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            throw new Exception('Failed to update patient');
        }

        if (!updatePatentLink($conn, $patientId, $data['patent']['patent'])) {
            throw new Exception('Failed to update patent');
        }

        if (!updateVisits($conn, $patientId, $data['patent']['department'], $data['patent']['location'])) {
            throw new Exception('Failed to update visits');
        }

        if (!updateSymptom($conn, $patientId, $data['patent']['patient-symptoms']['description'])) {
            throw new Exception('Failed to update symptoms');
        }

        if (!updateAddress($conn, $patientId, $data['address']['own'], $data['address']['relative'])) {
            throw new Exception('Failed to update addresses');
        }

        if (!linkRelativeUpdate($conn, $patientId, $data['patient_relative_info'])) {
            throw new Exception('Failed to update relative info');
        }

        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
            'action_type' => 'UPDATE', 
            'resource' => 'patients', 
            'resource_id' => $patientId, 
            'status' => 'Success',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode($data), 
            'response_data' => json_encode(['patientId' => $patientId]),
            'remarks' => 'Update patient',
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        return true;

    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,  
            'action_type' => 'UPDATE',  
            'resource' => 'patients', 
            'resource_id' => $patientId, 
            'status' => 'Failed',  
            'ip_address' => getIpAddress(), 
            'request_data' => json_encode($data),
            'response_data' => json_encode(['error' => $e->getMessage()]), 
            'remarks' => 'Failed to update patient', 
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        return false;
    }
}

function removePatient($conn, $patientId, $reasonForDeletion) {
    try {
        $sqlCheck = 'SELECT * FROM patients WHERE id = :patientId AND deleted = 0';
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        $stmtCheck->execute();
        $patient = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        $archivedBy = isset($_SESSION['userId']) ? $_SESSION['userId'] : null;

        if ($patient) {
            $sqlMarkDeleted = 'UPDATE patients SET deleted = 1 WHERE id = :patientId';
            $stmtMarkDeleted = $conn->prepare($sqlMarkDeleted);
            $stmtMarkDeleted->bindParam(':patientId', $patientId, PDO::PARAM_INT);
            if ($stmtMarkDeleted->execute()) {

                $sqlInsertDeletedPatient = 'INSERT INTO deleted_patients 
                    (original_patient_id, deleted_at, reason_for_deletion, archived_by, createdAt, updatedAt) 
                    VALUES (:original_patient_id, NOW(), :reason_for_deletion, :archived_by, NOW(), NOW())';

                $stmtInsertDeletedPatient = $conn->prepare($sqlInsertDeletedPatient);
                $stmtInsertDeletedPatient->bindParam(':original_patient_id', $patientId, PDO::PARAM_INT);
                $stmtInsertDeletedPatient->bindParam(':reason_for_deletion', $reasonForDeletion, PDO::PARAM_STR);
                $stmtInsertDeletedPatient->bindParam(':archived_by', $archivedBy, PDO::PARAM_INT);
                $stmtInsertDeletedPatient->execute();

                $logs = [
                    'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                    'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
                    'action_type' => 'MOVE',
                    'resource' => 'patients',
                    'resource_id' => $patientId,
                    'status' => 'Success',
                    'ip_address' => getIpAddress(),
                    'request_data' => json_encode(['patientId' => $patientId]),
                    'response_data' => json_encode(['patientId' => $patientId, 'fullname' => $patient['fullname']]),
                    'remarks' => 'Marked patient as deleted and archived in deleted_patients table',
                    'user_agent' => getUserAgent(),
                ];
                createLogs($conn, $logs);

                return true; 
            } else {
                throw new Exception('Failed to mark patient as deleted');
            }
        } else {
            throw new Exception('Patient not found or already deleted');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
            'action_type' => 'MOVE',
            'resource' => 'patients',
            'resource_id' => $patientId,
            'status' => 'Failed',
            'ip_address' => getIpAddress(),
            'request_data' => json_encode(['patientId' => $patientId]),
            'response_data' => json_encode(['error' => $e->getMessage()]),
            'remarks' => 'Failed to mark patient as deleted',
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        throw new Exception('Failed to mark patient as deleted: ' . $e->getMessage());
    }
}

function permanentlyDeletePatient($conn, $patientId) {
    try {
        $sqlCheck = 'SELECT * FROM patients WHERE id = :patientId';
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        $stmtCheck->execute();
        $patient = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($patient) {
            $sqlAddress = 'SELECT addressId, relativeAddressId FROM address_for_patient WHERE address_for_patient.patientId = :patientId';
            $stmtAddress = $conn->prepare($sqlAddress);
            $stmtAddress->bindParam(':patientId', $patientId, PDO::PARAM_INT);
            $stmtAddress->execute();
            $address = $stmtAddress->fetch(PDO::FETCH_ASSOC);

            $sqlDeleteAddressOwn = 'DELETE FROM address WHERE id = :address_own_id AND address.type = "own"';
            $stmtDeleteAddressOwn = $conn->prepare($sqlDeleteAddressOwn);
            $stmtDeleteAddressOwn->bindParam(':address_own_id', $address['addressId'], PDO::PARAM_INT);
            $stmtDeleteAddressOwn->execute();

            $sqlDeleteAddressRelative = 'DELETE FROM address WHERE id = :address_relative_id AND address.type = "relative"';
            $stmtDeleteAddressRelative = $conn->prepare($sqlDeleteAddressRelative);
            $stmtDeleteAddressRelative->bindParam(':address_relative_id', $address['relativeAddressId'], PDO::PARAM_INT);
            $stmtDeleteAddressRelative->execute();

            $sqlDeleteFromDeletedPatients = 'DELETE FROM deleted_patients WHERE original_patient_id = :patientId';
            $stmtDeleteFromDeletedPatients = $conn->prepare($sqlDeleteFromDeletedPatients);
            $stmtDeleteFromDeletedPatients->bindParam(':patientId', $patientId, PDO::PARAM_INT);
            $stmtDeleteFromDeletedPatients->execute();

            $sqlDeleteFromPatients = 'DELETE FROM patients WHERE id = :patientId';
            $stmtDeleteFromPatients = $conn->prepare($sqlDeleteFromPatients);
            $stmtDeleteFromPatients->bindParam(':patientId', $patientId, PDO::PARAM_INT);

            if ($stmtDeleteFromPatients->execute()) {
                $logs = [
                    'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                    'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
                    'action_type' => 'DELETE',
                    'resource' => 'patients',
                    'resource_id' => $patientId,
                    'status' => 'Success',
                    'ip_address' => getIpAddress(),
                    'request_data' => json_encode(['patientId' => $patientId]),
                    'response_data' => json_encode(['patientId' => $patientId, 'fullname' => $patient['fullname']]),
                    'remarks' => 'Permanently deleted patient from database',
                    'user_agent' => getUserAgent(),
                ];
                createLogs($conn, $logs);

                return true;
            } else {
                throw new Exception('Failed to permanently delete patient');
            }
        } else {
            throw new Exception('Patient not found');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
            'action_type' => 'DELETE',
            'resource' => 'patients',
            'resource_id' => $patientId,
            'status' => 'Failed',
            'ip_address' => getIpAddress(),
            'request_data' => json_encode(['patientId' => $patientId]),
            'response_data' => json_encode(['error' => $e->getMessage()]),
            'remarks' => 'Failed to permanently delete patient',
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        throw new Exception('Failed to permanently delete patient: ' . $e->getMessage());
    }
}


function restorePatient($conn, $patientId) {
    try {
        $sqlCheck = 'SELECT * FROM patients WHERE id = :patientId AND deleted = 1';
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        $stmtCheck->execute();
        $patient = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($patient) {
            $sqlMarkRestored = 'UPDATE patients SET deleted = 0 WHERE id = :patientId';
            $stmtMarkRestored = $conn->prepare($sqlMarkRestored);
            $stmtMarkRestored->bindParam(':patientId', $patientId, PDO::PARAM_INT);
            if ($stmtMarkRestored->execute()) {

                $sqlDeleteFromDeletedPatients = 'DELETE FROM deleted_patients WHERE original_patient_id = :patientId';
                $stmtDeleteFromDeletedPatients = $conn->prepare($sqlDeleteFromDeletedPatients);
                $stmtDeleteFromDeletedPatients->bindParam(':patientId', $patientId, PDO::PARAM_INT);
                $stmtDeleteFromDeletedPatients->execute();

                $logs = [
                    'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
                    'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
                    'action_type' => 'RESTORE',
                    'resource' => 'patients',
                    'resource_id' => $patientId,
                    'status' => 'Success',
                    'ip_address' => getIpAddress(),
                    'request_data' => json_encode(['patientId' => $patientId]),
                    'response_data' => json_encode(['patientId' => $patientId, 'fullname' => $patient['fullname']]),
                    'remarks' => 'Restored patient and removed from deleted_patients table',
                    'user_agent' => getUserAgent(),
                ];
                createLogs($conn, $logs);

                return true;
            } else {
                throw new Exception('Failed to restore patient');
            }
        } else {
            throw new Exception('Patient not found or not marked as deleted');
        }
    } catch (Exception $e) {
        $logs = [
            'userId' => isset($_SESSION['userId']) ? $_SESSION['userId'] : null,
            'loginId' => isset($_SESSION['loginId']) ? $_SESSION['loginId'] : null,
            'action_type' => 'RESTORE',
            'resource' => 'patients',
            'resource_id' => $patientId,
            'status' => 'Failed',
            'ip_address' => getIpAddress(),
            'request_data' => json_encode(['patientId' => $patientId]),
            'response_data' => json_encode(['error' => $e->getMessage()]),
            'remarks' => 'Failed to restore patient',
            'user_agent' => getUserAgent(),
        ];
        createLogs($conn, $logs);

        throw new Exception('Failed to restore patient: ' . $e->getMessage());
    }
}

