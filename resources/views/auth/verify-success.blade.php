<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verified | Watered</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #d4af37;
            /* Gold */
            --bg: #0c1427;
            /* Dark Luxury */
            --surface: #162447;
            --text: #ffffff;
            --text-muted: #a0aec0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
        }

        .container {
            text-align: center;
            padding: 2.5rem;
            background: linear-gradient(145deg, var(--surface), var(--bg));
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(212, 175, 55, 0.1);
            max-width: 400px;
            width: 90%;
            position: relative;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: rgba(212, 175, 55, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            border: 2px solid var(--primary);
        }

        .icon-box svg {
            width: 40px;
            height: 40px;
            color: var(--primary);
            animation: checkmark 0.5s ease-out 0.5s both;
        }

        @keyframes checkmark {
            from {
                opacity: 0;
                scale: 0.5;
            }

            to {
                opacity: 1;
                scale: 1;
            }
        }

        h1 {
            font-size: 1.75rem;
            margin-bottom: 0.75rem;
            font-weight: 600;
        }

        p {
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-block;
            background: var(--primary);
            color: var(--bg);
            text-decoration: none;
            padding: 0.875rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
            filter: brightness(1.1);
        }

        .success-accent {
            position: absolute;
            top: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            background: var(--primary);
            filter: blur(80px);
            opacity: 0.1;
            z-index: -1;
        }
    </style>
</head>

<body>
    <div class="success-accent"></div>
    <div class="container">
        <div class="icon-box">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
        </div>
        <h1>Email Verified</h1>
        <p>Your spiritual journey continues. Your email has been successfully verified, and your account is now fully
            active.</p>

        <a href="watered://verify-success" class="btn">Return to App</a>

        <div style="margin-top: 1rem; font-size: 0.8rem; color: var(--text-muted);">
            You can also simply close this browser tab.
        </div>
    </div>
</body>

</html>