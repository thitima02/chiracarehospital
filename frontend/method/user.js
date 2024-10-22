// ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
if (!localStorage.getItem('token')) {
    // ถ้ายังไม่ล็อกอิน ให้เปลี่ยนเส้นทางไปยังหน้า login
    window.location.href = "../login.html";
} else {
    // ดึงข้อมูลผู้ใช้จาก localStorage
    const user = JSON.parse(localStorage.getItem('user'));

    // // แสดงค่าใน console เพื่อดูว่าเก็บข้อมูลอะไรบ้าง
    // console.log('User data from localStorage:', user);

    if (user) {
        // แสดงชื่อผู้ใช้
        document.getElementById('department').textContent = user.department;

        // แสดงพื้นที่รับผิดชอบ
        document.getElementById('full_name').textContent = user.full_name;

        // ตรวจสอบว่ามีรูปภาพผู้ใช้หรือไม่
        if (user.user_image) {
            document.getElementById('profile-image').src = `data:image/jpeg;base64,${user.user_image}`;
        } else {
            // ถ้าไม่มีรูปภาพ ให้ใช้รูปภาพเริ่มต้น
            document.getElementById('profile-image').src = '../../assets/images/cat.jpg'; // รูปภาพเริ่มต้น
        }
    } else {
        console.error('User data is missing from localStorage');
    }
}

// ฟังก์ชันสำหรับไปยังหน้าโปรไฟล์
function goToProfilePage() {
    window.location.href = '../profile.html';
}
