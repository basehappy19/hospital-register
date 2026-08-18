function removeUser(userId, username) {
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
                inputPlaceholder: 'กรุณากรอกชื่อผู้ใช้เพื่อยืนยัน',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'ยกเลิก',
                confirmButtonText: 'ยืนยันการลบ',
                preConfirm: (inputValue) => {
                    if (inputValue != username) {
                        Swal.showValidationMessage('ชื่อผู้ใช้ไม่ถูกต้อง กรุณาลองอีกครั้ง');
                    } else {
                        return inputValue;
                    }
                }
            }).then((finalResult) => {
                if (finalResult.isConfirmed) {
                    window.location.href = './?page=remove_user&userId=' + userId;
                }
            });
        }
    });
}
