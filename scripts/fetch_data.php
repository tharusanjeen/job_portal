<?php

    session_start();

    error_reporting(E_ALL);
    ini_set("display_errors",1);

    include("./../config/db.php");
    
    header("Content-Type: application/json");

    if(isset($conn) && $_SESSION["email_address"]) {
        $email_address = $_SESSION["email_address"];

        $sql = "SELECT * FROM all_jobs WHERE job_provider_email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email_address);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = array();
        if($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            
            echo json_encode($data);
        }
        else {
            echo json_encode(array());
        }

    }
    else {
        echo json_encode(array("error" => "No database connection and session available!"));
    }

?>