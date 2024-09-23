<?php

session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

include("./../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    if (isset($_SESSION["email_address"])) {
        $userEmail = $_SESSION["email_address"];
        $currentPw = md5($_POST["current_password"]);

        $sql = "SELECT password FROM users WHERE email_address = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if ($row["password"] === $currentPw) {
                $newPw = $_POST["new_password"];
                $confirmPw = $_POST["confirm_new_password"];

                if ($newPw !== $confirmPw) {
                    $role = $_SESSION["role"];

                    if($role == 'job_provider') {
                        header("Location: ./../role_provider/pages/profile.php?newcurrent=false");
                        exit();
                    }
                    else {
                        header("Location: ./../role_seeker/pages/profile.php?newcurrent=false");
                        exit();
                    }
                } else {
                    $hasedNewPw = md5($newPw);

                    $updateSql = "UPDATE users SET password = ? WHERE email_address = ?";
                    $updateStmt = $conn -> prepare($updateSql);
                    $updateStmt -> bind_param('ss', $hasedNewPw, $userEmail);

                    if ( $updateStmt -> execute()) {
                        $role = $_SESSION["role"];

                        if($role == 'job_provider') {
                            header("Location: ./../role_provider/pages/profile.php?password=updated");
                            exit();
                        }
                        else {
                            header("Location: ./../role_seeker/pages/profile.php?password=updated");
                            exit();
                        }
                    }
                    else {
                        echo "<div style='height: 100vh; width: 100vh; display: grid; place-content: center;'>";
                        echo "Failed to update password. Please try again.";
                        echo "</div>";
                    }
                }
            } else {
                $role = $_SESSION["role"];

                if($role == 'job_provider') {
                    header("Location: ./../role_provider/pages/profile.php?currentpassword=wrong");
                    exit();
                }
                else {
                    header("Location: ./../role_seeker/pages/profile.php?currentpassword=wrong");
                    exit();
                }
            }
        } else {
            echo "nothing";
        }
    }
    else {
        echo "User session not found! Please log in again.";
    }
}
