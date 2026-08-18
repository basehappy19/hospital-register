function submitSecondaryForm() {
    if (validate()) {
      let form = document.getElementById("register-state-2");
  
      if (validate) {
        form.submit();
      }
    }
}


function validate() {
    let pass = true;
    let symptoms = document.querySelector("#symptoms");
    let location = document.querySelector("#location");
    let department = document.querySelector("#department");
    let dms = document.querySelector("#dms");
    let patent = document.querySelector('input[name="patent"]:checked');
    let patent_other = document.querySelector("#patent_other");
  
    if (!symptoms.value) {
      pass = false;
      Swal.fire({
        icon: "error",
        title: "กรุณาระบุอาการ",
        text: "โปรดระบุอาการ",
      });
      symptoms.style.border = "1px solid red";
    } else if (!location.value) {
        pass = false;
        Swal.fire({
          icon: "error",
          title: "กรุณาระบุสถานที่ต้องการตรวจ",
          text: "โปรดระบุสถานที่ต้องการตรวจ",
        });
        location.style.border = "1px solid red";
    } else if (!department.value) {
      pass = false;
      Swal.fire({
        icon: "error",
        title: "กรุณาเลือกแผนกที่ต้องการตรวจ",
        text: "โปรดเลือกแผนกที่ต้องการตรวจ",
      });
      department.style.border = '1px solid red';
    } 
    if (!pass) {
        return pass;  
    }
    if (!patent) {
        pass = false;
        Swal.fire({
            icon: "error",
            title: "ไม่ได้ระบุสิทธิการรักษา",
            text: "โปรดระบุสิทธิการรักษา",
        });
    } else if (patent.value === "อื่นๆ" && !patent_other.value) {
        pass = false;
        Swal.fire({
            icon: "error",
            title: "ไม่ได้ระบุสิทธิการรักษาอื่นๆ",
            text: "โปรดระบุสิทธิการรักษาอื่นๆ",
        });
        patent_other.style.border = '1px solid red';  
    }
    if (!pass) {
        return pass;
    }

    return pass;
}
  
