<?php
// reject_application.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the database configuration file to use the existing connection
include("./../config/db.php");

// Get the raw POST data (JSON)
$data = json_decode(file_get_contents('php://input'), true);

// Validate the input
if (!isset($data['application_id'])) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$application_id = $data['application_id'];
$status = 'rejected'; // Set status to 'rejected'

// Prepare the update query
$query = "UPDATE applications SET status = ? WHERE id = ?";
$stmt = $conn->prepare($query);

// Bind the parameters to the query
$stmt->bind_param('si', $status, $application_id);

// Execute the query and check if the update was successful
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to reject application']);
}

// Close the statement
$stmt->close();
?>
