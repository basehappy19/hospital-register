<?php
require './configs/db.php';
require './functions/Auth/Roles.php';

$user = getUserForEdit($conn, $userId);

if(!$user){
    header('Location: ./?page=manage_users');
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $user = [
        "username"=>(isset($_POST['username']) && $_POST['username'] != '') ? $_POST['username'] : "",
        "displayname"=>(isset($_POST['displayname']) && $_POST['displayname'] != '') ? $_POST['displayname'] : "",
        "password"=>(isset($_POST['password']) && $_POST['password'] != '') ? $_POST['password'] : null,
        "role"=>(isset($_POST['role']) && $_POST['role'] != '') ? (int)$_POST['role'] : "",
    ];
    if(updateUser($conn, $user, $userId)){
        $_SESSION['alert'] = '
            <script>
                Swal.fire({
                    title: "แก้ไขข้อมูลสำเร็จ",
                    text: "ข้อมูลผู้ใช้ถูกอัพเดทเรียบร้อยแล้ว",
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
                    text: "มีข้อผิดพลาดบางอย่างทำให้ไม่สามารถแก้ไขได้ โปรดลองอีกครั้ง",
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
<head><?php echo getTitle(text: "แก้ไขข้อมูลผู้ใช้ {$user['displayname']}"); ?></head>

<main>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-purple-800 mb-8">แก้ไขข้อมูลผู้ใช้</h1>
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 min-h-[70vh]">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4"><?php echo $user['displayname'] ?></h2>
            <form id="edit_user_form" action="" method="post">
                <div class="mb-5">
                    <label for="username" class="block text-sm font-medium text-gray-700">ชื่อผู้ใช้</label>
                    <input value="<?php echo $user['username'] ?>" onkeydown="clearBorder(this)" type="text" id="username" name="username" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                </div>
                <div class="mb-5">
                    <label for="displayname" class="block text-sm font-medium text-gray-700">ชื่อที่แสดงผล</label>
                    <input value="<?php echo $user['displayname'] ?>" onkeydown="clearBorder(this)" type="text" id="displayname" name="displayname" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                </div>
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-gray-700">รหัสผ่าน</label>
                    <input onkeydown="clearBorder(this)" type="text" id="password" name="password" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                </div>
                <div class="mb-5">
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">ยืนยันรหัสผ่าน</label>
                    <input onkeydown="clearBorder(this)" type="text" id="confirm_password" name="confirm_password" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                </div>
                <div class="mb-5">
                    <label for="role" class="block text-sm font-medium text-gray-700">ตำแหน่ง</label>
                    <select class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm" id="role" name="role" onclick="clearBorder(this)" required>
                        <?php $roles = getRoles($conn); ?> <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['id'] ?>" <?php echo $user['roleId'] == $role['id'] ? 'selected' : ''; ?>><?php echo $role['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mt-8 flex justify-end space-x-4">
                    <button type="button" onclick="updateUser()" class="transition-all duration-300 px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">แก้ไขข้อมูล</button>
                    <a href="./?page=manage_users" class="transition-all duration-300 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">ย้อนกลับ</a>
                </div>
            </form>
        </div>
    </div>
</main>
<script src="./functions/ValidateForms/userEdit.js"></script>
