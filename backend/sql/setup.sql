CREATE DATABASE IF NOT EXISTS chiracare_patient_data;


USE chiracare_patient_data;

CREATE TABLE IF NOT EXISTS patients (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    patient_type VARCHAR(100) NOT NULL,  -- ประเภทของผู้ป่วย เช่น ประชาชน, กำลังพล, ครอบครัว
    total INT(11) NOT NULL               -- จำนวนผู้ป่วยในแต่ละประเภท
);
INSERT INTO patients (patient_type, total) VALUES
('ประชาชน', 120),
('กำลังพล', 80),
('ครอบครัว', 50);

