<?php

session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

include("./../../config/db.php");

if (!isset($_SESSION["AUTHENTICATED"]) || $_SESSION["AUTHENTICATED"] !== true) {
    header("location: ./../../index.php?authentication=false");
}

if (isset($_SESSION["AUTHENTICATED"]) && $_SESSION["AUTHENTICATED"] === true) {
    if ($_SESSION["role"] !== "job_seeker") {
        header("Location: ./../../role_provider/pages/dashboard.php");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="./../../assets/icons/logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css"
        integrity="sha512-OQDNdI5rpnZ0BRhhJc+btbbtnxaj+LdQFeh0V9/igiEPDiWE2fG+ZsXl0JEH+bjXKPJ3zcXqNyP4/F/NegVdZg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Job Portal</title>
    <script defer src="./../../assets/shared-js/shared.js"></script>
    <script defer src="./../../assets/shared-js/profile.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            letter-spacing: 1px;
        }

        #wrapper {
            display: flex;
        }

        .sideNav {
            background-color: white;
            width: 18rem;
            height: 100vh;
            transition: width linear .3s;
            z-index: 999;
        }

        .hidden-sidebar {
            width: 0;
            overflow: hidden;
            transition: width linear .3s;
        }

        .logo-container {
            height: 6rem;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: default;
        }

        .logo-container h1 {
            text-transform: capitalize;
            color: #54A9DE;
            letter-spacing: 2px;
            font-size: 1.5rem;
        }

        .logo-container img {
            width: 5rem;
        }

        #nav-links {
            list-style: none;
            margin-top: 2rem;

        }

        #nav-links a {
            display: block;
            padding: 1rem 0 1rem 4rem;
            text-decoration: none;
            text-transform: capitalize;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            color: rgba(0, 0, 0, 0.7);
            transition: background-color linear .5s;
        }

        #nav-links a:hover {
            background-color: #EEF4F8;
            transition: background-color linear .5s;
        }

        #nav-links a i {
            font-size: 1.4rem;
        }

        nav {
            height: 6rem;
            max-width: 100%;
            background-color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 5rem;
        }

        #toggleNav {
            border: none;
            background-color: transparent;
            cursor: pointer;
        }

        #toggleNav i {
            font-size: 1.6rem;
            font-weight: bold;
            color: #54A9DE;
        }

        #toggleSidebar {
            border: none;
            background-color: transparent;
            cursor: pointer;
            display: none;
        }

        #toggleSidebar i {
            font-size: 1.6rem;
            font-weight: bold;
            color: #54A9DE;
        }

        .center-logo {
            display: none;
        }

        #dashboard-txt {
            text-transform: capitalize;
            letter-spacing: 3px;
            font-weight: 400;
        }

        #userlog-container {
            display: flex;
            align-items: center;
            cursor: pointer;
            background-color: #54A9DE;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 3px;
            gap: 0.5rem;
            position: relative;
        }

        #userlog-container div {
            width: 1.8rem;
            height: 1.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            color: black;
            border-radius: 50%;
        }

        #userlog-container div i {
            font-size: 1.4rem;
            color: #54A9DE;
        }

        #user_name {
            text-transform: capitalize;
        }

        .logout-form {
            position: absolute;
            top: 130%;
            left: 0;
            width: 100%;
            display: flex;
            flex-direction: column;
            max-height: 0;
            overflow: hidden;
            transition: max-height linear .2s;
            z-index: 999;
        }

        .logout-form-show {
            max-height: 3rem;
            transition: max-height linear .2s;
        }

        #logoutBtn {
            border: none;
            color: white;
            background-color: rgba(255, 0, 0, 1);
            text-transform: capitalize;
            padding: 0.8rem 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border-radius: 3px;
            transition: background-color linear 0.4s;
        }

        #logoutBtn i {
            font-size: 1.2rem;
        }

        #logoutBtn:hover {
            background-color: rgba(255, 25, 25, 0.6);
            transition: background-color linear 0.4s;
        }

        #contents-container {
            height: calc(100vh - 6rem);
            overflow-y: scroll;
            overflow-x: hidden;
            background-color: #EFF4F8;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            border-left: 1px solid rgba(0, 0, 0, 0.1);
        }

        main {
            width: calc(100vw - 18rem);
            height: 100vh;
            background-color: green;
            transition: width linear .3s;
        }

        .full-main {
            width: 100vw;
            transition: width linear .3s;
        }

        #contents-container {
            padding: 2rem 4rem;
            position: relative;
        }

        #jobs_status {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
        }

        .job_status {
            width: 30rem;
            background-color: white;
            padding: 2rem 2.4rem;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            border-bottom: 5px solid;
        }

        .job_status h1 {
            text-transform: capitalize;
            font-weight: 500;
            color: #082643;
            font-size: 1.2rem;
            letter-spacing: 2px;
        }

        .job_status_header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .job_status_header span {
            font-size: 3rem;
            font-weight: bold;
        }

        .job_status_header div {
            padding: 0.8rem 1.4rem;
            border-radius: 4px;
        }

        .job_status_header div i {
            font-size: 2rem;
        }

        .job_status.pending {
            border-color: #F1C046;
        }

        .job_status.pending .job_status_header {
            color: #F1C046;
        }

        .job_status.pending .job_status_header div {
            background-color: #FDF4C7;
        }

        .job_status.approved {
            border-color: #6764CB;
        }

        .job_status.approved .job_status_header {
            color: #6764CB;
        }

        .job_status.approved .job_status_header div {
            background-color: #DFE6F9;
        }

        .job_status.declined {
            border-color: #EA5065;
        }

        .job_status.declined .job_status_header {
            color: #EA5065;
        }

        .job_status.declined .job_status_header div {
            background-color: #FFECEE;
        }

        #profile-container {
            max-width: 60rem;
            margin: auto;
            background-color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
            border-radius: 5px;
            box-shadow: 1px 1px 10px 1px rgba(0, 0, 0, 0.1);
        }

        #show-details {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1rem;
        }

        #show-details input {
            width: 35rem;
            outline: none;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 3px;
            padding: 0.4rem;
        }

        #form-btns {
            display: flex;
            margin-top: 1.5rem;
            gap: 1rem;
        }

        #form-btns button {
            padding: 0.5rem 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            border: none;
            border-radius: 3px;
            color: white;
            transition: box-shadow linear .3s;
        }

        #form-btns button.profile {
            background-color: rgba(0, 0, 0, 0.4);
        }

        #form-btns button.password {
            background-color: #2BA4DF;
        }

        #form-btns button.delete-account {
            background-color: rgba(255, 0, 0, 1);
        }

        #form-btns button:hover {
            box-shadow: 1px 1px 10px 4px rgba(0, 0, 0, 0.2);
            transition: box-shadow linear .3s;
        }

        .update-form-container {
            position: absolute;
            top: 0;
            left: 0;
            background-color: #EEF4F8;
            width: 100%;
            height: 100%;
            display: none;
        }

        .update-form-container-show {
            display: block;
            display: grid;
            place-content: center;
        }

        .update-form-container form {
            width: 40rem;
            border-radius: 4px;
            padding: 2rem;
            box-shadow: 1px 1px 20px 3px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            gap: 1rem;
            background-color: white;
        }


        .update-form-container form div {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .update-form-container form label {
            text-transform: capitalize;
        }

        .update-form-container form input {
            padding: 0.4rem;
            width: 100%;
        }

        .update-form-container button {
            text-transform: capitalize;
            letter-spacing: 2px;
            padding: 0.4rem;
            cursor: pointer;
            border: none;
            color: white;
            border-radius: 4px;
        }

        .update-form-container button.form-close {
            background-color: #151D74;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.7rem;
        }

        .update-form-container button.form-close i {
            font-size: 1.4rem;
        }

        .update-form-container button.update {
            background-color: #2BA4DF;
        }

        .passMessages {
            height: 100%;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            background-color: #EEF4F8;
            display: grid;
            place-content: center;
            display: none;
        }

        .passMessages div {
            background-color: white;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            padding: 2rem 3rem;
            border-radius: 4px;
            box-shadow: 1px 1px 10px 2px rgba(0, 0, 0, 0.1);
        }

        .passMessages div h1 {
            font-weight: 500;
            font-size: 1rem;
        }

        .passMessages div button {
            align-self: end;
            padding: 0.5rem 1rem;
            cursor: pointer;
            border: none;
            background-color: #2BA4DF;
            color: white;
            text-transform: uppercase;
            border-radius: 3px;
            transition: box-shadow linear .4s;
        }

        .passMessages div button:hover {
            box-shadow: 1px 1px 10px 3px rgba(0, 0, 0, 0.2);
            transition: box-shadow linear .4s;
        }

        @media screen and (max-width: 1400px) {
            .form-row {
                flex-wrap: wrap;
            }

            .update-form-container form {
                width: 30rem;
            }

        }

        @media screen and (max-width: 1200px) {
            #show-details input {
                width: 25rem;
            }


            #update-details form {
                width: 25rem;
            }
        }

        @media screen and (max-width: 886px) {

            nav {
                padding: 2rem;
            }

            #center-div {
                display: none;
            }

            /* Making Sidebar Responsive */
            .sideNav {
                position: fixed;
                width: 18rem;
                left: -18rem;
                top: 0;
                box-shadow: 3px 0 20px 5px rgba(0, 0, 0, 0.1);
                transition: all linear .3s;
            }

            main {
                width: 100vw;
            }

            #toggleNav {
                display: none;
            }

            #toggleSidebar {
                display: block;
            }

            .show-sidebar {
                left: 0;
                transition: all linear .3s;
            }

            #nav-links a {
                padding-left: 3rem;
            }

            input {
                width: 0;
            }

            #show-details input {
                width: 20rem;
            }


            #form-btns {
                flex-direction: column;
            }

            #form-btns button {
                width: 15rem;
            }
        }
    </style>

