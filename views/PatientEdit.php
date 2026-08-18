<?php
require './configs/db.php';
require './functions/Departments/Departments.php';
require './functions/Locations/Locations.php';
require './functions/Patents/Patents.php';

$departments = getDepartments($conn);
$locations = getLocations($conn);
$patents = getPatents($conn);

$patient = getPatientForEdit($conn, $patientId);
if (!$patient) {
    header('Location: ./?page=dashboard');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_edited = [
        "patient" => [
            "cid" => isset($_POST['cid']) ? $_POST['cid'] : "",
            "fullname" => isset($_POST['fullname']) ? $_POST['fullname'] : "",
            "phone_number" => isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : "",
            "bday" => isset($_POST['bday']) ? $_POST['bday'] : "",
            "age" => isset($_POST['age']) ? (int)$_POST['age'] : "",
            "gender" => isset($_POST['gender']) ? $_POST['gender'] : "",
            "job" => isset($_POST['job']) ? $_POST['job'] : "",
            "marital_status" => isset($_POST['marital_status']) ? $_POST['marital_status'] : "",
            "dms" => (isset($_POST['dms']) && $_POST['dms'] == 'have' ? 'have' : 'none')
        ],
        "patent" => [
            "patient-symptoms" => [
                "description" => isset($_POST['symptom_description']) ? $_POST['symptom_description'] : ""
            ],
            "department" => isset($_POST['department']) ? (int)$_POST['department'] : "",
            "location" => isset($_POST['location']) ? (int)$_POST['location'] : "",
            "patent" => [
                "patentId" => (isset($_POST['patent']) && $_POST['patent'] != "อื่นๆ" ? (int)$_POST['patent'] : null),
                "patent_other" => (isset($_POST['patent']) && $_POST['patent'] == "อื่นๆ" ? ($_POST['patent_other'] ?? null) : null),
            ],
        ],
        "address" => [
            "own" => [
                "village" => isset($_POST['address_own_village']) ? $_POST['address_own_village'] : "",
                "houseNumber" => isset($_POST['address_own_house_number']) ? $_POST['address_own_house_number'] : "",
                "no" => isset($_POST['address_own_no']) ? $_POST['address_own_no'] : "",
                "building" => isset($_POST['address_own_building']) ? $_POST['address_own_building'] : "",
                "floor" => isset($_POST['address_own_floor']) ? $_POST['address_own_floor'] : "",
                "road" => isset($_POST['address_own_road']) ? $_POST['address_own_road'] : "",
                "alley" => isset($_POST['address_own_alley']) ? $_POST['address_own_alley'] : "",
                "subdistrict" => isset($_POST['address_own_subdistrict']) ? $_POST['address_own_subdistrict'] : "",
                "district" => isset($_POST['address_own_district']) ? $_POST['address_own_district'] : "",
                "province" => isset($_POST['address_own_province']) ? $_POST['address_own_province'] : "",
                "zipcode" => isset($_POST['address_own_zipcode']) ? (int)$_POST['address_own_zipcode'] : "",
                "type" => 'own',
            ],
            "relative" => [
                "village" => isset($_POST['address_relative_village']) ? $_POST['address_relative_village'] : "",
                "houseNumber" => isset($_POST['address_relative_house_number']) ? $_POST['address_relative_house_number'] : "",
                "no" => isset($_POST['address_relative_no']) ? $_POST['address_relative_no'] : "",
                "building" => isset($_POST['address_relative_building']) ? $_POST['address_relative_building'] : "",
                "floor" => isset($_POST['address_relative_floor']) ? $_POST['address_relative_floor'] : "",
                "road" => isset($_POST['address_relative_road']) ? $_POST['address_relative_road'] : "",
                "alley" => isset($_POST['address_relative_alley']) ? $_POST['address_relative_alley'] : "",
                "subdistrict" => isset($_POST['address_relative_subdistrict']) ? $_POST['address_relative_subdistrict'] : "",
                "district" => isset($_POST['address_relative_district']) ? $_POST['address_relative_district'] : "",
                "province" => isset($_POST['address_relative_province']) ? $_POST['address_relative_province'] : "",
                "zipcode" => isset($_POST['address_relative_zipcode']) ? (int)$_POST['address_relative_zipcode'] : "",
                "type" => 'relative',
            ],
        ],
        'patient_relative_info' => [
            "fullname" => isset($_POST['relative_fullname']) ? $_POST['relative_fullname'] : "",
            "relationship" => ($_POST['relationship'] == 'อื่นๆ' ? $_POST['relationship_other'] : $_POST['relationship']),
            "phone_number" => isset($_POST['relative_phone_number']) ? $_POST['relative_phone_number'] : "",
        ],
    ];
    if (patientUpdate($conn, $patientId, $patient_edited)) {
        $_SESSION['alert'] = '
            <script>
                Swal.fire({
                    title: "แก้ไขข้อมูลสำเร็จ",
                    text: "ข้อมูลผู้ป่วยถูกอัพเดทเรียบร้อยแล้ว",
                    icon: "success",
                    showConfirmButton: false,
                    timer: 1500, 
                });
            </script>
        ';
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        $_SESSION['alert'] = '
            <script>
                Swal.fire({
                    title: "แก้ไขข้อมูลไม่ได้",
                    text: "มีข้อผิดพลาดบางอย่างทำให้ไม่สามารถอัพเดทได้ โปรดลองอีกครั้ง",
                    icon: "error",
                    showConfirmButton: false,
                    timer: 1500, 
                });
            </script>
        ';
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }
}


