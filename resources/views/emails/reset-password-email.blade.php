<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        body {
            background-color: #F8F5F2;
            color: #1A1A1A;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }

        .wrapper {
            background-color: #F8F5F2;
            padding: 40px 20px;
        }

        .container {
            background-color: #FFFFFF;
            border-radius: 24px;
            margin: 0 auto;
            max-width: 600px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #E2E8F0;
        }

        .header {
            background-color: #0077BE;
            padding: 40px;
            text-align: center;
        }

        .logo {
            height: 80px;
            width: auto;
        }

        .content {
            padding: 40px;
            line-height: 1.6;
            text-align: center;
        }

        .content h1 {
            color: #0077BE;
            font-size: 28px;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 24px;
        }

        .content p {
            font-size: 16px;
            color: #4A5568;
            margin-bottom: 32px;
        }

        .button-wrapper {
            margin-top: 32px;
            margin-bottom: 32px;
        }

        .button {
            background-color: #0077BE;
            border-radius: 12px;
            color: #FFFFFF !important;
            display: inline-block;
            font-size: 16px;
            font-weight: 700;
            padding: 18px 36px;
            text-decoration: none;
            letter-spacing: 1px;
            box-shadow: 0 4px 14px 0 rgba(0, 119, 190, 0.39);
        }

        .footer {
            color: #718096;
            font-size: 12px;
            padding: 40px;
            text-align: center;
            background-color: #F7FAFC;
            border-top: 1px solid #EDF2F7;
        }

        .footer a {
            color: #0077BE;
            text-decoration: none;
            font-weight: 600;
        }

        .notice {
            font-size: 14px;
            color: #A0AEC0;
            margin-top: 24px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <img src="https://mywatered.com/storage/logo/WateredAppicon.png" alt="Watered Logo" class="logo">
            </div>
            <div class="content">
                <h1>Reset Your Password</h1>
                <p>Hello, {{ $name }}!</p>
                <p>We received a request to reset your password for your Watered account. Tap the button below to set a
                    new one:</p>

                <div class="button-wrapper">
                    <a href="{{ $resetUrl }}" class="button">SET NEW PASSWORD</a>
                </div>

                <p class="notice">If you didn't request this, you can safely ignore this email.</p>
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} Watered. All rights reserved.<br>
                <a href="https://mywatered.com">mywatered.com</a>
            </div>
        </div>
    </div>
</body>

</html>