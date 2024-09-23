<?php

    session_start();

    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    if(isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
        $user_role = $_SESSION["role"];
        $user_email = $_SESSION["email_address"];

        include('./../config/db.php');

        // Starting a transaction to ensure that all operations succeed or fail together

        $conn -> begin_transaction();

        try {

            if($user_role === 'job_provider') {
                $sql_delete_jobs = "DELETE FROM all_jobs WHERE job_provider_email = ?";
                $stmt_delete_jobs = $conn -> prepare($sql_delete_jobs);
                $stmt_delete_jobs -> bind_param("s", $user_email);
                $stmt_delete_jobs -> execute();
                $stmt_delete_jobs -> close();
            }

            $sql_delete_account = "DELETE FROM users WHERE id = ?";
            $stmt_delete_account = $conn -> prepare($sql_delete_account);
            $stmt_delete_account -> bind_param("i", $user_id);

            if($stmt_delete_account -> execute()) {

                // Commit the transaction
                $conn -> commit();

                // Unset session and destroy it
                session_unset();
                session_destroy();

                header("Location: ./../index.php");
                exit();

            }
            else {
                $conn -> rollback();
                echo "Error deleting account.";
            }


        } catch (Exception $err) {
            // Rollback the transaction in case of any error
            $conn -> rollback();
            echo "Error: ". $err -> getMessage();
        }

        $conn -> close();
    }

?>