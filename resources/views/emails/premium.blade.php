<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        body {
            background-color: #0F172A;
            color: #F1F5F9;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100% !important;
        }

        .wrapper {
            background-color: #0F172A;
            padding: 40px 20px;
        }

        .container {
            background-color: #1E293B;
            border: 1px solid rgba(234, 179, 8, 0.2);
            border-radius: 24px;
            margin: 0 auto;
            max-width: 600px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .header {
            background: linear-gradient(to bottom, rgba(234, 179, 8, 0.05), transparent);
            padding: 40px;
            text-align: center;
        }

        .logo {
            height: 64px;
            width: auto;
        }

        .content {
            padding: 40px;
            line-height: 1.6;
        }

        .content h1 {
            color: #EAB308;
            font-size: 24px;
            font-weight: 700;
            margin-top: 0;
            text-align: center;
        }

        .content p {
            font-size: 16px;
            color: #CBD5E1;
        }

        .button-wrapper {
            padding: 20px 0;
            text-align: center;
        }

        .button {
            background-color: #EAB308;
            border-radius: 12px;
            color: #0F172A !important;
            display: inline-block;
            font-size: 16px;
            font-weight: 700;
            padding: 16px 32px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .footer {
            color: #64748B;
            font-size: 12px;
            padding: 40px;
            text-align: center;
            border-top: 1px solid rgba(234, 179, 8, 0.1);
        }

        .footer a {
            color: #EAB308;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <img src="{{ asset('images/watered-logo.png') }}" alt="Watered Logo" class="logo">
            </div>
            <div class="content">
                {!! $body !!}
            </div>
            <div class="footer">
                &copy; {{ date('Y') }} Watered. All rights reserved.<br>
                <a href="{{ config('app.url') }}">Visit our website</a>
            </div>
        </div>
    </div>
</body>

</html>