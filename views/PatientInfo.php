<?php
require './configs/db.php';
if (isset($_SESSION['cid'])) {
    $patient = getPatient($conn, $_SESSION['cid']);
    if (!$patient) {
        $_SESSION['error'] = true;
        header('Location: ./?page=patient_check');
        exit();
    }
} else {
    header('Location: ./?page=patient_check');
    exit();
}
?>

<head>
    <?php echo getTitle(text: "ข้อมูลบัตรผู้ป่วย {$patient['fullname']}"); ?>
</head>


<main class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-center text-purple-600 mb-6">ข้อมูลบัตรผู้ป่วย</h1>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="space-y-4">
                <?php if (isset($patient['call_status']) && $patient['call_status'] == 'ติดต่อไม่ได้') : ?>
                    <?php
                        $call_status = $patient['call_status'];
                        $call_timestamp = $patient['call_timestamp'];
                        include_once './components/Status/FailedCallStatus.php';
                    ?>
                <?php endif; ?>

                <?php if (isset($patient['hn_status'])) : ?>
                    <?php
                    $hn_status = $patient['hn_status'];
                    if ($hn_status == 'รอเจ้าหน้าที่ยืนยัน') {
                        include_once './components/Status/UnconfirmedStatus.php';
                    } elseif ($hn_status == 'ยืนยันแล้ว') {
                        include_once './components/Status/ConfirmedStatus.php';
                    } else {
                        include_once './components/Status/UnavailableStatus.php';
                    }
                    ?>
                <?php endif; ?>

                <div class="mb-4">
                    <h2 class="text-2xl font-semibold text-gray-800">
                        <?php echo htmlspecialchars($patient['fullname'], ENT_QUOTES, 'UTF-8') ?>
                    </h2>
                    <hr class="border-gray-300 my-2">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php
                    $patient_info = [
                        'วันที่ลงทะเบียน' => [
                            'key' => 'registered_at',
                            'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>'
                        ],
                        'เลขบัตรประชาชน' => [
                            'key' => 'cid',
                            'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>'
                        ],
                        'เพศ' => [
                            'key' => 'gender',
                            'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>'
                        ],
                        'เบอร์โทรศัพท์' => [
                            'key' => 'phone_number',
                            'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>'
                        ],
                        'วันเกิด' => [
                            'key' => 'bday',
                            'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z"></path></svg>'
                        ],
                        'อายุ' => [
                            'key' => 'age',
                            'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                        ],
                        'สถานะภาพ' => [
                            'key' => 'marital_status',
                            'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>'
                        ],
                        'อาชีพ' => [
                            'key' => 'job',
                            'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>'
                        ],
                        'สิทธิการรักษาพยาบาล' => [
                            'key' => 'patent_name',
                            'alternative_key' => 'patent_other',
                            'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>'
                        ],
                        'อาการ' => [
                            'key' => 'symptom_description',
                            'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>'
                        ],
                        'สถานที่ต้องการตรวจ' => [
                            'key' => 'location_name',
                            'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>'
                        ],
                        'แผนกที่ต้องการตรวจ' => [
                            'key' => 'department_name',
                            'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>'
                        ],
                    ];

                    foreach ($patient_info as $label => $info) :
                    ?>
                        <div class="flex flex-col mb-4">
                            <div class="flex items-center mb-1">
                                <?php echo $info['icon']; ?>
                                <span class="text-sm font-medium text-gray-500"><?php echo $label; ?></span>
                            </div>
                            <span class="text-lg text-gray-800 ml-7">
                                <?php
                                    if ($info['key'] == 'registered_at') {
                                        $value = isset($patient['registered_at']) ? formatDateTime($patient['registered_at']) : '';
                                    } elseif ($info['key'] == 'patent_name' && empty($patient['patent_name'])) {
                                        $value = htmlspecialchars($patient['patent_other'], ENT_QUOTES, 'UTF-8');
                                    } else {
                                        $value = $info['key'] == 'age'
                                            ? htmlspecialchars($patient[$info['key']], ENT_QUOTES, 'UTF-8') . ' ปี'
                                            : htmlspecialchars($patient[$info['key']], ENT_QUOTES, 'UTF-8');
                                    }
                                    echo $value;
                                ?>
                            </span>
                        </div>
                    <?php endforeach; ?>

                    <?php if (isset($patient['dms']) && $patient['dms'] == 'have') : ?>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-500">DMS FAMILY</span>
                            <span class="text-lg text-gray-800">มี</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mt-6">
                    <span class="text-sm font-medium text-gray-500">ที่อยู่ของท่าน</span>
                    <p class="text-lg text-gray-800">
                        <?php
                        $address = [
                            'house_number' => $patient['address_own_house_number'],
                            'village' => $patient['address_own_village'],
                            'no' => $patient['address_own_no'],
                            'building' => $patient['address_own_building'],
                            'floor' => $patient['address_own_floor'],
                            'road' => $patient['address_own_road'],
                            'alley' => $patient['address_own_alley'],
                            'subdistrict' => $patient['address_own_subdistrict'],
                            'district' => $patient['address_own_district'],
                            'province' => $patient['address_own_province'],
                            'zipcode' => $patient['address_own_zipcode'],
                        ];
                        echo htmlspecialchars(formatAddress($address), ENT_QUOTES, 'UTF-8');
                        ?>
                    </p>
                </div>

                <hr class="border-gray-300 my-6">

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">ข้อมูลญาติ</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php
                        $relative_info = [
                            'ชื่อ - นามสกุลญาติ' => [
                                'key' => 'relative_fullname',
                                'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>'
                            ],
                            'ความสัมพันธ์ญาติ' => [
                                'key' => 'relationship',
                                'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>'
                            ],
                            'เบอร์โทรศัพท์ญาติ' => [
                                'key' => 'relative_phone_number',
                                'icon' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>'
                            ],
                        ];

                        foreach ($relative_info as $label => $info) :
                        ?>
                            <div class="flex flex-col mb-4">
                                <div class="flex items-center mb-1">
                                    <?php echo $info['icon']; ?>
                                    <span class="text-sm font-medium text-gray-500"><?php echo $label; ?></span>
                                </div>
                                <span class="text-lg text-gray-800 ml-7"><?php echo htmlspecialchars($patient[$info['key']], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm font-medium text-gray-500">ที่อยู่ญาติ</span>
                        <p class="text-lg text-gray-800">
                            <?php
                            $address = [
                                'house_number' => $patient['address_relative_house_number'],
                                'village' => $patient['address_relative_village'],
                                'no' => $patient['address_relative_no'],
                                'building' => $patient['address_relative_building'],
                                'floor' => $patient['address_relative_floor'],
                                'road' => $patient['address_relative_road'],
                                'alley' => $patient['address_relative_alley'],
                                'subdistrict' => $patient['address_relative_subdistrict'],
                                'district' => $patient['address_relative_district'],
                                'province' => $patient['address_relative_province'],
                                'zipcode' => $patient['address_relative_zipcode'],
                            ];
                            echo htmlspecialchars(formatAddress($address), ENT_QUOTES, 'UTF-8');
                            ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-right">
                <a href="./?page=patient_check_exit" class="text-purple-600 hover:text-purple-800 transition duration-300">กลับหน้าหลัก</a>
            </div>
        </div>

        <?php include './components/Register/SectionInfo.php'; ?>
    </div>
</main>


<?php require_once './layouts/Footer.php'; ?>