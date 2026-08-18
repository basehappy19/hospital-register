<?php
require './configs/db.php';
require './functions/Departments/Departments.php';

$departments = getDepartments($conn);

if (isset($_GET['hn_status']) && $_GET['hn_status'] == 'confirmed') {
    $hn_status = 2;
} elseif (isset($_GET['hn_status']) && $_GET['hn_status'] == 'not_confirmed') {
    $hn_status = 1;
} else {
    $hn_status = null;
}

if (isset($_GET['call_status']) && $_GET['call_status'] == 'called') {
    $call_status = 4;
} elseif (isset($_GET['call_status']) && $_GET['call_status'] == 'not_called') {
    $call_status = 3;
} elseif (isset($_GET['call_status']) && $_GET['call_status'] == 'failed') {
    $call_status = 5;
} else {
    $call_status = null;
}

$registerAt = isset($_GET['registerAt']) && $_GET['registerAt'] != '' ? $_GET['registerAt'] : null;
$departmentId = isset($_GET['departmentId']) && $_GET['departmentId'] != '' ? $_GET['departmentId'] : null;
$cid = isset($_GET['cid']) && $_GET['cid'] != '' ? $_GET['cid'] : null;
$deleted = (isset($_GET['deleted']) && $_GET['deleted'] != '' ? ($_GET['deleted'] == 'true' ? 1 : 0) : 0);

$page = isset($_GET['page_query']) && is_numeric($_GET['page_query']) ? (int)$_GET['page_query'] : 1;
$limit = 9;

$allowed_hn_status = null;
$allowed_registerAt = null;
$allowed_departmentId = null;
$allowed_call_status = null;
$allowed_deleted_status = null;

if ($user['roleId'] == 1) {
    $allowed_hn_status = $hn_status;
    $allowed_registerAt = $registerAt;
    $allowed_departmentId = $departmentId;
    $allowed_deleted_status = $deleted;
} elseif ($user['roleId'] == 3) {
    $allowed_registerAt = $registerAt;
    $allowed_departmentId = $departmentId;
    $allowed_call_status = $call_status;
    $allowed_deleted_status = 0;
} elseif ($user['roleId'] == 2) {
    $allowed_hn_status = $hn_status;
    $allowed_registerAt = $registerAt;
    $allowed_departmentId = $departmentId;
    $allowed_call_status = $call_status;
    $allowed_deleted_status = $deleted;
}

$patients = getAndSearchPatients(
    conn: $conn,
    hn_status: $allowed_hn_status,
    registerAt: $allowed_registerAt,
    departmentId: $allowed_departmentId,
    call_status: $allowed_call_status,
    deleted: $allowed_deleted_status,
    cid: $cid,
    limit: $limit,
    page: $page
);

if ($user['roleId'] == 1 || $user['roleId'] == 2) {
    $patients_hn_static = getPatientHNStatistics($conn);
}

if ($user['roleId'] == 2 || $user['roleId'] == 3) {
    $patients_call_static = getPatientCallStatistics($conn);
}

$total_patients = countPatients($conn, $hn_status, $registerAt, $departmentId, $call_status, $deleted, $cid);
$total_pages = ceil($total_patients / $limit);
?>

<head>
    <?php echo getTitle(text: 'เช็คสถานะผู้ป่วย'); ?>
</head>

