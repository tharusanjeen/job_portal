<?php

// Database connection details
$host = 'localhost';
$dbname = 'job_portal';  // Replace with your actual database name
$user = 'root';
$password = '';  // Adjust your password here if necessary

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to check if a table exists
function tableExists($tableName) {
    global $conn;
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    return $result->num_rows > 0;
}

// Create `users` table if it doesn't exist
if (!tableExists('users')) {
    $create_users_table = "CREATE TABLE users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email_address VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        auth_token VARCHAR(255) NULL,
        role ENUM('job_seeker', 'job_provider') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($create_users_table) === TRUE) {
        echo "Users table created successfully.\n";
    } else {
        echo "Error creating users table: " . $conn->error . "\n";
    }
}

// Create `all_jobs` table if it doesn't exist
if (!tableExists('all_jobs')) {
    $create_all_jobs_table = "CREATE TABLE all_jobs (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        job_provider_id INT(11) NOT NULL,
        job_provider_email VARCHAR(255) NOT NULL,
        job_title VARCHAR(255) NOT NULL,
        company_name VARCHAR(255) NOT NULL,
        job_location VARCHAR(255) NOT NULL,
        contact_email VARCHAR(255) NOT NULL,
        job_type ENUM('full time', 'part time', 'remote', 'internship') NOT NULL,
        employment_type ENUM('permanent', 'temporary', 'freelance') NOT NULL,
        working_hours VARCHAR(50) NOT NULL,
        job_status ENUM('pending', 'approved', 'declined') DEFAULT 'pending' NOT NULL,
        deadline DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    if ($conn->query($create_all_jobs_table) === TRUE) {
        echo "All Jobs table created successfully.\n";
    } else {
        echo "Error creating All Jobs table: " . $conn->error . "\n";
    }
}

// Create `applications` table if it doesn't exist
if (!tableExists('applications')) {
    $create_applications_table = "CREATE TABLE applications (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        job_id INT(11) NOT NULL,
        job_provider_id INT(11) NOT NULL,
        qualification VARCHAR(255) NOT NULL,
        certificate_url VARCHAR(255) NOT NULL,
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (job_id) REFERENCES all_jobs(id),
        FOREIGN KEY (job_provider_id) REFERENCES users(id)
    )";

    if ($conn->query($create_applications_table) === TRUE) {
        echo "Applications table created successfully.\n";
    } else {
        echo "Error creating Applications table: " . $conn->error . "\n";
    }
}

?>
