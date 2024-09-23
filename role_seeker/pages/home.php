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
    if(isset($_COOKIE["auth"])) {
        $auth_token = $_COOKIE["auth"];

        $sql = "SELECT * FROM users WHERE auth_token = ?";
        $stmt = $conn -> prepare($sql);
        $stmt -> bind_param("s", $auth_token);
        $stmt -> execute();
        $result = $stmt -> get_result();

        if($result -> num_rows > 0) {
            $row = $result -> fetch_assoc();
            $user_id = $row["id"];
        }
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
    <title>All Jobs - Job Portal</title>
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
            width: 1.4rem;
            height: 1.4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            color: black;
            border-radius: 50%;
        }

        #userlog-container div i {
            font-size: 1.2rem;
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

        /* All Jobs */
        #contents-container {
            padding: 4rem;
        }

        #jobs_count {
            font-size: 1.1rem;
            font-weight: 600;
            text-transform: capitalize;
            padding: 0 0 1rem 0.4rem;
            text-align: left;
            color: #082643;
        }

        #all_jobs {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .job_card {
            width: 45rem;
            background-color: white;
            border-radius: 6px;
            transition: box-shadow linear .3s;
            color: #082643;
        }

        .job_card i {
            color: #7C98B1;
        }

        .job_card:hover {
            box-shadow: 1px 2px 20px 4px rgba(0, 0, 0, 0.1);
            transition: box-shadow linear .3s;
        }

        .card_header {
            display: flex;
            align-items: center;
            gap: 2rem;
            padding: 1rem 1.4rem;
            border-bottom: 2px solid #EEF4F8;
        }

        .job_logo {
            padding: 0.8rem 1.4rem;
            text-transform: uppercase;
            background-color: #2BA4DF;
            color: white;
            border-radius: 5px;
        }

        .card_title {
            width: 100%;
            line-height: 1.5rem;
        }

        .job_title {
            font-weight: 400;
            font-size: 1.1rem;
            text-transform: capitalize;
        }

        .card_title span {
            font-size: 0.9rem;
            font-weight: 400;
            text-transform: capitalize;
            color: gray;
        }

        .card_footer {
            padding: 1rem 1.5rem;
        }

        .card_btns button {
            padding: 0.7rem 2rem;
            border: none;
            text-transform: uppercase;
            border-radius: 3px;
            color: white;
            cursor: pointer;
            background-color: #009EE0;
            margin-right: 0.7rem;
            transition: box-shadow linear .3s;
        }

        .card_btns button:hover {
            box-shadow: 1px 2px 20px 4px rgba(0, 0, 0, 0.2);
            transition: box-shadow linear .3s;
        }

        .card_footer .job_details {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .card_footer .job_details .details_row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card_footer .job_details .details_row div {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 1rem;
            text-transform: capitalize;
        }

        .card_footer .job_details .details_row div i {
            font-size: 1.2rem;
        }

        #search_form {
            background-color: white;
            padding: 1.5rem 2rem;
            margin-bottom: 1rem;
        }

        #search_form h1 {
            font-weight: 400;
            text-transform: capitalize;
            margin-bottom: 1rem;
            color: #082643;
        }

        #search_row {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .search_col {
            width: 25rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .search_col label {
            text-transform: capitalize;
            color: #082643;
            letter-spacing: 2px;
        }

        .search_col input,
        .search_col select {
            padding: 0.4rem 0.8rem;
            outline: none;
            text-transform: capitalize;
            background-color: #EEF4F8;
            border: 1px solid rgba(0, 0, 0, 0.4);
            border-radius: 3px;
            letter-spacing: 2px;
        }

        .search_col select {
            cursor: pointer;
        }

        #clear_filters {
            background-color: rgba(200, 0, 0, 0.9);
            border: none;
            text-transform: capitalize;
            font-size: 0.9rem;
            color: white;
            border-radius: 4px;
            padding: 0.5rem;
            cursor: pointer;
            transition: background-color linear .3s;
            letter-spacing: 2px;
        }

        #clear_filters:hover {
            transition: background-color linear .3s;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .apply_now {
            width: 100vw;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            background-color: #EEF4F8;
            overflow: auto;
            display: none;
        }

        #apply_form_container {
            max-width: 70rem;
            margin: auto;
            margin-top: 10rem;
            background-color: white;
            padding: 1.5rem 2rem;
            border-radius: 3px;
        }

        #apply_form_container h2 {
            text-align: center;
            text-transform: uppercase;
            font-weight: 500;
            letter-spacing: 2px;
            margin-bottom: 1rem;
        }

        #apply_form_container form {
            display: flex;
            flex-direction: column;
            gap: 1.4rem;
        }

        #apply_form_container .apply_row {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        #apply_form_container .apply_col {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        #apply_form_container .apply_col label {
            text-transform: capitalize;
            letter-spacing: 2px;
        }

        #apply_form_container form input {
            background-color: #EDF4F8;
            border: 1px solid rgba(0, 0, 0, 0.3);
            padding: 0.4rem 0.6rem;
            text-transform: capitalize;
            letter-spacing: 2px;
            outline: none
        }

        #apply_form_container button {
            text-transform: uppercase;
            padding: 0.4rem 3rem;
            border: none;
            letter-spacing: 2px;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color linear .4s;
        }

        #apply_form_container button.apply_cancel {
            background-color: rgba(255, 0, 0, 0.9);
        }

        #apply_form_container button.apply_submit {
            background-color: #2BA4DF;
        }

        #apply_form_container button:hover {
            transition: background-color linear .4s;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .show {
            display: block;
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

            /* All Jobs */

            #contents-container {
                padding: 1rem;
            }

            .card_footer .job_details .details_row.last {
                flex-direction: column;
                gap: 1rem;
            }
        }

        @media screen and (max-width: 1800px) {
            .search_col {
                width: 20rem;
            }

        }

        @media screen and (max-width: 1136px) {
            .search_col {
                width: 100%;
            }

        }

        @media screen and (max-width: 866px) {

            .apply_row {
                flex-direction: column;
                gap: 1rem;
            }

            #apply_form_container {
                margin-top: 0;
            }

            #apply_form_container button {
                width: 100%;
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
                    <a href="./stats.php"><i class="ri-bar-chart-2-fill"></i>stats</a>
                </li>
                <li>
                    <a href="#" style="color: #54A9DE"><i class="ri-menu-search-line"></i>all jobs</a>
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
                <div id="search_form">
                    <h1>search form</h1>
                    <div id="search_row">
                        <div class="search_col">
                            <label for="search">search</label>
                            <input type="search" name="search" id="search" placeholder="Job title">
                        </div>
                        <div class="search_col">
                            <label for="">job status</label>
                            <select name="status" id="status">
                                <option value="all">all</option>
                                <option value="pending">pending</option>
                                <option value="declined">declined</option>
                            </select>
                        </div>
                        <div class="search_col">
                            <label for="">job type</label>
                            <select name="job_type" id="job_type">
                                <option value="all">all</option>
                                <option value="full time">full time</option>
                                <option value="part time">part time</option>
                                <option value="remote">remote</option>
                                <option value="internship">internship</option>
                            </select>
                        </div>
                        <div class="search_col">
                            <label for="">sort</label>
                            <select name="sort" id="sort">
                                <option value="latest">latest</option>
                                <option value="oldest">oldest</option>
                                <option value="ascending">ascending</option>
                                <option value="descending">descending</option>
                            </select>
                        </div>
                        <div class="search_col">
                            <label for="" style="color: white; cursor: default;">hello</label>
                            <button id="clear_filters">clear filters</button>
                        </div>
                    </div>
                </div>
                <div id="jobs_count"></div>
                <div id="all_jobs">
                </div>
            </section>
        </main>
        <section class="apply_now">
            <div id="apply_form_container">
                <div class="logo-container">
                    <img src="./../../assets/icons/logo.png" alt="">
                    <h1>job portal</h1>
                </div>
                <h2>apply now</h2>
                <form action="../../scripts/apply_now.php" id="apply_now_form" method="post">
                    <div class="apply_row">
                    <input type="hidden" id="user_id" name="user_id" value=<?php echo $user_id; ?>>
                    <input type="hidden" id="job_id" name="job_id">
                    <input type="hidden" id="job_provider_id" name="job_provider_id">
                    </div>
                    <div class="apply_row">
                        <div class="apply_col">
                            <label for="">job title</label>
                            <input type="text" name="apply_job_title" id="apply_job_title" readonly>
                        </div>
                        <div class="apply_col">
                            <label for="">job location</label>
                            <input type="text" name="job_location" id="job_location" readonly>
                        </div>
                    </div>
                    <div class="apply_row">
                        <div class="apply_col">
                            <label for="">job provider</label>
                            <input type="text" name="job_provider" id="job_provider" readonly>
                        </div>
                        <div class="apply_col">
                            <label for="">company name</label>
                            <input type="text" name="company_name" id="company_name" readonly>
                        </div>
                    </div>
                    <div class="apply_row">
                        <div class="apply_col">
                            <label for="">posted date</label>
                            <input type="text" name="posted_date" id="posted_date" readonly>
                        </div>
                        <div class="apply_col">
                            <label for="">deadline</label>
                            <input type="text" name="deadline" id="deadline" readonly>
                        </div>
                    </div>
                    <div class="apply_row">
                        <div class="apply_col">
                            <label for="">qualification</label>
                            <input type="text" name="qualification" id="qualification" required placeholder="e.g. BLE, SEE, +2, BBS, BCA, course">
                        </div>
                        <div class="apply_col">
                            <label for="">certificate link</label>
                            <input style="text-transform: lowercase;" type="text" name="certificate_link" id="certificate_link" required placeholder="link to your resume/certificate">
                        </div>
                    </div>
                    <div class="apply_row">
                        <button id="cancel_apply" class="apply_cancel">cancel</button>
                        <button type="submit" class="apply_submit">submit</button>
                    </div>
                </form>

            </div>
        </section>
    </div>

    <script>
        // Fetching data from the server

        function fetchAllJobs() {
            fetch("./../../scripts/fetch_all_data.php")
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Network response was not ok!");
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        console.error("Server error: ", data.error);
                    } else {
                        window.allJobsData = data;
                        filterData();
                    }

                })
                .catch(error => {
                    console.error("Error fetching data: ", error);
                })
        }

        fetchAllJobs();

        // Displaying the recieved data from the server

        function displayData(data) {
            const jobsContainer = document.querySelector("#all_jobs");
            const jobsCount = document.querySelector("#jobs_count");

            if (data.length > 0) {
                jobsContainer.innerHTML = "";
                jobsCount.innerHTML = "";

                let count = `${data.length} ${data.length > 1 ? "jobs" : "job"} found`;

                let allData = data.map(dataItem => {
                    const {
                        id,
                        job_title,
                        company_name,
                        job_location,
                        contact_email,
                        job_type,
                        employment_type,
                        working_hours,
                        job_status,
                        deadline,
                        created_at
                    } = dataItem;
                    return `<div key=${id} class="job_card">
                        <div class="card_header">
                            <h1 class="job_logo">${company_name[0]}</h1>
                            <div class="card_title">
                                <h1 class="job_title">${job_title}<h1>
                                <span>${company_name}</span>
                            </div>
                        </div>
                        <div class="card_footer">
                            <div class="job_details">
                                <div class="details_row">
                                    <div><i class="ri-send-plane-line"></i><span>${job_location}</span></div>
                                    <div><i class="ri-briefcase-4-line"></i><span>${job_type}</span></div>
                                </div>
                                <div class="details_row">
                                    <div><i class="ri-printer-cloud-line"></i><span>${employment_type}</span></div>
                                    <div><i class="ri-time-line"></i><span>${working_hours} <span style="font-size: 0.7rem; text-transform: lowercase; color: rgba(0, 0, 0, 0.7)">h/w</span></span></div>
                                </div>
                                <div class="details_row">
                                    <div><i class="ri-calendar-check-line"></i><span>${created_at.slice(0, 10)}</span></div>
                                    <div><i class="ri-calendar-schedule-line"></i><span>${deadline}</span></div>
                                    </div>
                                    <div class="details_row last">
                                    <div><i class="ri-mail-send-fill"></i><span>${contact_email}</span></div>
                                    <div style="background-color: #FFECEE; color: gray; padding: 0.5rem 1rem; border-radius: 3px;">${job_status}</div>
                                </div>
                            </div>
                            <div class="card_btns">
                                <button class="apply_btn" data-id="${id}">apply now</button>
                            </div>
                        </div>
                    </div>`;
                }).join(" ");

                jobsContainer.innerHTML = allData;
                jobsCount.textContent = count;


                // Making apply functionality Functional

                const applyBtns = document.querySelectorAll(".apply_btn");
                applyBtns.forEach(function(applyBtn) {
                    applyBtn.addEventListener("click", function(e) {
                        const jobId = e.target.getAttribute("data-id");
                        const apply_now = document.querySelector(".apply_now");

                        const all_jobs = window.allJobsData;
                        const thisJob = all_jobs.filter(function(job) {
                            return String(jobId) === String(job.id);
                        })


                        document.querySelector("#job_id").value = thisJob[0].id;
                        document.querySelector("#job_provider_id").value = thisJob[0].job_provider_id;
                        document.querySelector("#apply_job_title").value = thisJob[0].job_title;
                        document.querySelector("#job_location").value = thisJob[0].job_location;
                        document.querySelector("#job_provider").value = thisJob[0].job_provider;
                        document.querySelector("#company_name").value = thisJob[0].company_name;
                        document.querySelector("#posted_date").value = thisJob[0].created_at;
                        document.querySelector("#deadline").value = thisJob[0].deadline;
                        document.querySelector("#qualification").value = "";
                        document.querySelector("#certificate_link").value = "";

                        apply_now.classList.add("show");

                        const hide_apply_now_btn = document.querySelector("#cancel_apply");
                        hide_apply_now_btn.addEventListener("click", function(e) {
                            e.preventDefault();
                            apply_now.classList.remove("show");
                        })
                    })
                })
            } else {
                jobsCount.innerHTML = "";
                jobsContainer.innerHTML = "No jobs found!";
            }
        };

        function filterData() {
            const searchInput = document.querySelector("#search").value.toLowerCase();
            const statusInput = document.querySelector("#status").value;
            const jobTypeInput = document.querySelector("#job_type").value;
            const sortInput = document.querySelector("#sort").value;

            let filteredData = window.allJobsData.filter(function(job) {
                const statusFilter = (statusInput === 'all' || job.job_status === statusInput);
                const typeFilter = (jobTypeInput === 'all' || job.job_type === jobTypeInput);
                const searchFilter = job.job_title.toLowerCase().includes(searchInput);

                return statusFilter && typeFilter && searchFilter;
            });

            if (sortInput === "ascending") {
                filteredData.sort((a, b) => a.job_title.localeCompare(b.job_title));
            } else if (sortInput === "descending") {
                filteredData.sort((a, b) => b.job_title.localeCompare(a.job_title));
            } else if (sortInput === "latest") {
                filteredData.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            } else if (sortInput === "oldest") {
                filteredData.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
            }

            displayData(filteredData);
        }

        document.querySelector("#search").addEventListener("input", filterData);
        document.querySelector("#status").addEventListener("change", filterData);
        document.querySelector("#job_type").addEventListener("change", filterData);
        document.querySelector("#sort").addEventListener("change", filterData);

        // Clear filters
        const clear_filters = document.querySelector("#clear_filters");

        clear_filters.addEventListener("click", function() {
            document.querySelector("#search").value = "";
            document.querySelector("#status").value = "all";
            document.querySelector("#job_type").value = "all";
            document.querySelector("#sort").value = "latest";
            displayData(window.allJobsData);
        })
    </script>
</body>

</html>