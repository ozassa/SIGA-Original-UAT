<?php
/**
 * CSP Security Dashboard
 * Monitor CSP violations, analyze security status, and manage policies
 */

require_once('advanced_security_system.php');

// Check admin access
session_start();
if (!isset($_SESSION['userID']) || !checkAdminAccess($_SESSION['userID'])) {
    header('HTTP/1.1 403 Forbidden');
    exit('Access denied');
}

// Handle actions
$action = $_GET['action'] ?? '';
$message = '';

switch($action) {
    case 'toggle_csp':
        toggleCSPMode();
        $message = 'CSP mode toggled successfully';
        break;
    case 'clear_logs':
        clearViolationLogs();
        $message = 'Violation logs cleared';
        break;
    case 'export_violations':
        exportViolations();
        exit;
        break;
}

// Get current stats
$stats = getSecurityStats();
$recentViolations = getRecentViolations(50);
$topViolations = getTopViolations();

/**
 * Check if user has admin access
 */
function checkAdminAccess($userId) {
    // Implement your admin check logic here
    // For now, assuming all logged users can access (adjust as needed)
    return true;
}

/**
 * Toggle CSP between Report-Only and Enforcement mode
 */
function toggleCSPMode() {
    $configFile = 'csp_config.json';
    $config = ['report_only' => true];
    
    if (file_exists($configFile)) {
        $config = json_decode(file_get_contents($configFile), true);
    }
    
    $config['report_only'] = !$config['report_only'];
    file_put_contents($configFile, json_encode($config));
}

/**
 * Clear violation logs
 */
function clearViolationLogs() {
    $logFiles = ['logs/csp_violations.log', 'logs/csp_critical_violations.log'];
    foreach($logFiles as $file) {
        if (file_exists($file)) {
            file_put_contents($file, '');
        }
    }
}

/**
 * Export violations as CSV
 */
function exportViolations() {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="csp_violations_' . date('Y-m-d') . '.csv"');
    
    $violations = getRecentViolations(1000);
    
    echo "Timestamp,IP,Blocked URI,Document URI,Violated Directive,Source File,Line Number\n";
    foreach($violations as $violation) {
        echo csv_escape($violation['timestamp']) . ',';
        echo csv_escape($violation['ip']) . ',';
        echo csv_escape($violation['blocked_uri']) . ',';
        echo csv_escape($violation['document_uri']) . ',';
        echo csv_escape($violation['violated_directive']) . ',';
        echo csv_escape($violation['source_file']) . ',';
        echo csv_escape($violation['line_number']) . "\n";
    }
}

function csv_escape($str) {
    return '"' . str_replace('"', '""', $str) . '"';
}

/**
 * Get security statistics
 */
