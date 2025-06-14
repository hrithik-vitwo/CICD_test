<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="https://alpha.vitwo.in//public/storage/logo/165985132599981.ico">
    <title>Vitwo.ai | Dashboard</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"><!-- JavaScript Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="./public/assets/sales-order.css">
    <link rel="stylesheet" href="./public/assets/listing.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        nav.navbar.vendor-rfq-quotation {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            width: 100%;
            z-index: 99999;
        }

        .logo-section img {
            width: 120px;
            height: 30px;
            object-fit: contain;
        }

        .company-section a.dropdown-toggle {
            display: flex;
            align-items: center;
        }

        .company-section a p {
            font-size: 13px;
        }

        .main-container {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-flow: column;
            justify-content: center;
            align-items: center;
        }

        .check-container {
            width: 6.25rem;
            height: 7.5rem;
            display: flex;
            flex-flow: column;
            align-items: center;
            justify-content: space-between;
        }

        .check-container .check-background {
            width: 100%;
            height: calc(100% - 1.25rem);
            background: linear-gradient(to bottom right, #5de593, #41d67c);
            box-shadow: 0px 0px 0px 65px rgba(255, 255, 255, 0.25) inset, 0px 0px 0px 65px rgba(255, 255, 255, 0.25) inset;
            transform: scale(0.84);
            border-radius: 50%;
            animation: animateContainer 0.75s ease-out forwards 0.75s;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
        }

        .check-container .check-background svg {
            width: 65%;
            transform: translateY(0.25rem);
            stroke-dasharray: 80;
            stroke-dashoffset: 80;
            animation: animateCheck 0.35s forwards 1.25s ease-out;
        }

        .check-container .check-shadow {
            bottom: calc(-15% - 5px);
            left: 0;
            border-radius: 50%;
            background: radial-gradient(closest-side, #49da83, transparent);
            animation: animateShadow 0.75s ease-out forwards 0.75s;
        }

        @keyframes animateContainer {
            0% {
                opacity: 0;
                transform: scale(0);
                box-shadow: 0px 0px 0px 65px rgba(255, 255, 255, 0.25) inset, 0px 0px 0px 65px rgba(255, 255, 255, 0.25) inset;
            }

            25% {
                opacity: 1;
                transform: scale(0.9);
                box-shadow: 0px 0px 0px 65px rgba(255, 255, 255, 0.25) inset, 0px 0px 0px 65px rgba(255, 255, 255, 0.25) inset;
            }

            43.75% {
                transform: scale(1.15);
                box-shadow: 0px 0px 0px 43.334px rgba(255, 255, 255, 0.25) inset, 0px 0px 0px 65px rgba(255, 255, 255, 0.25) inset;
            }

            62.5% {
                transform: scale(1);
                box-shadow: 0px 0px 0px 0px rgba(255, 255, 255, 0.25) inset, 0px 0px 0px 21.667px rgba(255, 255, 255, 0.25) inset;
            }

            81.25% {
                box-shadow: 0px 0px 0px 0px rgba(255, 255, 255, 0.25) inset, 0px 0px 0px 0px rgba(255, 255, 255, 0.25) inset;
            }

            100% {
                opacity: 1;
                box-shadow: 0px 0px 0px 0px rgba(255, 255, 255, 0.25) inset, 0px 0px 0px 0px rgba(255, 255, 255, 0.25) inset;
            }
        }

        @keyframes animateCheck {
            from {
                stroke-dashoffset: 80;
            }

            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes animateShadow {
            0% {
                opacity: 0;
                width: 100%;
                height: 15%;
            }

            25% {
                opacity: 0.25;
            }

            43.75% {
                width: 40%;
                height: 7%;
                opacity: 0.35;
            }

            100% {
                width: 85%;
                height: 15%;
                opacity: 0.25;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar vendor-rfq-quotation navbar-fixed navbar-expand-lg navbar-light bg-light">
        <div class="logo-section">
            <img src="public/assets/img/logo/vitwo-logo.png" alt="company-logo">
            
        </div>
        <div class="company-section">
            <div class="dropdown">
                <a type="button" class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown">
                <img src="public/assets/img/header-icon/company.png" alt="" width="30px">
                    <p class="text-xs font-bold ml-2">
                        Company Name
                    </p>

                </a>
            </div>
        </div>
    </nav>
    <div class="main-container">
        <div class="check-container">
            <div class="check-background">
                <svg viewBox="0 0 65 51" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 25L27.3077 44L58.5 7" stroke="white" stroke-width="13" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="check-shadow"></div>

        </div>

        
        <h4 class="text-center mt-2">Thanks for participating and providing the information</h4>
            <p class="text-center text-xs mt-2">Our respond will be available soon in your inbox</p>
    </div>

    



</body>

</html>