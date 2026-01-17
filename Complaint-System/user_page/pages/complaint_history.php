<?php
// Get filter values
$filter_type = isset($_GET['type']) ? $_GET['type'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';
$filter_month = isset($_GET['month']) ? $_GET['month'] : 'all';

// Get ALL resolved complaints
$sql = "SELECT * FROM complaints WHERE user_id = :user_id AND status = 'resolved'";
$params = ['user_id' => $user['id']];

if (!empty($filter_type)) {
    $sql .= " AND complaint_type = :type";
    $params['type'] = $filter_type;
}

if (!empty($filter_date)) {
    $sql .= " AND CAST(created_at AS DATE) = :filter_date";
    $params['filter_date'] = $filter_date;
}

if ($filter_month != 'all') {
    $month_parts = explode('_', $filter_month);
    $month_num = date('m', strtotime($month_parts[0]));
    $year = $month_parts[1];
    $sql .= " AND MONTH(created_at) = :month AND YEAR(created_at) = :year";
    $params['month'] = $month_num;
    $params['year'] = $year;
}

$sql .= " ORDER BY COALESCE(resolved_date, updated_at) DESC, created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$db_complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to format date
function formatDate($date) {
    return date('M d, Y h:i A', strtotime($date));
}

// Sample complaints (same as before)
$sample_complaints = [
    [
        'id' => 3,
        'complaint_type' => 'billing',
        'description' => 'Incorrect charges on monthly statement',
        'created_at' => '2025-10-13 10:00:00',
        'resolved_date' => '2025-10-17 15:45:00',
        'room' => '302',
        'name' => 'Bob Wilson',
        'records' => [
            ['timestamp' => '2025-10-13 10:00:00', 'message' => 'Billing discrepancy reported'],
            ['timestamp' => '2025-10-13 11:30:00', 'message' => 'Finance team notified'],
            ['timestamp' => '2025-10-15 09:00:00', 'message' => 'Billing records reviewed and verified'],
            ['timestamp' => '2025-10-17 14:00:00', 'message' => 'Credit issued to tenant account'],
            ['timestamp' => '2025-10-17 15:45:00', 'message' => 'Tenant notified and confirmed resolution']
        ]
    ],
    [
        'id' => 6,
        'complaint_type' => 'maintenance',
        'description' => 'Broken window lock needs replacement',
        'created_at' => '2025-10-12 08:30:00',
        'resolved_date' => '2025-10-16 14:00:00',
        'room' => '210',
        'name' => 'Emily Davis',
        'records' => [
            ['timestamp' => '2025-10-12 08:30:00', 'message' => 'Window lock repair requested'],
            ['timestamp' => '2025-10-12 09:15:00', 'message' => 'Maintenance team assigned'],
            ['timestamp' => '2025-10-14 14:30:00', 'message' => 'Lock replacement scheduled'],
            ['timestamp' => '2025-10-16 10:00:00', 'message' => 'Repair work completed'],
            ['timestamp' => '2025-10-16 14:00:00', 'message' => 'Inspection passed and approved by tenant']
        ]
    ]
];

// Combine database and sample complaints
$all_complaints = array_merge($db_complaints, $sample_complaints);

// Apply filters
$filtered_complaints = [];
foreach ($all_complaints as $complaint) {
    $include = true;
    if (!empty($filter_type) && isset($complaint['complaint_type'])) {
        if ($complaint['complaint_type'] != $filter_type) $include = false;
    }
    if ($include) $filtered_complaints[] = $complaint;
}

$total_complaints = count($filtered_complaints);
?>

<link rel="stylesheet" href="styles/user_front.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,1,0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Include complaints.js -->
<script src="js/complaints.js"></script>

<style>
/* Action Buttons Styles */
.action-buttons {
    display: flex;
    gap: 8px;
    margin-top: 8px;
}

.action-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
}

