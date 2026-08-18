<?php
require './configs/db.php';

$users = getUsers($conn);
?>

<head><?php echo getTitle(text: 'จัดการข้อมูลผู้ใช้'); ?></head>

<main>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center text-purple-800 mb-8">จัดการข้อมูลผู้ใช้</h1>
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 min-h-[70vh]">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">ผู้ใช้ทั้งหมด</h2>
                <a href="./?page=add_user" class="transition-all duration-300 bg-blue-500 hover:bg-blue-600 text-sm font-medium text-white py-2 px-4 rounded" type="button">เพิ่มผู้ใช้</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse">
                    <thead>
                        <tr class="bg-purple-100">
                            <th class="px-4 py-2 text-left text-purple-800">#</th>
                            <th class="px-4 py-2 text-left text-purple-800">ชื่อผู้ใช้</th>
                            <th class="px-4 py-2 text-left text-purple-800">ชื่อที่แสดง</th>
                            <th class="px-4 py-2 text-left text-purple-800">ตำแหน่ง</th>
                            <th class="px-4 py-2 text-center text-purple-800">การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        foreach ($users as $user): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-4 py-3"><?php echo htmlspecialchars($i); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($user['username']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($user['displayName']); ?></td>
                                <td class="px-4 py-3">
                                    <?php echo htmlspecialchars($user['role_name']); ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex text-center items-center md:flex-row flex-col justify-center gap-2">
                                        <a href="./?page=edit_user&userId=<?php echo $user['id'] ?>" class="transition-all duration-300 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs">แก้ไข</a>
                                        <button type="button" onclick="removeUser(<?php echo (string)$user['id'] ?>, '<?php echo $user['username'] ?>')" class="transition-all duration-300 bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs">ลบ</button>
                                        <a href="./?page=user_activity_logs&userId=<?php echo $user['id'] ?>" class="transition-all duration-300 bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded text-xs">ประวัติการใช้งาน</a>
                                    </div>
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