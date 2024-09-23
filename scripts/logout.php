<?php

    session_start();

    include("./../config/db.php");

    // Ensure user is logged in
    if(!isset($_SESSION["email_address"])) {
        header("Location: ./../index.php");
        exit();
    }

    // Remove auth_token from database
    $email_address = $_SESSION["email_address"];
    $stmt = $conn->prepare("UPDATE users SET auth_token = NULL WHERE email_address = ?");
    $stmt->bind_param("s", $email_address);
    $stmt->execute();
    $stmt->close();


    // Destroy the session;
    session_unset();
    session_destroy();

    // Remove the cookie by setting its expiration time to the past
    setcookie('auth', '', time() - 3600, '/', '', false, true);

    // Redirect to login page
    header('Location: ./../index.php?log_out=success');
    exit();
?>