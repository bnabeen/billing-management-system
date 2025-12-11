document.getElementById("loginForm").addEventListener("submit", function (event) {
    let username = document.getElementById("username").value.trim();
    let password = document.getElementById("password").value.trim();
    let errorMsg = document.getElementById("errorMsg");

    if (username === "" || password === "") {
        errorMsg.textContent = "All fields are required!";
        event.preventDefault();
    }
});

document.getElementById("signupForm").addEventListener("submit", function (event) {
    let phone = document.getElementById("phone").value.trim();
    let username = document.getElementById("username").value.trim();
    let pass = document.getElementById("password").value.trim();
    let confirm = document.getElementById("confirm").value.trim();
    let errorMsg = document.getElementById("errorMsg");
    let messages = [];

    if (phone === "" || username === "" || pass === "" || confirm === "") {
        messages.push("All fields are required!");
    }

    // Phone Validation
    if (phone.length !== 10 || isNaN(phone)) {
        messages.push("Phone number must be exactly 10 digits.");
    }

    if (pass !== confirm) {
        messages.push("Passwords do not match!");
    }

    if (messages.length > 0) {
        errorMsg.innerHTML = messages.join("<br>");
        event.preventDefault();
    }
});
