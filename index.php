<?php
session_start();
ob_start();
require './configs/db.php';
require './functions/MetaTitle.php';
require './functions/Auth/Login.php';
require './functions/Auth/Users.php';
require './functions/Patients/Patents.php';
require './functions/Patients/Patients.php';
require './functions/Patients/Address.php';
require './functions/Patients/Call_Status.php';
require './functions/Patients/HN_Verification.php';
require './functions/Patients/Relative.php';
require './functions/Patients/Symptoms.php';
require './functions/Patients/Visits.php';
require './functions/ActivityLogs.php';
require './functions/Format.php';
require 'vendor/autoload.php';


function load_view($view, $data = [], $user = null, $patientId = null, $userId = null, $departmentId = null): void {
    extract($data);
    if ($user) {
        $data['user'] = $user;
    }

    if($patientId){
        $data['patientId'] = (int)$patientId;
    }

    require __DIR__ . "/views/{$view}.php";
}

$page = isset($_GET['page']) && $_GET['page'] != '' ? $_GET['page'] : null;
$state = isset($_GET['state']) && $_GET['state'] != '' ? $_GET['state'] : null;
$patientId = (int)isset($_GET['patientId']) && $_GET['patientId'] != '' ? $_GET['patientId'] : null;
$userId = (int)isset($_GET['userId']) && $_GET['userId'] != '' ? $_GET['userId'] : null;
$departmentId = (int)isset($_GET['departmentId']) && $_GET['departmentId'] != '' ? $_GET['departmentId'] : null;
$loginId = (int)isset($_GET['loginId']) && $_GET['loginId'] != '' ? $_GET['loginId'] : null;
$reasonForDeletion = isset($_GET['reasonForDeletion']) && $_GET['reasonForDeletion'] != '' ? $_GET['reasonForDeletion'] : null;
$reasonForFailedCall = isset($_GET['reasonForFailedCall']) && $_GET['reasonForFailedCall'] != '' ? $_GET['reasonForFailedCall'] : null;

if (isset($_SESSION['userId']) && isset($_SESSION['loginId'])) {
    if(checkSession($conn, $_SESSION['loginId'], $_SESSION['userId'])){
        $user = getUserData($conn, $_SESSION['userId']);
        if(!$user){
            unset($_SESSION['userId']);
            unset($_SESSION['loginId']);
            header('Location: ./?page=login');
            exit();
        }
    } else {
        $_SESSION['alert'] = '
                    <script>
                        Swal.fire({
                            title: "เซสชั่นหมดอายุ",
                            text: "กรุณาเข้าสู่ระบบใหม่อีกครั้ง",
                            icon: "error",
                            showConfirmButton: false,
                            timer: 3000, 
                        });
                    </script>
        ';
        unset($_SESSION['userId']);
        unset($_SESSION['loginId']);
        header('Location: ./?page=login');
        exit();
    }
} else {
    $user = null; 
}

