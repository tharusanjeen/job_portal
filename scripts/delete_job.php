<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');


    header('Content-Type: application/json');

    // Database
    include("./../config/db.php");

    $data = json_decode(file_get_contents('php://input'), true);

    if(isset($data["id"])) {
        $jobId = $data["id"];

        // Prepare the SQL DELETE statement
        $stmt = $conn -> prepare("DELETE FROM all_jobs WHERE id = ?");
        $stmt -> bind_param("i", $jobId);

        if($stmt -> execute()) {
            echo json_encode(["success" => true]);
        }
        else {
            echo json_encode(["success" => false, "error" => $stmt -> error]);
        }
        $stmt -> close();
    }
    else {
        echo json_encode(["success" => false, "error" => "Invalid input"]);
    }

    $conn -> close();

?>