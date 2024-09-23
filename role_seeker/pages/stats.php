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
    <title>Dashboard - Job Portal</title>
    <script defer src="./../../assets/shared-js/shared.js"></script>
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
            padding: 2rem;
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

        @media screen and (max-width: 1400px) {
            .form-row {
                flex-wrap: wrap;
            }
        }

        @media screen and (max-width: 886px) {
            #contents-container {
                padding: 2rem;
            }

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
                    <a href="./profile.php"><i class="ri-profile-line"></i>profile</a>
                </li>
                <li>
                    <a href="#" style="color: #54A9DE"><i class="ri-bar-chart-2-fill"></i>stats</a>
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
                <div id="jobs_status">
                    <div class="job_status pending">
                        <div class="job_status_header">
                            <span>
                                <?php

                                if (isset($_SESSION["email_address"])) {
                                    $email_address = $_SESSION["email_address"];

                                    $sql = "SELECT * FROM all_jobs where job_provider_email = ? and job_status = 'pending'";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("s", $email_address);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        echo $result->num_rows;
                                    } else {
                                        echo 0;
                                    }
                                }

                                ?>
                            </span>
                            <div>
                                <i class="ri-briefcase-4-fill"></i>
                            </div>
                        </div>
                        <h1>pending applications</h1>
                    </div>
                    <div class="job_status approved">
                        <div class="job_status_header">
                            <span>
                                <?php

                                if (isset($_SESSION["email_address"])) {
                                    $email_address = $_SESSION["email_address"];

                                    $sql = "SELECT * FROM all_jobs where job_provider_email = ? and job_status = 'approved'";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("s", $email_address);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        echo $result->num_rows;
                                    } else {
                                        echo 0;
                                    }
                                }

                                ?>
                            </span>
                            <div>
                                <i class="ri-calendar-check-fill"></i>
                            </div>
                        </div>
                        <h1>approved applications</h1>
                    </div>
                    <div class="job_status declined">
                        <div class="job_status_header">
                            <span>
                                <?php

                                if (isset($_SESSION["email_address"])) {
                                    $email_address = $_SESSION["email_address"];

                                    $sql = "SELECT * FROM all_jobs where job_provider_email = ? and job_status = 'declined'";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("s", $email_address);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        echo $result->num_rows;
                                    } else {
                                        echo 0;
                                    }
                                }

                                ?>
                            </span>
                            <div>
                                <i class="ri-bug-fill"></i>
                            </div>
                        </div>
                        <h1>rejected applications</h1>
                    </div>
                </div>
            </section>
        </main>
    </div>

</body>

</html>