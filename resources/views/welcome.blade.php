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
        /* --- General Reset & Fonts --- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            /* The font in the image looks like a clean sans-serif */
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        /* --- Full Page Background --- */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #000000;

            /* The main teal background color */
            padding: 20px;
        }

        /* --- Main Container (The rounded rectangle) --- */
        .login-container {
            width: 100%;
            max-width: 900px;
            /* Adjust max-width as needed */
            min-height: 550px;
            background-color: #FFFFFF;
            border-radius: 20px;
            /* Rounded corners for the whole card */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.63);
            display: flex;
            overflow: hidden;
            /* Crucial for clipping the left image/background */
        }

        /* --- Left Panel (Image/Content Area) --- */
        .left-panel {
            flex: 1;
            /* Adjust the width of the image section */
            max-width: 50%;
            position: relative;
            background:
                linear-gradient(135deg, #ff0000f4, #8b0404);
            color for the left side */ padding: 0;
            /* Remove padding from your original design */
        }


        .left-panel h1 {
            position: relative;
            /* z-index: 2; */
            /* padding: 20px; */
            color: white;
            font-size: 80px;
            font-weight: 900;
            text-align: left;
            margin-top: 150px;
            letter-spacing: 0.1em;
            top: 25px;
            left: 88px;
            font-family: 'Times New Roman', Times, serif;
        }

        .left-panel-content .sub-text {
            /* position: relative; */
            /* z-index: 2; */
            /* padding: 5px; */
            color: white;
            font-size: 30px;
            text-align: left;
            position: absolute;
            font-weight: 650;
            left: 40px;
            right: 40px;
            top: 270px;
            font-family: 'Times New Roman', Times, serif;
            text-align: center;
        }

        .left-panel-content .footer-text {
            /* position: relative; */
            /* z-index: 2; */
            /* padding: 5px; */
            color: white;
            font-size: 12px;
            text-align: left;
            position: absolute;
            font-weight: 400;
            left: 40px;
            right: 40px;
            top: 312px;
            letter-spacing: 0.2em;
            font-family: Arial, Helvetica, sans-serif;
            text-align: center;
        }

        /* --- Right Panel (Login Form Area) --- */
        .right-panel {
            flex: 1;
            /* Adjust the width of the form section */
            max-width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 40px;
        }




        .login-form-content {
            width: 100%;
            max-width: 350px;
            z-index: 2;
            /* Ensure content is above the pseudo-element curve */
        }

        /* --- Text and Headers --- */
        .login-form-content h2 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
            text-align: center;
        }

        .login-form-content .sub-text {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 30px;
            text-align: center;
        }

        /* --- Form Inputs (The rounded, icon-prefixed fields) --- */
        .input-group {
            margin-bottom: 15px;
        }

        .form-control {
            /* Make inputs very rounded */
            border-radius: 50px;
            padding: 12px 20px 12px 50px;
            /* Adjust left padding for icon */
            border: 1px solid #e0e0e0;
            box-shadow: none !important;
            /* Remove Bootstrap's default focus ring */
        }

        /* Style for the icons inside the input fields */
        .input-group-text {
            border-top-left-radius: 50px;
            border-bottom-left-radius: 50px;
            padding-left: 20px;
            background-color: transparent;
            border: none;
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
            color: #495057;
        }

        /* --- Forgot Password Link --- */
        .forgot-password {
            text-align: right;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .forgot-password a {
            color: #1b00a4;
            /* Match the primary teal color */
            text-decoration: none;
            font-weight: 500;
        }

        /* --- Login Button --- */
        .login-btn {
            width: 100%;
            padding: 12px;
            /* Teal to Cyan gradient from the image */
            color: white;
            border: none;
            border-radius: 50px;
            /* Very rounded button */
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            background: linear-gradient(135deg, #f90505f4, #aa0b13);
            transition: background 0.3s;
        }

        .login-btn:hover {
            background: linear-gradient(135deg, #c10707f4, #d63232ef);
        }


        /* --- Media Queries for Mobile Responsiveness --- */
        @media (max-width: 768px) {
            body {
                padding: 0;
                /* Remove padding for a flush mobile look */
                align-items: flex-start;
                /* Start from the top on mobile */
                background-color: #fff;
                /* Match the card background */
            }

            .login-container {
                flex-direction: column;
                /* Stack vertically */
                min-height: 100vh;
                border-radius: 0;
                /* Full screen width on mobile */
                box-shadow: none;
            }

            /* --- Top Section with Background --- */
            .left-panel {
                max-width: 100%;
                min-height: 250px;
                /* Reduced height for header */
                flex: none;
            }

            .left-panel-content {
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                border-radius: 0 0 40px 40px;
                /* Rounded bottom corners */
            }

            .left-panel h1 {
                position: absolute;
                width: 100%;
                left: 0;
                top: 60%;
                /* Center the text vertically */
                text-align: center;
                margin: 0;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                font-size: 32px;
                left: 0;
                /* Reset previous desktop left value */
            }


            /* --- Form Section --- */
            .right-panel {
                max-width: 100%;
                padding: 40px 25px;
                flex: 1;
                background: #fff;
            }

            .right-panel::before {
                display: none;
                /* Hide the desktop curve */
            }

            .login-form-content {
                max-width: 100%;
            }

            .login-form-content h2 {
                font-size: 32px;
                /* Large "Welcome" */
                margin-bottom: 8px;
            }

            /* --- UI Elements --- */
            .form-control {
                background-color: #f8f9fa;
                /* Slightly gray input background */
                border: 1px solid #eee;
                height: 55px;
                /* Taller inputs for easier tapping */
            }

            .login-btn {
                height: 55px;
                font-size: 18px;
                margin-top: 10px;
            }

            /* Hide subtext from the left panel content on mobile if it blocks the title */
            .left-panel-content .sub-text {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="left-panel">
            <div class="left-panel-content">
                {{-- <i class="bi bi-cart3"></i> --}}
                <h1>LOME</h1>
                <p class="sub-text">Shop Mart</p>
                <p class="footer-text">D'GLOMIE MARKETING CORPORATION</p>
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
