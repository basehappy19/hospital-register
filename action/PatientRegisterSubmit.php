<?php

if (
    isset($_SESSION['register_data']['state-1']) && 
    isset($_SESSION['register_data']['state-2']) && 
    isset($_SESSION['register_data']['state-3'])
) {
    patientRegister($conn, $_SESSION['register_data']);
    $_SESSION['cid'] = $_SESSION['register_data']['state-1']['patient']['cid'];
    header(header: 'location: ./?page=patient_info');
    exit();
}
