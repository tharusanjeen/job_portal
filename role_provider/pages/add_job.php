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

echo $_SESSION["user_id"];
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
    <title>Add a Job - Job Portal</title>
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
            position: relative;
            height: calc(100vh - 6rem);
            overflow-y: scroll;
            overflow-x: hidden;
            background-color: #EFF4F8;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            border-left: 1px solid rgba(0, 0, 0, 0.1);
            padding: 2rem 5rem;
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

        /* Customizing form */
        .form-container {
            width: 100%;
            position: relative;
            background-color: white;
            padding: 3rem 2.5rem;
            border-radius: 8px;
            color: #082643;
        }

        .form-container h1 {
            font-size: 1.8rem;
            font-weight: 400;
            margin-bottom: 1rem;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 1.4rem;
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .form-col {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-col label {
            text-transform: capitalize;
        }

        .form-col input,
        .form-col select,
        .form-col textarea {
            background-color: #EFF4F8;
            outline: none;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            padding: 0.5rem 1rem;
        }

        .form-btns {
            width: 100%;
            align-self: end;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .form-btns button {
            width: 45%;
            border: none;
            cursor: pointer;
            color: white;
            text-transform: capitalize;
            padding: 0.6rem 0;
            border-radius: 5px;
            transition: background-color linear .4s;
        }

        #clear_form {
            background-color: #5C7A98;
        }

        #clear_form:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        #submit_job {
            background-color: #2BA4DF;
        }

        #submit_job:hover {
            background-color: #2ba3df9d;
        }

        .job_added_not {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            display: grid;
            place-content: center;
            background-color: #EEF4F8;
            display: none;
        }

        .job_added_not div {

            background-color: white;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 1px 2px 10px 3px rgba(0, 0, 0, 0.2);
            padding: 2rem;
            border-radius: 5px;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            font-size: 1.1rem;
        }

        .job_added_not button {
            border: none;
            font-size: 1.1rem;
            padding: 0.4rem;
            text-transform: capitalize;
            border-radius: 5px;
            background-color: #5C7A98;
            color: white;
            cursor: pointer;
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
                    <a href="./dashboard.php"><i class="ri-bar-chart-2-fill"></i>stats</a>
                </li>
                <li>
                    <a href="./all_jobs.php"><i class="ri-menu-search-line"></i>all jobs</a>
                </li>
                <li>
                    <a href="#" style="color: #54A9DE"><i class="ri-file-add-line"></i>add job</a>
                </li>
                <li>
                    <a href="./applications.php"><i class="ri-group-line"></i>applications</a>
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

                <section class="form-container">
                    <h1>Add a Job</h1>
                    <form action="./../../scripts/add_job.php" method="post">
                        <div class="form-row">
                            <div class="form-col">
                                <label for="job_title">job title</label>
                                <input type="text" name="job_title" id="job_title" required>
                            </div>
                            <div class="form-col">
                                <label for="company_name">company name</label>
                                <input type="text" name="company_name" id="company_name" required>
                            </div>
                            <div class="form-col">
                                <label for="job_location">location</label>
                                <input type="text" name="job_location" id="job_location" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-col">
                                <label for="">job type</label>
                                <select name="job_type" id="job_type" required>
                                    <option value="full time" selected>Full Time</option>
                                    <option value="part time">Part Time</option>
                                    <option value="remote">Remote</option>
                                    <option value="internship">Internship</option>
                                </select>
                            </div>
                            <div class="form-col">
                                <label for="">employment type</label>
                                <select name="employment_type" id="employment_type" required>
                                    <option value="permanent" selected>Permanent</option>
                                    <option value="temporary">Temporary</option>
                                    <option value="freelance">Freelance</option>
                                </select>
                            </div>
                            <div class="form-col">
                                <label for="">working hours</label>
                                <select name="working_hours" id="working_hours" required>
                                    <option value="40" selected>40</option>
                                    <option value="< 40">
                                        < 40</option>
                                    <option value="> 40">> 40</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-col">
                                <label for="">status</label>
                                <select name="job_status" id="status" required read-only>
                                    <option value="pending" selected>Pending</option>
                                    <option value="approved" disabled>Approved</option>
                                    <option value="declined" disabled>Declined</option>
                                </select>
                            </div>
                            <div class="form-col" required>
                                <label for="deadline">application deadline</label>
                                <input type="date" name="deadline" id="deadline" required>
                            </div>
                            <div class="form-col">
                                <label for="contact_email">contact email</label>
                                <input type="email" name="contact_email" id="contact_email" required
                                    value=<?php echo $_SESSION["email_address"]  ?>>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-btns">
                                <button id="clear_form" type="reset">clear</button>
                                <button id="submit_job" type="submit" name="submit_job">submit</button>
                            </div>
                        </div>
                    </form>
                </section>
                <section class="job_added_not">
                    <div>
                        <p>Job added Successfully!</p>
                        <button class="close_job_not">ok</button>
                    </div>
                </section>
            </section>
        </main>
    </div>

    <script>

        document.addEventListener("DOMContentLoaded", function() {
            const params = new URLSearchParams(window.location.search);

            if(params.has("job_added")) {
                const div = document.querySelector(".job_added_not");
                div.style.display = 'block';
                div.style.display = 'grid';

                const removeDivBtn = document.querySelector('.close_job_not');
                removeDivBtn.addEventListener("click", function() {
                    div.style.display = "none";
                })
            }
        })

    </script>
</body>

</html>