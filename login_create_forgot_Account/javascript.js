// Toggle Login ↔ Signup
function toggleForms() {
  document.getElementById("loginForm").classList.toggle("active");
  document.getElementById("signupForm").classList.toggle("active");
  document.getElementById("forgotPasswordFormSection").classList.remove("active");
  clearAllErrors();
}

// Show Forgot Password Form
function showForgotPasswordForm() {
  document.getElementById("loginForm").classList.remove("active");
  document.getElementById("signupForm").classList.remove("active");
  document.getElementById("forgotPasswordFormSection").classList.add("active");
  clearAllErrors();
}

// Show Login Form
function showLoginForm() {
  document.getElementById("forgotPasswordFormSection").classList.remove("active");
  document.getElementById("signupForm").classList.remove("active");
  document.getElementById("loginForm").classList.add("active");
  clearAllErrors();
}

// Clear all errors
function clearAllErrors() {
  const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], input[type="tel"]');
  inputs.forEach(input => {
    input.classList.remove('error', 'shake');
  });
  const errorMessages = document.querySelectorAll('.error-message');
  errorMessages.forEach(msg => {
    msg.textContent = '';
  });
}

// Show error message
function showError(inputId, errorMessage) {
  const input = document.getElementById(inputId);
  const errorElement = document.getElementById(inputId + '-error');
  
  input.classList.add('error', 'shake');
  if (errorElement) {
    errorElement.textContent = errorMessage;
  }
  
  setTimeout(() => {
    input.classList.remove('shake');
  }, 300);
}

// Clear error for specific input
function clearError(inputId) {
  const input = document.getElementById(inputId);
  const errorElement = document.getElementById(inputId + '-error');
  
  input.classList.remove('error');
  if (errorElement) {
    errorElement.textContent = '';
  }
}

// Add focus event listeners
document.addEventListener('DOMContentLoaded', function() {
  const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], input[type="tel"]');
  inputs.forEach(input => {
    input.addEventListener('focus', function() {
      clearError(this.id);
    });
  });
});

/* ========== VALIDATION FUNCTIONS ========== */
function validateLogin() {
  const email = document.getElementById("login-email").value.trim();
  const password = document.getElementById("login-password").value.trim();

  let valid = true;

  clearError("login-email");
  clearError("login-password");

  if (email === "") {
    showError("login-email", "Email or username is required");
    valid = false;
  }

  if (password === "") {
    showError("login-password", "Password is required");
    valid = false;
  }

  if (!valid) {
    return;
  }

  // Send to backend
  const formData = new FormData();
  formData.append('email_or_username', email);
  formData.append('password', password);

  fetch('login_handler.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      // Check user type and redirect accordingly
      if (data.user_type === 'admin') {
        alert('Welcome Admin!');
        window.location.href = data.redirect; // admin_dashboard.php
      } else {
        alert('Login Successful!');
        window.location.href = data.redirect; // user_dashboard.php
      }
    } else {
      showError("login-password", data.message);
    }
  })
  .catch(error => {
    alert('Error: ' + error);
  });
}

function validateSignup() {
  const name = document.getElementById("signup-name").value.trim();
  const email = document.getElementById("signup-email").value.trim();
  const pass = document.getElementById("signup-password").value.trim();
  const phone = document.getElementById("signup-phone").value.trim();
  const room = document.getElementById("signup-room").value.trim();

  clearError("signup-name");
  clearError("signup-email");
  clearError("signup-password");
  clearError("signup-phone");
  clearError("signup-room");

  let valid = true;

  if (name === "") {
    showError("signup-name", "Full name is required");
    valid = false;
  }
  if (email === "") {
    showError("signup-email", "Email is required");
    valid = false;
  }
  if (pass === "") {
    showError("signup-password", "Password is required");
    valid = false;
  }
  
  if (phone === "") {
    showError("signup-phone", "Phone number is required");
    valid = false;
  } else if (!/^\d+$/.test(phone)) {
    showError("signup-phone", "Phone number must contain only digits");
    valid = false;
  } else if (phone.length !== 11) {
    showError("signup-phone", "Phone number must be exactly 11 digits");
    valid = false;
  }
  
  if (room === "") {
    showError("signup-room", "Room number is required");
    valid = false;
  }

  if (!valid) {
    return;
  }

  // Send to backend
  const formData = new FormData();
  formData.append('full_name', name);
  formData.append('email', email);
  formData.append('password', pass);
  formData.append('phone_number', phone);
  formData.append('room_number', room);

  fetch('register.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      alert('Account created successfully!');
      showLoginForm();
    } else {
      if (data.message.includes('Email')) {
        showError("signup-email", data.message);
      } else if (data.message.includes('Room')) {
        showError("signup-room", data.message);
      } else {
        alert(data.message);
      }
    }
  })
  .catch(error => {
    alert('Error: ' + error);
  });
}

function validateForgotPassword() {
  const email = document.getElementById("forgot-email").value.trim();
  
  clearError("forgot-email");

  if (email === "") {
    showError("forgot-email", "Email is required");
    return;
  }

  alert("Reset link sent to your email!");
}