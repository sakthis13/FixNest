<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FixNest - Hostel Complaint Management System</title>

    <!-- Browser Tab Logo -->
    <link rel="icon" type="image/png" href="images/image.png">


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }


        body {
            background-color: #f7f9fc;
            color: #222;
        }
        background-image:
        linear-gradient(
        rgba(255, 255, 255, 0.65),
        rgba(255, 255, 255, 0.65)
    ),
    url("images/hostel.jpg");

        /* ================= NAVBAR ================= */

        nav {
            height: 75px;

            background-color: white;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 3%;

            border-bottom: 1px solid #e5e5e5;
        }


        /* ================= LOGO ================= */

        .brand {
            display: flex;
            align-items: center;

            gap: 10px;
        }


        .brand img {
            width: 55px;
            height: 55px;

            object-fit: contain;
        }


        .brand-name {
            font-size: 25px;

            font-weight: bold;

            color: #075894;
        }


        .brand-name span {
            color: #ffb511;
        }


        /* ================= NAVIGATION ================= */

        .nav-links {
            display: flex;
            align-items: center;

            gap: 30px;
        }


        .nav-links a {
            text-decoration: none;

            color: #444;

            font-size: 14px;
        }


        .nav-links a:hover {
            color: #075894;
        }


        .login {
            background-color: #075894;

            color: white !important;

            padding: 10px 22px;

            border-radius: 5px;
        }


        .login:hover {
            background-color: #064879;

            color: white !important;
        }


        /* ================= HERO SECTION ================= */

        .hero {

            min-height: 500px;

            display: flex;

            align-items: center;

            padding: 0 8%;


            /* Hostel / College Building Background */

            background-image:

                linear-gradient(
                    rgba(255, 255, 255, 0.65),
                    rgba(255, 255, 255, 0.65)
                ),

                url("images/hostel.jpg");


            background-size: cover;

            background-position: center;

            background-repeat: no-repeat;
        }


        .hero-content {

            max-width: 650px;
        }


        /* ================= SMALL LABEL ================= */

        .tag {

            display: inline-block;

            background-color: #e5f1f9;

            color: #075894;

            padding: 7px 14px;

            border-radius: 20px;

            font-size: 13px;

            margin-bottom: 20px;
        }


        /* ================= MAIN HEADING ================= */

        .hero h1 {

            font-size: 52px;

            color: #075894;

            margin-bottom: 10px;
        }


        .hero h2 {

            font-size: 24px;

            font-weight: normal;

            color: #333;

            margin-bottom: 20px;
        }


        .hero p {

            color: #555;

            font-size: 16px;

            line-height: 1.7;

            max-width: 560px;
        }


        /* ================= BUTTONS ================= */

        .hero-buttons {

            margin-top: 30px;
        }


        .primary-btn {

            display: inline-block;

            background-color: #075894;

            color: white;

            text-decoration: none;

            padding: 13px 28px;

            border-radius: 5px;

            font-size: 14px;
        }


        .primary-btn:hover {

            background-color: #064879;
        }


        .secondary-btn {

            display: inline-block;

            margin-left: 10px;

            color: #075894;

            text-decoration: none;

            padding: 13px 20px;

            font-size: 14px;
        }


        .secondary-btn:hover {

            color: #064879;
        }


        /* ================= FEATURES ================= */

        .info {

            background-color: white;

            padding: 35px 8%;

            display: flex;

            justify-content: space-between;

            border-top: 1px solid #eee;
        }


        .info-box {

            width: 30%;
        }


        .info-box h3 {

            color: #075894;

            font-size: 16px;

            margin-bottom: 8px;
        }


        .info-box p {

            color: #777;

            font-size: 13px;

            line-height: 1.5;
        }


        /* ================= FOOTER ================= */

        footer {

            text-align: center;

            padding: 18px;

            background-color: #075894;

            color: white;

            font-size: 12px;
        }


        /* ================= MOBILE ================= */

        @media (max-width: 700px) {


            nav {

                padding: 0 3%;
            }


            .nav-links {

                gap: 10px;
            }


            .nav-links a:not(.login) {

                display: none;
            }


            .hero {

                padding: 60px 7%;

                min-height: 500px;
            }


            .hero h1 {

                font-size: 40px;
            }


            .hero h2 {

                font-size: 21px;
            }


            .hero p {

                font-size: 15px;
            }


            .info {

                flex-direction: column;

                gap: 25px;
            }


            .info-box {

                width: 100%;
            }


            .primary-btn {

                padding: 12px 20px;
            }


            .secondary-btn {

                padding: 12px 10px;
            }

        }

    </style>

</head>


<body>


    <!-- ================= NAVBAR ================= -->

    <nav>


        <!-- LOGO -->

        <div class="brand">

            <img
                src="images/image.png"
                alt="FixNest Logo"
            >


            <div class="brand-name">

                Fix<span>Nest</span>

            </div>

        </div>


        <!-- NAVIGATION LINKS -->

        <div class="nav-links">


            <a href="Home.php">
                Home
            </a>


            <a href="#about">
                About
            </a>


            <a href="#features">
                Features
            </a>


            <a href="Index.php" class="login">
                Login
            </a>


        </div>

    </nav>



    <!-- ================= HERO SECTION ================= -->

    <section class="hero">


        <div class="hero-content">


            <!-- SMALL LABEL -->

            <div class="tag">

                Hostel Management System

            </div>


            <!-- MAIN TITLE -->

            <h1>

                FixNest

            </h1>


            <!-- SUBTITLE -->

            <h2>

                Hostel Complaint Management System

            </h2>


            <!-- DESCRIPTION -->

            <p>

                A simple and efficient platform for students
                to report hostel complaints and for staff to
                manage, track and resolve them.

            </p>


            <!-- BUTTONS -->

            <div class="hero-buttons">


                <a
                    href="Index.php"
                    class="primary-btn"
                >

                    Login to FixNest

                </a>


                <a
                    href="#about"
                    class="secondary-btn"
                >

                    Learn More →

                </a>


            </div>


        </div>

    </section>



    <!-- ================= FEATURES SECTION ================= -->

    <section
        class="info"
        id="about"
    >


        <!-- FEATURE 1 -->

        <div class="info-box">


            <h3>

                Report Complaints

            </h3>


            <p>

                Students can easily submit
                hostel-related complaints.

            </p>


        </div>



        <!-- FEATURE 2 -->

        <div
            class="info-box"
            id="features"
        >


            <h3>

                Track Progress

            </h3>


            <p>

                Complaints can be reviewed
                and tracked through their status.

            </p>


        </div>



        <!-- FEATURE 3 -->

        <div class="info-box">


            <h3>

                Quick Resolution

            </h3>


            <p>

                Maintenance staff can handle
                and update complaints efficiently.

            </p>


        </div>


    </section>



    <!-- ================= FOOTER ================= -->

    <footer>

        © 2026 FixNest | Hostel Complaint Management System

    </footer>


</body>

</html>