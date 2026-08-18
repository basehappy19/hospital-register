<div class="bg-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <p class="text-sm text-gray-500 text-center">
            พัฒนาโดย
            <a href="https://portfolio.mathgift.online/" class="text-gray-600 font-semibold hover:text-gray-900">นายภาคภูมิ ทีดินดำ</a>
        </p>
    </div>
</div>
<footer class="bg-gray-100 text-gray-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-8 border-b border-gray-200">
            <div class="flex flex-wrap justify-between items-center">
                <div class="w-full md:w-auto mb-4 md:mb-0">
                    <span class="font-semibold">ติดต่อเรา</span>
                </div>
                <div class="flex space-x-4">
                    <a href="https://www.facebook.com/MettapracharakHospital" class="text-gray-400 hover:text-blue-500 transition-colors duration-300">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="https://www.youtube.com/MettapracharakH" class="text-gray-400 hover:text-red-500 transition-colors duration-300">
                        <span class="sr-only">YouTube</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M19.812 5.418c.861.23 1.538.907 1.768 1.768C21.998 8.746 22 12 22 12s0 3.255-.418 4.814a2.504 2.504 0 0 1-1.768 1.768c-1.56.419-7.814.419-7.814.419s-6.255 0-7.814-.419a2.505 2.505 0 0 1-1.768-1.768C2 15.255 2 12 2 12s0-3.255.417-4.814a2.507 2.507 0 0 1 1.768-1.768C5.744 5 11.998 5 11.998 5s6.255 0 7.814.418ZM15.194 12 10 15V9l5.194 3Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="https://twitter.com/mettapracharak" class="text-gray-400 hover:text-blue-400 transition-colors duration-300">
                        <span class="sr-only">Twitter</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-4">
                        โรงพยาบาลเมตตาประชารักษ์ (วัดไร่ขิง)
                    </h3>
                    <p class="mt-4 text-sm text-gray-500">
                        มุ่งสู่การเป็นสถาบันจักษุแห่งชาติ ขีดความสามารถในระดับสากล พึ่งตนเองได้สูง เพื่อประชาชนมีสุขภาพตาที่ดีอย่างยั่งยืน
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-4">
                        เมนู
                    </h3>
                    <ul class="mt-4 space-y-2">
                        <li><a href="./" class="text-sm text-gray-500 hover:text-gray-900">หน้าหลัก</a></li>
                        <li><a href="./?page=register" class="text-sm text-gray-500 hover:text-gray-900">ลงทะเบียนบัตรผู้ป่วยใหม่</a></li>
                        <li><a href="./?page=patient_check" class="text-sm text-gray-500 hover:text-gray-900">เช็คข้อมูลบัตรผู้ป่วยใหม่</a></li>
                        <?php if (isset($user)): ?>
                            <li><a href="./?page=dashboard" class="text-sm text-gray-500 hover:text-gray-900">แดชบอร์ดผู้ป่วย</a></li>
                        <?php endif; ?>
                        <?php if (isset($user) && $user['roleId'] == 2): ?>
                            <li><a href="./?page=manage_users" class="text-sm text-gray-500 hover:text-gray-900">จัดการผู้ใช้</a></li>
                            <li><a href="./?page=manage_departments" class="text-sm text-gray-500 hover:text-gray-900">จัดการแผนกงาน</a></li>
                        <?php endif; ?>
                        <?php if (isset($user)): ?>
                            <li><a href="./?page=logout" class="text-sm text-gray-500 hover:text-gray-900">ออกจากระบบ</a></li>
                        <?php else: ?>
                            <li><a href="./?page=login" class="text-sm text-gray-500 hover:text-gray-900">ล็อคอินเข้าสู่ระบบ</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 tracking-wider uppercase mb-4">
                        ที่อยู่
                    </h3>
                    <ul class="mt-4 space-y-2">
                        <li class="flex items-start">
                            <svg class="h-6 w-6 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm text-gray-500">เลขที่ 52 หมู่ 2 ตำบลไร่ขิง อำเภอสามพราน จังหวัดนครปฐม 73210</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span class="text-sm text-gray-500">(034) 225830, (034) 388700</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span class="text-sm text-gray-500">(034) 388744</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-sm text-gray-500 text-center">
                © 2567 Copyright :
                <a href="https://metta.go.th" class="text-gray-600 font-semibold hover:text-gray-900">metta.go.th</a>
            </p>
        </div>
    </div>
</footer>