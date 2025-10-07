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
            background: #10b981;
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
        .checkmark {
            font-size: 64px;
            color: #10b981;
            text-align: center;
            margin: 20px 0;
        }
        .info-box {
            background: white;
            border: 1px solid #e2e8f0;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #64748b;
            width: 150px;
        }
        .info-value {
            color: #1e293b;
        }
        .footer {
            background: #f1f5f9;
            padding: 20px;
            border-radius: 0 0 8px 8px;
            font-size: 12px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 24px;"><?= esc($site_name) ?></h1>
        <p style="margin: 5px 0 0 0; opacity: 0.9;">Email Configuration Test</p>
    </div>
    
    <div class="content">
        <div class="checkmark">✓</div>
        
        <h2 style="margin: 0; color: #10b981; text-align: center;">Test Email Successful!</h2>
        
        <p style="text-align: center; color: #64748b; margin-top: 10px;">
            Your email configuration is working correctly.
        </p>
        
        <div class="info-box">
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value" style="color: #10b981; font-weight: 600;">✓ Configuration Valid</div>
            </div>
            <div class="info-row">
                <div class="info-label">Timestamp:</div>
                <div class="info-value"><?= $timestamp ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Service:</div>
                <div class="info-value"><?= esc($site_name) ?></div>
            </div>
        </div>
        
        <p style="color: #64748b; font-size: 14px; text-align: center; margin-top: 30px;">
            You can now send notifications to your subscribers.
        </p>
    </div>
    
    <div class="footer">
        <p style="margin: 0;">
            This is an automated test email from <?= esc($site_name) ?>.
        </p>
    </div>
</body>
</html>
