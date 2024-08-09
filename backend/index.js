const express = require('express');
const mysql = require('mysql2');

const app = express();

// สร้างการเชื่อมต่อกับฐานข้อมูล MySQL
const db = mysql.createConnection({
    host: 'localhost',
    user: '@chirapchiracarerawat.com',
    password: 'ChiraCare1234',
    database: 'ChiraCare_follow_up_db'
});
// ตรวจสอบการเชื่อมต่อ
db.connect((err) => {
    if (err) {
        console.error('การเชื่อมต่อกับฐานข้อมูลมีปัญหา: ' + err.stack);
        return;
    }
    console.log('เชื่อมต่อกับฐานข้อมูลสำเร็จใน thread ID: ' + db.threadId);
});

// เริ่มต้นเซิร์ฟเวอร์
app.listen(3000, () => {
    console.log('เซิร์ฟเวอร์กำลังทำงานบนพอร์ต 3000');
});

app.get('/users', (req, res) => {
    const sql = 'SELECT * FROM users';

    db.query(sql, (err, results) => {
        if (err) {
            return res.status(500).json({ error: err });
        }
        res.json(results);
    });
});

