<?php
// Include database connection
require_once __DIR__ . '/../../db_config.php';


// Fetch all complaints grouped by status
$pendingQuery = "SELECT * FROM complaints WHERE status = 'Pending' ORDER BY created_date DESC";
$pendingComplaints = getAllRows($conn, $pendingQuery);

$inProgressQuery = "SELECT * FROM complaints WHERE status = 'In Progress' ORDER BY last_updated DESC";
$inProgressComplaints = getAllRows($conn, $inProgressQuery);

$resolvedQuery = "SELECT * FROM complaints WHERE status = 'Resolved' ORDER BY resolved_date DESC";
$resolvedComplaints = getAllRows($conn, $resolvedQuery);

// Function to get records for a complaint
function getComplaintRecords($conn, $complaintId) {
    $query = "SELECT * FROM complaint_records WHERE complaint_id = ? ORDER BY log_timestamp ASC";
    return getAllRows($conn, $query, array($complaintId));
}
?>

<link rel="stylesheet" href="../styles/manage_complaints.css">
<?php include 'main_file.php'; ?>

<!-- Manage Complaints Page -->
<div id="complaints-page" class="main-content">
    <h1 class="greeting">Manage Complaints</h1>
    <p class="subtitle">Manage all complaints in the system</p>

    <!-- Container for both sections -->
    <div class="top-section">
        <!-- Handle Complaint -->
        <div class="handle-menu">
            <div class="menu-title">Handle Complaint</div>
            <form class="menu-form" method="POST" action="handle_complaint.php">
                <div class="form-group">
                    <label for="complaint-id">Complaint ID</label>
                    <input type="text" id="complaint-id" name="complaint_id" placeholder="Enter ID (e.g., C1)" required>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="">Select status</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Resolved">Resolved</option>
                    </select>
                </div>

                <div class="form-group notes-group">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" placeholder="Add notes..." required></textarea>
                </div>

                <button type="submit" class="submit-btn">Submit</button>
            </form>
        </div>

        <!-- Filter Container -->
        <div class="filter-container">
            <div class="filter-title">Filter By</div>
            <form method="GET" action="">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="filter-type">Complaint Type</label>
                        <select id="filter-type" name="type">
                            <option value="">All Types</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Facility">Facility</option>
                            <option value="Noise">Noise Complaint</option>
                            <option value="Security">Security</option>
                            <option value="Billing">Billing</option>
                            <option value="Neighbor">Neighbor</option>
                            <option value="Other">Others</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filter-date">Date</label>
                        <input type="date" id="filter-date" name="date">
                    </div>
                    <div class="filter-group">
                        <label for="filter-room">Room Number</label>
                        <select id="filter-room" name="room">
                            <option value="">All Rooms</option>
                            <optgroup label="1st Floor">
                                <?php for($i = 101; $i <= 110; $i++) echo "<option>$i</option>"; ?>
                            </optgroup>
                            <optgroup label="2nd Floor">
                                <?php for($i = 201; $i <= 210; $i++) echo "<option>$i</option>"; ?>
                            </optgroup>
                            <optgroup label="3rd Floor">
                                <?php for($i = 301; $i <= 310; $i++) echo "<option>$i</option>"; ?>
                            </optgroup>
                            <optgroup label="4th Floor">
                                <?php for($i = 401; $i <= 410; $i++) echo "<option>$i</option>"; ?>
                            </optgroup>
                            <optgroup label="5th Floor">
                                <?php for($i = 501; $i <= 510; $i++) echo "<option>$i</option>"; ?>
                            </optgroup>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Kanban Columns -->
    <h2 class="section-title">Complaints Overview</h2>
    <div class="kanban-container">
        
        <!-- Pending Column -->
        <div class="kanban-column">
            <div class="kanban-header pending">
                <h3>Pending</h3>
                <span class="column-count"><?php echo count($pendingComplaints); ?></span>
            </div>
            <div class="kanban-cards">
                <?php foreach ($pendingComplaints as $complaint): ?>
                <div class="complaint-card">
                    <div class="card-id"><?php echo htmlspecialchars($complaint['complaint_id']); ?></div>
                    <div class="card-info">
                        <span>🏠 Room <?php echo htmlspecialchars($complaint['room_number']); ?></span>
                        <span>👤 <?php echo htmlspecialchars($complaint['tenant_name']); ?></span>
                    </div>
                    <div class="type-badge"><?php echo htmlspecialchars($complaint['complaint_type']); ?></div>
                    <div class="card-description"><?php echo htmlspecialchars($complaint['description']); ?></div>
                    <div class="card-date">📅 <?php echo formatDate($complaint['created_date']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="kanban-column">
            <div class="kanban-header progress">
                <h3>In Progress</h3>
                <span class="column-count"><?php echo count($inProgressComplaints); ?></span>
            </div>
            <div class="kanban-cards">
                <?php foreach ($inProgressComplaints as $complaint): 
                    $records = getComplaintRecords($conn, $complaint['complaint_id']);
                ?>
                <div class="complaint-card">
                    <div class="card-id"><?php echo htmlspecialchars($complaint['complaint_id']); ?></div>
                    <div class="card-info">
                        <span>🏠 Room <?php echo htmlspecialchars($complaint['room_number']); ?></span>
                        <span>👤 <?php echo htmlspecialchars($complaint['tenant_name']); ?></span>
                    </div>
                    <div class="type-badge"><?php echo htmlspecialchars($complaint['complaint_type']); ?></div>
                    <div class="card-description"><?php echo htmlspecialchars($complaint['description']); ?></div>
                    <div class="card-date">📅 <?php echo formatDate($complaint['created_date']); ?></div>
                    <?php if ($complaint['last_updated']): ?>
                    <div class="last-update">🔄 Updated: <?php echo formatDate($complaint['last_updated']); ?></div>
                    <?php endif; ?>
                    
                    <?php if (count($records) > 0): ?>
                    <div class="records-title">Records</div>
                    <div class="record-logs-container">
                        <?php foreach ($records as $record): ?>
                        <div class="record-log">
                            <span class="log-timestamp"><?php echo formatDate($record['log_timestamp']); ?>:</span>
                            <span class="log-message"><?php echo htmlspecialchars($record['log_message']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Resolved Column -->
        <div class="kanban-column">
            <div class="kanban-header resolved">
                <h3>Resolved</h3>
                <span class="column-count"><?php echo count($resolvedComplaints); ?></span>
            </div>
            <div class="kanban-cards">
                <?php foreach ($resolvedComplaints as $complaint): 
                    $records = getComplaintRecords($conn, $complaint['complaint_id']);
                ?>
                <div class="complaint-card">
                    <div class="card-header">
                        <div class="card-id"><?php echo htmlspecialchars($complaint['complaint_id']); ?></div>
                        <form method="POST" action="delete_complaint.php" style="display: inline;">
                            <input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars($complaint['complaint_id']); ?>">
                            <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this complaint?')">
                                <span class="material-symbols-rounded" style="font-size: 20px;">delete</span>
                                Delete
                            </button>
                        </form>
                    </div>

                    <div class="card-info">
                        <span>🏠 Room <?php echo htmlspecialchars($complaint['room_number']); ?></span>
                        <span>👤 <?php echo htmlspecialchars($complaint['tenant_name']); ?></span>
                    </div>
                    <div class="type-badge"><?php echo htmlspecialchars($complaint['complaint_type']); ?></div>
                    <div class="card-description"><?php echo htmlspecialchars($complaint['description']); ?></div>
                    <div class="card-date">📅 <?php echo formatDate($complaint['created_date']); ?></div>
                    <?php if ($complaint['resolved_date']): ?>
                    <div class="last-update" style="color: #66BB6A;">✅ Resolved: <?php echo formatDate($complaint['resolved_date']); ?></div>
                    <?php endif; ?>

                    <?php if (count($records) > 0): ?>
                    <div class="records-title">Records</div>
                    <div class="record-logs-container">
                        <?php foreach ($records as $record): ?>
                        <div class="record-log">
                            <span class="log-timestamp"><?php echo formatDate($record['log_timestamp']); ?>:</span>
                            <span class="log-message"><?php echo htmlspecialchars($record['log_message']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Removed the old handleComplaint function since we're using PHP form submission now
</script>

<?php
// Close database connection
closeConnection($conn);
?>