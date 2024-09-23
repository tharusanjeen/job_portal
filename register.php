<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

include("./config/db.php");

if (isset($_SESSION["AUTHENTICATED"]) && $_SESSION["AUTHENTICATED"] == true) {
   if ($_SESSION["role"] == "job_provider") {
      header("Location: ./role_provider/pages/dashboard.php");
      exit();
   } else {
      header("Location: ./role_seeker/pages/home.php");
      exit();
   }
}

if (!isset($_SESSION["AUTHENTICATED"]) || $_SESSION["AUTHENTICATED"] !== true) {
   if (isset($_COOKIE["auth"])) {
      $auth_token = $_COOKIE["auth"];

      $sql = "SELECT * FROM user where auth_token = ?";
      $stmt->prepare($sql);
      $stmt->bind_param("s", $auth_token);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
         $row = $result->fetch_assoc();

         $_SESSION["AUTHENTICATED"] = true;
         $_SESSION["full_name"] = $row["full_name"];
         $_SESSION["email_address"] = $row["email_address"];
         $_SESSION["role"] = $row["role"];

         if ($_SESSION["role"] == "job_provider") {
            header("Location: ./role_provider/pages/dashboard.php");
            exit();
         } else {
            header("Location: ./role_seeker/pages/home.php");
            exit();
         }

      } else {
         setcookie('auth', '', time() - 3600, '/', '', false, true);
         header('location: ./index.php');
         exit();
      }
   } else {
      if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])) {
         $user_name = strtolower($_POST["name"]);
         $email_address = $_POST["email"];
         $password = md5($_POST["password"]);
         $role = $_POST["role"];

         $checkEmail = "SELECT * FROM users WHERE email_address = '$email_address'";
         $result = $conn->query($checkEmail);
         if ($result->num_rows > 0) {
            header("location: register.php?err=duplicate_email");
            exit();
         } else {
            $full_name = $_POST["name"];
            $email_address = $_POST["email"];
            $password = md5($_POST["password"]);
            $role = $_POST["role"];

            $insertData = "INSERT INTO users(full_name, email_address, password, role)
                     VALUES ('$full_name', '$email_address', '$password', '$role')";

            if ($conn->query($insertData)) {
               header("location: index.php?registration=success");
               exit();
            } else {
               echo "Form is not submitted properly!";
            }

         }
      }
   }
}

?>

<!-- Registration page -->
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="shortcut icon" href="./assets/icons/logo.png" type="image/x-icon">
   <title>Register - Job Portal</title>
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
         margin-top: 10rem;
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

      .go-register a {
         text-align: center;
      }

      .go-register a:hover {
         text-decoration: underline;
      }

      form {
         display: flex;
         flex-direction: column;
         gap: 1rem;
      }

      .form-item {
         display: flex;
         flex-direction: column;
      }

      label {
         text-transform: capitalize;
         margin-bottom: 0.4rem;
         font-size: 0.9rem;
         cursor: pointer;
      }

      input {
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
      }

      #password_reset {
         text-transform: capitalize;
         padding-top: 0.5rem;
         font-size: 0.9rem;
      }

      .compulsary {
         color: red;
         margin-left: 0.1rem;
      }

      .role-container {
         display: flex;
         flex-direction: column;
      }

      .radio-container {
         display: flex;
         gap: 1rem;
      }

      .pop-up {
         margin: auto;
         padding: 1rem;
      }

      .pop-up p {
         font-size: 1.1rem;
         text-transform: capitalize;
      }

      .dup-email {
         margin-top: 0.5rem;
         font-size: 0.8rem;
         color: red;
         display: none;
      }
   </style>
</head>

<body>
   <div class="form-container">
      <a id="logo" href="./index.php">
         <img id="logo-img" src="./assets/icons/logo.png" alt="">
         <h1>job portal</h1>
      </a>
      <p>register</p>
      <form action="./register.php" method="post">
         <div class="form-item">
            <label for="name">full name<span class="compulsary">*</span></label>
            <input type="name" name="name" id="name" placeholder="What would you like to be called?" required>
         </div>
         <div class="form-item">
            <label for="email">email<span class="compulsary">*</span></label>
            <input type="email" name="email" id="email" placeholder="Your email" required>
            <span class="dup-email">Email already exists!</span>
         </div>
         <div class="form-item">
            <label for="password">password<span class="compulsary">*</span></label>
            <input type="password" name="password" id="password" minlength="8" maxlength="16" placeholder="Don't forget this" required>
         </div>
         <div class="role-container">
            <label for="">who are you <span class="compulsary">*</span></label>
            <div class="radio-container">
               <div>
                  <input type="radio" name="role" id="provider" value="job_provider" required>
                  <label for="provider">provider</label>
               </div>
               <div>
                  <input type="radio" name="role" id="seeker" value="job_seeker" required>
                  <label for="seeker">seeker</label>
               </div>
            </div>
         </div>
         <input name="submit" id="submit-btn" type="submit" value="submit">
      </form>
      <div class="go-register">
         <span>Already a member?</span>
         <a href="./index.php">login</a>
      </div>
   </div>

   <script>

      window.addEventListener("DOMContentLoaded", function () {
         const params = new URLSearchParams(window.location.search);

         if (params.has('err') && params.get('err') === 'duplicate_email') {
            var err_msg = document.querySelector(".dup-email");
            err_msg.style.display = 'inline-block';
         }
      })

   </script>
</body>

</html>