<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password | Watered</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Outfit:wght@300;400;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary-color: #0077BE;
            --bg-color: #F8F5F2;
            --text-color: #1A1A1A;
            --card-bg: #FFFFFF;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        h1 {
            font-family: 'Cinzel', serif;
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        p {
            font-size: 15px;
            color: rgba(26, 26, 26, 0.6);
            margin-bottom: 32px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--primary-color);
        }

        input {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid rgba(0, 119, 190, 0.1);
            background: #F9FAFB;
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(0, 119, 190, 0.05);
        }

        button {
            width: 100%;
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 12px;
            letter-spacing: 0.5px;
        }

        button:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 24px;
        }

        .message {
            margin-top: 20px;
            padding: 12px;
            border-radius: 8px;
            display: none;
            font-size: 14px;
        }

        .error {
            background-color: #FEE2E2;
            color: #BC1C1C;
        }

        .success {
            background-color: #DEF7EC;
            color: #03543F;
        }

        #loading {
            display: none;
            margin: 20px auto;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <img src="/storage/logo/WateredAppicon.png" alt="Watered Logo" class="logo"
            onerror="this.src='https://watered-8eb60.firebaseapp.com/favicon.ico'">
        <h1>Reset Password</h1>
        <p id="instruction">Please enter your new password below.</p>

        <div id="reset-form-container">
            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" placeholder="••••••••" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" placeholder="••••••••" required>
            </div>
            <button id="reset-button">SET NEW PASSWORD</button>
            <div id="loading"></div>
        </div>

        <div id="message" class="message"></div>
    </div>

    <!-- Firebase SDKs -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
        import { getAuth, confirmPasswordReset, verifyPasswordResetCode } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

        const firebaseConfig = @json($firebaseConfig);
        const oobCode = "{{ $oobCode }}";

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);

        const form = document.getElementById('reset-form-container');
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm-password');
        const submitBtn = document.getElementById('reset-button');
        const messageDiv = document.getElementById('message');
        const loading = document.getElementById('loading');
        const instruction = document.getElementById('instruction');

        // Verify the code first
        verifyPasswordResetCode(auth, oobCode).then((email) => {
            instruction.innerText = `Setting new password for ${email}`;
        }).catch((error) => {
            showError("Invalid or expired reset link. Please request a new one from the app.");
            form.style.display = 'none';
        });

        submitBtn.addEventListener('click', async () => {
            const password = passwordInput.value;
            const confirmPassword = confirmInput.value;

            if (password.length < 6) {
                showError("Password must be at least 6 characters.");
                return;
            }

            if (password !== confirmPassword) {
                showError("Passwords do not match.");
                return;
            }

            hideMessage();
            setLoading(true);

            try {
                await confirmPasswordReset(auth, oobCode, password);
                showSuccess("Password updated successfully! You can now log in to the app.");
                form.style.display = 'none';
            } catch (error) {
                showError("Failed to update password. Error: " + error.message);
                setLoading(false);
            }
        });

        function showError(msg) {
            messageDiv.innerText = msg;
            messageDiv.className = 'message error';
            messageDiv.style.display = 'block';
        }

        function showSuccess(msg) {
            messageDiv.innerText = msg;
            messageDiv.className = 'message success';
            messageDiv.style.display = 'block';
        }

        function hideMessage() {
            messageDiv.style.display = 'none';
        }

        function setLoading(isLoading) {
            loading.style.display = isLoading ? 'block' : 'none';
            submitBtn.style.display = isLoading ? 'none' : 'block';
        }
    </script>
</body>

</html>