function getSecurityStats() {
    $stats = [
        'csp_mode' => getCurrentCSPMode(),
        'total_violations' => 0,
        'critical_violations' => 0,
        'violations_today' => 0,
        'unique_blocked_uris' => 0,
        'most_common_violation' => 'None'
    ];
    
    // Count total violations
    if (file_exists('logs/csp_violations.log')) {
        $stats['total_violations'] = count(file('logs/csp_violations.log', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    }
    
    // Count critical violations
    if (file_exists('logs/csp_critical_violations.log')) {
        $stats['critical_violations'] = count(file('logs/csp_critical_violations.log', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    }
    
    // Count today's violations
    $today = date('Y-m-d');
    $violations = getRecentViolations(1000);
    foreach($violations as $violation) {
        if (strpos($violation['timestamp'], $today) === 0) {
            $stats['violations_today']++;
        }
    }
    
    // Get unique blocked URIs
    $blockedUris = [];
    foreach($violations as $violation) {
        if (!in_array($violation['blocked_uri'], $blockedUris)) {
            $blockedUris[] = $violation['blocked_uri'];
        }
    }
    $stats['unique_blocked_uris'] = count($blockedUris);
    
    // Get most common violation
    $violationCounts = [];
    foreach($violations as $violation) {
        $directive = $violation['violated_directive'];
        $violationCounts[$directive] = ($violationCounts[$directive] ?? 0) + 1;
    }
    if (!empty($violationCounts)) {
        $stats['most_common_violation'] = array_search(max($violationCounts), $violationCounts);
    }
    
    return $stats;
}

/**
 * Get current CSP mode
 */
function getCurrentCSPMode() {
    $configFile = 'csp_config.json';
    if (file_exists($configFile)) {
        $config = json_decode(file_get_contents($configFile), true);
        return $config['report_only'] ? 'Report-Only' : 'Enforcement';
    }
    return 'Report-Only';
}

/**
 * Get recent violations
 */
function getRecentViolations($limit = 50) {
    $violations = [];
    
    if (!file_exists('logs/csp_violations.log')) {
        return $violations;
    }
    
    $lines = file('logs/csp_violations.log', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_reverse($lines); // Most recent first
    
    foreach(array_slice($lines, 0, $limit) as $line) {
        $violation = json_decode($line, true);
        if ($violation) {
            $violations[] = $violation;
        }
    }
    
    return $violations;
}

/**
 * Get top violation types
 */
function getTopViolations() {
    $violations = getRecentViolations(500);
    $counts = [];
    
    foreach($violations as $violation) {
        $directive = $violation['violated_directive'];
        $counts[$directive] = ($counts[$directive] ?? 0) + 1;
    }
    
    arsort($counts);
    return array_slice($counts, 0, 10, true);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>SIGA - CSP Security Dashboard</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .header { background: #333; color: white; padding: 20px; margin: -20px -20px 20px -20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stat-value { font-size: 2em; font-weight: bold; color: #333; }
        .stat-label { color: #666; margin-top: 5px; }
        .critical { color: #e74c3c; }
        .warning { color: #f39c12; }
        .success { color: #27ae60; }
        .actions { margin: 20px 0; }
        .btn { padding: 10px 20px; margin-right: 10px; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #3498db; color: white; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-success { background: #27ae60; color: white; }
        .violations-table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        .violations-table th, .violations-table td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        .violations-table th { background: #f8f9fa; font-weight: bold; }
        .violations-table tr:hover { background: #f5f5f5; }
        .message { padding: 10px; margin-bottom: 20px; border-radius: 3px; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .top-violations { background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .violation-bar { background: #ecf0f1; height: 20px; margin: 5px 0; border-radius: 10px; overflow: hidden; }
        .violation-fill { background: #3498db; height: 100%; border-radius: 10px; }
        .status-indicator { display: inline-block; width: 10px; height: 10px; border-radius: 50%; margin-right: 5px; }
        .status-active { background: #27ae60; }
        .status-report { background: #f39c12; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🛡️ SIGA - CSP Security Dashboard</h1>
        <p>Content Security Policy Monitoring & Management</p>
    </div>

    <?php if ($message): ?>
        <div class="message success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value <?php echo $stats['csp_mode'] === 'Enforcement' ? 'success' : 'warning'; ?>">
                <span class="status-indicator <?php echo $stats['csp_mode'] === 'Enforcement' ? 'status-active' : 'status-report'; ?>"></span>
                <?php echo $stats['csp_mode']; ?>
            </div>
            <div class="stat-label">CSP Mode</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['total_violations']; ?></div>
            <div class="stat-label">Total Violations</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value critical"><?php echo $stats['critical_violations']; ?></div>
            <div class="stat-label">Critical Violations</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value warning"><?php echo $stats['violations_today']; ?></div>
            <div class="stat-label">Violations Today</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value"><?php echo $stats['unique_blocked_uris']; ?></div>
            <div class="stat-label">Unique Blocked URIs</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-value" style="font-size: 1.2em;"><?php echo htmlspecialchars($stats['most_common_violation']); ?></div>
            <div class="stat-label">Most Common Violation</div>
        </div>
    </div>

    <div class="actions">
        <a href="?action=toggle_csp" class="btn <?php echo $stats['csp_mode'] === 'Enforcement' ? 'btn-danger' : 'btn-success'; ?>">
            <?php echo $stats['csp_mode'] === 'Enforcement' ? 'Switch to Report-Only' : 'Enable Enforcement'; ?>
        </a>
        <a href="?action=export_violations" class="btn btn-primary">Export Violations</a>
        <a href="?action=clear_logs" class="btn btn-danger" onclick="return confirm('Are you sure you want to clear all violation logs?')">Clear Logs</a>
        <a href="?" class="btn btn-primary">Refresh</a>
    </div>

    <?php if (!empty($topViolations)): ?>
    <div class="top-violations">
        <h3>Top Violation Types</h3>
        <?php 
        $maxCount = max($topViolations);
        foreach($topViolations as $directive => $count): 
            $percentage = ($count / $maxCount) * 100;
        ?>
            <div style="margin-bottom: 15px;">
                <div><strong><?php echo htmlspecialchars($directive); ?></strong> (<?php echo $count; ?> violations)</div>
                <div class="violation-bar">
                    <div class="violation-fill" style="width: <?php echo $percentage; ?>%;"></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($recentViolations)): ?>
    <h3>Recent Violations</h3>
    <table class="violations-table">
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>IP</th>
                <th>Violated Directive</th>
                <th>Blocked URI</th>
                <th>Document URI</th>
                <th>Source File</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach(array_slice($recentViolations, 0, 20) as $violation): ?>
            <tr>
                <td><?php echo htmlspecialchars($violation['timestamp']); ?></td>
                <td><?php echo htmlspecialchars($violation['ip']); ?></td>
                <td><strong><?php echo htmlspecialchars($violation['violated_directive']); ?></strong></td>
                <td><?php echo htmlspecialchars(substr($violation['blocked_uri'], 0, 50)); ?><?php echo strlen($violation['blocked_uri']) > 50 ? '...' : ''; ?></td>
                <td><?php echo htmlspecialchars(substr($violation['document_uri'], 0, 50)); ?><?php echo strlen($violation['document_uri']) > 50 ? '...' : ''; ?></td>
                <td><?php echo htmlspecialchars($violation['source_file']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="stat-card">
            <h3>🎉 No Violations Detected</h3>
            <p>Your CSP policy is working correctly with no violations recorded.</p>
        </div>
    <?php endif; ?>

    <div style="margin-top: 40px; padding: 20px; background: white; border-radius: 5px;">
        <h3>Security Recommendations</h3>
        <ul>
            <?php if ($stats['csp_mode'] === 'Report-Only'): ?>
                <li><strong>Action Required:</strong> You're in Report-Only mode. Review violations and switch to Enforcement mode for full protection.</li>
            <?php endif; ?>
            
            <?php if ($stats['critical_violations'] > 0): ?>
                <li class="critical"><strong>Critical:</strong> <?php echo $stats['critical_violations']; ?> critical violations detected. Review immediately.</li>
            <?php endif; ?>
            
            <?php if ($stats['violations_today'] > 10): ?>
                <li class="warning"><strong>High Activity:</strong> <?php echo $stats['violations_today']; ?> violations today. Consider policy adjustments.</li>
            <?php endif; ?>
            
            <?php if ($stats['total_violations'] === 0): ?>
                <li class="success"><strong>Excellent:</strong> No CSP violations detected. Your security policy is well-configured.</li>
            <?php endif; ?>
        </ul>
    </div>

    <script>
        // Auto-refresh every 30 seconds
        setTimeout(function() {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>