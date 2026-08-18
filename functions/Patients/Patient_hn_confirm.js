function ConfirmPatient(patientId) {
    Swal.fire({
        title: 'ยืนยันการยืนยันข้อมูลผู้ป่วย',
        text: 'คุณต้องการยืนยันข้อมูลของผู้ป่วยนี้หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'ยกเลิก',
        confirmButtonText: 'ยืนยัน',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = './?page=patient_hn_confirm&patientId=' + patientId;
        }
    });
}