.action-btn.delete {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.action-btn.delete:hover {
    background: #fee2e2;
    color: #b91c1c;
    border-color: #fca5a5;
    transform: translateY(-1px);
}

.action-btn.export {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}

.action-btn.export:hover {
    background: #dbeafe;
    color: #1e40af;
    border-color: #93c5fd;
    transform: translateY(-1px);
}

.action-btn .material-symbols-rounded {
    font-size: 16px;
}

@media (max-width: 768px) {
    .action-buttons {
        flex-direction: column;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="main-content">
    <h1 class="greeting">Complaint History</h1>
    <p class="subtitle">See your resolved complaint logs here.</p>
    
    <!-- Filter Container -->
    <div class="filter-container">
        <div class="filter-header">
            <div class="filter-title">Filter Complaints</div>
            <button class="clear-filters-btn" onclick="clearFilters()">Clear All</button>
        </div>

        <div class="filter-grid">
            <div class="filter-group">
                <label for="filter-type">Complaint Type</label>
                <select id="filter-type" onchange="applyFilters()">
                    <option value="">All Types</option>
                    <option value="maintenance" <?php echo $filter_type=='maintenance'?'selected':''; ?>>Maintenance</option>
                    <option value="facility" <?php echo $filter_type=='facility'?'selected':''; ?>>Facility</option>
                    <option value="noise" <?php echo $filter_type=='noise'?'selected':''; ?>>Noise</option>
                    <option value="billing" <?php echo $filter_type=='billing'?'selected':''; ?>>Billing</option>
                    <option value="neighbor" <?php echo $filter_type=='neighbor'?'selected':''; ?>>Neighbor</option>
                    <option value="security" <?php echo $filter_type=='security'?'selected':''; ?>>Security</option>
                    <option value="other" <?php echo $filter_type=='other'?'selected':''; ?>>Other</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="filter-date">Specific Date</label>
                <input type="date" id="filter-date" value="<?php echo $filter_date; ?>" onchange="applyFilters()">
            </div>

            <div class="filter-group">
                <label for="monthFilter">Monthly</label>
                <select id="monthFilter" onchange="applyFilters()">
                    <option value="all">All Months</option>
                    <?php
                    $months = ['January', 'February', 'March', 'April', 'May', 'June', 
                              'July', 'August', 'September', 'October', 'November', 'December'];
                    foreach($months as $month_name):
                        $value = strtolower($month_name) . '_' . date('Y');
                        $selected = $filter_month == $value ? 'selected' : '';
                    ?>
                        <option value="<?php echo $value; ?>" <?php echo $selected; ?>>
                            <?php echo $month_name . ' ' . date('Y'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Resolved Complaints Section -->
    <div class="kanban-column full-width">
        <div class="kanban-header resolved">
            <h3>Resolved Complaint Records</h3>
            <span class="column-count"><?php echo $total_complaints; ?></span>
        </div>

        <div class="kanban-cards">
            <?php if(empty($filtered_complaints)): ?>
                <div class="no-complaints-message">
                    <p>No resolved complaints found.</p>
                    <p>Complaints marked as resolved will appear here.</p>
                </div>
            <?php else: ?>
                <?php foreach($filtered_complaints as $complaint): ?>
                    <?php 
                    $is_sample = isset($complaint['room']);
                    $resolved_date = !empty($complaint['resolved_date']) ? $complaint['resolved_date'] : 
                                    (!empty($complaint['updated_at']) ? $complaint['updated_at'] : '');
                    ?>
                    <div class="complaint-card">
                        <div class="card-header">
                            <div>
                                <span class="card-id">C<?php echo $complaint['id']; ?></span>
                                <div class="action-buttons">
                                    <button class="action-btn delete" onclick="<?php echo $is_sample ? "alert('This is sample data. Real complaints will have delete functionality.')" : "deleteComplaint({$complaint['id']}, 'history')"; ?>">
                                        <span class="material-symbols-rounded">delete</span>
                                        Delete
                                    </button>
                                    <button class="action-btn export" onclick="exportToPDF(<?php echo $complaint['id']; ?>)">
                                        <span class="material-symbols-rounded">download</span>
                                        Export PDF
                                    </button>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-weight: 500; color: #0a1128;">🏠 Room <?php echo htmlspecialchars($is_sample ? $complaint['room'] : ($user['room'] ?? $user['room_number'] ?? 'N/A')); ?></div>
                                <div style="color: #6b7280; font-size: 14px;">👤 <?php echo htmlspecialchars($is_sample ? $complaint['name'] : ($user['name'] ?? $user['full_name'] ?? 'User')); ?></div>
                            </div>
                        </div>
                        
                        <div class="type-badge"><?php echo ucfirst($complaint['complaint_type']); ?></div>
                        
                        <div class="card-description">
                            <p><?php echo htmlspecialchars($complaint['description']); ?></p>
                        </div>
                        
                        <div class="card-date">
                            📅 Submitted: <?php echo formatDate($complaint['created_at']); ?>
                        </div>
                        
                        <div class="last-update" style="color: #66BB6A;">
                            ✅ Resolved: <?php echo !empty($resolved_date) ? formatDate($resolved_date) : 'Date not available'; ?>
                        </div>

                        <?php if(!empty($complaint['admin_notes']) || !empty($complaint['records'])): ?>
                            <div class="records-title">Resolution Timeline</div>
                            <div class="record-logs-container">
                                <?php if(!empty($complaint['admin_notes'])): ?>
                                    <?php 
                                    $notes = explode("\n", trim($complaint['admin_notes']));
                                    foreach($notes as $note):
                                        if(trim($note)):
                                    ?>
                                        <div class="record-log">
                                            <span class="log-message"><?php echo htmlspecialchars(trim($note)); ?></span>
                                        </div>
                                    <?php endif; endforeach; ?>
                                <?php elseif(!empty($complaint['records'])): ?>
                                    <?php foreach($complaint['records'] as $record): ?>
                                        <div class="record-log">
                                            <span class="log-timestamp"><?php echo formatDate($record['timestamp']); ?>:</span>
                                            <span class="log-message"><?php echo htmlspecialchars($record['message']); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>