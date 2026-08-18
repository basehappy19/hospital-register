<nav id="navbar" class="bg-white border-b-4 border-[#84BEB2] shadow-[0_5px_rgba(255,107,213,0.32)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="./">
                        <img class="h-10 w-auto" src="./public/logo.png" alt="Logo" draggable="false">
                    </a>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                    <a href="./" class="transition-all duration-300 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                        <img src="./public/icon/home.png" alt="Home" class="h-5 w-5 mr-1">
                        หน้าแรก
                    </a>
                    <?php if (!isset($user) || $user['roleId'] != 2): ?>
                        <a href="./?page=register" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            <img src="./public/icon/register.png" alt="Register" class="h-5 w-5 mr-1">
                            ลงทะเบียนบัตรผู้ป่วยใหม่
                        </a>
                        <a href="./?page=patient_check" class="transition-all duration-300 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            <img src="./public/icon/card.png" alt="Check" class="h-5 w-5 mr-1">
                            เช็คข้อมูลบัตรผู้ป่วยใหม่
                        </a>
                    <?php endif; ?>
                    <?php if (isset($user)): ?>
                        <a href="./?page=dashboard" class="transition-all duration-300 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            <img src="./public/icon/dashboard.png" alt="Dashboard" class="h-5 w-5 mr-2">
                            แดชบอร์ดผู้ป่วย
                        </a>
                    <?php endif; ?>
                    <?php if (isset($user['roleId']) && $user['roleId'] == 2): ?>
                        <a href="./?page=manage_users" class="transition-all duration-300 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            <img src="./public/icon/programmer.png" alt="User Management" class="h-5 w-5 mr-1">
                            จัดการผู้ใช้
                        </a>
                        <a href="./?page=manage_departments" class="transition-all duration-300 inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">
                            <img src="./public/icon/department.png" alt="Department" class="h-5 w-5 mr-1">
                            จัดการแผนกงาน
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="hidden sm:ml-6 sm:flex sm:items-center">
                <?php if (isset($user)): ?>
                    <span class="text-sm font-medium text-gray-500 mr-4"><?php echo $user['displayname']; ?></span>
                    <a href="./?page=logout" class="transition-all duration-300 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        ออกจากระบบ
                        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
            <div class="-mr-2 flex items-center sm:hidden">
                <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="sm:hidden hidden" id="mobile-menu">
        <div class="pt-2 pb-3 space-y-1">
            <a href="./" class="transition-all duration-300 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">
                <img src="./public/icon/home.png" alt="Home" class="inline-flex h-5 w-5 mr-1">
                หน้าแรก
            </a>
            <a href="./?page=register" class="transition-all duration-300 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">
                <img src="./public/icon/register.png" alt="Register" class="inline-flex h-5 w-5 mr-1">
                ลงทะเบียนบัตรผู้ป่วยใหม่
            </a>
            <a href="./?page=patient_check" class="transition-all duration-300 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">
                <img src="./public/icon/card.png" alt="Check" class="inline-flex h-5 w-5 mr-1">
                เช็คข้อมูลบัตรผู้ป่วยใหม่
            </a>
            <?php if (isset($user)): ?>
                <a href="./?page=dashboard" class="transition-all duration-300 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">
                    <img src="./public/icon/dashboard.png" alt="Dashboard" class="inline-flex h-5 w-5 mr-1">
                    แดชบอร์ดผู้ป่วย
                </a>
            <?php endif; ?>
            <?php if (isset($user['roleId']) && $user['roleId'] == 2): ?>
                <a href="./?page=manage_users" class="transition-all duration-300 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">
                    <img src="./public/icon/programmer.png" alt="User Management" class="inline-flex h-5 w-5 mr-1">
                    จัดการผู้ใช้
                </a>
                <a href="./?page=manage_departments" class="transition-all duration-300 block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">
                    <img src="./public/icon/department.png" alt="Department" class="inline-flex h-5 w-5 mr-1">
                    จัดการแผนกงาน
                </a>
            <?php endif; ?>
            <?php if (isset($user)): ?>
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <div class="flex gap-3 items-center px-4 mt-4">
                        <div class="flex-shrink-0">
                            <span class="inline-block h-10 w-10 rounded-full overflow-hidden bg-gray-100">
                                <svg class="inline-flex h-full w-full" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                        </div>
                        <div class="ml-3">
                            <div class="text-base font-medium text-gray-800"><?php echo $user['displayname']; ?></div>
                        </div>
                    </div>
                    <div class="mt-3 space-y-1">
                        <a href="./?page=logout" class="transition-all duration-300 block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">
                            ออกจากระบบ
                            <svg class="inline-flex h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
    const mobileMenuButton = document.querySelector('button[aria-controls="mobile-menu"]');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileMenuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });
</script>