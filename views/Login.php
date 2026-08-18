<?php
require './configs/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if(authLogin($conn, $username, $password)){
        header('Location: ./?page=dashboard');
        exit();
    } else {
        $_SESSION['failed_auth'] = true;
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }
}

?>

<head>
    <?php echo getTitle(text: "เข้าสู่ระบบแอดมิน"); ?>
</head>

<main class="min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-2xl">
        <div class="text-center">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">เข้าสู่ระบบ</h2>
        </div>
        <form class="mt-8 space-y-6" action="" method="post">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="username" class="sr-only">ชื่อผู้ใช้</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="inline-flex z-20 text-gray-600">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 2a5 5 0 1 1 -5 5l.005 -.217a5 5 0 0 1 4.995 -4.783z" />
                                <path d="M14 14a5 5 0 0 1 5 5v1a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-1a5 5 0 0 1 5 -5h4z" />
                            </svg>
                        </div>
                        <input id="username" name="username" type="text" required class="appearance-none rounded-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" placeholder="ชื่อผู้ใช้">
                    </div>
                </div>
                <div>
                    <label for="password" class="sr-only">รหัสผ่าน</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="inline-flex z-20 text-gray-600">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14.52 2c1.029 0 2.015 .409 2.742 1.136l3.602 3.602a3.877 3.877 0 0 1 0 5.483l-2.643 2.643a3.88 3.88 0 0 1 -4.941 .452l-.105 -.078l-5.882 5.883a3 3 0 0 1 -1.68 .843l-.22 .027l-.221 .009h-1.172c-1.014 0 -1.867 -.759 -1.991 -1.823l-.009 -.177v-1.172c0 -.704 .248 -1.386 .73 -1.96l.149 -.161l.414 -.414a1 1 0 0 1 .707 -.293h1v-1a1 1 0 0 1 .883 -.993l.117 -.007h1v-1a1 1 0 0 1 .206 -.608l.087 -.1l1.468 -1.469l-.076 -.103a3.9 3.9 0 0 1 -.678 -1.963l-.007 -.236c0 -1.029 .409 -2.015 1.136 -2.742l2.643 -2.643a3.88 3.88 0 0 1 2.741 -1.136m.495 5h-.02a2 2 0 1 0 0 4h.02a2 2 0 1 0 0 -4" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 pl-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" placeholder="รหัสผ่าน">
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['failed_auth'])) : ?>
                <p class="text-red-500 text-sm">ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง</p>
            <?php endif;
            unset($_SESSION['failed_auth']) ?>

            <div>
                <button type="submit" class="transition duration-300 group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium text-white bg-green-500 rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50">
                    เข้าสู่ระบบ
                </button>
            </div>
        </form>
        <div class="text-center">
            <a href="./" class="transition duration-300 font-medium text-indigo-600 hover:text-indigo-500 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                กลับหน้าหลัก
            </a>
        </div>
    </div>
</main>