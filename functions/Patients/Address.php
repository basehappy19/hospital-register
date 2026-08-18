<?php

function addSingleAddress($conn, $address) {
    try {
        $sql = 'INSERT INTO address (house_number, village, no, building, floor, road, alley, subdistrict, district, province, zipcode, type)
            VALUES (:house_number, :village, :no, :building, :floor, :road, :alley, :subdistrict, :district, :province, :zipcode, :type)';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':house_number', $address['houseNumber'], PDO::PARAM_STR);
        $stmt->bindParam(':village', $address['village'], PDO::PARAM_STR);
        $stmt->bindParam(':no', $address['no'], PDO::PARAM_STR);
        $stmt->bindParam(':building', $address['building'], PDO::PARAM_STR);
        $stmt->bindParam(':floor', $address['floor'], PDO::PARAM_STR);
        $stmt->bindParam(':road', $address['road'], PDO::PARAM_STR);
        $stmt->bindParam(':alley', $address['alley'], PDO::PARAM_STR);
        $stmt->bindParam(':subdistrict', $address['subdistrict'], PDO::PARAM_STR);
        $stmt->bindParam(':district', $address['district'], PDO::PARAM_STR);
        $stmt->bindParam(':province', $address['province'], PDO::PARAM_STR);
        $stmt->bindParam(':zipcode', $address['zipcode'], PDO::PARAM_INT);
        $stmt->bindParam(':type', $address['type'], PDO::PARAM_STR);
        
        if($stmt->execute()) {
            return $conn->lastInsertId();
        } else {
            throw new Exception('Failed to insert address');
        }
    } catch (Exception $e) {
        throw new Exception('Failed to add single address: ' . $e->getMessage());
    }
}

function linkAddressToPatient($conn, $patientId, $ownAddressId, $relativeAddressId) {
    try {
        $sql = 'INSERT INTO address_for_patient (patientId, addressId, relativeAddressId)
            VALUES (:patientId, :addressId, :relativeAddressId)';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        $stmt->bindParam(':addressId', $ownAddressId, PDO::PARAM_INT);
        $stmt->bindParam(':relativeAddressId', $relativeAddressId, PDO::PARAM_INT);
    
        if($stmt->execute()){
            return true;
        } else {
            throw new Exception('Failed to link Address');
        };
    } catch (Exception $e) {
        throw new Exception('Failed to link address to patient: ' . $e->getMessage());
    }
}

function addAddress($conn, $patientId, $ownAddress, $relativeAddress) {
    try {
        $ownAddressId = addSingleAddress($conn, $ownAddress);
        if(!$ownAddressId) {
            throw new Exception('Own address insertion failed');
        }

        $relativeAddressId = addSingleAddress($conn, $relativeAddress);
        if(!$relativeAddressId) {
            throw new Exception('Relative address insertion failed');
        }

        if(linkAddressToPatient($conn, $patientId, $ownAddressId, $relativeAddressId)) {
            return true; 
        } else {
            throw new Exception('Failed to link addresses to patient');
        }
    } catch (Exception $e) {
        throw new Exception('Failed to add address: ' . $e->getMessage());
    }
}


function updateSingleAddress($conn, $addressId, $address) {
    try {
        $sql = 'UPDATE address SET 
            house_number = :house_number,
            village = :village,
            no = :no,
            building = :building,
            floor = :floor,
            road = :road,
            alley = :alley,
            subdistrict = :subdistrict,
            district = :district,
            province = :province,
            zipcode = :zipcode,
            type = :type
            WHERE id = :addressId';
            
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':house_number', $address['houseNumber'], PDO::PARAM_STR);
        $stmt->bindParam(':village', $address['village'], PDO::PARAM_STR);
        $stmt->bindParam(':no', $address['no'], PDO::PARAM_STR);
        $stmt->bindParam(':building', $address['building'], PDO::PARAM_STR);
        $stmt->bindParam(':floor', $address['floor'], PDO::PARAM_STR);
        $stmt->bindParam(':road', $address['road'], PDO::PARAM_STR);
        $stmt->bindParam(':alley', $address['alley'], PDO::PARAM_STR);
        $stmt->bindParam(':subdistrict', $address['subdistrict'], PDO::PARAM_STR);
        $stmt->bindParam(':district', $address['district'], PDO::PARAM_STR);
        $stmt->bindParam(':province', $address['province'], PDO::PARAM_STR);
        $stmt->bindParam(':zipcode', $address['zipcode'], PDO::PARAM_INT);
        $stmt->bindParam(':type', $address['type'], PDO::PARAM_STR);
        $stmt->bindParam(':addressId', $addressId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception('Failed to update address');
        }
    } catch (Exception $e) {
        throw new Exception('Failed to update single address: ' . $e->getMessage());
    }
}

function updateAddressLinkForPatient($conn, $patientId, $ownAddressId, $relativeAddressId) {
    try {
        $sql = 'UPDATE address_for_patient SET 
            addressId = :addressId, 
            relativeAddressId = :relativeAddressId 
            WHERE patientId = :patientId';
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        $stmt->bindParam(':addressId', $ownAddressId, PDO::PARAM_INT);
        $stmt->bindParam(':relativeAddressId', $relativeAddressId, PDO::PARAM_INT);
    
        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception('Failed to update address link for patient');
        }
    } catch (Exception $e) {
        throw new Exception('Failed to update address link for patient: ' . $e->getMessage());
    }
}

function updateAddress($conn, $patientId, $ownAddress, $relativeAddress) {
    try {
        $sql = 'SELECT addressId, relativeAddressId FROM address_for_patient WHERE patientId = :patientId';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':patientId', $patientId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            throw new Exception('No address found for the given patientId');
        }
        
        $ownAddressId = $result['addressId'];
        $relativeAddressId = $result['relativeAddressId'];

        if (!updateSingleAddress($conn, $ownAddressId, $ownAddress)) {
            throw new Exception('Own address update failed');
        }

        if (!updateSingleAddress($conn, $relativeAddressId, $relativeAddress)) {
            throw new Exception('Relative address update failed');
        }

        if (updateAddressLinkForPatient($conn, $patientId, $ownAddressId, $relativeAddressId)) {
            return true;
        } else {
            throw new Exception('Failed to update address link for patient');
        }
    } catch (Exception $e) {
        throw new Exception('Failed to update address: ' . $e->getMessage());
    }
}

