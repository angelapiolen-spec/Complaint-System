<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Management System - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link rel="stylesheet" href="..//styles/main_navbar.css">
</head>

<body>
    <!-- Top Bar -->
    <header class="top-bar">
        <!-- Logo -->
        <div class="logo-container">
            <img src="LOGO1.png" alt="LOGO" class="logo" />
            <span class="logo-text-complaint">Complaint</span>
            <span class="logo-text-desk">Desk</span>
        </div>

        <nav class="nav-menu">
            <a href="../html_pages/dashboard.php" class="nav-link">Dashboard</a>
            <a href="../html_pages/manage_complaints.php" class="nav-link">Manage Complaints</a>
            <a href="../html_pages/manage_users.php" class="nav-link">Manage Users</a>
            <a href="http://localhost/Complaint-System/admin_page/html_pages/admin_accountsettings.php" class="nav-link">Account Settings</a>
        </nav>


        <!-- Hamburger Menu -->
        <div class="menu-toggle" id="menuToggle" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </header>

    <!-- Main Content Area -->
    <div id="content-container"></div>

    <script>
        // Track the current active page
        let currentPage = 'dashboard';

        // Navigation function
        function navigateTo(event, page) {
            event.preventDefault();
            currentPage = page;

            // Update active link styling
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            if (event && event.currentTarget) {
                event.currentTarget.classList.add('active');
            }

            // Close mobile menu if open
            const navMenu = document.getElementById('navMenu');
            navMenu.classList.remove('open');

            // Define file paths for each page
            const files = {
            dashboard: '../html_pages/dashboard.php',
            manage_complaints: '../html_pages/manage_complaints.php',
            manage_users: '../html_pages/manage_users.php',
            account_settings: '../html_pages/admin_accountsettings.php'
            };

            // Load the selected HTML page
            fetch(files[page])
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Page not found: ' + files[page]);
                    }
                    return response.text();
                })
                .then(html => {
                    const container = document.getElementById('content-container');
                    container.innerHTML = html;

                    // Run scripts inside the loaded content
                    const scripts = container.querySelectorAll('script');
                    scripts.forEach(script => {
                        const newScript = document.createElement('script');
                        newScript.textContent = script.textContent;
                        document.body.appendChild(newScript);
                        document.body.removeChild(newScript);
                    });
                })
                .catch(error => {
                    console.error('Error loading page:', error);
                    document.getElementById('content-container').innerHTML = 
                        '<div style="color: white; padding: 2rem; text-align: center;">Error loading page. Please try again.</div>';
                });
        }

        // Automatically load the Dashboard on first load
        document.addEventListener('DOMContentLoaded', () => {
            const dashboardLink = document.querySelector('.nav-link.active');
            navigateTo({ preventDefault: () => {}, currentTarget: dashboardLink }, 'dashboard');
        });

        function toggleMenu() {
            const navMenu = document.getElementById('navMenu');
            navMenu.classList.toggle('open');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const navMenu = document.getElementById('navMenu');
            const menuToggle = document.getElementById('menuToggle');
            
            if (!navMenu.contains(event.target) && !menuToggle.contains(event.target)) {
                navMenu.classList.remove('open');
            }
        });
    </script>
</body>

</html>
