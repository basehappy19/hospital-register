function logoutSession(loginId, userId) {
    Swal.fire({
        title: 'ท่านแน่ใจหรือไม่?',
        text: "เมื่อยืนยันผู้ใช้อุปกรณ์นี้จำเป็นต้องเข้าสู่ระบบใหม่",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'ยกเลิก',
        confirmButtonText: 'ยืนยัน',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = './?page=logout_session&loginId=' + loginId + '&userId=' + userId;
        }   
    });
}


function logoutAllSession(userId) {
    Swal.fire({
        title: 'ท่านแน่ใจหรือไม่?',
        text: "เมื่อยืนยันผู้ใช้ทั้งหมด จำเป็นต้องเข้าสู่ระบบใหม่",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'ยกเลิก',
        confirmButtonText: 'ยืนยัน',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = './?page=logout_session_all&userId=' + userId;
        }   
    });
}