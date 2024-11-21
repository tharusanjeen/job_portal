<?php

session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

include("./../config/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $user_id = filter_var($_POST["user_id"], FILTER_SANITIZE_NUMBER_INT);
    $job_id = filter_var($_POST["job_id"], FILTER_SANITIZE_NUMBER_INT);
    $job_provider_id = filter_var($_POST["job_provider_id"], FILTER_SANITIZE_NUMBER_INT);
    $qualification = strtolower(htmlspecialchars(trim($_POST["qualification"])));
    $certificate_url = filter_var(trim($_POST["certificate_link"]), FILTER_SANITIZE_URL);

    // Validate required fields
    if (empty($qualification) || empty($certificate_url)) {
        echo "Qualification and certificate link are required.";
        exit();
    }

    // Check if the user has already applied for this job
    $checkSql = "SELECT COUNT(*) FROM applications WHERE user_id = ? AND job_id = ?";
    if ($conn) {
        if ($checkStmt = $conn->prepare($checkSql)) {
            $checkStmt->bind_param("ii", $user_id, $job_id);
            $checkStmt->execute();
            $checkStmt->bind_result($count);
            $checkStmt->fetch();
            $checkStmt->close();

            if ($count > 0) {
                echo "<div style='height: 100vh; width: 100vw; display: grid; place-content: center; letter-spacing: 1px;'>";
                echo "<p style='font-size: 1.3rem; display: flex; flex-direction: column; gap: 1rem; align-items: center;'>";
                echo "You have already applied for this job.";
                echo "<a style='text-decoration: none; text-transform: uppercase; font-size: 1rem;' href='./../role_seeker/pages/home.php'>return to home</a>";
                echo "</p>";
                echo "</div>";
                exit();
            }
        }

        // Prepare the insert statement if the user has not applied
        $sql = "INSERT INTO applications (user_id, job_id, job_provider_id, qualification, certificate_url, applied_at) VALUES (?, ?, ?, ?, ?, NOW())";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("iiiss", $user_id, $job_id, $job_provider_id, $qualification, $certificate_url);

            if ($stmt->execute()) {
                echo "<div style='height: 100vh; width: 100vw; display: grid; place-content: center; letter-spacing: 1px;'>";
                echo "<p style='font-size: 1.3rem; display: flex; flex-direction: column; gap: 1rem; align-items: center;'>";
                echo "Application submitted successfully.";
                echo "<a style='text-decoration: none; text-transform: uppercase; font-size: 1rem;' href='./../role_seeker/pages/home.php'>return to home</a>";
                echo "</p>";
                echo "</div>";
            } else {
                error_log("Error executing query: " . $stmt->error);
                echo "An error occurred while submitting your application.";
            }
            $stmt->close();
        } else {
            error_log("Error preparing query: " . $conn->error);
            echo "An error occurred while preparing the application.";
        }
    } else {
        echo "Database connection error.";
    }

    $conn->close();
}
?>
