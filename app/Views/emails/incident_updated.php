<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica', 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #1e293b;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8fafc;
            padding: 30px;
            border: 1px solid #e2e8f0;
            border-top: none;
        }
        .update-box {
            background: white;
            border-left: 4px solid #3b82f6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .status-investigating { background: #fef3c7; color: #92400e; }
        .status-identified { background: #fed7aa; color: #9a3412; }
        .status-monitoring { background: #dbeafe; color: #1e40af; }
        .status-resolved { background: #d1fae5; color: #065f46; }
        .timestamp {
            font-size: 14px;
            color: #64748b;
            margin-top: 10px;
        }
        .components {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            font-size: 14px;
            color: #64748b;
        }
        .footer {
            background: #f1f5f9;
            padding: 20px;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
            color: #64748b;
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 24px;"><?= esc($site_name) ?></h1>
        <p style="margin: 5px 0 0 0; opacity: 0.9;">Status Update</p>
    </div>
    
    <div class="content">
        <h2 style="margin-top: 0; color: #3b82f6;">Incident Update</h2>
        
        <h3 style="margin: 0 0 20px 0;"><?= esc($incident->title) ?></h3>
        
        <div class="update-box">
            <span class="status-badge status-<?= strtolower($update->status) ?>">
                <?= esc(ucfirst($update->status)) ?>
            </span>
            
            <p style="margin: 15px 0;"><?= esc($update->message) ?></p>
            
            <div class="timestamp">
                <?= date('F j, Y, g:i a', strtotime($update->created_at)) ?>
            </div>
            
            <?php if (!empty($components)): ?>
            <div class="components">
                <strong>Affected Components:</strong> <?= esc($components) ?>
            </div>
            <?php endif; ?>
        </div>
        
        <a href="<?= site_url() ?>" class="button">View Status Page</a>
    </div>
    
    <div class="footer">
        <p style="margin: 0;">
            You're receiving this email because you subscribed to status updates for <?= esc($site_name) ?>.
        </p>
        <p style="margin: 10px 0 0 0;">
            <a href="<?= site_url('subscribe/manage') ?>" style="color: #3b82f6;">Manage Subscription</a>
        </p>
    </div>
</body>
</html>
