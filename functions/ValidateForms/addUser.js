function addUser() {
    if (validate()) {
      let form = document.getElementById("add_user_form");
  
      if (validate()) {
        form.submit();
      }
    }
}


function validate() {
    let pass = true;
    let username = document.querySelector("#username");
    let password = document.querySelector("#password");
    let confirm_password = document.querySelector("#confirm_password");
    let role = document.querySelector("#role");
  
    if (username.value.length < 6) {
      pass = false
      Swal.fire({
            icon: "error",
            title: "กรุณาระบุชื่อผู้ใช้ 6 ตัวอักษรขึ้นไป",
            text: "โปรดระบุชื่อผู้ใช้ 6 ตัวอักษรขึ้นไป",
            confirmButtonText: 'ลองอีกครั้ง',
            confirmButtonColor: '#f5424b',
      });
      username.style.border = "1px solid red";
    } else if (password.value.length < 6){
            pass = false;
            Swal.fire({
                icon: "error",
                title: "กรุณาตั้งรหัสผ่าน 6 ตัวอักษรขึ้นไป",
                text: "โปรดตั้งรหัสผ่าน 6 ตัวอักษรขึ้นไป",
                confirmButtonText: 'ลองอีกครั้ง',
                confirmButtonColor: '#f5424b',
            });
            password.style.border = "1px solid red";
        if (password.value !== confirm_password.value) {
            pass = false;
            Swal.fire({
                icon: "error",
                title: "กรุณาป้อนยืนยันรหัสผ่านให้ตรงกัน",
                text: "โปรดป้อนยืนยันรหัสผ่านให้ตรงกัน",
                confirmButtonText: 'ลองอีกครั้ง',
                confirmButtonColor: '#f5424b',
            });
            password.style.border = '1px solid red';
            confirm_password.style.border = '1px solid red';
        }
    } else if (!role.value) {
        pass = false;
        Swal.fire({
            icon: "error",
            title: "กรุณาเลือกตำแหน่ง",
            text: "โปรดเลือกตำแหน่ง",
            confirmButtonText: 'ลองอีกครั้ง',
            confirmButtonColor: '#f5424b',
        });
        role.style.border = '1px solid red';
    } 
    if (!pass) {
        return pass;  
    }

    return pass;
}
  
