<?php
// ========================================
// MANAGE USERS PAGE - DYNAMIC VERSION
// File: manage_users.php
// ========================================

require_once __DIR__ . '/../../db_config.php';

// ========================================
// FETCH FILTERS FROM URL
// ========================================
$searchName = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterRoom = isset($_GET['room']) ? trim($_GET['room']) : '';
$sortName = isset($_GET['sort_name']) ? $_GET['sort_name'] : 'az';
$sortDate = isset($_GET['sort_date']) ? $_GET['sort_date'] : 'newest';

// ========================================
// BUILD QUERY FOR ADMINS
// ========================================
$adminQuery = "SELECT * FROM users WHERE user_type = 'Admin'";
$adminParams = array();

if (!empty($searchName)) {
    $adminQuery .= " AND full_name LIKE ?";
    $adminParams[] = "%$searchName%";
}

// Sort by name
if ($sortName == 'az') {
    $adminQuery .= " ORDER BY full_name ASC";
} else {
    $adminQuery .= " ORDER BY full_name DESC";
}

// Override with date sort if needed
if ($sortDate == 'newest') {
    $adminQuery .= ", created_at DESC";
} else {
    $adminQuery .= ", created_at ASC";
}

$admins = getAllRows($conn, $adminQuery, $adminParams);

// ========================================
// BUILD QUERY FOR REGULAR USERS
// ========================================
$userQuery = "SELECT * FROM users WHERE user_type = 'User'";
$userParams = array();

if (!empty($searchName)) {
    $userQuery .= " AND full_name LIKE ?";
    $userParams[] = "%$searchName%";
}

if (!empty($filterRoom)) {
    $userQuery .= " AND room_number = ?";
    $userParams[] = $filterRoom;
}

// Sort by name
if ($sortName == 'az') {
    $userQuery .= " ORDER BY full_name ASC";
} else {
    $userQuery .= " ORDER BY full_name DESC";
}

// Override with date sort if needed
if ($sortDate == 'newest') {
    $userQuery .= ", created_at DESC";
} else {
    $userQuery .= ", created_at ASC";
}

$users = getAllRows($conn, $userQuery, $userParams);
?>

<link rel="stylesheet" href="../styles/manage_users.css">
<?php include 'main_file.php'; ?>

