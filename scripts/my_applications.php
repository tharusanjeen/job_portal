<?php

session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

include("./../config/db.php");

header("Content-Type: application/json");

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

if (isset($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];

    // Modified query to join applications with all_jobs table
    $sql = "SELECT applications.*, all_jobs.* 
            FROM applications
            INNER JOIN all_jobs ON applications.job_id = all_jobs.id
            WHERE applications.user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $applications = array();
        while ($row = $result->fetch_assoc()) {
            $applications[] = $row;
        }
        echo json_encode(array(
            "status" => "success",
            "applications" => $applications
        ), JSON_PRETTY_PRINT);
    } else {
        echo json_encode(array(
            "status" => "no_applications"
        ));
    }

    $stmt->close();
} else {
    echo json_encode(array(
        "status" => "unauthorized"
    ));
}

$conn->close();

?>
