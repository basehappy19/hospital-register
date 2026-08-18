function submitTertiaryForm() {
    if (validate()) {
      let form = document.getElementById("register-state-3");
  
      if (validate) {
        form.submit();
      }
    }
}

function validate() {
    let pass = true;
    let relationship = document.querySelector("#relationship");
    let relationship_other = document.querySelector("#relationship_other");
    let prefix = document.querySelector("#prefix");
    let prefix_other = document.querySelector("#prefix_other");
    let firstName = document.querySelector("#firstName");
    let lastName = document.querySelector("#lastName");
    let phoneNumber = document.querySelector("#phoneNumber");
    let relativeSameAddress = document.querySelector("#relativeSameAddress");
  
    if (!relationship.value) {
      pass = false;
      Swal.fire({
        icon: "error",
        title: "กรุณาระบุความสัมพันธ์ของญาติ",
        text: "โปรดระบุความสัมพันธ์ของญาติ",
      });
      relationship.style.border = "1px solid red";
    } else if (relationship.value === 'อื่นๆ' && !relationship_other.value) {
        pass = false;
        Swal.fire({
          icon: "error",
          title: "กรุณากรอกความสัมพันธ์ของญาติ",
          text: "โปรดกรอกความสัมพันธ์ของญาติ",
        });
        relationship_other.style.border = "1px solid red";
    } else if (!prefix.value) {
        pass = false;
        Swal.fire({
          icon: "error",
          title: "ไม่ได้เลือกคำนำหน้า",
          text: "โปรดเลือกคำนำหน้า",
        });
      prefix.style.border = "1px solid red";
    } else if (prefix.value === "อื่นๆ" && !prefix_other.value) {
      pass = false;
      Swal.fire({
        icon: "error",
        title: "ไม่ได้ระบุคำนำหน้า",
        text: "โปรดระบุคำนำหน้า",
      });
      prefix_other.style.border = "1px solid red";
    } else if (!firstName.value) {
      pass = false;
      Swal.fire({
        icon: "error",
        title: "กรุณาระบุชื่อญาติ",
        text: "โปรดระบุชื่อญาติ",
      });
      firstName.style.border = '1px solid red';
    } else if (!lastName.value) {
      pass = false;
      Swal.fire({
        icon: "error",
        title: "กรุณาระบุนามสกุลญาติ",
        text: "โปรดระบุนามสกุลญาติ",
      });
      lastName.style.border = '1px solid red';
    } else if (phoneNumber.value.length !== 10 || !/^\d+$/.test(phoneNumber.value)) {
      pass = false;
      Swal.fire({
        icon: "error",
        title: "เบอร์โทรศัพท์ญาติไม่ถูกต้อง",
        text: "โปรดกรอกเบอร์โทรศัพท์ญาติให้ถูกต้อง",
      });
      phoneNumber.style.border = "1px solid red";
    } 
    if (!pass) {
        return pass;  
    }
    
    if(!relativeSameAddress.checked){
      if(!houseNumber.value) {
        pass = false;
        Swal.fire({
            icon: "error",
            title: "กรุณาระบุบ้านเลขที่",
            text: "โปรดกรอกบ้านเลขที่",
        });
        houseNumber.style.border = "1px solid red";
      } else if (!no.value) {
          pass = false;
          Swal.fire({
              icon: "error",
              title: "กรุณาระบุหมู่ที่",
              text: "โปรดกรอกหมู่ที่",
          });
          no.style.border = "1px solid red";
      } else if (!subdistrict.value) {
          pass = false;
          Swal.fire({
              icon: "error",
              title: "กรุณาระบุตำบล",
              text: "โปรดกรอกตำบล",
          });
          subdistrict.style.border = "1px solid red";
      } else if (!district.value) {
          pass = false;
          Swal.fire({
              icon: "error",
              title: "กรุณาระบุอำเภอ",
              text: "โปรดกรอกอำเภอ",
          });
          district.style.border = "1px solid red";
      } else if (!province.value) {
          pass = false;
          Swal.fire({
              icon: "error",
              title: "กรุณาระบุจังหวัด",
              text: "โปรดกรอกจังหวัด",
          });
          province.style.border = "1px solid red";
      } else if (!zipcode.value) {
          pass = false;
          Swal.fire({
              icon: "error",
              title: "กรุณาระบุรหัสไปรษณีย์",
              text: "โปรดกรอกรหัสไปรษณีย์",
          });
          zipcode.style.border = "1px solid red";
      }
    }
    if (!pass) {
      return pass;  
    }

    return pass;
}
  