<!-- Manage Users Page -->
<div id="users-page" class="main-content">
    <h1 class="greeting">Manage Users</h1>
    <p class="subtitle">View and manage all registered users in the system</p>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <?php 
                if ($_GET['success'] == 'admin_added') echo '✅ Admin successfully added!';
                if ($_GET['success'] == 'user_deleted') echo '✅ User successfully deleted!';
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <?php 
                if ($_GET['error'] == 'empty_fields') echo '❌ All fields are required!';
                if ($_GET['error'] == 'email_exists') echo '❌ Email already exists!';
                if ($_GET['error'] == 'insert_failed') echo '❌ Failed to add admin!';
                if ($_GET['error'] == 'cannot_delete_admin') echo '❌ Cannot delete admin accounts!';
                if ($_GET['error'] == 'user_not_found') echo '❌ User not found!';
                if ($_GET['error'] == 'delete_failed') echo '❌ Failed to delete user!';
            ?>
        </div>
    <?php endif; ?>

    <!-- Control Wrapper -->
    <div class="top-section">
        <!-- Controls Container -->
        <div class="controls">
            <form method="GET" action="" id="filterForm">
                <div class="filter-grid">
                    <!-- Search -->
                    <div class="filter-group">
                        <label for="searchInput">Search by Name</label>
                        <div class="search-wrapper">
                            <span class="material-symbols-rounded search-icon">search</span>
                            <input type="text" id="searchInput" name="search" placeholder="Search by name..." 
                                   value="<?php echo htmlspecialchars($searchName); ?>" onchange="submitFilter()">
                        </div>
                    </div>

                    <!-- Sort by Alphabetical Order -->
                    <div class="filter-group">
                        <label for="sortAlphabetSelect">Sort by Name</label>
                        <select id="sortAlphabetSelect" name="sort_name" onchange="submitFilter()">
                            <option value="az" <?php echo $sortName == 'az' ? 'selected' : ''; ?>>A to Z</option>
                            <option value="za" <?php echo $sortName == 'za' ? 'selected' : ''; ?>>Z to A</option>
                        </select>
                    </div>

                    <!-- Sort by Room Number -->
                    <div class="filter-group">
                        <label for="sortRoomnumberselect">Sort by Room Number</label>
                        <select id="sortRoomnumberselect" name="room" onchange="submitFilter()">
                            <option value="">All Rooms</option>
                            <optgroup label="1st Floor">
                                <?php for($i = 101; $i <= 110; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $filterRoom == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </optgroup>
                            <optgroup label="2nd Floor">
                                <?php for($i = 201; $i <= 210; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $filterRoom == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </optgroup>
                            <optgroup label="3rd Floor">
                                <?php for($i = 301; $i <= 310; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $filterRoom == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </optgroup>
                            <optgroup label="4th Floor">
                                <?php for($i = 401; $i <= 410; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $filterRoom == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </optgroup>
                            <optgroup label="5th Floor">
                                <?php for($i = 501; $i <= 510; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $filterRoom == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Sort by Date -->
                    <div class="filter-group">
                        <label for="sortDateSelect">Sort by Date Joined</label>
                        <select id="sortDateSelect" name="sort_date" onchange="submitFilter()">
                            <option value="newest" <?php echo $sortDate == 'newest' ? 'selected' : ''; ?>>Newest to Oldest</option>
                            <option value="oldest" <?php echo $sortDate == 'oldest' ? 'selected' : ''; ?>>Oldest to Newest</option>
                        </select>
                    </div>
                </div>
            </form>

            <button class="add-admin-btn" onclick="openAddAdminModal()">
                <span class="material-symbols-rounded">person_add</span>
                Add New Admin
            </button>
        </div>
    </div>

    <!-- Add New Admin Modal -->
    <div class="modal-overlay" id="addAdminModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">
                    <span class="material-symbols-rounded">person_add</span>
                    Add New Admin
                </h2>
                <p class="modal-subtitle">Add new administrator account. Please handle everything with transparency and responsibility.</p>
            </div>

            <form method="POST" action="add_admin.php" id="addAdminForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="profileName">
                            <span class="material-symbols-rounded">badge</span>
                            Name
                        </label>
                        <input type="text" id="profileName" name="full_name" placeholder="Enter full name" required>
                    </div>

                    <div class="form-group">
                        <label for="profileEmail">
                            <span class="material-symbols-rounded">email</span>
                            Email
                        </label>
                        <input type="email" id="profileEmail" name="email" placeholder="admin@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="profilePhone">
                            <span class="material-symbols-rounded">phone</span>
                            Phone Number
                        </label>
                        <input type="tel" id="profilePhone" name="phone_number" placeholder="09123456789" required>
                    </div>

                    <div class="form-group">
                        <label for="profilePassword">
                            <span class="material-symbols-rounded">lock</span>
                            Password
                        </label>
                        <input type="password" id="profilePassword" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="profileConfirmPassword">
                            <span class="material-symbols-rounded">lock_open</span>
                            Confirm Password
                        </label>
                        <input type="password" id="profileConfirmPassword" required>
                        <small style="color: #666; font-size: 12px; margin-top: 4px; display: block;">
                            Must be at least 8 characters with uppercase, lowercase, number, and special character
                        </small>
                    </div>

                    <div class="error-message" id="editError"></div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="submit-btn">
                        <span class="material-symbols-rounded">check</span> Save
                    </button>
                    <button type="button" class="cancel-btn" onclick="closeAddAdminModal()">
                        <span class="material-symbols-rounded">close</span> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Kanban Columns -->
    <h2 class="section-title">Users Overview</h2>
    <div class="kanban-container">
    
        <!-- Admins Column -->
        <div class="kanban-column">
            <div class="kanban-header admins">
                <h3>Administrators</h3>
                <span class="column-count"><?php echo count($admins); ?></span>
            </div>
            <div class="kanban-cards">
                <?php if (empty($admins)): ?>
                    <div class="empty-state">No administrators found</div>
                <?php else: ?>
                    <?php foreach ($admins as $admin): ?>
                    <div class="user-card">
                        <button class="delete-btn" disabled>
                            <span class="material-symbols-rounded" style="font-size: 20px;">delete</span>
                            Remove
                        </button>
                        <div class="card-header">
                            <span class="user-id-badge">ID: <?php echo str_pad($admin['user_id'], 3, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        <div class="user-info">
                            <div class="info-row">
                                <span class="material-symbols-rounded info-icon">person</span>
                                <span class="info-label">Name:</span>
                                <span class="info-value"><?php echo htmlspecialchars($admin['full_name']); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="material-symbols-rounded info-icon">email</span>
                                <span class="info-label">Email:</span>
                                <span class="info-value"><?php echo htmlspecialchars($admin['email']); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="material-symbols-rounded info-icon">phone</span>
                                <span class="info-label">Phone:</span>
                                <span class="info-value"><?php echo htmlspecialchars($admin['phone_number']); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="material-symbols-rounded info-icon">calendar_today</span>
                                <span class="info-label">Date Joined:</span>
                                <span class="info-value"><?php echo formatDate($admin['created_at']); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Users Column -->
        <div class="kanban-column">
            <div class="kanban-header users">
                <h3>Users</h3>
                <span class="column-count"><?php echo count($users); ?></span>
            </div>
            <div class="kanban-cards">
                <?php if (empty($users)): ?>
                    <div class="empty-state">No users found</div>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                    <div class="user-card">
                        <form method="POST" action="delete_user.php" style="display: inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                            <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to remove this user?')">
                                <span class="material-symbols-rounded" style="font-size: 20px;">delete</span>
                                Remove
                            </button>
                        </form>
                        <div class="card-header">
                            <span class="user-id-badge">ID: <?php echo str_pad($user['user_id'], 3, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        <div class="user-info">
                            <div class="info-row">
                                <span class="material-symbols-rounded info-icon">person</span>
                                <span class="info-label">Name:</span>
                                <span class="info-value"><?php echo htmlspecialchars($user['full_name']); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="material-symbols-rounded info-icon">email</span>
                                <span class="info-label">Email:</span>
                                <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="material-symbols-rounded info-icon">phone</span>
                                <span class="info-label">Phone:</span>
                                <span class="info-value"><?php echo htmlspecialchars($user['phone_number']); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="material-symbols-rounded info-icon">home</span>
                                <span class="info-label">Room No.:</span>
                                <span class="info-value"><?php echo htmlspecialchars($user['room_number']); ?></span>
                            </div>
                            <div class="info-row">
                                <span class="material-symbols-rounded info-icon">calendar_today</span>
                                <span class="info-label">Date Joined:</span>
                                <span class="info-value"><?php echo formatDate($user['created_at']); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
    // Auto-submit filter form when inputs change
    function submitFilter() {
        document.getElementById('filterForm').submit();
    }

    // Validation Functions
    function validateName(name) {
        if (!name || name.trim().length === 0) return 'Name is required';
        if (name.trim().length < 2) return 'Name must be at least 2 characters long';
        if (name.trim().length > 100) return 'Name must not exceed 100 characters';
        if (!/^[a-zA-Z\s'-]+$/.test(name)) return 'Name can only contain letters, spaces, hyphens, and apostrophes';
        return null;
    }

    function validateEmail(email) {
        if (!email || email.trim().length === 0) return 'Email is required';
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) return 'Please enter a valid email address';
        return null;
    }

    function validatePhone(phone) {
        if (!phone || phone.trim().length === 0) return 'Phone number is required';
        const digitsOnly = phone.replace(/\D/g, '');
        if (digitsOnly.length > 11) return 'Phone number must not exceed 11 digits';
        const phoneRegex = /^(09|\+639)\d{9}$/;
        if (!phoneRegex.test(digitsOnly)) return 'Please enter a valid Philippine phone number';
        return null;
    }
    
    function validatePassword(password, confirmPassword) {
        if (!password || password.trim().length === 0) return 'Password is required';
        if (password.length < 8) return 'Password must be at least 8 characters';
        if (!/[A-Z]/.test(password)) return 'Password must contain at least one uppercase letter';
        if (!/[a-z]/.test(password)) return 'Password must contain at least one lowercase letter';
        if (!/[0-9]/.test(password)) return 'Password must contain at least one number';
        if (!/[!@#$%^&*(),.?":{}|<>_]/.test(password)) return 'Password must contain at least one special character';
        if (!confirmPassword || confirmPassword.trim().length === 0) return 'Please confirm your password';
        if (password !== confirmPassword) return 'Passwords do not match';
        return null;
    }

    // Modal Functions
function openAddAdminModal() {
    const modal = document.getElementById('addAdminModal');
    modal.classList.add('active');
    
    // Prevent body scrolling
    document.body.style.overflow = 'hidden';
    
    // Force lower z-index on navigation elements
    const sidebar = document.querySelector('.sidebar');
    const header = document.querySelector('header');
    const nav = document.querySelector('nav');
    const topNav = document.querySelector('.top-nav');
    
    if (sidebar) sidebar.style.zIndex = '1';
    if (header) header.style.zIndex = '1';
    if (nav) nav.style.zIndex = '1';
    if (topNav) topNav.style.zIndex = '1';
}

function closeAddAdminModal() {
    const modal = document.getElementById('addAdminModal');
    modal.classList.remove('active');
    clearError();
    
    // Restore body scrolling
    document.body.style.overflow = '';
    
    // Restore z-index
    const sidebar = document.querySelector('.sidebar');
    const header = document.querySelector('header');
    const nav = document.querySelector('nav');
    const topNav = document.querySelector('.top-nav');
    
    if (sidebar) sidebar.style.zIndex = '';
    if (header) header.style.zIndex = '';
    if (nav) nav.style.zIndex = '';
    if (topNav) topNav.style.zIndex = '';
}

    function showError(message) {
        const errorDiv = document.getElementById('editError');
        errorDiv.textContent = message;
        errorDiv.classList.add('active');
    }

    function clearError() {
        const errorDiv = document.getElementById('editError');
        errorDiv.textContent = '';
        errorDiv.classList.remove('active');
    }

    // Form submission validation
    document.getElementById('addAdminForm').addEventListener('submit', function(e) {
        clearError();
        
        const name = document.getElementById('profileName').value;
        const email = document.getElementById('profileEmail').value;
        const phone = document.getElementById('profilePhone').value;
        const password = document.getElementById('profilePassword').value;
        const confirmPassword = document.getElementById('profileConfirmPassword').value;

        const nameError = validateName(name);
        if (nameError) {
            e.preventDefault();
            showError(nameError);
            return;
        }

        const emailError = validateEmail(email);
        if (emailError) {
            e.preventDefault();
            showError(emailError);
            return;
        }

        const phoneError = validatePhone(phone);
        if (phoneError) {
            e.preventDefault();
            showError(phoneError);
            return;
        }

        const passwordError = validatePassword(password, confirmPassword);
        if (passwordError) {
            e.preventDefault();
            showError(passwordError);
            return;
        }
    });

function openAddAdminModal() {
    const modal = document.getElementById('addAdminModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // HIDE navigation completely
    document.querySelectorAll('header, nav, .sidebar, .navbar').forEach(el => {
        el.style.visibility = 'hidden';
    });
}

function closeAddAdminModal() {
    const modal = document.getElementById('addAdminModal');
    modal.classList.remove('active');
    clearError();
    document.body.style.overflow = '';
    
    // SHOW navigation again
    document.querySelectorAll('header, nav, .sidebar, .navbar').forEach(el => {
        el.style.visibility = '';
    });
}

// Close modal when clicking outside
document.getElementById('addAdminModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddAdminModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('addAdminModal');
        if (modal.classList.contains('active')) {
            closeAddAdminModal();
        }
    }
});

</script>

<?php
// Close database connection
closeConnection($conn);
?>
