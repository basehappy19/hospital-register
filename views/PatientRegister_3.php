<?php
require './configs/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $register_data = [
        'patient_relative_info' => [
            "fullname" => ($_POST['prefix'] == 'อื่นๆ' ? $_POST['prefix_other'] : $_POST['prefix']) . $_POST['firstName'] . " " . $_POST['lastName'],
            "relationship" => isset($_POST['relationship']) ? $_POST['relationship'] != "อื่นๆ" ? $_POST['relationship'] : $_POST['relationship_other'] : "",
            "phone_number" => isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : "",
        ],

    ];
    if(isset($_POST['relativeSameAddress']) && $_POST['relativeSameAddress'] == 1){
        $register_data['address']['relative'] = $_SESSION['register_data']['state-1']['address']['own'];
        $register_data['address']['relative']['type'] = 'relative';
    } else {
        $register_data['address'] = [
            "relative" => [
                "village" => isset($_POST['village']) ? $_POST['village'] : "",
                "houseNumber" => isset($_POST['houseNumber']) ? $_POST['houseNumber'] : "",
                "no" => isset($_POST['no']) ? $_POST['no'] : "",
                "building" => isset($_POST['building']) ? $_POST['building'] : "",
                "floor" => isset($_POST['floor']) ? $_POST['floor'] : "",
                "road" => isset($_POST['road']) ? $_POST['road'] : "",
                "alley" => isset($_POST['alley']) ? $_POST['alley'] : "",
                "subdistrict" => isset($_POST['subdistrict']) ? $_POST['subdistrict'] : "",
                "district" => isset($_POST['district']) ? $_POST['district'] : "",
                "province" => isset($_POST['province']) ? $_POST['province'] : "",
                "zipcode" => isset($_POST['zipcode']) ? (int)$_POST['zipcode'] : "",
                "type" => 'relative',
            ]
        ];
    }

    $_SESSION['register_data']['state-3'] = $register_data;
    header('Location: ./?page=register_submit');
    exit();
}
?>

<head>
    <?php echo getTitle(text: 'ที่อยู่ / ญาติ'); ?>
</head>
<style>
    .tt-suggestion {
        padding: 10px;
        border-bottom: 1px solid #ccc;
    }

    .tt-selectable:hover {
        background-color: #f0f0f0;
    }