<main>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-purple-800 mb-8">ระบบเช็คสถานะผู้ป่วย</h1>
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">ผู้ป่วยทั้งหมด</h2>
            <?php if ($user['roleId'] == 1 || $user['roleId'] == 2) : ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-blue-100 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <p class="text-blue-800">จำนวนผู้ป่วยทั้งหมด</p>
                            <p class="text-2xl font-bold text-blue-600"><?php echo $patients_hn_static['total_patients']; ?> คน</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-800 inline-flex icon icon-tabler icons-tabler-outline icon-tabler-clipboard-heart">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" />
                            <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" />
                            <path d="M11.993 16.75l2.747 -2.815a1.9 1.9 0 0 0 0 -2.632a1.775 1.775 0 0 0 -2.56 0l-.183 .188l-.183 -.189a1.775 1.775 0 0 0 -2.56 0a1.899 1.899 0 0 0 0 2.632l2.738 2.825z" />
                        </svg>
                    </div>
                    <div class="bg-green-100 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <p class="text-green-800">จำนวนผู้ป่วยที่ยืนยันแล้ว</p>
                            <p class="text-2xl font-bold text-green-600"><?php echo $patients_hn_static['confirmed_hn']; ?> คน</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-800 inline-flex icon icon-tabler icons-tabler-outline icon-tabler-file-check">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                            <path d="M9 15l2 2l4 -4" />
                        </svg>
                    </div>
                    <div class="bg-yellow-100 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <p class="text-yellow-800">จำนวนผู้ป่วยที่ยังไม่ยืนยัน</p>
                            <p class="text-2xl font-bold text-yellow-600"><?php echo $patients_hn_static['not_confirmed_hn']; ?> คน</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-yellow-800 inline-flex icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-1">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 7v5" />
                            <path d="M12 12l2 -3" />
                        </svg>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($user['roleId'] == 2 || $user['roleId'] == 3) : ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-blue-100 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <p class="text-blue-800">จำนวนผู้ป่วยยังไม่ติดต่อ</p>
                            <p class="text-2xl font-bold text-blue-600"><?php echo $patients_call_static['not_called']; ?> คน</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-800 inline-flex icon icon-tabler icons-tabler-outline icon-tabler-phone">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                        </svg>
                    </div>
                    <div class="bg-green-100 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <p class="text-green-800">จำนวนผู้ป่วยที่ติดต่อแล้ว</p>
                            <p class="text-2xl font-bold text-green-600"><?php echo $patients_call_static['called']; ?> คน</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-800 inline-flex icon icon-tabler icons-tabler-outline icon-tabler-clock-hour-1">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 7v5" />
                            <path d="M12 12l2 -3" />
                        </svg>

                    </div>
                    <div class="bg-yellow-100 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <p class="text-yellow-800">จำนวนผู้ป่วยที่ติดต่อไม่ได้</p>
                            <p class="text-2xl font-bold text-yellow-600"><?php echo $patients_call_static['failed']; ?> คน</p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-yellow-800 inline-flex icon icon-tabler icons-tabler-outline icon-tabler-phone-incoming">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                            <path d="M15 9l5 -5" />
                            <path d="M15 5l0 4l4 0" />
                        </svg>
                    </div>
                </div>
            <?php endif; ?>

            <div class="flex flex-col gap-2 mb-6 text-center">
                <?php if ($user['roleId'] == 1 || $user['roleId'] == 2) : ?>
                    <div class="flex flex-row gap-2">
                        <a href="./?page=dashboard" class="w-1/3 transition-all duration-300 bg-blue-500 hover:bg-blue-600 text-sm font-medium text-white py-2 px-4 rounded">
                            ผู้ป่วยทั้งหมด
                        </a>
                        <a href="./?page=dashboard&hn_status=confirmed&call_status=<?php echo $_GET['call_status'] ?? '' ?>&page_query=<?php echo $_GET['page_query'] ?? '' ?>&registerAt=<?php echo $_GET['registerAt'] ?? '' ?>&departmentId=<?php echo $_GET['departmentId'] ?? '' ?>" class="w-1/3 transition-all duration-300 bg-green-500 hover:bg-green-600 text-sm font-medium text-white py-2 px-4 rounded">
                            ผู้ป่วยที่ยืนยันแล้ว
                        </a>
                        <a href="./?page=dashboard&hn_status=not_confirmed&call_status=<?php echo $_GET['call_status'] ?? '' ?>&page_query=<?php echo $_GET['page_query'] ?? '' ?>&registerAt=<?php echo $_GET['registerAt'] ?? '' ?>&departmentId=<?php echo $_GET['departmentId'] ?? '' ?>" class="w-1/3 transition-all duration-300 bg-orange-500 hover:bg-orange-600 text-sm font-medium text-white py-2 px-4 rounded">
                            ผู้ป่วยที่ยังไม่ยืนยัน
                        </a>
                    </div>
                <?php endif; ?>
                <?php if ($user['roleId'] == 2 || $user['roleId'] == 3) : ?>
                    <div class="flex flex-row gap-2">
                        <a href="./?page=dashboard&call_status=not_called&hn_status=<?php echo $_GET['hn_status'] ?? '' ?>&page_query=<?php echo $_GET['page_query'] ?? '' ?>&registerAt=<?php echo $_GET['registerAt'] ?? '' ?>&departmentId=<?php echo $_GET['departmentId'] ?? '' ?>" class="w-1/3 transition-all duration-300 bg-blue-500 hover:bg-blue-600 text-sm font-medium text-white py-2 px-4 rounded">
                            ผู้ป่วยที่ยังไม่ติดต่อ
                        </a>
                        <a href="./?page=dashboard&call_status=called&hn_status=<?php echo $_GET['hn_status'] ?? '' ?>&page_query=<?php echo $_GET['page_query'] ?? '' ?>&registerAt=<?php echo $_GET['registerAt'] ?? '' ?>&departmentId=<?php echo $_GET['departmentId'] ?? '' ?>" class="w-1/3 transition-all duration-300 bg-green-500 hover:bg-green-600 text-sm font-medium text-white py-2 px-4 rounded">
                            ผู้ป่วยที่ติดต่อแล้ว
                        </a>
                        <a href="./?page=dashboard&call_status=failed&hn_status=<?php echo $_GET['hn_status'] ?? '' ?>&page_query=<?php echo $_GET['page_query'] ?? '' ?>&registerAt=<?php echo $_GET['registerAt'] ?? '' ?>&departmentId=<?php echo $_GET['departmentId'] ?? '' ?>" class="w-1/3 transition-all duration-300 bg-orange-500 hover:bg-orange-600 text-sm font-medium text-white py-2 px-4 rounded">
                            ผู้ป่วยที่ติดต่อไม่ได้
                        </a>
                    </div>
                <?php endif; ?>
                <div class="flex flex-row gap-2">
                    <a href="./?page=dashboard" class="<?php echo $user['roleId'] == 3 ? "w-full" : "w-1/2" ?> bg-gray-500 hover:bg-gray-600 text-sm font-medium text-white py-2 px-4 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-flex icon icon-tabler icons-tabler-outline icon-tabler-search">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M21 21l-6 -6" />
                        </svg> ล้างการค้นหา
                    </a>
                    <?php if ($user['roleId'] == 1 || $user['roleId'] == 2) : ?>
                        <a href="<?php echo $deleted == 0 ? "./?page=dashboard&deleted=true" : "./?page=dashboard" ?>" class="w-1/2 transition-all duration-300 bg-red-500 hover:bg-red-600 text-sm font-medium text-white py-2 px-4 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="inline-flex icon icon-tabler icons-tabler-filled icon-tabler-archive">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M2 3m0 2a2 2 0 0 1 2 -2h16a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-16a2 2 0 0 1 -2 -2z" />
                                <path d="M19 9c.513 0 .936 .463 .993 1.06l.007 .14v7.2c0 1.917 -1.249 3.484 -2.824 3.594l-.176 .006h-10c-1.598 0 -2.904 -1.499 -2.995 -3.388l-.005 -.212v-7.2c0 -.663 .448 -1.2 1 -1.2h14zm-5 2h-4l-.117 .007a1 1 0 0 0 0 1.986l.117 .007h4l.117 -.007a1 1 0 0 0 0 -1.986l-.117 -.007z" />
                            </svg>
                            <?php echo $deleted == 0 ? "ผู้ป่วยที่ถูกลบ" : "ผู้ป่วยที่ไม่ได้ถูกลบ" ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="flex flex-row gap-2">
                    <a href="./" class="w-full transition-all duration-300 bg-red-500 hover:bg-red-600 text-sm font-medium text-white py-2 px-4 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="inline-flex icon icon-tabler icons-tabler-filled icon-tabler-home">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12.707 2.293l9 9c.63 .63 .184 1.707 -.707 1.707h-1v6a3 3 0 0 1 -3 3h-1v-7a3 3 0 0 0 -2.824 -2.995l-.176 -.005h-2a3 3 0 0 0 -3 3v7h-1a3 3 0 0 1 -3 -3v-6h-1c-.89 0 -1.337 -1.077 -.707 -1.707l9 -9a1 1 0 0 1 1.414 0m.293 11.707a1 1 0 0 1 1 1v7h-4v-7a1 1 0 0 1 .883 -.993l.117 -.007z" />
                        </svg> กลับหน้าหลัก
                    </a>
                </div>
            </div>

            <div class="mb-6">
                <form action="" method="get">
                    <input type="hidden" name="page" value="dashboard">
                    <div class="grid sm:grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="registerAt" class="block text-sm font-medium text-gray-700 mb-1">ค้นหาตามวันที่ลงทะเบียน</label>
                            <input value="<?php echo isset($_GET['registerAt']) && $_GET['registerAt'] != '' ? $_GET['registerAt'] : '' ?>" type="date" id="registerAt" name="registerAt" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                        </div>
                        <div>
                            <label for="departmentId" class="block text-sm font-medium text-gray-700 mb-1">ค้นหาตามแผนก</label>
                            <select id="departmentId" name="departmentId" class="relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                                <option value="">-- แผนก --</option>
                                <?php
                                foreach ($departments as $department) : ?>
                                    <option <?php echo isset($departmentId) && $departmentId == $department['id'] ? "selected" : "" ?> value="<?php echo $department['id'] ?>"><?php echo $department['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-span-full">
                            <label for="cid" class="block text-sm font-medium text-gray-700 mb-1">เลขบัตรประชาชน</label>
                            <input value="<?php echo isset($_GET['cid']) && $_GET['cid'] != '' ? $_GET['cid'] : '' ?>" type="text" id="cid" name="cid" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="transition-all duration-300 bg-green-500 hover:bg-green-600 text-sm font-medium text-white py-2 px-4 rounded">ค้นหา</button>
                    </div>
                </form>
            </div>

            <?php if ($user['roleId'] == 1 || $user['roleId'] == 2) : ?>
                <?php if (isset($_GET['hn_status'])) :
                    if ($_GET['hn_status'] == 'confirmed') {
                        include_once './components/Search/HN_status_confirmed.php';
                    } else if ($_GET['hn_status'] == 'not_confirmed') {
                        include_once './components/Search/HN_status_not_confirmed.php';
                    }
                endif; ?>
            <?php endif; ?>

            <?php if ($user['roleId'] == 2 || $user['roleId'] == 3) : ?>
                <?php if (isset($_GET['call_status'])) :
                    if ($_GET['call_status'] == 'called') {
                        include_once './components/Search/CalledSatus.php';
                    } else if ($_GET['call_status'] == 'not_called') {
                        include_once './components/Search/NotCalledStatus.php';
                    } else if ($_GET['call_status'] == 'failed') {
                        include_once './components/Search/FailedCallStatus.php';
                    }
                endif; ?>
            <?php endif; ?>

            <?php if (isset($_GET['cid']) && $_GET['cid'] != ''): ?>
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-3" role="alert">
                    <p class="font-bold">กำลังค้นหาข้อมูลเลขบัตรประชาชน : <?php echo htmlspecialchars($_GET['cid']); ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registerAt']) && $_GET['registerAt'] != ''): ?>
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-3" role="alert">
                    <p class="font-bold">กำลังค้นหาข้อมูลที่ลงทะเบียนใน : <?php echo htmlspecialchars(formatDate($_GET['registerAt'])); ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['departmentId']) && $_GET['departmentId'] != ''): ?>
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-3" role="alert">
                    <p class="font-bold">กำลังค้นหาข้อมูลที่ลงทะเบียนในแผนก :
                        <?php foreach ($departments as $department) :
                            echo $department['id'] == $departmentId ? $department['name'] : "";
                        endforeach; ?>
                    </p>
                </div>
            <?php endif; ?>
            <?php if ($user['roleId'] == 1 || $user['roleId'] == 2) : ?>

                <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 'true'): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-3" role="alert">
                        <p class="font-bold">กำลังค้นหาข้อมูลผู้ป่วยที่ถูกลบ</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>


            <div class="flex flex-wrap justify-center items-center space-x-2 my-4">
                <?php if ($page > 1): ?>
                    <a href="./?page=dashboard&page_query=<?php echo $page - 1 ?>&hn_status=<?php echo $_GET['hn_status'] ?? '' ?>&call_status=<?php echo $_GET['call_status'] ?? '' ?>&registerAt=<?php echo $_GET['registerAt'] ?? '' ?>&departmentId=<?php echo $_GET['departmentId'] ?? '' ?>"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        ย้อนหลัง
                    </a>
                <?php endif; ?>

                <div class="hidden sm:flex sm:space-x-2">
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $page + 2);

                    if ($start > 1) {
                        echo '<span class="px-4 py-2 text-sm font-medium text-gray-700">...</span>';
                    }

                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <a href="./?page=dashboard&page_query=<?php echo $i ?>&hn_status=<?php echo $_GET['hn_status'] ?? '' ?>&call_status=<?php echo $_GET['call_status'] ?? '' ?>&registerAt=<?php echo $_GET['registerAt'] ?? '' ?>&departmentId=<?php echo $_GET['departmentId'] ?? '' ?>"
                            class="px-4 py-2 text-sm font-medium <?php echo $i == $page ? 'text-blue-600 bg-blue-50 border border-blue-500' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'; ?> rounded-md">
                            <?php echo $i; ?>
                        </a>
                    <?php
                    endfor;

                    if ($end < $total_pages) {
                        echo '<span class="px-4 py-2 text-sm font-medium text-gray-700">...</span>';
                    }
                    ?>
                </div>

                <?php if ($page < $total_pages): ?>
                    <a href="./?page=dashboard&page_query=<?php echo $page + 1 ?>&hn_status=<?php echo $_GET['hn_status'] ?? '' ?>&call_status=<?php echo $_GET['call_status'] ?? '' ?>&registerAt=<?php echo $_GET['registerAt'] ?? '' ?>&departmentId=<?php echo $_GET['departmentId'] ?? '' ?>"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        ถัดไป
                    </a>
                <?php endif; ?>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <?php if (!empty($patients)) :
                    foreach ($patients as $patient): ?>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <?php if ($patient['deleted'] != 1) : ?>
                                <?php if (($user['roleId'] == 1 || $user['roleId'] == 2)) : ?>
                                    <?php if (isset($patient['hn_status'])) : ?>
                                    <?php
                                        if ($patient['hn_status'] == 'รอเจ้าหน้าที่ยืนยัน') {
                                            include './components/Status/UnconfirmedStatus.php';
                                        } elseif ($patient['hn_status'] == 'ยืนยันแล้ว') {
                                            include './components/Status/ConfirmedStatus.php';
                                        } else {
                                            include './components/Status/UnavailableStatus.php';
                                        }
                                    endif; ?>
                                <?php endif; ?>

                                <?php if (($user['roleId'] == 2 || $user['roleId'] == 3)): ?>
                                    <?php if (isset($patient['call_status'])) :
                                        $call_status = $patient['call_status'];
                                        $failure_note = $patient['failure_note'];
                                        $call_timestamp = $patient['call_timestamp'];
                                        if ($call_status == 'ยังไม่ติดต่อ') {
                                            include './components/Status/NotCalledStatus.php';
                                        } else if ($call_status == 'ติดต่อผู้ป่วยเรียบร้อย') {
                                            include './components/Status/CalledStatus.php';
                                        } else if ($call_status == 'ติดต่อไม่ได้') {
                                            include './components/Status/FailedCallStatus.php';
                                        }
                                    endif; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                                    <p class="font-bold">ข้อมูลผู้ป่วยถูกลบ</p>
                                </div>
                            <?php endif; ?>


                            <div class="space-y-4">
                                <div class="mb-4">
                                    <div class="text-md font-semibold text-gray-800">
                                        ID: <?php echo htmlspecialchars($patient['id'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
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
                                                if ($info['key'] == 'bday') {
                                                    $value = isset($patient['bday']) ? formatDate($patient['bday']) : '';
                                                } elseif ($info['key'] == 'registered_at') {
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
                            <hr class="border-gray-300 my-3">
                            <?php if ($patient['deleted'] != 1) : ?>
                                <?php if (($user['roleId'] == 2 || $user['roleId'] == 3)): ?>
                                    <?php if ($patient['call_status'] != 'ติดต่อผู้ป่วยเรียบร้อย') : ?>
                                        <div class="flex mt-6 items-center flex-col gap-y-3 text-center">
                                            สถานะการโทร
                                            <?php if ($patient['call_status'] != 'ติดต่อไม่ได้') : ?>
                                                <button type="button" onclick="CallPatient(<?php echo $patient['id'] ?>)" class="cursor-pointer w-full text-sm font-medium text-white transition-all duration-300 bg-blue-500 hover:bg-blue-600 rounded-md py-2 px-4">โทรหาผู้ป่วยเรียบร้อย / โทรไม่สำเร็จ</button>
                                            <?php endif; ?>
                                            <?php if ($patient['call_status'] == 'ติดต่อไม่ได้') : ?>
                                                <button type="button" onclick="CallPatient(<?php echo $patient['id'] ?>)" class="cursor-pointer w-full text-sm font-medium text-white transition-all duration-300 bg-red-500 hover:bg-red-600 rounded-md py-2 px-4">โทรหาอีกครั้ง</button>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($user['roleId'] == 1 || $user['roleId'] == 2) : ?>
                                    <hr class="border-gray-300 my-3">
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (($user['roleId'] == 1 || $user['roleId'] == 2)) : ?>
                                <div class="flex mt-6 items-center flex-col gap-y-3 text-center">
                                    ข้อมูลในระบบ
                                    <?php if ($patient['deleted'] != 1) : ?>
                                        <?php if ($patient['hn_status'] != 'ยืนยันแล้ว') : ?>
                                            <button type="button" onclick="ConfirmPatient(<?php echo $patient['id'] ?>)" class="cursor-pointer w-full text-sm font-medium text-white transition-all duration-300 bg-green-500 hover:bg-green-600 rounded-md py-2 px-4">ยืนยันข้อมูล</button>
                                        <?php endif; ?>
                                        <a target="_blank" href="./?page=patient_edit&patientId=<?php echo $patient['id'] ?>" class="cursor-pointer w-full text-sm font-medium text-white transition-all duration-300 bg-yellow-500 hover:bg-yellow-600 rounded-md py-2 px-4">แก้ไข</a>
                                        <a target="_blank" href="./?page=patient_info_pdf&patientId=<?php echo $patient['id'] ?>" class="cursor-pointer w-full text-sm font-medium text-white transition-all duration-300 bg-blue-500 hover:bg-blue-600 rounded-md py-2 px-4">ส่งออก PDF</a>
                                        <a onclick="RemovePatient(<?php echo $patient['id'] ?>,<?php echo $patient['cid'] ?>)" class="cursor-pointer w-full text-sm font-medium text-white transition-all duration-300 bg-red-500 hover:bg-red-600 rounded-md py-2 px-4">ลบข้อมูลผู้ป่วย</a>
                                    <?php else: ?>
                                        <button type="button" onclick="RecoveryPatient(<?php echo $patient['id'] ?>)" class="cursor-pointer w-full text-sm font-medium text-white transition-all duration-300 bg-green-500 hover:bg-green-600 rounded-md py-2 px-4">กู้คืนข้อมูล</button>
                                        <button type="button" onclick="RemovePatientPermanent(<?php echo $patient['id'] ?>, <?php echo $patient['cid'] ?>)" class="cursor-pointer w-full text-sm font-medium text-white transition-all duration-300 bg-red-500 hover:bg-red-600 rounded-md py-2 px-4">ลบถาวร</button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach ?>
                <?php else : ?>
                    <div class="p-4 text-center col-span-3 w-full bg-gray-200 rounded-md font-medium">
                        ไม่พบข้อมูลผู้ป่วย
                    </div>
                <?php endif; ?>
                <?php ?>
            </div>
        </div>
    </div>
</main>
<script src="./functions/Patients/Patients.js"></script>
<script src="./functions/Patients/Patient_hn_confirm.js"></script>
<script>
    function CallPatient(patientId) {
        Swal.fire({
            title: 'ยืนยันการโทร',
            text: 'เลือกตัวเลือกสำหรับการโทร',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'ติดต่อไม่ได้',
            confirmButtonText: 'โทรสำเร็จ'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = './?page=patient_call_called&patientId=' + patientId;
            } else if (result.dismiss == Swal.DismissReason.cancel) {
                Swal.fire({
                    title: 'กรุณากรอกเหตุผล',
                    input: 'text',
                    icon: 'question',
                    inputPlaceholder: 'เหตุผลที่ไม่สามารถติดต่อได้',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonText: 'ยืนยัน',
                    preConfirm: (reasonForFailed) => {
                        if (reasonForFailed.length <= 0) {
                            Swal.showValidationMessage('กรุณากรอกเหตุผลที่ไม่สามารถติดต่อได้');
                        } else {
                            return reasonForFailed;
                        }
                    }
                }).then((inputResult) => {
                    if (inputResult.isConfirmed && inputResult.value) {
                        const reason = inputResult.value;
                        window.location.href = './?page=patient_call_failed&patientId=' + patientId + '&reasonForFailedCall=' + encodeURIComponent(reason);
                    }
                });
            }
        });
    }
</script>
<?php require_once './layouts/Footer.php'; ?>