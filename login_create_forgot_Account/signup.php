<?php

session_start();
require "db_config.php";
?>

<form id="signupForm">
    <input type="text" name="username" placeholder="Username" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit">Create Account</button>
</form>

<p id="signupMessage"></p>

<script>
document.getElementById("signupForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch("signup_handler.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById("signupMessage").textContent = data.message;
        if (data.status === "success") {
            window.location.href = data.redirect; // 👉 Goes to login.php
        }
    })
    .catch(err => {
        document.getElementById("signupMessage").textContent = "Server error: " + err;
    });
});
</script>
