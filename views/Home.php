<head>
    <?php echo getTitle(text: 'ระบบลงทะเบียนผู้ป่วยใหม่ออนไลน์'); ?>
</head>

<style>
    #navbar {
        display: none;
    };
    @keyframes animate {
        0% {
            transform: translateY(0) rotate(0deg);
            opacity: 1;
            border-radius: 0;
        }

        100% {
            transform: translateY(-1000px) rotate(720deg);
            opacity: 0;
            border-radius: 50%;
        }
    }

    .background {
        position: fixed;
        width: 100vw;
        height: 100vh;
        top: 0;
        left: 0;
        margin: 0;
        padding: 0;
        background: #ba8aea;
        overflow: hidden;
        z-index: -1; 
    }

    .background li {
        position: absolute;
        display: block;
        list-style: none;
        width: 20px;
        height: 20px;
        background: rgba(255, 255, 255, 0.2);
        animation: animate 9s linear infinite;
    }

    .background li:nth-child(0) {
        left: 44%;
        width: 159px;
        height: 159px;
        bottom: -159px;
        animation-delay: 1s;
    }

    .background li:nth-child(1) {
        left: 86%;
        width: 104px;
        height: 104px;
        bottom: -104px;
        animation-delay: 2s;
    }

    .background li:nth-child(2) {
        left: 54%;
        width: 116px;
        height: 116px;
        bottom: -116px;
        animation-delay: 4s;
    }

    .background li:nth-child(3) {
        left: 77%;
        width: 154px;
        height: 154px;
        bottom: -154px;
        animation-delay: 5s;
    }

    .background li:nth-child(4) {
        left: 29%;
        width: 135px;
        height: 135px;
        bottom: -135px;
        animation-delay: 11s;
    }

    .background li:nth-child(5) {
        left: 58%;
        width: 176px;
        height: 176px;
        bottom: -176px;
        animation-delay: 23s;
    }

    .background li:nth-child(6) {
        left: 28%;
        width: 138px;
        height: 138px;
        bottom: -138px;
        animation-delay: 24s;
    }

    .background li:nth-child(7) {
        left: 36%;
        width: 164px;
        height: 164px;
        bottom: -164px;
        animation-delay: 7s;
    }

    .background li:nth-child(8) {
        left: 89%;
        width: 174px;
        height: 174px;
        bottom: -174px;
        animation-delay: 32s;
    }

    .background li:nth-child(9) {
        left: 75%;
        width: 156px;
        height: 156px;
        bottom: -156px;
        animation-delay: 25s;
    }

    .background li:nth-child(10) {
        left: 59%;
        width: 146px;
        height: 146px;
        bottom: -146px;
        animation-delay: 26s;
    }

    .background li:nth-child(11) {
        left: 63%;
        width: 135px;
        height: 135px;
        bottom: -135px;
        animation-delay: 14s;
    }

    .background li:nth-child(12) {
        left: 68%;
        width: 174px;
        height: 174px;
        bottom: -174px;
        animation-delay: 21s;
    }

    .background li:nth-child(13) {
        left: 14%;
        width: 138px;
        height: 138px;
        bottom: -138px;
        animation-delay: 6s;
    }
</style>

<ul class="background">
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
    <li></li>
</ul>

<main class="min-h-screen flex items-center justify-center">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden max-w-4xl mx-auto" data-aos="zoom-in-up" data-aos-duration="1000">
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/2 p-6 flex items-center justify-center">
                        <img src="./public/brand.png" alt="Hospital Logo" class="max-w-full h-auto">
                    </div>
                    <div class="md:w-1/2 p-6 flex flex-col justify-center">
                        <h1 class="text-3xl font-bold text-center mb-2">ระบบลงทะเบียนผู้ป่วยใหม่ออนไลน์</h1>
                        <h2 class="text-xl text-center mb-6">โรงพยาบาลเมตตาประชารักษ์ (วัดไร่ขิง)</h2>
                        <div class="space-y-4">
                            <a href="./?page=register" class="drop-shadow-md block w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded text-center transition duration-300">
                                ลงทะเบียนผู้ป่วยใหม่ 
                                <i class="fas fa-hospital"></i>
                            </a>
                            <a href="./?page=patient_check" class="drop-shadow-md block w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded text-center transition duration-300">
                                เช็คข้อมูลบัตรผู้ป่วย 
                                <i class="fas fa-id-card"></i>
                            </a>
                            <a href="./?page=login" class="drop-shadow-md block w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded text-center transition duration-300">
                                สำหรับเจ้าหน้าที่ 
                                <i class="fas fa-user-lock"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</main>

<div id="agree" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <img src="./public/brand.png" alt="Hospital Logo" class="mx-auto h-48">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">นโยบายคุ้มครองข้อมูลส่วนบุคคล (Privacy Policy)</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    นโยบายคุ้มครองข้อมูลส่วนบุคคล (Privacy Policy) ระบบลงทะเบียนทำประวัติผู้ป่วยใหม่ ออนไลน์
                    ตระหนักในความสำคัญของข้อมูลส่วนบุคคลของท่านที่ให้ไว้ และ รพ.เมตตาประชารักษ์ (วัดไร่ขิง)
                    จะเก็บรวบรวมใช้ หรือเปิดเผยข้อมูลข้อมูลส่วนบุคคลของท่านเป็นไปตามหลักเกณฑ์ที่กำหนดไว้ใน
                    พระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 ท่านสามารถอ่านรายละเอียด นโยบายและประกาศ
                    การคุ้มครองข้อมูลส่วนบุคคลบนเว็บไซต์
                    <a href="http://www.metta.go.th" class="text-blue-600 hover:underline">www.metta.go.th</a>
                </p>
                <p class="text-sm text-gray-500 mt-4">
                    เว็บไซต์นี้ใช้คุกกี้เพื่อปรับปรุงประสบการณ์การใช้งานของคุณ โปรดทราบว่าเมื่อยอมรับเงื่อนไขนี้
                    จะเป็นการยินยอมให้เราใช้คุกกี้ คุณสามารถปรับการตั้งค่าคุกกี้ได้ในเบราว์เซอร์ของคุณ
                    เพื่อปฏิเสธหรือเปิดใช้งานคุกกี้
                </p>
                <p class="text-sm text-red-500 font-semibold mt-4">
                    ขอสงวนสิทธิ เฉพาะผู้ป่วยใหม่ ที่ไม่เคยมาตรวจกับโรงพยาบาล
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button id="acceptTerms" class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                    ยอมรับเงื่อนไข
                    <i class="fas fa-thumbs-up"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="./functions/acceptTerms.js"></script>
