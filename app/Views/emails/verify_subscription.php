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
            background: #3b82f6;
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
        .button {
            display: inline-block;
            padding: 14px 28px;
            background: #3b82f6;
            color: white !important;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: 600;
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
        <p style="margin: 5px 0 0 0; opacity: 0.9;">Status Updates</p>
    </div>
    
    <div class="content">
        <h2 style="margin-top: 0; color: #1e293b;">Verify Your Subscription</h2>
        
        <p>Thank you for subscribing to status updates for <?= esc($site_name) ?>!</p>
        
        <p>To confirm your subscription and start receiving incident notifications, please click the button below:</p>
        
        <div style="text-align: center;">
            <a href="<?= $verify_url ?>" class="button">Verify My Subscription</a>
        </div>
        
        <p style="color: #64748b; font-size: 14px; margin-top: 30px;">
            If the button doesn't work, you can also copy and paste this link into your browser:
        </p>
        <p style="color: #3b82f6; font-size: 12px; word-break: break-all;">
            <?= $verify_url ?>
        </p>
        
        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 30px 0;">
        
        <p style="color: #64748b; font-size: 14px;">
            If you did not request this subscription, you can safely ignore this email or 
            <a href="<?= $unsubscribe_url ?>" style="color: #3b82f6;">unsubscribe here</a>.
        </p>
    </div>
    
    <div class="footer">
        <p style="margin: 0;">
            This verification link will expire in 24 hours.
        </p>
    </div>
</body>
</html>
