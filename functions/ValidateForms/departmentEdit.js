function updateDepartment() {
    if (validate()) {
      let form = document.getElementById("edit_department_form");
  
      if (validate()) {
        form.submit();
      }
    }
}


function validate() {
    let pass = true;
    let name = document.querySelector("#name");
  
    if (!name.value) {
      pass = false
      Swal.fire({
            icon: "error",
            title: "กรุณาระบุชื่อแผนก",
            text: "โปรดระบุชื่อแผนก",
            confirmButtonText: 'ลองอีกครั้ง',
            confirmButtonColor: '#f5424b',
      });
      name.style.border = "1px solid red";
    }
    if (!pass) {
        return pass;  
    }

    return pass;
}
  
