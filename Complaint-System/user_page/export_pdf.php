<?php
// exportpdf.php
require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please login to access this page");
}

if (!isset($_GET['id'])) {
    die("Complaint ID not provided");
}

$complaint_id = clean($_GET['id']);

// Get complaint details
$stmt = $conn->prepare("
    SELECT c.*, u.name, u.room_number, u.email, u.phone 
    FROM complaints c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.id = :id AND c.user_id = :user_id
");
$stmt->execute(['id' => $complaint_id, 'user_id' => $_SESSION['user_id']]);
$complaint = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$complaint) {
    die("Complaint not found");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Complaint Receipt - C<?php echo $complaint['id']; ?></title>
    <style>
        /* Your existing PDF styles here - keep them as is */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f8fafc; color: #1e293b; line-height: 1.6; max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        .receipt-container { background: white; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e2e8f0; }
        .receipt-header { background: linear-gradient(135deg, #42a5f5 0%, #2196F3 100%); color: white; padding: 30px; text-align: center; }
        .logo { font-size: 24px; font-weight: 700; margin-bottom: 8px; letter-spacing: 1px; }
        .receipt-title { font-size: 32px; font-weight: 700; margin-bottom: 4px; }
        .receipt-subtitle { font-size: 16px; opacity: 0.9; font-weight: 400; }
        .receipt-id { background: rgba(255,255,255,0.15); display: inline-block; padding: 8px 20px; border-radius: 50px; font-size: 14px; font-weight: 600; margin-top: 15px; letter-spacing: 0.5px; }
        .receipt-content { padding: 40px; }
        .section { margin-bottom: 30px; }
        .section-title { font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #e2e8f0; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .info-item { margin-bottom: 12px; }
        .info-label { font-size: 13px; color: #64748b; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
        .info-value { font-size: 15px; color: #1e293b; font-weight: 500; }
        .status-badge { display: inline-block; padding: 6px 16px; border-radius: 6px; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-resolved { background: #10b981; color: white; }
        .status-pending { background: #f59e0b; color: white; }
        .status-in-progress { background: #3b82f6; color: white; }
        .complaint-description { background: #f8fafc; padding: 20px; border-radius: 10px; border-left: 4px solid #42a5f5; margin-top: 10px; font-size: 15px; line-height: 1.7; color: #475569; }
        .timeline { background: #f8fafc; padding: 20px; border-radius: 10px; margin-top: 10px; }
        .timeline-item { display: flex; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #e2e8f0; }
        .timeline-item:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
        .timeline-date { min-width: 120px; font-weight: 600; color: #64748b; font-size: 14px; }
        .timeline-content { flex: 1; color: #475569; font-size: 14px; }
        .receipt-footer { background: #f1f5f9; padding: 25px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer-text { color: #64748b; font-size: 14px; margin-bottom: 8px; }
        .generated-date { color: #94a3b8; font-size: 13px; }
        .print-btn { background: #42a5f5; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: 20px; display: inline-block; text-decoration: none; }
        .print-btn:hover { background: #2196F3; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(66,165,245,0.3); }
        
        @media print {
            body { padding: 0; background: white; }
            .receipt-container { box-shadow: none; border: 1px solid #ddd; }
            .print-btn { display: none; }
            .receipt-footer { background: #f8fafc; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="logo">COMPLAINT DESK</div>
            <div class="receipt-title">COMPLAINT RECEIPT</div>
            <div class="receipt-subtitle">Official Document</div>
            <div class="receipt-id">REFERENCE: C<?php echo $complaint['id']; ?></div>
        </div>
        
        <div class="receipt-content">
            <div class="section">
                <div class="section-title">Complaint Information</div>
                <div class="info-grid">
                    <div class="info-item"><div class="info-label">Complaint ID</div><div class="info-value">C<?php echo $complaint['id']; ?></div></div>
                    <div class="info-item"><div class="info-label">Status</div><div class="info-value"><span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $complaint['status'])); ?>"><?php echo ucfirst($complaint['status']); ?></span></div></div>
                    <div class="info-item"><div class="info-label">Type</div><div class="info-value"><?php echo ucfirst($complaint['complaint_type']); ?></div></div>
                    <div class="info-item"><div class="info-label">Submitted</div><div class="info-value"><?php echo date('F d, Y h:i A', strtotime($complaint['created_at'])); ?></div></div>
                    <?php if(!empty($complaint['resolved_date'])): ?>
                    <div class="info-item"><div class="info-label">Resolved</div><div class="info-value"><?php echo date('F d, Y h:i A', strtotime($complaint['resolved_date'])); ?></div></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">User Information</div>
                <div class="info-grid">
                    <div class="info-item"><div class="info-label">Full Name</div><div class="info-value"><?php echo htmlspecialchars($complaint['name'] ?? 'Not provided'); ?></div></div>
                    <div class="info-item"><div class="info-label">Room Number</div><div class="info-value"><?php echo htmlspecialchars($complaint['room_number'] ?? 'Not provided'); ?></div></div>
                    <div class="info-item"><div class="info-label">Email Address</div><div class="info-value"><?php echo htmlspecialchars($complaint['email'] ?? 'Not provided'); ?></div></div>
                    <div class="info-item"><div class="info-label">Phone Number</div><div class="info-value"><?php echo htmlspecialchars($complaint['phone'] ?? 'Not provided'); ?></div></div>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">Complaint Details</div>
                <div class="complaint-description"><?php echo nl2br(htmlspecialchars($complaint['description'])); ?></div>
            </div>
            
            <?php if(!empty($complaint['admin_notes'])): ?>
            <div class="section">
                <div class="section-title">Resolution Timeline</div>
                <div class="timeline">
                    <?php 
                    $notes = explode("\n", trim($complaint['admin_notes']));
                    foreach($notes as $note):
                        if(trim($note)): 
                    ?>
                    <div class="timeline-item">
                        <div class="timeline-date">
                            <?php 
                            $content = trim($note);
                            if(strpos($content, ':') !== false) {
                                $parts = explode(':', $content, 2);
                                $date_part = trim($parts[0]);
                                $content = trim($parts[1]);
                                echo preg_match('/[A-Za-z]{3} \d{1,2}, \d{4}/', $date_part) ? $date_part : date('M d, Y h:i A', strtotime($complaint['created_at']));
                            } else {
                                echo date('M d, Y h:i A', strtotime($complaint['created_at']));
                            }
                            ?>
                        </div>
                        <div class="timeline-content"><?php echo htmlspecialchars($content); ?></div>
                    </div>
                    <?php endif; endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="receipt-footer">
            <div class="footer-text">This is an official receipt from Complaint Desk Management System</div>
            <div class="footer-text">For any inquiries, please contact the administration office</div>
            <div class="generated-date">Generated on <?php echo date('F d, Y \a\t h:i A'); ?></div>
            <button class="print-btn" onclick="window.print()">🖨️ Print Receipt</button>
            <button class="print-btn" onclick="window.close()" style="margin-left: 10px; background: #64748b;">✕ Close Window</button>
        </div>
    </div>
    
    <script>
        window.onfocus = function() {
            if (window.afterPrint) {
                window.close();
            }
        };
        window.onafterprint = function() {
            window.afterPrint = true;
        };
    </script>
</body>
</html>