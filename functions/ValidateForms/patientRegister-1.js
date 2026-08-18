function submitFirstForm() {
    if (validate()) {
      let form = document.getElementById("register-state-1");
  
      if (validate) {
        form.submit();
      }
    }
  }

function calculateCheckDigit(citizen_id) {
    let sum = 0;
    for (let i = 0; i < 12; i++) {
      sum += parseInt(citizen_id[i]) * (13 - i);
    }
    let checkDigit = (11 - (sum % 11)) % 10;
    return checkDigit;
}

function validate() {
    let pass = true;
    let cid = document.querySelector("#cid");
    let age = document.querySelector("#age");
    let bday_day = document.querySelector("#bday_day");
    let bday_month = document.querySelector("#bday_month");
    let bday_year = document.querySelector("#bday_year");
    let prefix = document.querySelector("#prefix");
    let prefixOther = document.querySelector("#prefix_other");
    let gender = document.querySelector("#gender");
    let firstName = document.querySelector("#firstName");
    let fullName = document.querySelector("#fullName");
    let lastName = document.querySelector("#lastName");
    let phoneNumber = document.querySelector("#phoneNumber");
    let maritalStatus = document.querySelector('input[name="marital_status"]:checked');
    let job = document.querySelector("#job");
    
    let houseNumber = document.querySelector("#houseNumber");
    let village = document.querySelector("#village");
    let no = document.querySelector("#no");
    let building = document.querySelector("#building");
    let floor = document.querySelector("#floor");
    let road = document.querySelector("#road");
    let alley = document.querySelector("#alley");
    let subdistrict = document.querySelector("#subdistrict");
    let district = document.querySelector("#district");
    let province = document.querySelector("#province");
    let zipcode = document.querySelector("#zipcode");
  
    if (cid.value.length !== 13) {
      pass = false;
      Swal.fire({
        icon: "error",
        title: "รหัสบัตรประชาชนไม่ถูกต้อง",
        text: "โปรดกรอกรหัสบัตรประชาชนให้ถูกต้อง",
      });
      cid.style.border = "1px solid red";
    } else {
      const inputCheckDigit = parseInt(cid.value[12]);
      const calculatedCheckDigit = calculateCheckDigit(cid.value);
      if (inputCheckDigit !== calculatedCheckDigit) {
        pass = false;
        Swal.fire({
          icon: "error",
          title: "รหัสบัตรประชาชนไม่ถูกต้อง",
          text: "โปรดกรอกรหัสบัตรประชาชนให้ถูกต้อง",
        });
        cid.style.border = "1px solid red";
      }
    }
    if (!pass) {
      return pass;
    } else if (!age.value || parseInt(age.value) <= 0 || parseInt(age.value) >= 120) {
        pass = false;
        Swal.fire({
          icon: "error",
          title: "กรุณาระบุอายุ",
          text: "โปรดระบุอายุให้ถูกต้อง",
        });
        age.style.border = "1px solid red";
    } else if (!bday_day.value) {
      pass = false;
      Swal.fire({
        icon: "error",
        title: "กรุณาเลือกวันเกิด",
        text: "โปรดเลือกวันเกิดให้ถูกต้อง",
      });
    } else if (!bday_month.value) {
      pass = false;
      Swal.fire({
        icon: "error",
        title: "กรุณาเลือกเดือนเกิด",
        text: "โปรดเลือกเดือนให้ถูกต้อง",
      });
    } else if (!bday_year.value) {
      pass = false;
      Swal.fire({
        icon: "error",
        title: "กรุณาเลือกปีเกิด",
        text: "โปรดเลือกปีให้ถูกต้อง",
      });
    }
    if (!pass) {
      return pass;
    }
    if (!prefix.value) {
      pass = false;
      Swal.fire({
        icon: "error",
        title: "ไม่ได้เลือกคำนำหน้า",
        text: "โปรดเลือกคำนำหน้า",
      });
      prefix.style.border = "1px solid red";
    } else if (prefix.value === "อื่นๆ" && !prefixOther.value) {
      pass = false;
      Swal.fire({
        icon: "error",
        title: "ไม่ได้ระบุคำนำหน้า",
        text: "โปรดระบุคำนำหน้า",
      });
      prefixOther.style.border = "1px solid red";
    } else if (prefix.value === "อื่นๆ" && !gender.value) {
      pass = false;
      Swal.fire({
        icon: "error",
        title: "ไม่ได้ระบุเพศ",
        text: "โปรดระบุเพศ",
      });
      gender.style.border = "1px solid red";
    } else if (!firstName.value) {
        pass = false;
        Swal.fire({
          icon: "error",
          title: "ไม่ได้กรอกชื่อ",
          text: "โปรดกรอกชื่อให้ถูกต้อง",
        });
        firstName.style.border = "1px solid red";
    } else if (!lastName.value) {
        pass = false;
        Swal.fire({
          icon: "error",
          title: "ไม่ได้กรอกนามสกุล",
          text: "โปรดกรอกนามสกุลให้ถูกต้อง",
        });
        lastName.style.border = "1px solid red";
    } else if (phoneNumber.value.length !== 10 || !/^\d+$/.test(phoneNumber.value)) {
        pass = false;
        Swal.fire({
          icon: "error",
          title: "เบอร์โทรศัพท์ไม่ถูกต้อง",
          text: "โปรดกรอกเบอร์โทรศัพท์ให้ถูกต้อง",
        });
        phoneNumber.style.border = "1px solid red";
    } else if (!maritalStatus) {
      pass = false;
      Swal.fire({
        icon: "error",
        title: "กรุณาเลือกสถานะภาพ",
        text: "โปรดเลือกสถานะภาพทางการสมรส",
      });
    } else if (!job.value) {
        pass = false;
        Swal.fire({
          icon: "error",
          title: "ไม่ได้กรอกอาชีพ",
          text: "โปรดกรอกอาชีพให้ถูกต้อง",
        });
        job.style.border = "1px solid red";
    }
    if (!pass) {
        return pass;
    }

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


    return pass;
}
 