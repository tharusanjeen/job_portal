<?php

session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

include("./../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    if(isset($_COOKIE["auth"])) {
        $auth_token = $_COOKIE["auth"];
    
        $sql = "SELECT * FROM users WHERE auth_token = ?";
        $stmt = $conn -> prepare($sql);
        $stmt -> bind_param("s", $auth_token);
        $stmt -> execute();
        $result = $stmt -> get_result();
    
        if($result -> num_rows > 0) {
            $row = $result -> fetch_assoc();
            $_SESSION["user_id"] = $row["id"];
        }
    }
    $job_provider_id = $_SESSION["user_id"];
    $job_providers_email = $_SESSION["email_address"];

    $job_title = strtolower($_POST["job_title"]);
    $company_name = strtolower($_POST["company_name"]);
    $job_location = strtolower($_POST["job_location"]);
    $contact_email = strtolower($_POST["contact_email"]);
    $job_type = $_POST["job_type"];
    $employment_type = $_POST["employment_type"];
    $working_hours = $_POST["working_hours"];
    $job_status = $_POST["job_status"];
    $deadline = $_POST["deadline"];

    $stmt = $conn->prepare("INSERT INTO all_jobs (job_provider_id, job_provider_email, job_title, company_name, job_location, contact_email, job_type, employment_type, working_hours, job_status, deadline) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssssss", $job_provider_id, $job_providers_email, $job_title, $company_name, $job_location, $contact_email, $job_type, $employment_type, $working_hours, $job_status, $deadline);

    
    if($stmt -> execute()) {
        header("location: ./../role_provider/pages/add_job.php?job_added=success");
        exit();
    }

    $stmt->close();

}

?>