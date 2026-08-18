<?php
require './configs/db.php';
require './functions/Departments/Departments.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $department = [
        "name"=>(isset($_POST['name']) && $_POST['name'] != '') ? $_POST['name'] : "",
    ];
    if(!checkDepartmentExists($conn, $department['name'])){
        if(createDepartment($conn, $department)){
            $_SESSION['alert'] = '
                        <script>
                            Swal.fire({
                                title: "เพิ่มแผนกเรียบร้อย",
                                text: "แผนกใหม่ได้ถูกเพิ่มเข้าระบบแล้ว",
                                icon: "success",
                                showConfirmButton: false,
                                timer: 1500, 
                            });
                        </script>
            ';
            header("Location: ./?page=manage_departments");
            exit();
        } else {
            $_SESSION['alert'] = '
                <script>
                    Swal.fire({
                        title: "เกิดข้อผิดพลาด!",
                        text: "เกิดปัญหาบางอย่าง กรุณาลองอีกครั้ง",
                        icon: "error", 
                        confirmButtonText: "ลองอีกครั้ง",
                        confirmButtonColor: "#f5424b",                         
                    });
                </script>
            ';
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }
    } else {
        $_SESSION['alert'] = '
                <script>
                    Swal.fire({
                        title: "เกิดข้อผิดพลาด!",
                        text: "ชื่อแผนกนี้มีในระบบแล้ว กรุณาใช้ชื่อแผนกอื่น",
                        icon: "error", 
                        confirmButtonText: "ลองอีกครั้ง",
                        confirmButtonColor: "#f5424b",                         
                    });
                </script>
        ';
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    }
}

?>
<head><?php echo getTitle(text: "เพิ่มแผนก"); ?></head>

<main>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-purple-800 mb-8">เพิ่มแผนกใหม่</h1>
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 min-h-[70vh]">
            <form id="add_department_form" action="" method="post">
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700">ชื่อแผนก</label>
                    <input onkeydown="clearBorder(this)" type="text" id="name" name="name" class="appearance-none relative block w-full px-3 py-2 border shadow-sm border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-purple-500 focus:border-purple-500 focus:z-10 sm:text-sm">
                </div>
                <div class="mt-8 flex justify-end space-x-4">
                    <button type="button" onclick="addDepartment()" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">ยืนยัน</button>
                    <a href="./?page=manage_departments" class="transition-all duration-300 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">ย้อนกลับ</a>
                </div>
            </form>
        </div>
    </div>
</main>
<script src="./functions/ValidateForms/addDepartment.js"></script>
