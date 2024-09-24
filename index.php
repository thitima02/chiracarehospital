<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/topbar.css">
  <link rel="stylesheet" href="css/menubar.css">
  <link rel="stylesheet" href="css/logo.css">
  <link rel="stylesheet" href="css/mainpage/datatable.css">
  <link rel="stylesheet" href="css/mainpage/result.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Import icon -->
  <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

  <!-- Sidebar will be loaded here -->
  <div id="sidebar-container"></div>

  <!-- Topbar will be loaded here -->
  <div id="topbar-container"></div>

  <!-- the resule of many patient -->
  <container>
    <div class="stats-container">
      <div class="box new-order">
        <i class='bx bx-user'></i>
        <div>
          <h2>10</h2>
          <p>จำนวนผู้ป่วยที่มีนัดวันนี้</p>
        </div>
      </div>
      <div class="box box2 visitors">
        <i class='bx bx-smile'></i>
        <div>
          <h2>2834/5000</h2>
          <p>ติดตามเสร็จสิ้น</p>
        </div>
      </div>
      <div class="box box3 total-sales">
        <i class='bx bx-tired'></i>
        <div>
          <h2>2543/5000</h2>
          <p>ยังไม่ได้ติดตาม</p>
        </div>
      </div>
    </div>
  </container>

  <!-- the dropdrown  -->
  <div class="filter-container">
    <div class="filter-item search-bar">
      <h5>ค้นหารายชื่อ</h5>
      <input type="text" placeholder="ค้นหาที่นี่..." class="search-input">
      <i class='bx bx-search search-icon'></i> <!-- Add your icon here -->
    </div>
    <div class="filter-item">
      <h5>วันที่อัปเดตการติดตาม</h5>
      <input type="date" class="date-filter">
    </div>
    <div class="filter-item">
      <h5>ครั้งที่ติดตาม</h5>
      <select class="dropdown-filter">
        <option value="ทั้งหมด">ทั้งหมด</option>
        <option value="">1</option>
        <option value="">2</option>
        <option value="">3</option>
        <!-- Add other options as needed -->
      </select>
      <i class='bx bx-chevron-down dropdown-icon'></i>
    </div>


  </div>

  <!-- the datatable  -->
  <div class="table-container">
    <table class="patient-table">
      <thead>
        <tr>
          <th>รายชื่อผู้ป่วย</th>
          <th>วันที่เริ่มติดตาม</th>
          <th>วันที่สิ้นสุด</th>
          <th>ที่อยู่</th>
          <th>ครั้งที่ติดตาม</th>
          <th></th>
        </tr>
      </thead>
      <?php
        // รวมโค้ด PHP ที่ดึงข้อมูลจาก MySQL
        include 'fetch_patient_address.php'; // ไฟล์นี้คือไฟล์ที่เราเขียนโค้ด PHP ไว้เพื่อดึงข้อมูล
        ?>
      </tbody>

    </table>
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
        document.getElementById('page-title').textContent = 'หน้าหลัก'; // Set page title for the main page
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
      document.getElementById('add-button').addEventListener('click', function () {
        window.location.href = 'form.php';
      });
    }
  </script>
</body>

</html>