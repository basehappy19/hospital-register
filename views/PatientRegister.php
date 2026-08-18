<?php
require './configs/db.php';
require './functions/Patents/Patents.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $register_data = [
        "patient" => [
            "cid" => isset($_POST['cid']) ? $_POST['cid'] : "",
            "fullname" => ($_POST['prefix'] == 'อื่นๆ' ? $_POST['prefix_other'] : $_POST['prefix']) . $_POST['firstName'] . " " . $_POST['lastName'],
            "phone_number" => isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : "",
            "bday" => sprintf('%04d-%02d-%02d', $_POST['bday_year'], $_POST['bday_month'], $_POST['bday_day']),
            "age" => isset($_POST['age']) ? (int)$_POST['age'] : "",
            "prefix" => $_POST['prefix'] == 'อื่นๆ' ? $_POST['prefix_other'] : $_POST['prefix'],
            "gender" => ($_POST['prefix'] == 'อื่นๆ')
                ? $_POST['gender']
                : (
                    ($_POST['prefix'] == "นาย" || $_POST['prefix'] == "เด็กชาย")
                    ? "ชาย"
                    : (
                        ($_POST['prefix'] == "นาง" || $_POST['prefix'] == "นางสาว" || $_POST['prefix'] == "เด็กหญิง")
                        ? "หญิง"
                        : ""
                    )
                ),
            "job" => isset($_POST['job']) ? $_POST['job'] : "",
            "marital_status" => isset($_POST['marital_status']) ? $_POST['marital_status'] : "",
        ],
        "address" => [
            "own" => [
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
                "type" => 'own',
            ]
        ]
    ];

    if (checkPatientExists($conn, $register_data['patient']['cid'])) {
        $_SESSION['alert'] = '
            <script>
            Swal.fire({
                icon: "error",
                title: "รหัสบัตรประชาชนนี้ได้ลงทะเบียนในระบบแล้ว",
                text: "ถ้ามีข้อผิดพลาด กรุณาติดต่อโรงพยาบาล",
                confirmButtonText: "โอเค",
                confirmButtonColor: "#5ad664",
            })
            </script>
        ';
        header(header: 'Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        $_SESSION['register_data']['state-1'] = $register_data;
        header(header: 'Location: ' . $_SERVER['REQUEST_URI'] . '&state=2');
        exit();
    }
}
?>

