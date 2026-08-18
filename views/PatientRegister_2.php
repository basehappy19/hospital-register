<?php
require './configs/db.php';
require './functions/Locations/Locations.php';
require './functions/Departments/Departments.php';
require './functions/Patents/Patents.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $register_data = [
        "patient-symptoms" => [
            "description" => isset($_POST['symptoms']) ? $_POST['symptoms'] : "",
        ],
        "department" => isset($_POST['department']) ? (int)$_POST['department'] : "",
        "location" => isset($_POST['location']) ? (int)$_POST['location'] : "",
        "patent" => [
            "patentId" => ($_POST['patent'] != "อื่นๆ" ? $_POST['patent'] : null),
            "patent_other" => ($_POST['patent'] == "อื่นๆ" ? $_POST['patent_other'] : null),
        ],
        "patient" => [
            "dms" => (isset($_POST['dms']) ? 'have' : 'none')
        ]
    ];
    $_SESSION['register_data']['state-2'] = $register_data;
    header(header: 'Location: ./?page=register&state=3');
    exit();
}
?>

<head>
    <?php echo getTitle(text: 'สิทธิรักษาพยาบาล'); ?>
</head>

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
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                <form id="register-state-2" action="" method="post" class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700" for="symptoms">อาการ / โรคที่ต้องการตรวจ</label>
                            <textarea rows="3" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" type="text" id="symptoms" onkeydown="clearBorder(this)" name="symptoms" placeholder="อาการ" required></textarea>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="location" class="block text-sm font-medium text-gray-700">สถานที่ต้องการตรวจ</label>
                            <select class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" id="location" name="location" onclick="clearBorder(this)" required>
                                <option value="">-- โปรดเลือกสถานที่ --</option>
                                <?php $locations = getLocations($conn); ?> <?php foreach ($locations as $location): ?>
                                    <option value="<?php echo $location['id'] ?>"><?php echo $location['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="department" class="block text-sm font-medium text-gray-700">แผนกที่ต้องการตรวจ</label>
                            <select class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" id="department" name="department" onclick="clearBorder(this)" required>
                                <option value="">-- โปรดเลือกแผนก --</option>
                                    <?php $departments = getDepartments($conn);
                                    foreach ($departments as $department) : ?>
                                        <option value="<?php echo $department['id'] ?>"><?php echo $department['name'] ?></option>
                                    <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex gap-2 items-center">
                            <input id="dms" name="dms" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2">
                            <label for="dms" class="block text-sm font-medium text-gray-700">DMS Family</label>
                        </div>
                    </div>
                    <div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">สิทธิรักษาพยาบาล</label>
                            <div class="space-y-2">
                                <?php $patents = getPatents($conn); ?>
                                <?php $i = 1;
                                foreach ($patents as $patent) : ?>
                                    <div class="flex items-center p-2 bg-white rounded-lg shadow hover:bg-gray-100 transition duration-300 ease-in-out patent-option"
                                        data-radio="patent_<?php echo $i; ?>">
                                        <input id="patent_<?php echo $i; ?>"
                                            type="radio"
                                            value="<?php echo $patent['id'] ?>"
                                            name="patent"
                                            class="hidden">
                                        <label for="patent_<?php echo $i; ?>"
                                            class="ml-2 text-sm font-medium text-gray-900 cursor-pointer">
                                            <?php echo $patent['name'] ?>
                                        </label>
                                    </div>
                                <?php $i++;
                                endforeach; ?>
                                <div class="flex items-center p-2 bg-white rounded-lg shadow hover:bg-gray-100 transition duration-300 ease-in-out patent-option" data-radio="patent_other_radio">
                                    <input id="patent_other_radio" type="radio" value="อื่นๆ" name="patent" class="hidden">
                                    <label for="patent_other_radio" class="ml-2 text-sm font-medium text-gray-900 cursor-pointer">อื่นๆ</label>
                                </div>
                            </div>

                        </div>
                        <div class="mt-3" id="patent_other_container">
                            <label for="patent_other" class="block text-sm font-medium text-gray-700">ระบุชื่อโรงพยาบาลประกันสังคมรพ.อื่นๆ</label>
                            <input type="text" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" id="patent_other" name="patent_other" onkeydown="clearBorder(this)" placeholder="สิทธิการรักษาอื่นๆ">
                        </div>
                    </div>
                    <hr>
                    <a href="javascript:history.back(1)" class="transition duration-200 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700">ย้อนกลับ</a>
                    <button onclick="submitSecondaryForm()" type="button" class="transition duration-200 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">ต่อไป</button>
                </form>
            </div>
        </div>

    </div>
</main>
<script src="./functions/ValidateForms/patientRegister-2.js"></script>
<script>
    $(document).ready(function() {
        $('.patent-option').click(function() {
            let radioId = $(this).data('radio');
            $('#' + radioId).prop('checked', true).focus();

            $('.patent-option').css({
                'background-color': '',
                'border': ''
            });

            $(this).css({
                'background-color': '#f3f4f6',
                'border': '2px solid #3b82f6'
            });
            $('#' + radioId).trigger('change');
        });

        $("#patent_other_container").hide();

        $("input[name='patent']").change(function() {
            if ($(this).val() == "อื่นๆ") {
                $("#patent_other_container").slideDown();
                $("#patent_other").focus();
            } else {
                $("#patent_other_container").slideUp();
                $("#patent_other").val("");
            }
        });
    });
</script>
<?php require_once './layouts/Footer.php'; ?>