const toggleNav = document.querySelector("#toggleNav");
const toggleSidebar = document.querySelector("#toggleSidebar");
const sidebar = document.querySelector(".sideNav");
const main = document.querySelector("main");

const centerLogo = document.querySelector(".center-logo");
const dashboardText = document.querySelector("#dashboard-txt")

toggleNav.addEventListener("click", function () {
    toggleNav.classList.toggle('collapsed-nav');
    console.log(toggleNav.classList);

    if (toggleNav.classList.contains('collapsed-nav')) {
        sidebar.classList.add("hidden-sidebar");
        main.classList.add("full-main");
        dashboardText.style.display = 'none';
        centerLogo.style.display = 'block';
        centerLogo.style.display = 'flex';
    }
    else {
        sidebar.classList.remove("hidden-sidebar");
        main.classList.remove("full-main");
        dashboardText.style.display = 'block';
        centerLogo.style.display = 'none';
    }
});

toggleSidebar.addEventListener("click", function() {
    sidebar.classList.add("show-sidebar");
})

const userBtn = document.querySelector("#userlog-container");
const logoutForm = document.querySelector('.logout-form');

userBtn.addEventListener("click", function (e) {
    logoutForm.classList.add("logout-form-show");
})

document.addEventListener("click", function (e) {

    // Collapse log out btn when clicked outside.
    if (!logoutForm.contains(e.target) && !userBtn.contains(e.target)) {
        logoutForm.classList.remove("logout-form-show");
    }

    // Collapse sideNav btn when clicked outside.
    if (!sidebar.contains(e.target) && !toggleSidebar.contains(e.target)) {
        sidebar.classList.remove("show-sidebar");
    }
});