<head>
    <?php echo getTitle(text: 'ข้อมูลส่วนบุคคล'); ?>
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
<?php
if (isset($_SESSION['alert'])) {
    echo $_SESSION['alert'];
    unset($_SESSION['alert']);
}
?>
<main class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-center text-purple-800 mb-8">ระบบลงทะเบียนผู้ป่วยใหม่ออนไลน์</h1>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">ลงทะเบียนผู้ป่วยใหม่ออนไลน์</h2>

                <div class="flex justify-center space-x-8 mb-8">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-500">ที่อยู่ / ญาติ</p>
                    </div>
                </div>

                <form id="register-state-1" action="" method="post" class="space-y-6">
                    <div>
                        <label for="cid" class="block text-sm font-medium text-gray-700">เลขบัตรประชาชน</label>
                        <input onkeydown="clearBorder(this)" type="text" id="cid" name="cid" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" required>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
                        <div class="col-span-3">
                            <label for="age" class="block text-sm font-medium text-gray-700">อายุ</label>
                            <input onkeydown="clearBorder(this)" type="number" id="age" name="age" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" required>
                        </div>
                        <div>
                            <label for="bday_day" class="block text-sm font-medium text-gray-700">วันเกิด</label>
                            <select id="bday_day" name="bday_day" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" required>
                                <option value="">-- วัน --</option>
                                <?php for ($day = 1; $day <= 31; $day++) : ?>
                                    <option class="" value="<?= $day ?>"><?= $day ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label for="bday_month" class="block text-sm font-medium text-gray-700">เดือนเกิด</label>
                            <select id="bday_month" name="bday_month" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" required>
                                <option value="">-- เดือน --</option>
                                <?php $months = array(
                                    1 => 'มกราคม',
                                    2 => 'กุมภาพันธ์',
                                    3 => 'มีนาคม',
                                    4 => 'เมษายน',
                                    5 => 'พฤษภาคม',
                                    6 => 'มิถุนายน',
                                    7 => 'กรกฎาคม',
                                    8 => 'สิงหาคม',
                                    9 => 'กันยายน',
                                    10 => 'ตุลาคม',
                                    11 => 'พฤศจิกายน',
                                    12 => 'ธันวาคม',
                                );
                                foreach ($months as $monthNumber => $monthName) : ?>
                                    <option class="option" value="<?= $monthNumber ?>"><?= $monthName ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="bday_year" class="block text-sm font-medium text-gray-700">เดือนเกิด</label>
                            <select id="bday_year" name="bday_year" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" required>
                                <option value="">-- ปี --</option>
                                <?php
                                $currentYear = date(format: "Y");
                                $startYear = 1900;

                                for ($year = $currentYear; $year >= $startYear; $year--) :
                                    $thaiYear = $year + 543;
                                ?>
                                    <option class="option" value="<?= $year ?>"><?= $thaiYear ?></option>
                                <?php
                                endfor;
                                ?>

                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-3">
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
                        <div id="prefix_other_container">
                            <label for="prefix_other" class="block text-sm font-medium text-gray-700">คำนำหน้า อื่นๆ</label>
                            <input onkeydown="clearBorder(this)" type="text" id="prefix_other" name="prefix_other" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                        </div>
                        <div id="gender_container">
                            <label for="gender" class="block text-sm font-medium text-gray-700">เพศ</label>
                            <input onkeydown="clearBorder(this)" type="text" id="gender" name="gender" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                        </div>
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700">ชื่อ</label>
                            <input onkeydown="clearBorder(this)" type="text" id="firstName" name="firstName" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" required>
                        </div>
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700">นามสกุล</label>
                            <input onkeydown="clearBorder(this)" type="text" id="lastName" name="lastName" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" required>
                        </div>
                    </div>
                    <div>
                        <label for="phoneNumber" class="block text-sm font-medium text-gray-700">เบอร์โทรศัพท์</label>
                        <input onkeydown="clearBorder(this)" type="tel" id="phoneNumber" name="phoneNumber" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" required>
                    </div>

                    <div>
                        <span class="block text-sm font-medium text-gray-700 mb-2">สถานะภาพ</span>
                        <div class="space-y-2">
                            <label class="inline-flex items-center">
                                <input type="radio" name="marital_status" value="โสด" class="form-radio text-purple-600" required>
                                <span class="ml-2">โสด</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="marital_status" value="คู่สมรส" class="form-radio text-purple-600" required>
                                <span class="ml-2">คู่สมรส</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="marital_status" value="หม้าย" class="form-radio text-purple-600" required>
                                <span class="ml-2">หม้าย</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="marital_status" value="หย่า" class="form-radio text-purple-600" required>
                                <span class="ml-2">หย่า</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="marital_status" value="สมณะ / นักบวช" class="form-radio text-purple-600" required>
                                <span class="ml-2">สมณะ / นักบวช</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="job" class="block text-sm font-medium text-gray-700">อาชีพ</label>
                        <input onkeydown="clearBorder(this)" type="text" id="job" name="job" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" required>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">ที่อยู่</h3>
                        <div class="grid sm:grid-cols-1 gap-6 md:grid-cols-2">
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
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="submitFirstForm()" class="transition duration-200 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                            ต่อไป
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php include './components/Register/SectionInfo.php'; ?>
    </div>
</main>

<script>
    $(document).ready(function() {
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
    });
</script>

<script src="./functions/ValidateForms/patientRegister-1.js"></script>
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
<?php require_once './layouts/Footer.php'; ?>