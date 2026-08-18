<?php
require './configs/db.php';

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = isset($_GET['perPage']) ? intval($_GET['perPage']) : 10;
$activeInactivePage = isset($_GET['activeInactivePage']) ? intval($_GET['activeInactivePage']) : 1;
$historyPage = isset($_GET['historyPage']) ? intval($_GET['historyPage']) : 1;
$logsPage = isset($_GET['logsPage']) ? intval($_GET['logsPage']) : 1;
$logsPerPage = isset($_GET['logsPerPage']) ? intval($_GET['logsPerPage']) : 5;

$userId = isset($_GET['userId']) ? intval($_GET['userId']) : null;

$user = getHistoryUsers($conn, $userId, $page, $perPage, $activeInactivePage, $historyPage, $logsPage, $logsPerPage);
if (!$user) {
    header("Location: ./?page=manage_users");
    exit();
}

$activeCount = count($user['histories']['active']);
$inactiveCount = count($user['histories']['inactive']);
$totalPages = $user['pagination']['totalPages'];

?>

<head><?php echo getTitle(text: "ประวัติการใช้งาน {$user['user']['displayname']}"); ?></head>
<style>
    .transition-max-height {
        transition: max-height 0.3s ease-out;
        overflow: hidden;
    }
</style>

<main>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-purple-800 mb-8">ประวัติการใช้งาน</h1>
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 min-h-[70vh]">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4"><?php echo $user['user']['displayname']; ?></h2>
            <p class="text-gray-600">บทบาท: <?php echo $user['user']['role_name']; ?></p>
            <div class="text-left mb-6">
                <button onclick="logoutAllSession(<?php echo $user['user']['id'] ?>)" class="mt-3 transition-all duration-300 bg-blue-500 hover:bg-blue-600 text-sm font-medium text-white py-2 px-4 rounded" type="button">ออกจากระบบทุกเครื่อง ยกเว้นตัวเอง</button>
            </div>
            <div class="mb-6">
                <button class="w-full bg-green-500 text-white py-2 px-4 rounded-lg flex justify-between items-center hover:bg-green-600 transition duration-300 ease-in-out" onclick="toggleSection('active-sessions')">
                    <span class="font-semibold">เซสชันที่ใช้งานอยู่ (<?php echo $activeCount; ?>)</span>
                    <svg class="w-5 h-5 transform transition-transform duration-300" id="active-sessions-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div id="active-sessions" class="transition-max-height max-h-0">
                    <?php echo generatePagination($activeInactivePage, $totalPages, 'activeInactivePage'); ?>
                    <?php foreach ($user['histories']['active'] as $session): ?>
                        <div class="mt-4 bg-gray-50 rounded-lg p-4 hover:shadow-md transition duration-300 ease-in-out">
                            <div class="flex justify-between items-center cursor-pointer" onclick="toggleActivityLog('active-<?php echo $session['loginId']; ?>')">
                                <div>
                                    <p class="font-semibold text-gray-700">เข้าสู่ระบบเมื่อ: <?php echo formatDateTime($session['login_time']); ?></p>
                                    <?php if ($_SESSION['loginId'] == $session['loginId']) : ?>
                                        <p class="text-md text-blue-600">อุปกรณ์เครื่องนี้ ของคุณเอง</p>
                                    <?php endif; ?>
                                    <p class="text-sm text-gray-600">รายละเอียดอุปกรณ์: <?php echo getDeviceType($session['ip_address']); ?></p>
                                    <p class="text-sm text-gray-600">IP: <?php echo $session['ip_address']; ?></p>
                                    <?php if ($_SESSION['loginId'] != $session['loginId']) : ?>
                                        <button onclick="logoutSession(<?php echo $session['loginId'] ?>,<?php echo $session['userId'] ?>)" class="mt-3 transition-all duration-300 bg-blue-500 hover:bg-blue-600 text-sm font-medium text-white py-2 px-4 rounded" type="button">ออกจากระบบ</button>
                                    <?php endif; ?>
                                </div>
                                <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300" id="active-<?php echo $session['loginId']; ?>-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div id="active-<?php echo $session['loginId']; ?>-logs" class="transition-max-height max-h-0">
                                <div class="mt-4 space-y-3">
                                    <?php
                                    $logPagination = $session['log_pagination'];
                                    $logCurrentPage = $logPagination['currentPage'];
                                    $logTotalPages = $logPagination['totalPages'];

                                    echo generateLogPagination($logCurrentPage, $logTotalPages, 'logsPage', ['userId' => $userId, 'activeInactivePage' => $activeInactivePage]);
                                    ?>
                                    <?php foreach ($session['activity_logs'] as $log): ?>
                                        <div class="p-4 bg-white rounded-lg shadow-sm">
                                            <p class="font-medium text-gray-700"><?php echo $log['action_type']; ?> - <?php echo $log['resource']; ?> / (<?php echo $log['remarks']; ?>)</p>
                                            <p class="text-sm text-gray-600">เวลา: <?php echo formatDateTime($log['timestamp']); ?></p>
                                            <p class="text-sm text-gray-600">สถานะ: <?php echo $log['status']; ?></p>
                                            <div class="mt-4">
                                                <h3 class="text-md font-medium text-gray-700">ข้อมูลที่ส่งมายังฐานข้อมูล:</h3>
                                                <pre class="mt-2 p-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-600">
                                                    <?php echo trim(json_encode(json_decode($log['request_data']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?>
                                                </pre>
                                            </div>
                                            <div class="mt-4">
                                                <h3 class="text-md font-medium text-gray-700">ข้อมูลที่ได้รับคืน:</h3>
                                                <pre class="mt-2 p-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-600 whitespace-pre-wrap" style="line-height: 1.5;">
                                                    <?php echo trim(json_encode(json_decode($log['response_data']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?>
                                                </pre>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <button class="w-full bg-gray-500 text-white py-2 px-4 rounded-lg flex justify-between items-center hover:bg-gray-600 transition duration-300 ease-in-out" onclick="toggleSection('inactive-sessions')">
                    <span class="font-semibold">เซสชันที่ไม่ได้ใช้งาน (<?php echo $inactiveCount; ?>)</span>
                    <svg class="w-5 h-5 transform transition-transform duration-300" id="inactive-sessions-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div id="inactive-sessions" class="transition-max-height max-h-0">
                    <?php echo generatePagination($activeInactivePage, $totalPages, 'activeInactivePage'); ?>
                    <?php foreach ($user['histories']['inactive'] as $session): ?>
                        <div class="mt-4 bg-gray-50 rounded-lg p-4 hover:shadow-md transition duration-300 ease-in-out">
                            <div class="flex justify-between items-center cursor-pointer" onclick="toggleActivityLog('inactive-<?php echo $session['loginId']; ?>')">
                                <div>
                                    <p class="font-semibold text-gray-700">เข้าสู่ระบบเมื่อ: <?php echo formatDateTime($session['login_time']); ?></p>
                                    <p class="text-sm text-gray-600">ออกจากระบบเมื่อ: <?php echo formatDateTime($session['logout_time']); ?></p>
                                    <p class="text-sm text-gray-600">รายละเอียดอุปกรณ์: <?php echo getDeviceType($session['ip_address']); ?></p>
                                    <p class="text-sm text-gray-600">IP: <?php echo $session['ip_address']; ?></p>
                                </div>
                                <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-300" id="inactive-<?php echo $session['loginId']; ?>-icon" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div id="inactive-<?php echo $session['loginId']; ?>-logs" class="transition-max-height max-h-0">
                                <div class="mt-4 space-y-2">
                                    <?php
                                    $logPagination = $session['log_pagination'];
                                    $logCurrentPage = $logPagination['currentPage'];
                                    $logTotalPages = $logPagination['totalPages'];

                                    echo generateLogPagination($logCurrentPage, $logTotalPages, 'logsPage', ['userId' => $userId, 'activeInactivePage' => $activeInactivePage]);
                                    ?>
                                    <?php foreach ($session['activity_logs'] as $log): ?>
                                        <div class="p-4 bg-white rounded-lg shadow-sm">
                                            <p class="font-medium text-gray-700"><?php echo $log['action_type']; ?> - <?php echo $log['resource']; ?> / (<?php echo $log['remarks']; ?>)</p>
                                            <p class="text-sm text-gray-600">เวลา: <?php echo formatDateTime($log['timestamp']); ?></p>
                                            <p class="text-sm text-gray-600">สถานะ: <?php echo $log['status']; ?></p>
                                            <div class="mt-4">
                                                <h3 class="text-md font-medium text-gray-700">ข้อมูลที่ส่งมายังฐานข้อมูล:</h3>
                                                <pre class="mt-2 p-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-600">
                                                    <?php echo trim(json_encode(json_decode($log['request_data']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?>
                                                </pre>
                                            </div>
                                            <div class="mt-4">
                                                <h3 class="text-md font-medium text-gray-700">ข้อมูลที่ได้รับคืน:</h3>
                                                <pre class="mt-2 p-2 bg-gray-100 border border-gray-300 rounded-lg text-sm text-gray-600 whitespace-pre-wrap" style="line-height: 1.5;">
                                                    <?php echo trim(json_encode(json_decode($log['response_data']), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?>
                                                </pre>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="./functions/Auth/Session.js"></script>


<script>
    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        const icon = document.getElementById(`${sectionId}-icon`);
        const isOpen = section.style.maxHeight != '0px' && section.style.maxHeight != '';

        if (!isOpen) {
            section.style.maxHeight = 'none';
            icon.style.transform = 'rotate(180deg)';
            localStorage.setItem(sectionId, 'open');
        } else {
            section.style.maxHeight = '0px';
            icon.style.transform = 'rotate(0deg)';
            localStorage.setItem(sectionId, 'closed');
        }
    }

    function toggleActivityLog(logId) {
        const logSection = document.getElementById(`${logId}-logs`);
        const icon = document.getElementById(`${logId}-icon`);
        const isOpen = logSection.style.maxHeight != '0px' && logSection.style.maxHeight != '';

        if (!isOpen) {
            logSection.style.maxHeight = logSection.scrollHeight + 'px';
            icon.style.transform = 'rotate(180deg)';

            const parentSection = logSection.closest('.transition-max-height');
            if (parentSection) {
                const newMaxHeight = Math.max(parentSection.scrollHeight, parentSection.clientHeight + logSection.scrollHeight);
                parentSection.style.maxHeight = newMaxHeight + 'px';
            }

            localStorage.setItem(logId, 'open');
        } else {
            logSection.style.maxHeight = '0px';
            icon.style.transform = 'rotate(0deg)';

            const parentSection = logSection.closest('.transition-max-height');
            if (parentSection) {
                const newMaxHeight = Math.max(0, parentSection.scrollHeight - logSection.scrollHeight);
                parentSection.style.maxHeight = newMaxHeight + 'px';
            }

            localStorage.setItem(logId, 'closed');
        }
    }

    function restoreStates() {
        const sections = ['active-sessions', 'inactive-sessions'];

        sections.forEach(sectionId => {
            const section = document.getElementById(sectionId);
            const icon = document.getElementById(`${sectionId}-icon`);
            const state = localStorage.getItem(sectionId);

            if (state == 'open') {
                section.style.maxHeight = 'none';
                icon.style.transform = 'rotate(180deg)';
            } else {
                section.style.maxHeight = '0px';
                icon.style.transform = 'rotate(0deg)';
            }

            const sessionLogs = section.querySelectorAll('[id$="-logs"]');
            sessionLogs.forEach(logSection => {
                const logId = logSection.id.replace('-logs', '');
                const logIcon = document.getElementById(`${logId}-icon`);
                const logState = localStorage.getItem(logId);

                if (logState == 'open') {
                    logSection.style.maxHeight = logSection.scrollHeight + 'px';
                    logIcon.style.transform = 'rotate(180deg)';

                    const newMaxHeight = Math.max(section.scrollHeight, section.clientHeight + logSection.scrollHeight);
                    section.style.maxHeight = newMaxHeight + 'px';
                } else {
                    logSection.style.maxHeight = '0px';
                    logIcon.style.transform = 'rotate(0deg)';
                }
            });
        });
    }

    window.onload = restoreStates;
</script>