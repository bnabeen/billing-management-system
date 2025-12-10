document.getElementById("loginForm").addEventListener("submit", function(event) {
    let username = document.getElementById("username").value.trim();
    let password = document.getElementById("password").value.trim();
    let errorMsg = document.getElementById("errorMsg");

    if (username === "" || password === "") {
        errorMsg.textContent = "All fields are required!";
        event.preventDefault();
    }
});

document.getElementById("signupForm").addEventListener("submit", function(event) {
    let phone = document.getElementById("phone").value.trim();
    let username = document.getElementById("username").value.trim();
    let pass = document.getElementById("password").value.trim();
    let confirm = document.getElementById("confirm").value.trim();
    let errorMsg = document.getElementById("errorMsg");

    if (phone === "" || username === "" || pass === "" || confirm === "") {
        errorMsg.textContent = "All fields are required!";
        event.preventDefault();
        return;
    }

    if (pass !== confirm) {
        errorMsg.textContent = "Passwords do not match!";
        event.preventDefault();
    }
});