</style>
<main class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-center text-purple-800 mb-8">ระบบลงทะเบียนผู้ป่วยใหม่ออนไลน์</h1>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">ลงทะเบียนผู้ป่วยใหม่ออนไลน์</h2>

                <div class="flex justify-center space-x-8 mb-8">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-700">ข้อมูลส่วนบุคคล</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-500">สิทธิรักษาพยาบาล</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-500">ที่อยู่ / ญาติ</p>
                    </div>
                </div>
                <hr>
                <form id="register-state-3" action="" method="post" class="space-y-6">
                    <div class="mt-3">ข้อมูล / ญาติ</div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="relationship" class="block text-sm font-medium text-gray-700">ความสัมพันธ์</label>
                            <select onclick="clearBorder(this)" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" id="relationship" name="relationship" required>
                                <option value="">-- ความสัมพันธ์ --</option>
                                <option value="พ่อ">พ่อ</option>
                                <option value="แม่">แม่</option>
                                <option value="พี่">พี่</option>
                                <option value="น้อง">น้อง</option>
                                <option value="ปู่">ปู่</option>
                                <option value="ย่า">ย่า</option>
                                <option value="ตา">ตา</option>
                                <option value="ยาย">ยาย</option>
                                <option value="ลุง">ลุง</option>
                                <option value="ป้า">ป้า</option>
                                <option value="น้า">น้า</option>
                                <option value="อา">อา</option>
                                <option value="อื่นๆ">อื่นๆ ระบุ</option>
                            </select>
                        </div>
                        <div id="relationship_other_container">
                            <label for="relationship_other" class="block text-sm font-medium text-gray-700">ความสัมพันธ์ อื่นๆ</label>
                            <input onkeydown="clearBorder(this)" type="text" id="relationship_other" name="relationship_other" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                        </div>
                        <div>
                            <label for="prefix" class="block text-sm font-medium text-gray-700">คำนำหน้า</label>
                            <select onclick="clearBorder(this)" id="prefix" name="prefix" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" required>
                                <option value="">-- คำนำหน้า --</option>
                                <option class="option" value="นาย">นาย</option>
                                <option class="option" value="นาง">นาง</option>
                                <option class="option" value="นางสาว">นางสาว</option>
                                <option class="option" value="เด็กชาย">เด็กชาย</option>
                                <option class="option" value="เด็กหญิง">เด็กหญิง</option>
                                <option class="option" value="อื่นๆ">อื่นๆ ระบุ</option>
                            </select>
                        </div>
                        <div id="prefix_other_container" class="col-span-2">
                            <label for="prefix_other" class="block text-sm font-medium text-gray-700">คำนำหน้า อื่นๆ</label>
                            <input onkeydown="clearBorder(this)" type="text" id="prefix_other" name="prefix_other" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                        </div>
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700">ชื่อ</label>
                            <input onkeydown="clearBorder(this)" type="text" id="firstName" name="firstName" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" required>
                        </div>
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700">นามสกุล</label>
                            <input onkeydown="clearBorder(this)" type="text" id="lastName" name="lastName" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" required>
                        </div>
                        <div class="col-span-2">
                            <label for="phoneNumber" class="block text-sm font-medium text-gray-700">เบอร์โทรศัพท์</label>
                            <input onkeydown="clearBorder(this)" type="tel" id="phoneNumber" name="phoneNumber" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" required>
                        </div>
                    </div>
                    <hr>
                        <div class="">ที่อยู่ / ญาติ</div>
                        <div class="flex items-center mt-3">
                            <input id="relativeSameAddress" type="checkbox" name="relativeSameAddress" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label for="relativeSameAddress" class="ms-2 text-sm font-medium text-gray-900">ที่อยู่เดียวกัน</label>
                        </div>
                        <div id="address-container" class="grid sm:grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <label for="houseNumber" class="block text-sm font-medium text-gray-700">เลขที่</label>
                                <input onkeydown="clearBorder(this)" type="text" id="houseNumber" name="houseNumber" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            </div>
                            <div>
                                <label for="village" class="block text-sm font-medium text-gray-700">หมู่บ้าน</label>
                                <input onkeydown="clearBorder(this)" type="text" id="village" name="village" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            </div>
                            <div>
                                <label for="no" class="block text-sm font-medium text-gray-700">หมู่ที่</label>
                                <input onkeydown="clearBorder(this)" type="number" id="no" name="no" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            </div>
                            <div>
                                <label for="building" class="block text-sm font-medium text-gray-700">อาคาร</label>
                                <input onkeydown="clearBorder(this)" type="text" id="building" name="building" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            </div>
                            <div>
                                <label for="floor" class="block text-sm font-medium text-gray-700">ชั้น</label>
                                <input onkeydown="clearBorder(this)" type="number" id="floor" name="floor" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            </div>
                            <div>
                                <label for="road" class="block text-sm font-medium text-gray-700">ถนน</label>
                                <input onkeydown="clearBorder(this)" type="text" id="road" name="road" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            </div>
                            <div>
                                <label for="alley" class="block text-sm font-medium text-gray-700">ซอย</label>
                                <input onkeydown="clearBorder(this)" type="text" id="alley" name="alley" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            </div>
                            <div>
                                <label for="subdistrict" class="block text-sm font-medium text-gray-700">ตำบล</label>
                                <input onkeydown="clearBorder(this)" type="text" id="subdistrict" name="subdistrict" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            </div>
                            <div>
                                <label for="district" class="block text-sm font-medium text-gray-700">อำเภอ</label>
                                <input onkeydown="clearBorder(this)" type="text" id="district" name="district" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            </div>
                            <div>
                                <label for="province" class="block text-sm font-medium text-gray-700">จังหวัด</label>
                                <input onkeydown="clearBorder(this)" type="text" id="province" name="province" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            </div>
                            <div class="col-span-2">
                                <label for="zipcode" class="block text-sm font-medium text-gray-700">รหัสไปรษณีย์</label>
                                <input onkeydown="clearBorder(this)" type="number" id="zipcode" name="zipcode" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                            </div>
                        </div>
                    <hr>
                    <a href="javascript:history.back(1)" class="transition duration-200 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700">ย้อนกลับ</a>
                    <button type="button" onclick="submitTertiaryForm()" class="transition duration-200 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">ต่อไป</button>
                </form>
            </div>
        </div>

    </div>
</main>


<script src="./functions/ValidateForms/patientRegister-3.js"></script>
<script type="text/javascript" src="./lib/jquery.Thailand.min.js"></script>
<script type="text/javascript" src="./lib/JQL.min.js"></script>
<script type="text/javascript" src="./lib/typeahead.bundle.js"></script>

<script>
    $.Thailand({
        $district: $('#subdistrict'),
        $amphoe: $('#district'),
        $province: $('#province'),
        $zipcode: $('#zipcode'),

        onDataFill: function(data) {
            let html = '<b>ที่อยู่:</b> ตำบล' + data.district + ' อำเภอ' + data.amphoe + ' จังหวัด' + data.province + ' ' + data.zipcode;
            $('#demo2-output').html('')

        },

        onLoad: function() {
            $('#loader, .demo').toggle();
        }
    });
</script>

<script>
    $(document).ready(function() {
        $("#address-container").show();
        $("#relativeSameAddress").change(function() {
            if (this.checked) {
                $("#address-container").slideUp();
            } else {
                $("#address-container").slideDown();
            }
        });
        $("#prefix_other_container").hide();
        $("#prefix_other").hide();
        $("#gender_container").hide();
        $("#gender").hide();
        $("#prefix").change(function() {
            if ($(this).val() == "อื่นๆ") {
                $("#prefix_other_container").slideDown();
                $("#prefix_other").slideDown();
                $("#prefix_other").focus();
                $("#gender_container").slideDown();
                $("#gender").slideDown();
            } else {
                $("#prefix_other_container").slideUp();
                $("#prefix_other").slideUp();
                $("#gender_container").slideUp();
                $("#gender").slideUp();
                $("#prefix_other").val("");
                $("#gender").val("");
            }
        });
        $("#relationship_other_container").hide();
        $("#relationship_other").hide();
        $("#relationship").change(function() {
            if ($(this).val() == "อื่นๆ") {
                $("#relationship_other_container").slideDown();
                $("#relationship_other").slideDown();
                $("#relationship_other").focus();
            } else {
                $("#relationship_other_container").slideUp();
                $("#relationship_other").slideUp();
                $("#prefix_other").val("");
            }
        });
    });
</script>

<?php require_once './layouts/Footer.php'; ?>