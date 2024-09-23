<?php

session_start();

error_reporting(E_ALL);
ini_set("display_errors", 1);

include("./../../config/db.php");

if (!isset($_SESSION["AUTHENTICATED"]) || $_SESSION["AUTHENTICATED"] !== true) {
    header("location: ./../../index.php?authentication=false");
}

if (isset($_SESSION["AUTHENTICATED"]) || $_SESSION["AUTHENTICATED"] === true) {
    if ($_SESSION["role"] !== "job_provider") {
        header("Location: ./../../role_seeker/pages/home.php");
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
    <title>All Applications - Job Portal</title>
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
            padding: 2rem 4rem;
        }

        #top-content {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.3);
            border-bottom: 1px solid rgba(0, 0, 0, 0.3);
            padding: 1.5rem;
        }

        .btns-container {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        #top-content button,
        #top-content a {
            color: white;
            border: none;
            font-size: 1rem;
            letter-spacing: 2px;
            display: inline-block;
            text-transform: uppercase;
            padding: 0.4rem 2rem;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color linear .4s;
        }

        #top-content button {
            background-color: #132074;
        }

        #top-content a {
            text-decoration: none;
            background-color: #2BA4DF;
        }

        #top-content button:hover,
        #top-content a:hover {
            background-color: rgba(0, 0, 0, 0.7);
            transition: background-color linear .4s;
        }

        #top-content ul {
            list-style: square;
            list-style-position: inside;
        }

        #top-content ul li {
            letter-spacing: 2px;
            color: #163353;
        }

        #bottom-content {
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        #bottom-content h1 {
            text-align: center;
            text-transform: uppercase;
            font-weight: 500;
            letter-spacing: 2px;
        }
        
        #bottom-content p {
            letter-spacing: 2px;
        }

        @media screen and (max-width: 1400px) {
            .form-row {
                flex-wrap: wrap;
            }

            #top-content {
                flex-direction: column;
                ;
            }

            #contents-container {
                padding: 2rem;
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

            #contents-container {
                padding: 1rem;

            }

            #application-cards {
                justify-content: center;
            }

            .card_footer .job_details .details_row.last {
                flex-direction: column;
                gap: 1rem;
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
                    <a href="./dashboard.php"><i class="ri-bar-chart-2-fill"></i>stats</a>
                </li>
                <li>
                    <a href="./all_jobs.php"><i class="ri-menu-search-line"></i>all jobs</a>
                </li>
                <li>
                    <a href="./add_job.php"><i class="ri-file-add-line"></i>add job</a>
                </li>
                <li>
                    <a href="#" style="color: #54A9DE"><i class="ri-group-line"></i>applications</a>
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
                <div id="top-content">
                    <ul>
                        <li>You can see all your applications here.</li>
                        <li>Applications approved by your job provider will have <span style="color: green">approved</span> status.</li>
                        <li>Applications rejected by your job provider will have <span style="color: red">rejected</span> status.</li>
                    </ul>
                    <div class="btns-container">
                        <button id="reset">reset</button>
                        <a href="./add_job.php">add more</a>
                    </div>
                </div>
                <div id="bottom-content">
                    <h1>your applications</h1>
                    <p id="total"></p>
                    <div id="application-cards">

                    </div>
                </div>
            </section>
        </main>
    </div>
    <script>
        function fetchMyApplicants() {
            fetch('./../../scripts/my_applicants.php')
            .then(function(response) {
                if(!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then(function(data) {
                if(data.error) {
                    console.error("Server error: ", data.error);
                }
                console.log(data);
            })
            .catch(function(error) {
                console.error("Error fetching data: ", error);
            })
        }

        fetchMyApplicants();
    </script>
</body>

</html>