<?php

   session_start();

   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ini_set('display_startup_errors', 1);
   

   include("./config/db.php");

   if(isset($_SESSION["AUTHENTICATED"]) && $_SESSION["AUTHENTICATED"] == true) {
      if($_SESSION["role"] == "job_provider") {
         header("Location: ./role_provider/pages/dashboard.php");
         exit();
      }
      else {
         header("Location: ./role_seeker/pages/home.php");
         exit();
      }
   }

   if (!isset($_SESSION["AUTHENTICATED"]) || $_SESSION["AUTHENTICATED"] !== true) {
      if(isset($_COOKIE["auth"])) {
         $auth_token = $_COOKIE["auth"];

         $stmt = $conn->prepare("SELECT * FROM users WHERE auth_token = ?");
         $stmt->bind_param("s", $auth_token);
         $stmt->execute();
         $result = $stmt->get_result();

         if($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $_SESSION["AUTHENTICATED"] = true;
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["full_name"] = $row["full_name"];
            $_SESSION["email_address"] = $row["email_address"];
            $_SESSION["role"] = $row["role"];

            if($_SESSION["role"] == "job_provider") {
               header("Location: ./role_provider/pages/dashboard.php");
               exit();
            }
            else {
               header("Location: ./role_seeker/pages/home.php");
               exit();
            }

         }
         else {
            setcookie('auth', '', time() - 3600, '/', '', false, true);
            header('location: ./index.php');
            exit();
         }
      }
      else {
         if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])){
            $email_address = $_POST["email"];
            $password = md5($_POST["password"]);
      
            $result = $conn->query("SELECT * FROM users WHERE email_address = '$email_address' and password = '$password'");
            if($result -> num_rows > 0) {
               session_start();
               $row = $result->fetch_assoc();
      
               
               // Generating a authentication id
               $auth_token = bin2hex(random_bytes(32));
      
               // Updating the user record with the new auth_id
               $stmt = $conn->prepare("UPDATE users SET auth_token = ? WHERE email_address = ?");
               $stmt->bind_param('ss', $auth_token, $email_address);
               $stmt->execute();
               $stmt->close();
               
               $_SESSION["AUTHENTICATED"] = true;
               $_SESSION["user_id"] = $row["id"];
               $_SESSION["full_name"] = $row["full_name"];
               $_SESSION["email_address"] = $row["email_address"];
               $_SESSION["role"] = $row["role"];
      
               // Set a cookie with the authentication ID
               $cookie_expiration = time() + (30 * 24 * 60 * 60);
               setcookie('auth', $auth_token, $cookie_expiration, '/', '', false, true);
               
               if($_SESSION["role"] === 'job_provider') {
                  header("location: ./role_provider/pages/dashboard.php");
                  exit();
               }
               else {
                  header("location: ./role_seeker/pages/home.php");
                  exit();
               }
            }
            else {
               header("location: index.php?password=incorrect");
               exit();
            }
         }
      }
   }


?>

