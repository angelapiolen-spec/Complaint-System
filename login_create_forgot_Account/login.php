<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Complaint Desk - Login / Create Account</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="login_style.css" />
  <style>
    .form-section { display: none; }
    .form-section.active { display: block; }
  </style>
</head>
<body>
  <div class="container">
    <!-- LEFT PANEL -->
    <div class="left-panel">
      <div class="logo-container">
        <img src="LOGO1.png" alt="LOGO" class="logo" />
        <span class="logo-text-complaint">Complaint</span>
        <span class="logo-text-desk">Desk</span>
      </div>

      <div class="left-content">
        <img src="BG.png" alt="Complaint Desk Illustration" class="background-image" />
        <p class="tagline">
          Your voice matters. <br />We're here to listen and <br />help resolve your concerns.
        </p>
      </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
      <div class="form-container">

        <!-- ==================== LOGIN FORM ==================== -->
        <div class="form-section active" id="loginForm">
          <div class="header-text">
            <h1>Welcome Back</h1>
            <p>Enter your credentials to access your account</p>
          </div>

          <form action="login_handler.php" method="POST">
            <div class="form-group">
              <label for="login-email">Email</label>
              <input type="email" id="login-email" name="email" placeholder="Enter your email" required />
            </div>

            <div class="form-group">
              <label for="login-password">Password</label>
              <input type="password" id="login-password" name="password" placeholder="Enter your password" required />
            </div>

            <div class="checkbox-group">
              <label><input type="checkbox"/> Remember me</label>
            </div>

            <button type="submit" class="submit-btn">Log In</button>
          </form>

          <div class="toggle-form">
            Don't have an account? <a href="#" onclick="event.preventDefault(); toggleForms()">Sign Up</a>
          </div>
        </div>

        <!-- ==================== SIGN UP FORM ==================== -->
        <div class="form-section" id="signupForm">
          <div class="header-text">
            <h1>Create Account</h1>
            <p>Fill in your details to get started</p>
          </div>

          <form action="signup_handler.php" method="POST" onsubmit="return validateSignup()">
            <div class="form-group">
              <label for="signup-name">Full Name</label>
              <input type="text" id="signup-name" name="username" placeholder="Enter your full name" required />
            </div>

            <div class="form-group">
              <label for="signup-email">Email</label>
              <input type="email" id="signup-email" name="email" placeholder="Enter your email" required />
            </div>

            <div class="form-group">
              <label for="signup-password">Password</label>
              <input type="password" id="signup-password" name="password" placeholder="Create a password" required />
            </div>

            <div class="form-group">
              <label for="signup-phone">Phone Number</label>
              <input type="tel" id="signup-phone" name="phone_number" placeholder="Enter 11-digit phone number" required />
              <p class="error-message" id="phone-error"></p>
            </div>

            <div class="form-group">
              <label for="signup-room">Room Number</label>
              <input type="text" id="signup-room" name="room_number" placeholder="Enter your room number" required />
            </div>
            
            <button type="submit" class="submit-btn">Create Account</button>
          </form>

          <div class="toggle-form">
            Already have an account? <a href="#" onclick="event.preventDefault(); toggleForms()">Sign In</a>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- ==================== JAVASCRIPT ==================== -->
  <script>
    // Toggle Login â†” Signup
    function toggleForms() {
      document.getElementById("loginForm").classList.toggle("active");
      document.getElementById("signupForm").classList.toggle("active");
      document.getElementById("phone-error").textContent = '';
    }

    // Validate signup form
    function validateSignup() {
      const phone = document.getElementById("signup-phone").value;
      const errorElement = document.getElementById("phone-error");
      
      // Check if phone number is exactly 11 digits
      if (!/^\d{11}$/.test(phone)) {
        errorElement.textContent = "Phone number must be exactly 11 digits";
        return false;
      }
      
      errorElement.textContent = "";
      return true;
    }
  </script>
</body>
</html>