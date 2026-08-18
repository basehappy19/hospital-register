<?php
require './configs/db.php';
require './functions/Departments/Departments.php';

$departments = getDepartmentsForEdit($conn);
$department_counts = getCountPatientsInDepartment($conn);
?>

<head><?php echo getTitle(text: 'จัดการแผนก'); ?></head>

<main>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-purple-800 mb-8">จัดการข้อมูลแผนก</h1>
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 min-h-[70vh]">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">แผนกทั้งหมด</h2>
                <a href="./?page=add_department" class="transition-all duration-300 bg-blue-500 hover:bg-blue-600 text-sm font-medium text-white py-2 px-4 rounded" type="button">เพิ่มแผนก</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-purple-100">
                            <th class="px-4 py-2 text-left text-purple-800">#</th>
                            <th class="px-4 py-2 text-left text-purple-800">ชื่อแผนก</th>
                            <th class="px-4 py-2 text-left text-purple-800">ผู้ป่วยที่อยู่ในแผนก</th>
                            <th class="px-4 py-2 text-center text-purple-800">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($departments as $department): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3"><?php echo htmlspecialchars($i); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($department['name']); ?></td>
                                <td class="px-4 py-3">
                                    <?php
                                    $found = false; 
                                    foreach ($department_counts as $department_count) {
                                        if ($department_count['departmentId'] == $department['id']) {
                                            echo htmlspecialchars($department_count['patientCount']);
                                            $found = true; 
                                        }
                                    }
                                    if (!$found) {
                                        echo "0"; 
                                    }
                                    ?> คน</td>
                                <td class="px-4 py-3 text-center">
                                    <a href="./?page=edit_department&departmentId=<?php echo $department['id'] ?>" class="transition-all duration-300 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs mr-1">แก้ไข</a>
                                </td>
                            </tr>
                        <?php $i++;
                        endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<script src="./functions/Auth/RemoveUser.js"></script>