<!-- Login page -->
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="shortcut icon" href="./assets/icons/logo.png" type="image/x-icon">
   <title>Log in - Job Portal</title>
   <link rel="stylesheet" href="./assets/shared-css/index.css">
   <style>
      body {
         letter-spacing: 2px;
         background-color: #EEF4F8;
      }

      .form-container {
         max-width: 25rem;
         background-color: white;
         margin: auto;
         border-radius: 10px;
         padding: 2rem;
         display: flex;
         flex-direction: column;
         gap: 1rem;
         box-shadow: 1px 2px 10px 2px rgba(0, 0, 0, 0.2);
      }

      #logo {
         display: flex;
         align-items: center;
         justify-content: center;
         gap: 0.2rem;
         text-decoration: none;
      }

      #logo-img {
         width: 4rem;
      }

      #logo h1 {
         font-size: 1.4rem;
         text-transform: capitalize;
         color: #102173;
         text-align: center;
      }

      p {
         text-align: center;
         text-transform: uppercase;
         font-size: 1.4rem;
      }

      .form-container img {
         width: 5rem;
      }

      .go-register {
         text-align: center;
      }

      form {
         display: flex;
         flex-direction: column;
         gap: 1rem;
      }

      form div {
         display: flex;
         flex-direction: column;
      }

      label {
         text-transform: capitalize;
         margin-bottom: 0.4rem;
         font-size: 0.9rem;
      }

      input{
         padding: 0.5rem;
         border-radius: 5px;
         border-color: rgba(0, 0, 0, 0.3);
         letter-spacing: 1px;
         background-color: #E7EEFE;
      }


      #submit-btn {
         background-color: #385EF6;
         border: none;
         text-transform: uppercase;
         color: white;
         margin-top: 0.4rem;
         padding: 0.6rem;
         cursor: pointer;
         letter-spacing: 2px;
         transition: box-shadow linear .3s;
      }

      #submit-btn:hover {
         transition: box-shadow linear .3s;
         box-shadow: 1px 2px 15px 4px rgba(0, 0, 0, 0.3);
      }

      .go-register a {
         text-decoration: none;
         text-transform: capitalize;
         letter-spacing: 1px;
      }

      .go-register a:hover {
         text-decoration: underline;
      }

      #password_reset {
         text-transform: capitalize;
         padding-top: 0.5rem;
         font-size: 0.9rem;
         text-decoration: none;
         letter-spacing: 1px;
      }

      #password_reset:hover {
         text-decoration: underline;
      }

      .compulsary {
         color: red;
         margin-right: 0.1rem;
      }

      .popup-container {
         margin-top: 12rem;
         margin-bottom: 1rem;
         display: flex;
         align-items: center;
         justify-content: center;
         height: 3rem;
         overflow: hidden;
      }

      .reg-success {
         text-transform: capitalize;
         background-color: white;
         display: inline-block;
         padding: 0.5rem 1rem;
         border-radius: 5px;
         box-shadow: 1px 2px 10px 2px rgba(0, 0, 0, 0.1);
         display: none;
      }

      .show-popup {
         opacity: 1;
         display: block;
      }

      .pass-set {
         display: flex;
         flex-direction: row;
         justify-content: space-between;
         align-items: center;
      }

      .inc-pass {
         margin-top: 0.5rem;
         font-size: 0.8rem;
         color: red;
         cursor: default;
         letter-spacing: 1px;
         display: none;
      }

      .logged_out {
         text-transform: capitalize;
         background-color: white;
         display: inline-block;
         padding: 0.5rem 1rem;
         border-radius: 5px;
         box-shadow: 1px 2px 10px 2px rgba(0, 0, 0, 0.1);
         display: none;
      }
   </style>
</head>

<body>
   <div class="popup-container">
      <span class="reg-success">registration successful!</span>
      <span class="logged_out">logged out!</span>
   </div>
   <div class="form-container">
      <a id="logo" href="#">
         <img id="logo-img" src="./assets/icons/logo.png" alt="">
         <h1>job portal</h1>
      </a>
      <p>login</p>
      <form action="./index.php" method="post">
         <div>
            <label for="email">email<span class="compulsary">*</span></label>
            <input type="email" name="email" id="email" placeholder="Your email" required>
         </div>
         <div>
            <label for="password">password<span class="compulsary">*</span></label>
            <input type="password" name="password" id="password" placeholder="Do you remember that?" required>
            <div class="pass-set">
               <span class="inc-pass">Incorrect password</span>
            </div>
         </div>
         <input name="submit" id="submit-btn" type="submit" value="submit">
      </form>
      <div class="go-register">
         <span>Not a member yet?</span>
         <a href="./register.php">register</a>
      </div>
   </div>

   <script>

      document.addEventListener("DOMContentLoaded", function () {
         const params = new URLSearchParams(window.location.search);
         if (params.has('registration') && params.get('registration') === 'success') {
            const regSuccess = document.querySelector(".reg-success");

            if (regSuccess) {
               regSuccess.classList.add("show-popup");
               setInterval(function () {
                  popUp.classList.remove("show-popup");
               }, 5000);
            }
         }

         if (params.get('password') === 'incorrect') {
            const incPass = document.querySelector(".inc-pass");

            if(incPass) {
               incPass.style.display = 'inline-block';
               setInterval(function () {
                  incPass.style.display = 'none';
               }, 5000);
            }

         }

         if (params.get('log_out') === 'success') {
            const lgout = document.querySelector(".logged_out");
            
            if(lgout) {
               lgout.style.display = 'inline-block';
               setInterval(function () {
                  lgout.style.display = 'none';
               }, 5000);
            }
         }
      })

   </script>
</body>

</html>