if($page == 'patient_info_pdf' && isset($patientId) && (isset($user) && ($user['roleId'] == 1 || $user['roleId'] == 2))){

    $patient = getPatientForExportPDF($conn, $patientId);
    if(!$patient){
        header('Location: ./?page=dashboard');
        exit();
    }
    class MYPDF extends TCPDF {
        public $patient_name = '';
        public $patient_id = 0;
        public function Header() {
            $this->SetFont('thsarabunnew', 'B', 20);
            $this->Cell(0, 15, 'รายละเอียดผู้ป่วย', 0, false, 'C', 0, '', 0, false, 'M', 'M');

            $this->SetFont('thsarabunnew', '', 16);
            $this->Ln(10);
            $this->Cell(0, 10, $this->patient_name, 0, false, 'C', 0, '', 0, false, 'M', 'M');

            $this->SetFont('thsarabunnew', '', 16);
            $this->Ln(5);
            $this->Cell(0, 10, 'ID : '.$this->patient_id, 0, false, 'C', 0, '', 0, false, 'M', 'M');
        }
    }

    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->patient_name = $patient['fullname'];
    $pdf->patient_id = $patient['id'];

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('โรงพยาบาลเมตตาประชารักษ์ (วัดไร่ขิง)');
    $pdf->SetTitle("ข้อมูลผู้ป่วย {$patient['fullname']}");
    $pdf->SetSubject('รายละเอียดผู้ป่วย');
    $pdf->SetKeywords('ผู้ป่วย, ข้อมูล, การรักษา');

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    
    $pdf->AddFont('thsarabunnew', '', '', true);
    $pdf->AddFont('thsarabunnew', 'B', '', true);

    $pdf->AddPage();

    $pdf->SetFillColor(173, 216, 230);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('thsarabunnew', 'B', 16);
    $pdf->Cell(0, 10, 'ข้อมูลส่วนตัว', 0, 1, 'L', true); 

    $pdf->Cell(50, 10, 'ชื่อ-นามสกุล:', 0, 0);
    $pdf->Cell(0, 10, $patient['fullname'], 0, 1);

    $pdf->Cell(50, 10, 'เลขบัตรประชาชน:', 0, 0);
    $pdf->Cell(0, 10, $patient['cid'], 0, 1);

    $pdf->Cell(50, 10, 'วันเกิด:', 0, 0);
    $pdf->Cell(0, 10, formatDate($patient['bday']) . ' (อายุ ' . $patient['age'] . ' ปี)', 0, 1);

    $pdf->Cell(50, 10, 'เพศ:', 0, 0);
    $pdf->Cell(0, 10, $patient['gender'], 0, 1);

    $pdf->Cell(50, 10, 'อาชีพ:', 0, 0);
    $pdf->Cell(0, 10, $patient['job'], 0, 1);

    $pdf->Cell(50, 10, 'เบอร์โทรศัพท์:', 0, 0);
    $pdf->Cell(0, 10, $patient['phone_number'], 0, 1);

    $pdf->Cell(50, 10, 'สถานภาพ:', 0, 0);
    $pdf->Cell(0, 10, $patient['marital_status'], 0, 1);

    $address_own = [
        'house_number' => $patient['address_own_house_number'],
        'village' => $patient['address_own_village'],
        'no' => $patient['address_own_no'],
        'building' => $patient['address_own_building'],
        'floor' => $patient['address_own_floor'],
        'road' => $patient['address_own_road'],
        'alley' => $patient['address_own_alley'],
        'subdistrict' => $patient['address_own_subdistrict'],
        'district' => $patient['address_own_district'],
        'province' => $patient['address_own_province'],
        'zipcode' => $patient['address_own_zipcode'],
    ];

    $pdf->Cell(50, 10, 'ที่อยู่:', 0, 0);
    $pdf->MultiCell(0, 10, formatAddress($address_own), 0, 'L');

    $pdf->Ln(5);
    
    $pdf->SetFont('thsarabunnew', 'B', 16);
    $pdf->SetFillColor(173, 216, 230);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 10, 'ข้อมูลญาติ', 0, 1, 'L', true);
    $pdf->SetFont('thsarabunnew', '', 14);

    $pdf->Cell(50, 10, 'ชื่อ-นามสกุล:', 0, 0);
    $pdf->Cell(0, 10, $patient['relative_fullname'], 0, 1);

    $pdf->Cell(50, 10, 'ความสัมพันธ์:', 0, 0);
    $pdf->Cell(0, 10, $patient['relationship'], 0, 1);
    
    $pdf->Cell(50, 10, 'เบอร์โทรศัพท์:', 0, 0);
    $pdf->Cell(0, 10, $patient['relative_phone_number'], 0, 1);

    $address_relative = [
        'house_number' => $patient['address_relative_house_number'],
        'village' => $patient['address_relative_village'],
        'no' => $patient['address_relative_no'],
        'building' => $patient['address_relative_building'],
        'floor' => $patient['address_relative_floor'],
        'road' => $patient['address_relative_road'],
        'alley' => $patient['address_relative_alley'],
        'subdistrict' => $patient['address_relative_subdistrict'],
        'district' => $patient['address_relative_district'],
        'province' => $patient['address_relative_province'],
        'zipcode' => $patient['address_relative_zipcode'],
    ];

    $pdf->Cell(50, 10, 'ที่อยู่:', 0, 0);
    $pdf->MultiCell(0, 10, formatAddress($address_relative), 0, 'L');

    $pdf->Ln(5);

    $pdf->SetFont('thsarabunnew', 'B', 16);
    $pdf->SetFillColor(173, 216, 230);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 10, 'ข้อมูลการลงทะเบียน', 0, 1, 'L', true);
    $pdf->SetFont('thsarabunnew', '', 14);

    $pdf->Cell(50, 10, 'วันที่ลงทะเบียน:', 0, 0);
    $pdf->Cell(0, 10, formatDateTime($patient['registered_at']), 0, 1);

    $pdf->Cell(50, 10, 'อาการ:', 0, 0);
    $pdf->MultiCell(0, 10, $patient['symptom_description'], 0, 1);
    
    $pdf->Cell(50, 10, 'สิทธิการรักษา:', 0, 0);
    $pdf->MultiCell(0, 10, (isset($patient['patent_name']) && $patient['patent_name'] != 'อื่นๆ' ? $patient['patent_name'] : $patient['patent_other']), 0, 1);

    $pdf->Cell(50, 10, 'แผนกที่ต้องการ:', 0, 0);
    $pdf->MultiCell(0, 10, $patient['department_name'], 0, 1);
    
    $pdf->Cell(50, 10, 'สถานที่ต้องการ:', 0, 0);
    $pdf->MultiCell(0, 10, $patient['location_name'], 0, 1);
    
    $pdf->Output("ข้อมูลผู้ป่วย_{$patient['fullname']}.pdf", 'I');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./dist/globals.css">
    <link rel="stylesheet" href="./dist/index.css">
    <script src="./lib/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.2/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.2/dist/sweetalert2.all.min.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="./favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./favicon/favicon-16x16.png">
    <link rel="manifest" href="./favicon/site.webmanifest">
    <link rel="mask-icon" href="./favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
