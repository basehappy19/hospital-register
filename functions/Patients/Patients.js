function RecoveryPatient(patientId) {
    Swal.fire({
        title: 'ท่านแน่ใจหรือไม่?',
        text: "เมื่อยืนยัน ข้อมูลจะถูกนำกลับเข้าข้อมูลหลัก",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'ยกเลิก',
        confirmButtonText: 'ยืนยัน',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = './?page=recovery_patient&patientId=' + patientId;
        }
    });
}

function RemovePatient(patientId, cid) {
    Swal.fire({
        title: 'ท่านแน่ใจหรือไม่?',
        text: "การลบข้อมูลนี้สามารถกู้คืนได้",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'ยกเลิก',
        confirmButtonText: 'ยืนยันการลบ',
        input: 'text',
        inputPlaceholder: 'กรุณากรอกเหตุผลในการลบ',
        preConfirm: (reasonForDeletion) => {
            if (reasonForDeletion.length <= 0) {
                Swal.showValidationMessage('กรุณากรอกเหตุผลในการลบ');
            } else {
                return reasonForDeletion;
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const reasonForDeletion = result.value;
            Swal.fire({
                title: 'กรุณายืนยันอีกครั้ง',
                text: "ท่านแน่ใจหรือไม่ว่าต้องการลบข้อมูลผู้ป่วยนี้?",
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'กรุณากรอกเลขบัตรประชาชนผู้ป่วยเพื่อยืนยัน',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'ยกเลิก',
                confirmButtonText: 'ยืนยันการลบ',
                preConfirm: (inputValue) => {
                    if (inputValue != cid) {
                        Swal.showValidationMessage('เลขบัตรประชาชนไม่ถูกต้อง กรุณาลองอีกครั้ง');
                    } else {
                        return inputValue;
                    }
                }
            }).then((finalResult) => {
                if (finalResult.isConfirmed) {
                    window.location.href = './?page=remove_patient&patientId=' + patientId + '&reasonForDeletion=' + reasonForDeletion;
                }
            });
        }
    });
}

function RemovePatientPermanent(patientId, cid) {
    Swal.fire({
        title: 'ท่านแน่ใจหรือไม่?',
        text: "การลบข้อมูลนี้จะไม่สามารถย้อนกลับได้",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'ยกเลิก',
        confirmButtonText: 'ยืนยันการลบ',
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'กรุณายืนยันอีกครั้ง',
                text: "ท่านแน่ใจหรือไม่ว่าต้องการลบข้อมูลผู้ป่วยนี้?",
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'กรุณากรอกเลขบัตรประชาชนผู้ป่วยเพื่อยืนยัน',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'ยกเลิก',
                confirmButtonText: 'ยืนยันการลบ',
                preConfirm: (inputValue) => {
                    if (inputValue != cid) {
                        Swal.showValidationMessage('เลขบัตรประชาชนไม่ถูกต้อง กรุณาลองอีกครั้ง');
                    } else {
                        return inputValue;
                    }
                }
            }).then((finalResult) => {
                if (finalResult.isConfirmed) {
                    window.location.href = './?page=remove_patient_permanent&patientId=' + patientId;
                }
            });
        }
    });
}