?>

<head>
    <?php echo getTitle(text: 'แก้ไขข้อมูลผู้ป่วย'); ?>
</head>

<main>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-purple-800 mb-8">แก้ไขข้อมูลผู้ป่วย</h1>
        <form id='patient_edit_form' action="" method="post" class="bg-white shadow-md rounded-lg p-8">
            <div class="flex flex-col space-y-6">
                <div class="w-full">
                    <h2 class="text-xl font-semibold text-purple-700 mb-4">ข้อมูลส่วนตัว</h2>
                </div>
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="fullname" class="block text-sm font-medium text-gray-700">ชื่อ - นามสกุล</label>
                        <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($patient['fullname']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="cid" class="block text-sm font-medium text-gray-700">เลขประจำตัวประชาชน</label>
                        <input type="text" id="cid" name="cid" value="<?php echo htmlspecialchars($patient['cid']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="bday" class="block text-sm font-medium text-gray-700">วันเกิด</label>
                        <input type="date" id="bday" name="bday" value="<?php echo $patient['bday']; ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="age" class="block text-sm font-medium text-gray-700">อายุ</label>
                        <input type="text" id="age" name="age" value="<?php echo $patient['age']; ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="gender" class="block text-sm font-medium text-gray-700">เพศ</label>
                        <select id="gender" name="gender" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            <option value="ชาย" <?php echo $patient['gender'] == 'ชาย' ? 'selected' : ''; ?>>ชาย</option>
                            <option value="หญิง" <?php echo $patient['gender'] == 'หญิง' ? 'selected' : ''; ?>>หญิง</option>
                        </select>
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="job" class="block text-sm font-medium text-gray-700">อาชีพ</label>
                        <input type="text" id="job" name="job" value="<?php echo htmlspecialchars($patient['job']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="marital_status" class="block text-sm font-medium text-gray-700">สถานะภาพ</label>
                        <select id="marital_status" name="marital_status" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            <option value="โสด" <?php echo $patient['marital_status'] == 'โสด' ? 'selected' : ''; ?>>โสด</option>
                            <option value="คู่สมรส" <?php echo $patient['marital_status'] == 'คู่สมรส' ? 'selected' : ''; ?>>คู่สมรส</option>
                            <option value="หม้าย" <?php echo $patient['marital_status'] == 'หม้าย' ? 'selected' : ''; ?>>หม้าย</option>
                            <option value="หย่า" <?php echo $patient['marital_status'] == 'หย่า' ? 'selected' : ''; ?>>หย่า</option>
                            <option value="สมณะ / นักบวช" <?php echo $patient['marital_status'] == 'สมณะ / นักบวช' ? 'selected' : ''; ?>>สมณะ / นักบวช</option>
                        </select>
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="phoneNumber" class="block text-sm font-medium text-gray-700">เบอร์โทรศัพท์</label>
                        <input type="tel" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars($patient['phone_number']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                </div>

                <div class="w-full mt-6">
                    <h2 class="text-xl font-semibold text-purple-700 mb-4">ที่อยู่</h2>
                </div>
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_own_house_number" class="block text-sm font-medium text-gray-700">บ้านเลขที่</label>
                        <input type="text" id="address_own_house_number" name="address_own_house_number" value="<?php echo htmlspecialchars($patient['address_own_house_number']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_own_village" class="block text-sm font-medium text-gray-700">หมู่บ้าน</label>
                        <input type="text" id="address_own_village" name="address_own_village" value="<?php echo htmlspecialchars($patient['address_own_village']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_own_no" class="block text-sm font-medium text-gray-700">หมู่ที่</label>
                        <input type="text" id="address_own_no" name="address_own_no" value="<?php echo htmlspecialchars($patient['address_own_no']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_own_building" class="block text-sm font-medium text-gray-700">อาคาร</label>
                        <input type="text" id="address_own_building" name="address_own_building" value="<?php echo htmlspecialchars($patient['address_own_building']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_own_floor" class="block text-sm font-medium text-gray-700">ชั้น</label>
                        <input type="text" id="address_own_floor" name="address_own_floor" value="<?php echo htmlspecialchars($patient['address_own_floor']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_own_road" class="block text-sm font-medium text-gray-700">ถนน</label>
                        <input type="text" id="address_own_road" name="address_own_road" value="<?php echo htmlspecialchars($patient['address_own_road']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_own_alley" class="block text-sm font-medium text-gray-700">ซอย</label>
                        <input type="text" id="address_own_alley" name="address_own_alley" value="<?php echo htmlspecialchars($patient['address_own_alley']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_own_subdistrict" class="block text-sm font-medium text-gray-700">ตำบล/แขวง</label>
                        <input type="text" id="address_own_subdistrict" name="address_own_subdistrict" value="<?php echo htmlspecialchars($patient['address_own_subdistrict']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_own_district" class="block text-sm font-medium text-gray-700">อำเภอ/เขต</label>
                        <input type="text" id="address_own_district" name="address_own_district" value="<?php echo htmlspecialchars($patient['address_own_district']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_own_province" class="block text-sm font-medium text-gray-700">จังหวัด</label>
                        <input type="text" id="address_own_province" name="address_own_province" value="<?php echo htmlspecialchars($patient['address_own_province']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full px-3 mb-6">
                        <label for="address_own_zipcode" class="block text-sm font-medium text-gray-700">รหัสไปรษณีย์</label>
                        <input type="text" id="address_own_zipcode" name="address_own_zipcode" value="<?php echo htmlspecialchars($patient['address_own_zipcode']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                </div>

                <div class="w-full mt-6">
                    <h2 class="text-xl font-semibold text-purple-700 mb-4">ข้อมูลการรักษา</h2>
                </div>
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="patent" class="block text-sm font-medium text-gray-700">สิทธิการรักษา</label>
                        <select id="patent" name="patent" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            <?php foreach ($patents as $patent): ?>
                                <option value="<?php echo htmlspecialchars($patent['id']); ?>" <?php echo $patient['patent_name'] == $patent['name'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($patent['name']); ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="อื่นๆ" <?php echo isset($patient['patent_other']) ? 'selected' : ''; ?>>
                                อื่นๆ
                            </option>
                        </select>
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6" id="patent_other_container">
                        <label for="patent_other" class="block text-sm font-medium text-gray-700">ระบุชื่อโรงพยาบาลประกันสังคมรพ.อื่นๆ</label>
                        <input type="text" value="<?php echo $patient['patent_other'] ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" id="patent_other" name="patent_other" onkeydown="clearBorder(this)" placeholder="สิทธิการรักษาอื่นๆ">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="department" class="block text-sm font-medium text-gray-700">แผนกที่ต้องการตรวจ</label>
                        <select id="department" name="department" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            <?php foreach ($departments as $department): ?>
                                <option value="<?php echo htmlspecialchars($department['id']); ?>" <?php echo $patient['department_name'] == $department['name'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($department['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="location" class="block text-sm font-medium text-gray-700">สถานที่ต้องการตรวจ</label>
                        <select id="location" name="location" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            <?php foreach ($locations as $location): ?>
                                <option value="<?php echo htmlspecialchars($location['id']); ?>" <?php echo $patient['location_name'] == $location['name'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($location['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="w-full px-3 mb-6">
                        <label for="dms" class="block text-sm font-medium text-gray-700">DMS Family</label>
                        <select id="dms" name="dms" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            <option value="มี" <?php echo $patient['dms'] == 'have' ? 'selected' : ''; ?>>มี</option>
                            <option value="ไม่มี" <?php echo $patient['dms'] == 'none' ? 'selected' : ''; ?>>ไม่มี</option>
                        </select>
                    </div>
                    <div class="w-full px-3 mb-6">
                        <label for="symptom_description" class="block text-sm font-medium text-gray-700">อาการเบื้องต้น</label>
                        <textarea id="symptom_description" name="symptom_description" rows="3" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm"><?php echo htmlspecialchars($patient['symptom_description']); ?></textarea>
                    </div>
                </div>


                <div class="w-full mb-6">
                    <h2 class="text-xl font-semibold text-purple-700 mb-4">ข้อมูลญาติ</h2>
                </div>
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="relative_fullname" class="block text-sm font-medium text-gray-700">ชื่อ - นามสกุล</label>
                        <input type="text" id="relative_fullname" name="relative_fullname" value="<?php echo htmlspecialchars($patient['relative_fullname']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="relationship" class="block text-sm font-medium text-gray-700">ความสัมพันธ์</label>
                        <input type="text" id="relationship" name="relationship" value="<?php echo htmlspecialchars($patient['relationship']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full px-3 mb-6">
                        <label for="relative_phone_number" class="block text-sm font-medium text-gray-700">เบอร์โทรศัพท์ญาติ</label>
                        <input type="text" id="relative_phone_number" name="relative_phone_number" value="<?php echo htmlspecialchars($patient['relative_phone_number']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                </div>

                <div class="w-full mt-6">
                    <h2 class="text-xl font-semibold text-purple-700 mb-4">ที่อยู่ญาติ</h2>
                </div>
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_relative_house_number" class="block text-sm font-medium text-gray-700">บ้านเลขที่</label>
                        <input type="text" id="address_relative_house_number" name="address_relative_house_number" value="<?php echo htmlspecialchars($patient['address_relative_house_number']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_relative_village" class="block text-sm font-medium text-gray-700">หมู่บ้าน</label>
                        <input type="text" id="address_relative_village" name="address_relative_village" value="<?php echo htmlspecialchars($patient['address_relative_village']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_relative_no" class="block text-sm font-medium text-gray-700">หมู่ที่</label>
                        <input type="text" id="address_relative_no" name="address_relative_no" value="<?php echo htmlspecialchars($patient['address_relative_no']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_relative_building" class="block text-sm font-medium text-gray-700">อาคาร</label>
                        <input type="text" id="address_relative_building" name="address_relative_building" value="<?php echo htmlspecialchars($patient['address_relative_building']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_relative_floor" class="block text-sm font-medium text-gray-700">ชั้น</label>
                        <input type="text" id="address_relative_floor" name="address_relative_floor" value="<?php echo htmlspecialchars($patient['address_relative_floor']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_relative_road" class="block text-sm font-medium text-gray-700">ถนน</label>
                        <input type="text" id="address_relative_road" name="address_relative_road" value="<?php echo htmlspecialchars($patient['address_relative_road']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_relative_alley" class="block text-sm font-medium text-gray-700">ซอย</label>
                        <input type="text" id="address_relative_alley" name="address_relative_alley" value="<?php echo htmlspecialchars($patient['address_relative_alley']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_relative_subdistrict" class="block text-sm font-medium text-gray-700">ตำบล/แขวง</label>
                        <input type="text" id="address_relative_subdistrict" name="address_relative_subdistrict" value="<?php echo htmlspecialchars($patient['address_relative_subdistrict']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_relative_district" class="block text-sm font-medium text-gray-700">อำเภอ/เขต</label>
                        <input type="text" id="address_relative_district" name="address_relative_district" value="<?php echo htmlspecialchars($patient['address_relative_district']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6">
                        <label for="address_relative_province" class="block text-sm font-medium text-gray-700">จังหวัด</label>
                        <input type="text" id="address_relative_province" name="address_relative_province" value="<?php echo htmlspecialchars($patient['address_relative_province']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                    <div class="w-full px-3 mb-6">
                        <label for="address_relative_zipcode" class="block text-sm font-medium text-gray-700">รหัสไปรษณีย์</label>
                        <input type="text" id="address_relative_zipcode" name="address_relative_zipcode" value="<?php echo htmlspecialchars($patient['address_relative_zipcode']); ?>" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <a href="./?page=dashboard" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">ยกเลิก</a>
                <button type="button" onclick="UpdatePatient()" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">บันทึกข้อมูล</button>
            </div>
        </form>
    </div>

</main>
<script>
    $(document).ready(function() {
        if ($("select[name='patent']").val() == "อื่นๆ") {
            $("#patent_other_container").show();
        } else {
            $("#patent_other_container").hide();
        }

        $("select[name='patent']").change(function() {
            if ($(this).val() == "อื่นๆ") {
                $("#patent_other_container").slideDown();
                $("#patent_other").focus();
            } else {
                $("#patent_other_container").slideUp();
                $("#patent_other").val("");
            }
        });
    });

    function UpdatePatient() {
        Swal.fire({
            title: 'ท่านแน่ใจหรือไม่?',
            text: "ยืนยันการแก้ไขข้อมูลผู้ป่วย",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'ยกเลิก',
            confirmButtonText: 'ยืนยันการแก้ไข',
        }).then((result) => {
            if (result.isConfirmed) {
                document.querySelector('#patient_edit_form').submit();
            }
        });
    }
</script>

<?php require_once './layouts/Footer.php'; ?>