</head>

<body>
    <?php require './layouts/Navbar.php'; ?>
    <?php 
    if(isset($_SESSION['alert'])){
        echo $_SESSION['alert'];
        unset($_SESSION['alert']);
    }
    ?>
    <?php
    switch ($page) {
        case '':
            load_view(view: 'Home', data: [], user: $user);
            break;
        case 'register':
            if(!empty($_SESSION['register_data']['state-1'])){
                if(!empty($_SESSION['register_data']['state-3'])){
                    if($state == 3){
                        load_view(view: 'PatientRegister_3', data: [], user: $user);
                    } else if ($state == 2) {
                        load_view(view: 'PatientRegister_2', data: [], user: $user);
                    } else {
                        load_view(view: 'PatientRegister', data: [], user: $user);
                    }
                } else if (!empty($_SESSION['register_data']['state-2'])){
                    if($state == 3){
                        load_view(view: 'PatientRegister_3', data: [], user: $user);
                    } else if ($state == 2) {
                        load_view(view: 'PatientRegister_2', data: [], user: $user);
                    } else {
                        load_view(view: 'PatientRegister', data: [], user: $user);
                    }
                } else {
                    if($state == 3){
                        header(header: 'location: ./?page=register&state=2');
                        break;
                    } else if ($state == 2) {
                        load_view(view: 'PatientRegister_2', data: [], user: $user);
                    } else {
                        load_view(view: 'PatientRegister', data: [], user: $user);
                    }
                }
            } else {
                if($state){
                    header(header: 'location: ./?page=register');
                    break;
                }
                load_view(view: 'PatientRegister', data: [], user: $user);
            }
            break;
        case 'register_submit':
            require './action/PatientRegisterSubmit.php';
            break;
        case 'patient_check':
            load_view('PatientCheck', data: [], user: $user);
            break;
        case 'patient_info':
            load_view('PatientInfo', data: [], user: $user);
            break;
        case 'patient_edit':
            if(isset($patientId) && ($user && ($user['roleId'] == 1 || $user['roleId'] == 2))){
                load_view('PatientEdit', data: [], patientId: $patientId, user: $user);
            } else {
                header("Location: ./?page=dashboard");
            }
            break;
        case 'patient_call_called':
            if(isset($patientId) && ($user && ($user['roleId'] == 2 || $user['roleId'] == 3))){
                if(CalledStatus($conn, $patientId)){
                    $_SESSION['alert'] = '
                                    <script>
                                        Swal.fire({
                                            title: "สำเร็จ!",
                                            text: "อัปเดตสถานะการโทรสำเร็จ",
                                            icon: "success",
                                            showConfirmButton: false,
                                            timer: 1500, 
                                        });
                                    </script>
                    ';
                    header("Location: ./?page=dashboard");
                }
            } else {
                $_SESSION['alert'] = '
                        <script>
                            Swal.fire({
                                title: "เกิดข้อผิดพลาด!",
                                text: "ไม่สามารถอัปเดตสถานะการโทรได้",
                                icon: "error",
                                showConfirmButton: false,
                                timer: 1500,                            
                            });
                        </script>
                    ';
                header("Location: ./?page=dashboard");
            }
            break;
        case 'patient_call_failed':
            if(isset($patientId) && $reasonForFailedCall && ($user && ($user['roleId'] == 2 || $user['roleId'] == 3))){
                if(FailedCallStatus($conn, $patientId, $reasonForFailedCall)){
                    $_SESSION['alert'] = '
                                    <script>
                                        Swal.fire({
                                            title: "อัพเดทสถานะเป็นติดต่อไม่ได้",
                                            text: "อัปเดตสถานะการโทรสำเร็จ",
                                            icon: "success",
                                            showConfirmButton: false,
                                            timer: 1500, 
                                        });
                                    </script>
                    ';
                    header("Location: ./?page=dashboard");
                }
            } else {
                $_SESSION['alert'] = '
                        <script>
                            Swal.fire({
                                title: "เกิดข้อผิดพลาด!",
                                text: "ไม่สามารถอัปเดตสถานะการโทรได้",
                                icon: "error",
                                showConfirmButton: false,
                                timer: 1500,                            
                            });
                        </script>
                    ';
                header("Location: ./?page=dashboard");
            }
            break;
        case 'remove_patient':
            if($user && ($user['roleId'] == 1 || $user['roleId'] == 2)){
                if(removePatient($conn, $patientId, $reasonForDeletion)){
                    $_SESSION['alert'] = '
                                    <script>
                                        Swal.fire({
                                            title: "ย้ายข้อมูลสำเร็จ!",
                                            text: "ข้อมูลผู้ป่วยถูกย้ายไปไว้ใน ข้อมูลผู้ป่วยที่ถูกลบแล้ว",
                                            icon: "success",
                                            showConfirmButton: false,
                                            timer: 1500, 
                                        });
                                    </script>
                    ';
                    header("Location: ./?page=dashboard");
                } else {
                    $_SESSION['alert'] = '
                                    <script>
                                        Swal.fire({
                                            title: "เกิดข้อผิดพลาด!",
                                            text: "ไม่สามารถลบข้อมูลผู้ป่วยได้ กรุณาลองใหม่อีกครั้ง",
                                            icon: "success",
                                            showConfirmButton: false,
                                            timer: 1500, 
                                        });
                                    </script>
                    ';
                    header("Location: ./?page=dashboard");
                }
            } else {
                header(header: 'location: ./?page=login');
            }
            break;
        case 'remove_patient_permanent':
            if($user && ($user['roleId'] == 1 || $user['roleId'] == 2)){
                if(permanentlyDeletePatient($conn, $patientId)){
                    $_SESSION['alert'] = '
                                    <script>
                                        Swal.fire({
                                            title: "ลบข้อมูลสำเร็จ!",
                                            text: "ข้อมูลผู้ป่วยถูกนำออกจากระบบแล้ว",
                                            icon: "success",
                                            showConfirmButton: false,
                                            timer: 1500, 
                                        });
                                    </script>
                    ';
                    header("Location: ./?page=dashboard&deleted=true");
                } else {
                    $_SESSION['alert'] = '
                                    <script>
                                        Swal.fire({
                                            title: "เกิดข้อผิดพลาด!",
                                            text: "ไม่สามารถลบข้อมูลผู้ป่วยได้ กรุณาลองใหม่อีกครั้ง",
                                            icon: "success",
                                            showConfirmButton: false,
                                            timer: 1500, 
                                        });
                                    </script>
                    ';
                    header("Location: ./?page=dashboard&deleted=true");
                }
            } else {
                header(header: 'location: ./?page=login');
            }
            break;
        case 'recovery_patient':
            if($user && ($user['roleId'] == 1 || $user['roleId'] == 2)){
                if(restorePatient($conn, $patientId)){
                    $_SESSION['alert'] = '
                                    <script>
                                        Swal.fire({
                                            title: "กู้คืนข้อมูลสำเร็จ!",
                                            text: "ข้อมูลผู้ป่วยได้ถูกนำกลับเข้าข้อมูลหลักแล้ว",
                                            icon: "success",
                                            showConfirmButton: false,
                                            timer: 1500, 
                                        });
                                    </script>
                    ';
                    header("Location: ./?page=dashboard");
                } else {
                    $_SESSION['alert'] = '
                                    <script>
                                        Swal.fire({
                                            title: "เกิดข้อผิดพลาด!",
                                            text: "ไม่สามารถกู้คืนข้อมูลผู้ป่วยได้ กรุณาลองใหม่อีกครั้ง",
                                            icon: "showConfirmButton",
                                            showConfirmButton: false,
                                            timer: 1500, 
                                        });
                                    </script>
                    ';
                    header("Location: ./?page=dashboard");
                }
            } else {
                header(header: 'location: ./?page=login');
            }
            break;
        case 'patient_hn_confirm':
            if($user && ($user['roleId'] == 1 || $user['roleId'] == 2)){
                if(patient_hn_confirm($conn, $patientId)){
                    $_SESSION['alert'] = '
                                    <script>
                                        Swal.fire({
                                            title: "ยืนยันข้อมูลสำเร็จ!",
                                            text: "ข้อมูลผู้ป่วยได้รับการยืนยันแล้ว",
                                            icon: "success",
                                            showConfirmButton: false,
                                            timer: 1500,
                                        });
                                    </script>
                    ';
                    header("Location: ./?page=dashboard");
                }
            } else {
                $_SESSION['alert'] = '
                        <script>
                            Swal.fire({
                                title: "เกิดข้อผิดพลาด!",
                                text: "ไม่สามารถยืนยันข้อมูลผู้ป่วยได้ กรุณาลองใหม่อีกครั้ง",
                                icon: "error", 
                                showConfirmButton: false,
                                timer: 1500,                           
                            });
                        </script>
                ';
                header(header: 'location: ./?page=login');
            }
            break;
        case 'dashboard':
            if (!isset($user)) {
                header("Location: ./?page=login");
            } else {
                load_view('Dashboard', data: [], user: $user);
            }
            break;
        case 'manage_users':
            if (isset($user) && $user['roleId'] == 2) {
                load_view('Manage_users', data: [], user: $user);
            } else {
                header("Location: ./?page=login");
            }
            break;
        case 'user_activity_logs':
            if (isset($user) && $user['roleId'] == 2) {
                if(isset($userId)){
                    load_view('User_activity_logs', data: [], user: $user, userId: $userId);
                } else {
                    header("Location: ./?page=manage_users");
                }
            } else {
                header("Location: ./?page=login");
            }
            break;
        case 'logout_session':
            if (isset($user) && $user['roleId'] == 2) {
                if(isset($loginId) && isset($userId)) {
                    if(logoutSession($conn, $loginId, $userId)){
                        $_SESSION['alert'] = '
                                    <script>
                                        Swal.fire({
                                            title: "นำผู้ใช้ออกจากระบบแล้ว",
                                            text: "อุปกรณ์ดังกล่าวได้ถูกนำออกแล้ว",
                                            icon: "success",
                                            showConfirmButton: false,
                                            timer: 1500, 
                                        });
                                    </script>
                        ';
                        header("Location: ./?page=user_activity_logs&userId={$userId}");
                    } else {
                        $_SESSION['alert'] = '
                                <script>
                                    Swal.fire({
                                        title: "เกิดข้อผิดพลาด!",
                                        text: "ไม่สามารถนำผู้ใช้ออกจากระบบได้ กรุณาลองใหม่อีกครั้ง",
                                        icon: "error", 
                                        showConfirmButton: false,
                                        timer: 1500,                           
                                    });
                                </script>
                        ';
                        header("Location: ./?page=user_activity_logs&userId={$userId}");
                    };
                } else {
                    header("Location: ./?page=manage_users");
                }
            } else {
                header("Location: ./?page=login");
            }
            break;
        case 'logout_session_all':
            if (isset($user) && $user['roleId'] == 2) {
                if(isset($_SESSION['loginId']) && isset($userId)) {
                    if(logoutSessionAll($conn, $_SESSION['loginId'], $userId)){
                        $_SESSION['alert'] = '
                                    <script>
                                        Swal.fire({
                                            title: "นำผู้ใช้ทั้งหมดออกจากระบบแล้ว",
                                            text: "อุปกรณ์ทั้งหมดได้ถูกนำออกแล้ว",
                                            icon: "success",
                                            showConfirmButton: false,
                                            timer: 1500, 
                                        });
                                    </script>
                        ';
                        header("Location: ./?page=user_activity_logs&userId={$userId}");
                    } else {
                        $_SESSION['alert'] = '
                                <script>
                                    Swal.fire({
                                        title: "เกิดข้อผิดพลาด!",
                                        text: "ไม่สามารถนำผู้ใช้ทั้งหมดออกจากระบบได้ กรุณาลองใหม่อีกครั้ง",
                                        icon: "error", 
                                        showConfirmButton: false,
                                        timer: 1500,                           
                                    });
                                </script>
                        ';
                        header("Location: ./?page=user_activity_logs&userId={$userId}");
                    };
                } else {
                    header("Location: ./?page=manage_users");
                }
            } else {
                header("Location: ./?page=login");
            }
            break;
        case 'edit_user':
            if (isset($userId) && isset($user) && $user['roleId'] == 2) {
                load_view('UserEdit', data: [], user: $user, userId: $userId);
            } else {
                header("Location: ./?page=login");
            }
            break;
        case 'add_user':
            if (isset($user) && $user['roleId'] == 2) {
                load_view('AddUser', data: [], user: $user);
            } else {
                header("Location: ./?page=login");
            }
            break;
        case 'remove_user':
            if($user && $user['roleId'] == 2){
                if(removeUser($conn, $userId)){
                    $_SESSION['alert'] = '
                        <script>
                            Swal.fire({
                                title: "ลบข้อมูลสำเร็จ!",
                                text: "ข้อมูลผู้ใช้ได้ถูกลบออกจากระบบแล้ว",
                                icon: "success",
                                showConfirmButton: false,
                                timer: 1500, 
                            });
                        </script>
                    ';
                    header("Location: ./?page=manage_users");
                } else {
                    $_SESSION['alert'] = '
                        <script>
                            Swal.fire({
                                title: "เกิดข้อผิดพลาด!",
                                text: "ไม่สามารถลบข้อมูลผู้ใช้ได้ กรุณาลองใหม่อีกครั้ง",
                                icon: "error",
                                showConfirmButton: false,
                                timer: 1500, 
                            });
                        </script>
                    ';
                    header("Location: ./?page=manage_users");
                }
            } else {
                header(header: 'location: ./?page=login');
            }
            break;
        case 'manage_departments':
            if (isset($user) && $user['roleId'] == 2) {
                load_view('Manage_departments', data: [], user: $user);
            } else {
                header("Location: ./?page=login");
            }
            break;
        case 'edit_department':
            if (isset($departmentId) && isset($user) && $user['roleId'] == 2) {
                load_view('DepartmentEdit', data: [], user: $user, departmentId: $departmentId);
            } else {
                header("Location: ./?page=login");
            }
            break;
        case 'add_department':
            if (isset($user) && $user['roleId'] == 2) {
                load_view('AddDepartment', data: [], user: $user);
            } else {
                header("Location: ./?page=login");
            }
            break;
        case 'login':
            if (isset($user)) {
                header("Location: ./?page=dashboard");
                break;
            }
            load_view('Login', data: [], user: $user);
            break;
        case 'logout':
            if($_SESSION['userId']){
                authLogout($conn);
            }
            header(header: 'location: ./');
            break;
        case 'patient_check_exit':
            unset($_SESSION['cid']);
            header(header: 'location: ./');
            break;
        default:
            load_view('404', data: [], user: $user);
            break;
    }
    ?>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        function clearBorder(e) {
            if(document.querySelector(`#${e.id}`).style.border == "1px solid red"){
                document.querySelector(`#${e.id}`).style.border = "1px solid #dee2e6";
            }
        }
        AOS.init();
    </script>
</body>

</html>
<?php ob_end_flush(); ?>