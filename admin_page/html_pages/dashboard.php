<link rel="stylesheet" href="..//styles/dashboard.css">

<?php 
include 'main_file.php'; 
require_once __DIR__ . '/../../db_config.php';

// ========================================
// FETCH STATISTICS FROM DATABASE
// ========================================

// 1. Total ALL Complaints
$allQuery = "SELECT COUNT(*) as total FROM complaints";
$allResult = getSingleRow($conn, $allQuery);
$totalAll = $allResult ? $allResult['total'] : 0;

// 2. Total PENDING
$pendingQuery = "SELECT COUNT(*) as total FROM complaints WHERE status = 'Pending'";
$pendingResult = getSingleRow($conn, $pendingQuery);
$totalPending = $pendingResult ? $pendingResult['total'] : 0;

// 3. Total IN PROGRESS
$progressQuery = "SELECT COUNT(*) as total FROM complaints WHERE status = 'In Progress'";
$progressResult = getSingleRow($conn, $progressQuery);
$totalProgress = $progressResult ? $progressResult['total'] : 0;

// 4. Total RESOLVED
$resolvedQuery = "SELECT COUNT(*) as total FROM complaints WHERE status = 'Resolved'";
$resolvedResult = getSingleRow($conn, $resolvedQuery);
$totalResolved = $resolvedResult ? $resolvedResult['total'] : 0;

// 5. Recent (Last 7 days)
$recentQuery = "SELECT COUNT(*) as total FROM complaints WHERE created_date >= DATEADD(day, -7, GETDATE())";
$recentResult = getSingleRow($conn, $recentQuery);
$totalRecent = $recentResult ? $recentResult['total'] : 0;

// ========================================
// FETCH RECENT COMPLAINTS FOR KANBAN
// ========================================
$pendingComplaints = getAllRows($conn, "SELECT TOP 5 * FROM complaints WHERE status = 'Pending' ORDER BY created_date DESC");
$inProgressComplaints = getAllRows($conn, "SELECT TOP 5 * FROM complaints WHERE status = 'In Progress' ORDER BY last_updated DESC");
$resolvedComplaints = getAllRows($conn, "SELECT TOP 5 * FROM complaints WHERE status = 'Resolved' ORDER BY resolved_date DESC");

// Function to get records
function getComplaintRecords($conn, $complaintId) {
    $query = "SELECT * FROM complaint_records WHERE complaint_id = ? ORDER BY log_timestamp ASC";
    return getAllRows($conn, $query, array($complaintId));
}
?>

<div class="main-content">
    <h1 class="greeting">Good Morning, Admin!</h1>
    <p class="subtitle">Welcome to your complaint management dashboard</p>

    <h2 class="section-title">Summary</h2>
    <div class="stats-container">
        <div class="stat-card all">
            <div class="stat-title">All Complaints</div>
            <div class="stat-number"><?php echo $totalAll; ?></div>
        </div>
        <div class="stat-card pending">
            <div class="stat-title">Pending</div>
            <div class="stat-number"><?php echo $totalPending; ?></div>
        </div>
        <div class="stat-card progress">
            <div class="stat-title">In Progress</div>
            <div class="stat-number"><?php echo $totalProgress; ?></div>
        </div>
        <div class="stat-card resolved">
            <div class="stat-title">Resolved</div>
            <div class="stat-number"><?php echo $totalResolved; ?></div>
        </div>
        <div class="stat-card recent">
            <div class="stat-title">Recent (7 days)</div>
            <div class="stat-number"><?php echo $totalRecent; ?></div>
        </div>
    </div>

    <h2 class="section-title">Recent Complaints</h2>
    <div class="kanban-container">
        <!-- Pending Column -->
        <div class="kanban-column">
            <div class="kanban-header pending">
                <h3>Pending</h3>
                <span class="column-count"><?php echo count($pendingComplaints); ?></span>
            </div>
            <div class="kanban-cards">
                <?php if(empty($pendingComplaints)): ?>
                    <div class="empty-state">No pending complaints</div>
                <?php else: ?>
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
                <?php endif; ?>
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="kanban-column">
            <div class="kanban-header progress">
                <h3>In Progress</h3>
                <span class="column-count"><?php echo count($inProgressComplaints); ?></span>
            </div>
            <div class="kanban-cards">
                <?php if(empty($inProgressComplaints)): ?>
                    <div class="empty-state">No complaints in progress</div>
                <?php else: ?>
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
                <?php endif; ?>
            </div>
        </div>

        <!-- Resolved Column -->
        <div class="kanban-column">
            <div class="kanban-header resolved">
                <h3>Resolved</h3>
                <span class="column-count"><?php echo count($resolvedComplaints); ?></span>
            </div>
            <div class="kanban-cards">
                <?php if(empty($resolvedComplaints)): ?>
                    <div class="empty-state">No resolved complaints</div>
                <?php else: ?>
                    <?php foreach ($resolvedComplaints as $complaint): 
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
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function updateGreeting() {
        const hour = new Date().getHours();
        let greeting;

        if (hour >= 5 && hour < 12) {
            greeting = "Good Morning, Admin!";
        } else if (hour >= 12 && hour < 18) {
            greeting = "Good Afternoon, Admin!";
        } else {
            greeting = "Good Evening, Admin!";
        }

        const greetingElement = document.querySelector('.greeting');
        if (greetingElement) {
            greetingElement.textContent = greeting;
        }
    }

    updateGreeting();
</script>

<?php
// Close connection
closeConnection($conn);
?>