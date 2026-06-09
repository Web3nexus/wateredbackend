<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied - {{ config('app.name', 'Watered') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant:400,600,700|outfit:400,500,600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background: #0f0f12;
            color: #e2e2e6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .container {
            text-align: center;
            max-width: 480px;
        }
        .lock-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 32px;
            background: linear-gradient(135deg, #f59e0b20, #d9770620);
            border: 1px solid #f59e0b30;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .lock-icon svg {
            width: 36px;
            height: 36px;
            color: #f59e0b;
        }
        h1 {
            font-family: 'Cormorant', serif;
            font-size: 32px;
            font-weight: 700;
            color: #f59e0b;
            margin-bottom: 8px;
            letter-spacing: 0.02em;
        }
        .divider {
            width: 48px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #f59e0b80, transparent);
            margin: 16px auto;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            color: #a0a0a8;
            margin-bottom: 8px;
        }
        .hint {
            font-size: 14px;
            color: #6b6b78;
            margin-top: 4px;
        }
        .btn {
            display: inline-block;
            margin-top: 32px;
            padding: 12px 32px;
            background: #f59e0b;
            color: #0f0f12;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn:hover { background: #d97706; }
        .btn-secondary {
            display: inline-block;
            margin-top: 12px;
            margin-left: 12px;
            padding: 12px 32px;
            background: transparent;
            color: #a0a0a8;
            border: 1px solid #2a2a32;
            border-radius: 8px;
            font-weight: 500;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-secondary:hover {
            border-color: #f59e0b40;
            color: #e2e2e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="lock-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
        </div>

        <h1>Access Denied</h1>
        <div class="divider"></div>
        <p>You do not have permission to access this area.</p>
        <p class="hint">If you believe this is a mistake, contact an administrator.</p>

        <a href="{{ url('/securegate') }}" class="btn">Return to Dashboard</a>
        <a href="javascript:history.back()" class="btn-secondary">Go Back</a>
    </div>
</body>
</html>
