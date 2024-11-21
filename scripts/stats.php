<?php
// Include the database connection
require_once '../config/db.php';  // Ensure this path is correct

// Set the content type to JSON
header('Content-Type: application/json');

// Start the session and check if the user is a job provider
session_start();
if (!isset($_SESSION['AUTHENTICATED']) || $_SESSION['role'] != 'job_provider') {
    echo json_encode(['error' => 'You must be logged in as a job provider to view stats.']);
    exit();
}

// Get the job provider's user ID from session (assuming user_id is stored in session)
$job_provider_id = $_SESSION['user_id'];

// Initialize an array to hold the stats
$stats = [];

// Fetch the count of jobs by status (pending, approved, rejected)
$sql_jobs_status_count = "
    SELECT job_status, COUNT(*) AS count
    FROM all_jobs
    WHERE job_provider_id = ?
    GROUP BY job_status";
$stmt_jobs_status_count = $conn->prepare($sql_jobs_status_count);
if ($stmt_jobs_status_count === false) {
    echo json_encode(['error' => 'Error preparing jobs status count query: ' . $conn->error]);
    exit();
}
$stmt_jobs_status_count->bind_param("i", $job_provider_id);
$stmt_jobs_status_count->execute();
$result_jobs_status_count = $stmt_jobs_status_count->get_result();

$job_status_count = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
while ($row = $result_jobs_status_count->fetch_assoc()) {
    $job_status_count[$row['job_status']] = $row['count'];
}
$stats['jobs'] = $job_status_count;  // Store the job status count in the stats array

// Fetch the count of applications by status (pending, approved, rejected)
$sql_applications_status_count = "
    SELECT applications.status AS application_status, COUNT(*) AS count
    FROM applications
    JOIN all_jobs ON applications.job_id = all_jobs.id
    WHERE all_jobs.job_provider_id = ?
    GROUP BY applications.status";
$stmt_applications_status_count = $conn->prepare($sql_applications_status_count);
if ($stmt_applications_status_count === false) {
    echo json_encode(['error' => 'Error preparing applications status count query: ' . $conn->error]);
    exit();
}
$stmt_applications_status_count->bind_param("i", $job_provider_id);
$stmt_applications_status_count->execute();
$result_applications_status_count = $stmt_applications_status_count->get_result();

$application_status_count = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
while ($row = $result_applications_status_count->fetch_assoc()) {
    $application_status_count[$row['application_status']] = $row['count'];
}
$stats['applications'] = $application_status_count;  // Store the application status count in the stats array

// Close the statements (but not the connection yet)
$stmt_jobs_status_count->close();
$stmt_applications_status_count->close();

// Finally, close the connection after all operations
$conn->close();

// Output the stats as a JSON response
echo json_encode($stats);
?>
