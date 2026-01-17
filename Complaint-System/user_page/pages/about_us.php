<?php
// Start session if needed (optional, for user data)
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Complaint Desk</title>
    <link rel="stylesheet" href="styles/about_us.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="main-content">
        <h1 class="greeting">About Us</h1>
        <p class="subtitle">Learn more about Complaint Desk and how we can help you</p>
       
        <div class="about-container">
            <h2 class="section-title">About Complaint Desk</h2>
            
            <div class="about-section">
                <h3><i class="fas fa-info-circle"></i> What is Complaint Desk?</h3>
                <div class="about-content">
                    <p>Complaint Desk is a robust Complaint Management System designed to streamline the process of submitting, tracking, and resolving complaints. Our mission is to provide an efficient and transparent platform for users to voice their concerns and for administrators to manage these concerns effectively.</p>
                </div>
            </div>

            <div class="about-section">
                <h3><i class="fas fa-cogs"></i> How It Works</h3>
                <div class="about-content">
                    <p>Whether it's a maintenance issue, a neighbor dispute, or a general suggestion, Complaint Desk ensures that every complaint is logged, categorized, and assigned to the appropriate department for resolution. Users can track the status of their complaints in real-time, from "Pending" to "In Progress" and finally "Resolved."</p>
                </div>
            </div>

            <div class="about-section">
                <h3><i class="fas fa-users"></i> Our Team</h3>
                <div class="about-content">
                    <p>This system was developed through the collaborative efforts of:</p>
                    <ul class="values-list">
                        <li><strong>Arce, Corish Anne</strong> - Backend Development</li>
                        <li><strong>Cañares, Liberty Keith</strong> - Frontend Development</li>
                        <li><strong>Piolen, Angela</strong> - Backend Development & Database</li>
                        <li><strong>Raquedan, Angela</strong> - Frontend Development</li>
                    </ul>
                    <p>The frontend development was handled by Cañares, Liberty Keith, and Raquedan Angela, focusing on the user interface and overall user experience. The backend development and database management were handled by Arce, Corish Anne, and Piolen, Angela, ensuring reliable system functionality, secure data processing, and efficient data storage.</p>
                </div>
            </div>

            <div class="about-section">
                <h3><i class="fas fa-bullseye"></i> Our Mission</h3>
                <div class="about-content">
                    <p>Our goal is to foster a responsive and responsible environment where concerns are addressed promptly, leading to improved satisfaction and better community relations.</p>
                </div>
            </div>

            <div class="about-section contact-section">
                <h3><i class="fas fa-envelope"></i> Contact Us</h3>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-envelope contact-icon"></i>
                        <span>Email: support@complaintdesk.com</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone contact-icon"></i>
                        <span>Phone: 0900340090</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt contact-icon"></i>
                        <span>Location: Philippines</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock contact-icon"></i>
                        <span>Operating Hours: Mon-Fri, 9AM-6PM</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add hover effects for about sections
        document.addEventListener('DOMContentLoaded', function() {
            const aboutSections = document.querySelectorAll('.about-section');
            
            aboutSections.forEach(section => {
                section.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                
                section.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>