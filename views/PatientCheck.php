<?php
require './configs/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $cid = isset($_POST['cid']) ? $_POST['cid'] : null;
    $_SESSION['cid'] = isset($_POST['cid']) ? $_POST['cid'] : '';
    header('Location: ./?page=patient_info');
    exit();
    
};

?>

<head>
    <?php echo getTitle(text: 'เช็คข้อมูลบัตรผู้ป่วย'); ?>
</head>

<main class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-xl">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">เช็คข้อมูลบัตรผู้ป่วย</h2>
        <form action="" method="post" class="space-y-6">
            <div>
                <label for="cid" class="block text-sm font-medium text-gray-700 mb-1">เลขบัตรประชาชน</label>
                <input type="text" id="cid" name="cid" required placeholder="กรอกเลขบัตรประชาชน" class="w-full appearance-none relative block px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
            </div>
            <?php if (isset($_SESSION['error'])) : ?>
                <p class="text-red-500 text-sm">รหัสบัตรประชาชนไม่ถูกต้อง</p>
            <?php endif; unset($_SESSION['error']) ?>
            <div class="flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:space-x-3">
                <button type="submit" class="text-sm font-medium text-white w-full sm:w-auto px-4 py-2 bg-green-500 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition duration-200">
                    เช็คข้อมูล
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </button>
                <a href="./" class="text-center text-sm font-medium text-white w-full sm:w-auto px-4 py-2 bg-red-500 rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 transition duration-200">
                    กลับหน้าหลัก
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </a>
            </div>
        </form>
    </div>
</main>

<?php require_once './layouts/Footer.php'; ?>