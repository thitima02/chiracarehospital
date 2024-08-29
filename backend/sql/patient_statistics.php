<?php
// Database configuration
$host = 'localhost';
$dbname = 'chiracare_follow_up_db'; // Use the actual database name
$username = 'root'; // Default XAMPP username, change if needed
$password = ''; // Default XAMPP password, change if needed

try {
    // Create a new PDO instance
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL to create the patient_statistics table
    $createTableSql = "CREATE TABLE IF NOT EXISTS patient_statistics (
        id INT AUTO_INCREMENT PRIMARY KEY,
        year YEAR NOT NULL,
        patient_type VARCHAR(100) NOT NULL,
        total INT NOT NULL
    )";

    // Execute the SQL to create the table
    $conn->exec($createTableSql);
    echo "Table patient_statistics created successfully.<br>";

    // Insert data into the patient_statistics table
    $insertDataSql = "INSERT INTO patient_statistics (year, patient_type, total) VALUES
        (2566, 'ประชาชน', 20),
        (2566, 'กำลังพล', 10),
        (2566, 'ครอบครัว', -10),
        (2567, 'ประชาชน', 30),
        (2567, 'กำลังพล', 40),
        (2567, 'ครอบครัว', -50),
        (2568, 'ประชาชน', 60),
        (2568, 'กำลังพล', 20),
        (2568, 'ครอบครัว', -30),
        (2569, 'ประชาชน', 50),
        (2569, 'กำลังพล', 30),
        (2569, 'ครอบครัว', -40),
        (2570, 'ประชาชน', 40),
        (2570, 'กำลังพล', 50),
        (2570, 'ครอบครัว', -60),
        (2571, 'ประชาชน', 30),
        (2571, 'กำลังพล', 20),
        (2571, 'ครอบครัว', -70)";

    // Execute the SQL to insert the data
    $conn->exec($insertDataSql);
    echo "Data inserted into patient_statistics table successfully.<br>";
    
} catch(PDOException $e) {
    // Catch any errors
    echo "Error: " . $e->getMessage();
}

// Close the database connection
$conn = null;
?>
