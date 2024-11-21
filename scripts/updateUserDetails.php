<?php
    session_start();

    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    include("./../config/db.php");

    if($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_SESSION["user_id"])) {
        $user_id = $_SESSION["user_id"];
        $new_full_name = strtolower($_POST["new_full_name"]);
        $new_email_address = strtolower($_POST["new_email_address"]);

        $sql_update_info = "UPDATE users SET full_name = ?, email_address = ? WHERE id = ?";
        $stmt_update_info = $conn -> prepare($sql_update_info);
        $stmt_update_info -> bind_param('ssi', $new_full_name, $new_email_address, $user_id);

        if($stmt_update_info -> execute()) {
            $user_role = $_SESSION["role"];
            $_SESSION["full_name"] = $new_full_name;
            $_SESSION["email_address"] = $new_email_address;
            
            if($user_role === "job_provider") {
                header("Location: ./../role_provider/pages/profile.php?information=updated");
                exit();
            }
            else {
                header("Location: ./../role_seeker/pages/profile.php?information=updated");
                exit();
            }

        }
        else {
            echo "Error updating user information: ". $stmt_update_info -> error;
        }

        $stmt_update_info -> close();
        $conn -> close();

    }
?>