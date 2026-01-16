

<?php

require_once 'session_check.php';

require_once '../../db_config.php';

// Get fresh user data from database
$query = "SELECT user_id, full_name, email, phone_number, room_number, user_type, created_at 
          FROM users 
          WHERE user_id = ?";
$params = array($_SESSION['user_id']);
$stmt = sqlsrv_query($conn, $query, $params);

if ($stmt === false) {
    die("Database error: " . print_r(sqlsrv_errors(), true));
}

$userData = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
sqlsrv_free_stmt($stmt);

// Update session with latest data
if ($userData) {
    $_SESSION['username'] = $userData['full_name'];
    $_SESSION['email'] = $userData['email'];
}

// Calculate years since registration
$yearsSince = 0;
if (isset($userData['created_at'])) {
    $createdDate = $userData['created_at'];
    if ($createdDate instanceof DateTime) {
        $now = new DateTime();
        $diff = $now->diff($createdDate);
        $yearsSince = $diff->y;
        if ($yearsSince == 0) {
            $yearsSince = "Less than 1";
        }
    }
}

sqlsrv_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link rel="stylesheet" href="../styles/admin_accountsettings.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
</head>
<body>

<?php include 'main_file.php'; ?>

<div class="main-content">
    <h1 class="greeting">Account Settings</h1>
    <p class="subtitle">Welcome to your profile!</p>

    <div class="container">
        <!-- Profile Card -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="avatar-container">
                    <div class="avatar"><span class="material-symbols-rounded">person</span></div>
                </div>
                <h2 class="profile-name"><?php echo htmlspecialchars($userData['full_name']); ?></h2>
                <div class="profile-role">
                    <span class="material-symbols-rounded">admin_panel_settings</span> 
                    <?php echo ucfirst(htmlspecialchars($userData['user_type'])); ?>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon">
                        <span class="material-symbols-rounded">badge</span>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Full Name</div>
                        <div class="info-value" id="display-name"><?php echo htmlspecialchars($userData['full_name']); ?></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <span class="material-symbols-rounded">email</span>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Email Address</div>
                        <div class="info-value" id="display-email"><?php echo htmlspecialchars($userData['email']); ?></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <span class="material-symbols-rounded">phone</span>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value" id="display-phone"><?php echo htmlspecialchars($userData['phone_number']); ?></div>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <span class="material-symbols-rounded">date_range</span>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Member Since</div>
                        <div class="info-value"><?php echo $yearsSince; ?> year<?php echo ($yearsSince != 1 && is_numeric($yearsSince)) ? 's' : ''; ?></div>
                    </div>
                </div>
            </div>

            <button class="edit-profile-btn" onclick="openEditModal()">
                <span class="material-symbols-rounded">edit</span>
                Edit Profile
            </button>
        </div>

        <!-- Actions Card -->
        <div class="actions-card">
            <!-- Security Settings -->
            <div class="action-item">
                <div class="action-header">
                    <div class="action-icon-circle security">
                        <span class="material-symbols-rounded">lock</span>
                    </div>
                    <div class="action-content">
                        <h3 class="action-title">Security Settings</h3>
                        <p class="action-subtitle">Manage your login credentials and keep your account secure.</p>
                    </div>
                </div>
                <button class="action-btn security" onclick="openPasswordModal()">
                    <span class="material-symbols-rounded">key</span>
                    Change Password
                </button>
            </div>

            <!-- Logout -->
            <div class="action-item logout">
                <div class="action-header">
                    <div class="action-icon-circle logout">
                        <span class="material-symbols-rounded">logout</span>
                    </div>
                    <div class="action-content">
                        <h3 class="action-title">Logout</h3>
                        <p class="action-subtitle">End your current session and return to the login page.</p>
                    </div>
                </div>
                <button class="action-btn logout-btn" onclick="handleLogout()">
                    <span class="material-symbols-rounded">logout</span>
                    Logout Now
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">
                <span class="material-symbols-rounded">edit</span>
                Edit Your Profile
            </h2>
            <p class="modal-subtitle">Update your personal information to keep your profile accurate and up to date.</p>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">
                    <span class="material-symbols-rounded">badge</span>
                    Name
                </label>
                <input type="text" class="form-input" id="profileName" value="<?php echo htmlspecialchars($userData['full_name']); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <span class="material-symbols-rounded">email</span>
                    Email
                </label>
                <input type="email" class="form-input" id="profileEmail" value="<?php echo htmlspecialchars($userData['email']); ?>">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <span class="material-symbols-rounded">phone</span>
                    Phone Number
                </label>
                <input type="tel" class="form-input" id="profilePhone" value="<?php echo htmlspecialchars($userData['phone_number']); ?>">
            </div>

            <div class="error-message" id="editError"></div>
            <div class="success-message" id="editSuccess"></div>
        </div>
        <div class="modal-footer">
            <button class="btn-save" onclick="saveProfile()">
                <span class="material-symbols-rounded">check</span>
                Save
            </button>
            <button class="btn-cancel" onclick="closeEditModal()">
                <span class="material-symbols-rounded">close</span>
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal-overlay" id="passwordModal">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">
                <span class="material-symbols-rounded">key</span>
                Change Password
            </h2>
            <p class="modal-subtitle">Update your password to enhance your account's security.</p>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">
                    <span class="material-symbols-rounded">lock</span>
                    Current Password
                </label>
                <input type="password" class="form-input" id="currentPassword">
            </div>

            <div class="form-group">
                <label class="form-label">
                    <span class="material-symbols-rounded">lock_open</span>
                    New Password
                </label>
                <input type="password" class="form-input" id="newPassword">
                <small style="color: #666; font-size: 12px; margin-top: 4px; display: block;">
                    Must be at least 8 characters with uppercase, lowercase, and number
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <span class="material-symbols-rounded">verified_user</span>
                    Confirm New Password
                </label>
                <input type="password" class="form-input" id="confirmPassword">
            </div>

            <div class="error-message" id="passwordError"></div>
            <div class="success-message" id="passwordSuccess"></div>
        </div>
        <div class="modal-footer">
            <button class="btn-save" onclick="savePassword()">
                <span class="material-symbols-rounded">check</span>
                Save
            </button>
            <button class="btn-cancel" onclick="closePasswordModal()">
                <span class="material-symbols-rounded">close</span>
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    // Validation Functions
    function validateName(name) {
        if (!name || name.trim().length === 0) {
            return 'Name is required';
        }
        if (name.trim().length < 2) {
            return 'Name must be at least 2 characters long';
        }
        if (name.trim().length > 100) {
            return 'Name must not exceed 100 characters';
        }
        if (!/^[a-zA-Z\s'-]+$/.test(name)) {
            return 'Name can only contain letters, spaces, hyphens, and apostrophes';
        }
        return null;
    }

    function validateEmail(email) {
        if (!email || email.trim().length === 0) {
            return 'Email is required';
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            return 'Please enter a valid email address';
        }
        return null;
    }

    function validatePhone(phone) {
        if (!phone || phone.trim().length === 0) {
            return 'Phone number is required';
        }
        // 11-digit Philippine phone number
        if (!/^\d{11}$/.test(phone)) {
            return 'Phone number must be exactly 11 digits';
        }
        return null;
    }

    function validatePassword(password) {
        if (!password || password.length === 0) {
            return 'Password is required';
        }
        if (password.length < 8) {
            return 'Password must be at least 8 characters long';
        }
        if (!/[A-Z]/.test(password)) {
            return 'Password must contain at least one uppercase letter';
        }
        if (!/[a-z]/.test(password)) {
            return 'Password must contain at least one lowercase letter';
        }
        if (!/[0-9]/.test(password)) {
            return 'Password must contain at least one number';
        }
        return null;
    }

    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        
        const successElement = document.getElementById(elementId.replace('Error', 'Success'));
        if (successElement) {
            successElement.style.display = 'none';
        }
    }

    function showSuccess(elementId, message) {
        const successElement = document.getElementById(elementId);
        successElement.textContent = message;
        successElement.style.display = 'block';
        
        const errorElement = document.getElementById(elementId.replace('Success', 'Error'));
        if (errorElement) {
            errorElement.style.display = 'none';
        }
    }

    function clearMessages(prefix) {
        const errorElement = document.getElementById(prefix + 'Error');
        const successElement = document.getElementById(prefix + 'Success');
        if (errorElement) errorElement.style.display = 'none';
        if (successElement) successElement.style.display = 'none';
    }

    // Edit Profile Modal
    function openEditModal() {
        document.getElementById('editModal').classList.add('active');
        clearMessages('edit');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.remove('active');
        clearMessages('edit');
    }

    function saveProfile() {
        clearMessages('edit');
        
        const name = document.getElementById('profileName').value;
        const email = document.getElementById('profileEmail').value;
        const phone = document.getElementById('profilePhone').value;

        // Validate all fields
        const nameError = validateName(name);
        if (nameError) {
            showError('editError', nameError);
            return;
        }

        const emailError = validateEmail(email);
        if (emailError) {
            showError('editError', emailError);
            return;
        }

        const phoneError = validatePhone(phone);
        if (phoneError) {
            showError('editError', phoneError);
            return;
        }

        // Send AJAX request to update profile
        fetch('update_profile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                name: name,
                email: email,
                phone: phone
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('editSuccess', 'Profile updated successfully!');
                
                // Update the displayed values
                document.querySelector('.profile-name').textContent = name;
                document.getElementById('display-name').textContent = name;
                document.getElementById('display-email').textContent = email;
                document.getElementById('display-phone').textContent = phone;
                
                setTimeout(() => {
                    closeEditModal();
                }, 1500);
            } else {
                showError('editError', data.message || 'Failed to update profile');
            }
        })
        .catch(error => {
            showError('editError', 'An error occurred. Please try again.');
            console.error('Error:', error);
        });
    }

    // Change Password Modal
    function openPasswordModal() {
        document.getElementById('passwordModal').classList.add('active');
        clearMessages('password');
        document.getElementById('currentPassword').value = '';
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
    }

    function closePasswordModal() {
        document.getElementById('passwordModal').classList.remove('active');
        clearMessages('password');
    }

    function savePassword() {
        clearMessages('password');
        
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (!currentPassword || currentPassword.length === 0) {
            showError('passwordError', 'Current password is required');
            return;
        }

        const passwordError = validatePassword(newPassword);
        if (passwordError) {
            showError('passwordError', passwordError);
            return;
        }

        if (currentPassword === newPassword) {
            showError('passwordError', 'New password must be different from current password');
            return;
        }

        if (!confirmPassword || confirmPassword.length === 0) {
            showError('passwordError', 'Please confirm your new password');
            return;
        }

        if (newPassword !== confirmPassword) {
            showError('passwordError', 'New password and confirmation do not match');
            return;
        }

        // Send AJAX request to change password
        fetch('change_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('passwordSuccess', 'Password changed successfully!');
                setTimeout(() => {
                    closePasswordModal();
                }, 1500);
            } else {
                showError('passwordError', data.message || 'Failed to change password');
            }
        })
        .catch(error => {
            showError('passwordError', 'An error occurred. Please try again.');
            console.error('Error:', error);
        });
    }

    // Logout
    function handleLogout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'logout.php';
        }
    }

    // Close modal when clicking outside
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });

    document.getElementById('passwordModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePasswordModal();
        }
    });
</script>

<style>
    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 12px;
        border-radius: 8px;
        margin-top: 10px;
        font-size: 14px;
        display: none;
        border: 1px solid #c3e6cb;
    }
    
    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 12px;
        border-radius: 8px;
        margin-top: 10px;
        font-size: 14px;
        display: none;
        border: 1px solid #f5c6cb;
    }
</style>

</body>
</html>