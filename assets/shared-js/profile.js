document.addEventListener("DOMContentLoaded", function() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('currentpassword') === 'wrong') {
        const currentPwWrong = document.querySelector("#currentPwWrong");

        if (currentPwWrong) {
            currentPwWrong.style.display = 'block';
            currentPwWrong.style.display = 'grid';
        }
    }

    if (params.get('newcurrent') === 'false') {
        const newAndCurrentPwFalse = document.querySelector("#newAndCurrentPwFalse");

        if (newAndCurrentPwFalse) {
            newAndCurrentPwFalse.style.display = 'block';
            newAndCurrentPwFalse.style.display = 'grid';
        }
    }

    if (params.get('password') === 'updated') {
        const pwChangeSuccessDiv = document.querySelector("#pwChangeSuccessDiv");

        if (pwChangeSuccessDiv) {
            pwChangeSuccessDiv.style.display = 'block';
            pwChangeSuccessDiv.style.display = 'grid';
        }
    }

    if (params.get('information') === 'updated') {
        const infoUpdatedDiv = document.querySelector("#infoUpdated");

        if (infoUpdatedDiv) {
            infoUpdatedDiv.style.display = 'block';
            infoUpdatedDiv.style.display = 'grid';
        }
    }


    const hidePopUps = document.querySelectorAll(".passMessagesBtns");

    hidePopUps.forEach(function(hidePopUp) {
        hidePopUp.addEventListener("click", function(e) {
            const formContainer = e.currentTarget.parentElement.parentElement;
            formContainer.style.display = 'none';
        })
    });
});


const updateBtns = document.querySelectorAll(".update-btn");

updateBtns.forEach(function(updateBtn) {
    updateBtn.addEventListener("click", function(e) {
        if (e.target.classList.contains("profile")) {
            const updateProfileInfo = document.querySelector(".update-details-container");

            updateProfileInfo.classList.add("update-form-container-show");
        } else if (e.target.classList.contains("password")) {
            const updatePassword = document.querySelector(".update-password-container");

            updatePassword.classList.add("update-form-container-show");
        } else {
            const deleteAccountDiv = document.querySelector(".delete-account-container");

            deleteAccountDiv.classList.add("update-form-container-show");
        }
    })
})

// hiding for profile edit and password change containers

const hideContainersBtns = document.querySelectorAll(".form-close");
const updateFormContainers = document.querySelectorAll(".update-form-container");

hideContainersBtns.forEach(function(hideContainerBtn) {
    hideContainerBtn.addEventListener("click", function(e) {
        e.preventDefault();
        updateFormContainers.forEach(function(updateFormContainer) {
            updateFormContainer.classList.remove("update-form-container-show")
        })
    })
})