</head>

<body>

    <div id="wrapper">
        <aside class="sideNav">
            <div class="logo-container">
                <img src="./../../assets/icons/logo.png" alt="">
                <h1>job portal</h1>
            </div>
            <ul id="nav-links">
                <li>
                    <a href="#" style="color: #54A9DE"><i class="ri-profile-line"></i>profile</a>
                </li>
                <li>
                    <a href="./stats.php"><i class="ri-bar-chart-2-fill"></i>stats</a>
                </li>
                <li>
                    <a href="./home.php"><i class="ri-menu-search-line"></i>all jobs</a>
                </li>
                <li>
                    <a href="./applications.php"><i class="ri-group-line"></i>my applications</a>
                </li>
            </ul>

        </aside>
        <main>
            <nav>
                <div>
                    <button id="toggleNav">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <button id="toggleSidebar">
                        <i class="ri-menu-2-line"></i>
                    </button>
                </div>
                <div id="center-div">
                    <div class="logo-container center-logo">
                        <img src="./../../assets/icons/logo.png" alt="">
                        <h1>job portal</h1>
                    </div>
                    <h1 id="dashboard-txt">dashboard</h1>
                </div>
                <div id="userlog-container">
                    <div>
                        <i class="ri-user-line"></i>
                    </div>
                    <p id="user_name"><?php echo $_SESSION["full_name"] ?></p>
                    <span><i class="ri-arrow-down-s-line"></i></span>
                    <form class="logout-form" action="./../../scripts/logout.php" method="post">
                        <button type="submit" name="logout" id="logoutBtn"><i class="ri-shut-down-line"></i>log
                            out</button>
                    </form>
                </div>
            </nav>
            <section id="contents-container">
                <div id="profile-container">
                    <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                        <div style="background-color: #EEF4F8; border-radius: 50%; overflow: hidden; padding: 1rem;">
                            <i class="ri-user-line" style="font-size: 5rem; color: #2BA4DF"></i>
                        </div>
                        <h1 style="text-transform:uppercase; letter-spacing: 2px; font-weight: 600;"><?php echo $_SESSION["full_name"] ?></h1>
                    </div>
                    <div id="show-details">
                        <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                            <label for="email_address" style="text-transform: capitalize; letter-spacing: 2px;">email address</label>
                            <input type="text" name="email_address" id="email_address" value=<?php echo $_SESSION["email_address"] ?> readonly>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                            <label for="role" style="text-transform: capitalize; letter-spacing: 2px;">role</label>
                            <input type="text" name="role" id="role" value=<?php echo $_SESSION["role"] ?> readonly>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                            <label for="token" style="text-transform: capitalize; letter-spacing: 2px;">token</label>
                            <input type="text" name="token" id="token" value=<?php echo $_COOKIE["auth"] ?> readonly>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                            <label for="token" style="text-transform: capitalize; letter-spacing: 2px;">joined on</label>
                            <input type="text" name="created_at" id="created_at"
                                value=<?php
                                        if ($_SESSION && $_SESSION["email_address"]) {
                                            $sql = "SELECT created_at FROM users WHERE email_address = ?";

                                            $stmt = $conn->prepare($sql);
                                            $stmt->bind_param("s", $_SESSION["email_address"]);
                                            $stmt->execute();
                                            $result = $stmt->get_result();

                                            if ($row = $result->fetch_assoc()) {
                                                echo htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8');
                                            }
                                        }
                                        ?> readonly>
                        </div>
                    </div>
                    <div id="form-btns">
                        <button class="update-btn profile">
                            update details
                        </button>
                        <button class="update-btn password">
                            change password
                        </button>
                        <button class="update-btn delete-account">
                            delete account
                        </button>
                    </div>
                </div>
                <div class="update-form-container update-details-container">

                    <form action="./../../scripts/updateUserDetails.php" method="post">
                        <div>
                            <label for="new_full_name">new full name <span style="color: red;">*</span></label>
                            <input type="text" name="new_full_name" id="new_full_name" required>
                        </div>
                        <div>
                            <label for="new_email_address">new email address <span style="color: red;">*</span></label>
                            <input type="email" name="new_email_address" id="new_email_address" required>
                        </div>
                        <div>
                            <button class="form-close"><i class="ri-arrow-left-circle-line"></i>go back</button>
                            <button class="update" type="submit">submit</button>
                        </div>
                    </form>
                </div>
                <div class="update-form-container update-password-container">

                    <form action="./../../scripts/changePassword.php" method="post">
                        <div>
                            <label for="current_password">current password <span style="color: red;">*</span></label>
                            <input type="password" name="current_password" id="current_password" minlength="8" maxlength="20" required>
                        </div>
                        <div>
                            <label for="new_password">new password <span style="color: red;">*</span></label>
                            <input type="password" name="new_password" id="new_password" minlength="8" maxlength="20" required>
                        </div>
                        <div>
                            <label for="confirm_new_password">confirm password <span style="color: red;">*</span></label>
                            <input type="password" name="confirm_new_password" id="confirm_new_password" minlength="8" maxlength="20" required>
                        </div>
                        <div>
                            <button class="form-close"><i class="ri-arrow-left-circle-line"></i>go back</button>
                            <button class="update" name="submit" type="submit">submit</button>
                        </div>
                    </form>
                </div>
                <div class="update-form-container delete-account-container">

                    <form action="./../../scripts/deleteAccount.php" method="post">
                        <h1 style="font-size: 1.4rem; font-weight: 500;">You want to delete your account, are you sure? </h1>
                            <button class="form-close">no</button>
                            <button type="submit" name="submit" style="background-color: red;">yes</button>
                    </form>
                </div>
                <section class='passMessages' id="pwChangeSuccessDiv">
                    <div>
                        <h1>password updated successfully!</h1>
                        <button class="passMessagesBtns pwChangeSuccessBtn">ok</button>
                    </div>
                </section>
                <section class='passMessages' id="currentPwWrong">
                    <div>
                        <h1>Current password is wrong!</h1>
                        <button class="passMessagesBtns currentPwBtn">try again</button>
                    </div>
                </section>
                <section class='passMessages' id="newAndCurrentPwFalse">
                    <div>
                        <h1>New and current password don't match!</h1>
                        <button class="passMessagesBtns newAndCurrentPwFalseBtn">ok</button>
                    </div>
                </section>
                <section class='passMessages' id="infoUpdated">
                    <div>
                        <h1>Information updated successfully.</h1>
                        <button class="passMessagesBtns infoUpdatedBtn">ok</button>
                    </div>
                </section>
            </section>
        </main>
    </div>

</body>

</html>