<?php

function formatAddress($address) {
    if (!$address) {
        return '';
    }

    $houseNumber = isset($address['house_number']) ? $address['house_number'] : '';
    $village = isset($address['village']) ? $address['village'] : '';
    $no = isset($address['no']) ? $address['no'] : '';
    $building = isset($address['building']) ? $address['building'] : '';
    $floor = isset($address['floor']) ? $address['floor'] : '';
    $road = isset($address['road']) ? $address['road'] : '';
    $alley = isset($address['alley']) ? $address['alley'] : '';
    $subdistrict = isset($address['subdistrict']) ? $address['subdistrict'] : '';
    $district = isset($address['district']) ? $address['district'] : '';
    $province = isset($address['province']) ? $address['province'] : '';
    $zipcode = isset($address['zipcode']) ? $address['zipcode'] : '';

    $formattedAddress = [];
    
    if (!empty($houseNumber)) {
        $formattedAddress[] = "บ้านเลขที่ $houseNumber";
    }
    if (!empty($village)) {
        $formattedAddress[] = "หมู่บ้าน$village";
    }
    if (!empty($no)) {
        $formattedAddress[] = "หมู่ $no";
    }
    if (!empty($building)) {
        $formattedAddress[] = "อาคาร $building";
    }
    if (!empty($floor)) {
        $formattedAddress[] = "ชั้น $floor";
    }
    if (!empty($road)) {
        $formattedAddress[] = "ถนน$road";
    }
    if (!empty($alley)) {
        $formattedAddress[] = "ซอย$alley";
    }
    if (!empty($subdistrict)) {
        $formattedAddress[] = "ตำบล$subdistrict";
    }
    if (!empty($district)) {
        $formattedAddress[] = "อำเภอ$district";
    }
    if (!empty($province)) {
        $formattedAddress[] = "จังหวัด$province";
    }
    if (!empty($zipcode)) {
        $formattedAddress[] = "รหัสไปรษณีย์ $zipcode";
    }

    return implode(' ', $formattedAddress);
}

function formatDate($date) {
    if ($date) {
        $timestamp = strtotime($date);
        $thai_day_arr = array("อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์");
        $thai_month_arr = array(
            "0" => "",
            "1" => "มกราคม",
            "2" => "กุมภาพันธ์",
            "3" => "มีนาคม",
            "4" => "เมษายน",
            "5" => "พฤษภาคม",
            "6" => "มิถุนายน", 
            "7" => "กรกฎาคม",
            "8" => "สิงหาคม",
            "9" => "กันยายน",
            "10" => "ตุลาคม",
            "11" => "พฤศจิกายน",
            "12" => "ธันวาคม"
        );

        $day = $thai_day_arr[date("w", $timestamp)];
        $dayNumber = date("j", $timestamp);
        $month = $thai_month_arr[date("n", $timestamp)];
        $year = date("Y", $timestamp) + 543; // แปลงเป็นปี พ.ศ.

        return "วัน{$day}ที่ {$dayNumber} {$month} {$year}";
    }
    return "";
}

function formatDateTime($datetime) {
    if ($datetime) {
        $timestamp = strtotime($datetime);
        $thai_day_arr = array("อาทิตย์","จันทร์","อังคาร","พุธ","พฤหัสบดี","ศุกร์","เสาร์");
        $thai_month_arr = array(
            "0" => "",
            "1" => "มกราคม",
            "2" => "กุมภาพันธ์",
            "3" => "มีนาคม",
            "4" => "เมษายน",
            "5" => "พฤษภาคม",
            "6" => "มิถุนายน", 
            "7" => "กรกฎาคม",
            "8" => "สิงหาคม",
            "9" => "กันยายน",
            "10" => "ตุลาคม",
            "11" => "พฤศจิกายน",
            "12" => "ธันวาคม"
        );

        $day = $thai_day_arr[date("w", $timestamp)];
        $dayNumber = date("j", $timestamp);
        $month = $thai_month_arr[date("n", $timestamp)];
        $year = date("Y", $timestamp) + 543; // แปลงเป็นปี พ.ศ.
        $time = date("H:i", $timestamp);

        return "วัน{$day}ที่ {$dayNumber} {$month} {$year} {$time} น.";
    }
    return "";
}

