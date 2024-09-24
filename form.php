<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/topbar.css">
  <link rel="stylesheet" href="css/logo.css">
  <link rel="stylesheet" href="css/button.css">
  <link rel="stylesheet" href="css/form.css">
  <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="background-color:#edeff1;">

  <!-- Sidebar will be loaded here -->
  <div id="sidebar-container"></div>

  <!-- Topbar will be loaded here -->
  <div id="topbar-container"></div>

  <div class="form-section">
    <form action="submit.php" method="post">
      <!-- Topic -->
      <divc class="Topic">
        <p>ข้อมูลการติดตาม</p>
      </divc>

      <form>
        <label for="name">ชื่อ – นามสกุล</label>
        <input type="text" id="name" placeholder="นายปลาร้า ไม่เหม็น">

        <label for="disease">โรคของผู้ป่วยที่ติดตาม</label>
        <select id="disease">
          <option value="">เบาหวาน</option>
          <option value="โรคหัวใจ">โรคหัวใจ</option>
          <option value="มะเร็ง">มะเร็ง</option>
        </select>

        <label for="symptoms">อาการทั่วไป</label>
        <input type="text" id="symptoms" placeholder="กรุณากรอกอาการทั่วไป">

        <label for="treatment-plan">ค่าระดับน้ำตาลในเลือด</label>
        <input type="text" id="Blood-sugar" placeholder="70">

        <label for="treatment-plan">ค่าสัญญาณชีพ</label>
        <input type="text" id="vital-signs" placeholder="130">

        <label for="disease">สาเหตุที่ไม่มารับการรักษา</label>
        <select id="not-receiving-treatment">
          <option value="">กรุณาเลือกสาเหตุ</option>
          <option value="ลืมนัด/จำวันนัดผิด">ลืมนัด/จำวันนัดผิด</option>
          <option value="ใบนัดหาย">ใบนัดหาย</option>
          <option value="ตั้งใจมาหลังนัด">ตั้งใจมาหลังนัด</option>
          <option value="ยาเหลือ">ยาเหลือ</option>
          <option value="มีปัญหาด้านการเดินทาง">มีปัญหาด้านการเดินทาง</option>
          <option value="ติดธุระจำเป็นอื่นๆ">ติดธุระจำเป็นอื่นๆ</option>
          <option value="ไม่มีอาการผิดปกติ ">ไม่มีอาการผิดปกติ </option>
        </select>

        <label for="Date ">วันที่กรอกฟอร์ม</label>
        <input type="date" class="date-filter">
      </form>
      <div id="popup-card" class="popup hidden">
        <div class="popup-content">
          <h2>คุณแน่ใจหรือไม่ว่าจะส่งฟอร์มนี้?</h2>
          <p>กรุณาตรวจสอบข้อมูลก่อนทำการยืนยันข้อมูล</p>
          <div class="popup-buttons">
            <button id="confirm-btn" class="btn confirm-btn">ยืนยันการส่ง</button>
            <button id="cancel-btn" class="btn cancel-btn">ยกเลิก</button>
          </div>
        </div>
      </div>
      <!-- Submit Button -->
      <div class="button-group">
        <!-- <button type="submit">บันทึก</button> -->
        <button type="back" id="submit-btn">ยืนยัน</button>
      </div>



  </div>
  </form>
  </div>

  <script>
    // Load sidebar
    fetch('sidebar.html')
      .then(response => response.text())
      .then(data => {
        document.getElementById('sidebar-container').innerHTML = data;
        loadSidebarScript(); // Initialize sidebar toggle after loading
      });

    // Load topbar and set the page title
    fetch('topbar.html')
      .then(response => response.text())
      .then(data => {
        document.getElementById('topbar-container').innerHTML = data;
        document.getElementById('page-title').textContent = 'การแจ้งเตือน'; // Set page title for the main page
      });

    function loadSidebarScript() {
      let sidebar = document.querySelector(".sidebar");
      let closeBtn = document.querySelector("#btn");

      closeBtn.addEventListener("click", () => {
        sidebar.classList.toggle("open");
        menuBtnChange();
      });

      function menuBtnChange() {
        if (sidebar.classList.contains("open")) {
          closeBtn.classList.replace("bx-menu", "bx-menu-alt-right");
        } else {
          closeBtn.classList.replace("bx-menu-alt-right", "bx-menu");
        }
      }

      // เมื่อกดปุ่มยืนยันแล้วจะมี pop up เด้งขึ้น ถามความชัวร์ว่า ยืนยันการส่งฟอร์ม จะมีปุ่ม แก้ไข กับ ยืนยันส่งฟอร์ม
      document.getElementById('submit-btn').addEventListener('click', function () {
        document.getElementById('popup-card').classList.remove('hidden');
      });

      document.getElementById('confirm-btn').addEventListener('click', function () {
        // alert('ฟอร์มถูกส่งแล้ว!');
        document.getElementById('popup-card').classList.add('hidden');
        window.location.href = 'index.php';
      });

      document.getElementById('cancel-btn').addEventListener('click', function () {
        document.getElementById('popup-card').classList.add('hidden');
      });

      document.getElementById('confirm-btn').addEventListener('click', function () {
        // สร้างองค์ประกอบ <table>
        var table = document.createElement('table');

        // สร้างแถวหัวตาราง
        var thead = document.createElement('thead');
        var headerRow = document.createElement('tr');

        headers.forEach(function (headerText) {
          var th = document.createElement('th');
          th.textContent = headerText;
          headerRow.appendChild(th);
        });

        thead.appendChild(headerRow);
        table.appendChild(thead);

        // สร้างแถวข้อมูล
        var tbody = document.createElement('tbody');
        for (var i = 1; i <= 3; i++) { // สร้าง 3 แถว
          var row = document.createElement('tr');
          for (var j = 1; j <= 3; j++) { // สร้าง 3 เซลล์ในแต่ละแถว
            var td = document.createElement('td');
            td.textContent = 'Cell ' + i + '-' + j;
            row.appendChild(td);
          }
          tbody.appendChild(row);
        }

        table.appendChild(tbody);

        // เพิ่มตารางลงใน <div id="table-container">
        var container = document.getElementById('table-container');
        container.innerHTML = ''; // ล้างเนื้อหาก่อนเพิ่มตารางใหม่
        container.appendChild(table);
      });


    }
  </script>
</body>

</html>