<?php
session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

include("./../config/db.php");

$response = [];

try {
    // Check if the auth cookie is set
    if (isset($_COOKIE["auth"])) {
        $auth_token = $_COOKIE["auth"];

        // Prepare and execute the query to get user info based on auth token
        $sql = "SELECT * FROM users WHERE auth_token = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }
        $stmt->bind_param("s", $auth_token);
        $stmt->execute();
        $result = $stmt->get_result();

        // If a user is found, store their ID in the session
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION["job_provider_id"] = $row["id"];
        }
    }

    // Check if the job provider ID is set in the session
    if (isset($_SESSION["job_provider_id"])) {
        $job_provider_id = $_SESSION["job_provider_id"];
        
        // Query to fetch applications along with job details
        $sql_application = "
            SELECT a.*, j.job_provider_email, j.job_title, j.company_name, j.job_location, 
                   j.contact_email, j.job_type, j.employment_type, j.working_hours, 
                   j.job_status, j.deadline, j.created_at 
            FROM applications a 
            JOIN all_jobs j ON a.job_id = j.id 
            WHERE a.job_provider_id = ?";
        
        $stmt_application = $conn->prepare($sql_application);
        if (!$stmt_application) {
            throw new Exception("Database error: " . $conn->error);
        }
        $stmt_application->bind_param("i", $job_provider_id);
        $stmt_application->execute();
        $result_application = $stmt_application->get_result();

        // Check if applications are found
        if ($result_application->num_rows > 0) {
            while ($row = $result_application->fetch_assoc()) {
                // Combine application and job details
                $response[] = [
                    'application' => [
                        'id' => $row['id'],
                        'user_id' => $row['user_id'],
                        'job_id' => $row['job_id'],
                        'certificate_url' => $row['certificate_url'],
                        'qualification' => $row['qualification'],
                        'applied_at' => $row['applied_at'],
                        'status' => $row['status']
                    ],
                    'job' => [
                        'id' => $row['id'], // Job ID from all_jobs
                        'job_provider_id' => $row['job_provider_id'],
                        'job_provider_email' => $row['job_provider_email'],
                        'job_title' => $row['job_title'],
                        'company_name' => $row['company_name'],
                        'job_location' => $row['job_location'],
                        'contact_email' => $row['contact_email'],
                        'job_type' => $row['job_type'],
                        'employment_type' => $row['employment_type'],
                        'working_hours' => $row['working_hours'],
                        'job_status' => $row['job_status'],
                        'deadline' => $row['deadline'],
                        'created_at' => $row['created_at']
                    ]
                ];
            }
        } else {
            $response = ["message" => "No applications found for this job provider."];
        }
    } else {
        $response = ["message" => "User not authenticated or job provider ID not set."];
    }
} catch (Exception $e) {
    $response = ["error" => $e->getMessage()];
}

// Set the content type to application/json
header('Content-Type: application/json');

// Return the response as JSON
echo json_encode($response, JSON_PRETTY_PRINT);

// Close the prepared statements and connection
$stmt->close();
$stmt_application->close();
$conn->close();
?>
