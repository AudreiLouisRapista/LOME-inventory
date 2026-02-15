@section('title', 'Login')

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        /* Importing Poppins for that clean, professional look */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        /* --- Animated Gradient Background --- */

        body {

            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(-45deg, #000000, #1a1a1a, #4e0606, #000000);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            padding: 20px;
        }

        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }

        }

        /* --- Responsive Container --- */
        .login-container {
            width: 100%;
            max-width: 1000px;
            min-height: 600px;
            background-color: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: row;
            /* Desktop default */
            overflow: hidden;
        }

        /* --- Left Panel (Branding) --- */
        .left-panel {
            flex: 1.2;
            background: linear-gradient(135deg, #8b0000 0%, #4a0000 100%);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: #ffffff;
        }

        .left-panel-content {
            text-align: center;
            z-index: 2;
        }

        .left-panel h1 {
            font-size: clamp(5px, 8vw, 90px);
            /* Fluid typography */
            font-weight: 800;
            line-height: 0.9;
            text-transform: uppercase;
            letter-spacing: -2px;
            margin-bottom: 10px;
            font-family: serif;
        }

        .title-text {
            font-size: clamp(12px, 2vw, 16px);
            letter-spacing: 4px;
            text-transform: uppercase;
            opacity: 0.9;
            font-weight: 300;
        }

        /* --- Right Panel (Form) --- */
        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
        }

        .login-form-content {
            width: 100%;
            max-width: 320px;
        }

        .login-form-content h2 {
            font-size: 28px;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .sub-text {
            color: #71717a;
            font-size: 14px;
            margin-bottom: 30px;
        }

        /* Better Icon Integration */
        .input-group {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            position: relative;
        }

        .input-group-text {
            position: absolute;
            left: 15px;
            background: transparent;
            border: none;
            z-index: 10;
            color: #a1a1aa;
        }

        .form-control {
            width: 100%;
            padding: 14px 15px 14px 45px !important;
            /* Forces space for the icon */
            border-radius: 12px !important;
            border: 1.5px solid #e4e4e7;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: #fcfcfc;
        }

        .form-control:focus {
            border-color: #8b0000 !important;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(139, 0, 0, 0.1) !important;
        }

        /* Fix for the footer text */
        .login-form-content p:last-child {
            font-size: 12px;
            color: #a1a1aa;
            letter-spacing: 0.5px;
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            background: #000000;
            /* Darker button for aesthetic contrast */
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .login-btn:hover {
            background: #8b0000;
            transform: translateY(-2px);
        }

        /* --- RESPONSIVENESS --- */

        /* Tablet (768px to 1024px) */
        @media (max-width: 1024px) {
            .login-container {
                max-width: 800px;
                min-height: 500px;
            }
        }

        /* Mobile (Everything below 768px) */
        @media (max-width: 768px) {
            body {
                padding: 0;
            }

            .login-container {
                flex-direction: column;
                border-radius: 0;
                min-height: 100vh;
            }

            .left-panel {
                flex: 0.4;
                /* Takes up less space on mobile */
                padding: 60px 20px;
            }

            .right-panel {
                flex: 0.6;
                padding: 40px 20px;
                align-items: flex-start;
                /* Better thumb-reach */
            }

            .left-panel h1 {
                font-size: 50px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="left-panel">
            <div class="left-panel-content">

                <h1>LOME</h1>
                <p class="title-text">Inventory Management System</p>
                <p class="sub-text">Efficiently manage your inventory with ease and precision.</p>

            </div>
        </div>

        <div class="right-panel">
            <div class="login-form-content">
                @include('layout.partials.alerts')

                <h2>Welcome</h2>
                <p class="sub-text">Log in to your account to continue</p>

                <form action="{{ route('auth_user') }}" method="POST">
                    @csrf

                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email"
                            required>
                    </div>

                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="password" required>
                    </div>


                    <button type="submit" class="login-btn" style="margin-top: 25px;">Log In</button>
                </form>
                <p style="text-align: center; margin-top: 30px;">Be responsible for your actions</p>



            </div>
        </div>
    </div>


</body>

</html>
