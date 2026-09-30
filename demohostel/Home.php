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


        html {
            scroll-behavior: smooth;
        }


        body {
            background-color: #f7f9fc;
            color: #222;
        }


        /* ================= NAVBAR ================= */

        nav {
            height: 75px;

            background-color: white;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 5%;

            border-bottom: 1px solid #e5e5e5;

            position: sticky;
            top: 0;
            z-index: 1000;
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

            font-size: 15px;

            font-weight: 500;

            transition: 0.3s;
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
        }


        /* ================= HOME / HERO ================= */

        .hero {
    min-height: 560px;

    display: flex;
    align-items: center;

    padding: 70px 8%;

    background-image: url("images/hostel.jpg");

    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}


        .hero-content {
            max-width: 680px;
        }


        /* Small heading */

        .tag {

            display: inline-block;

            background-color: #e5f1f9;

            color: #075894;

            padding: 8px 15px;

            border-radius: 20px;

            font-size: 13px;

            margin-bottom: 20px;
        }


        /* Main heading */

        .hero h1 {

            font-size: 55px;

            color: #075894;

            margin-bottom: 10px;
        }


        .hero h2 {

            font-size: 25px;

            font-weight: normal;

            color: #333;

            margin-bottom: 20px;
        }


        .hero p {

            color: #555;

            font-size: 16px;

            line-height: 1.7;

            max-width: 570px;
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

            transition: 0.3s;
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


        /* ================= ABOUT SECTION ================= */

        .about {

            background-color: white;

            padding: 70px 8%;

            text-align: center;
        }


        .section-title {

            color: #075894;

            font-size: 32px;

            margin-bottom: 15px;
        }


        .section-line {

            width: 60px;

            height: 3px;

            background-color: #ffb511;

            margin: 0 auto 25px auto;
        }


        .about-content {

            max-width: 850px;

            margin: auto;
        }


        .about-content p {

            color: #666;

            font-size: 16px;

            line-height: 1.8;

            margin-bottom: 15px;
        }


        /* ================= FEATURES SECTION ================= */

        .features {

            background-color: #f7f9fc;

            padding: 70px 8%;

            text-align: center;
        }


        .features-container {

            display: flex;

            justify-content: center;

            gap: 30px;

            margin-top: 40px;
        }


        .feature-box {

            background-color: white;

            width: 30%;

            padding: 35px 25px;

            border-radius: 8px;

            border: 1px solid #e5e5e5;

            transition: 0.3s;
        }


        .feature-box:hover {

            transform: translateY(-5px);

            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }


        .feature-number {

            width: 50px;

            height: 50px;

            display: flex;

            align-items: center;

            justify-content: center;

            margin: 0 auto 20px auto;

            border-radius: 50%;

            background-color: #e5f1f9;

            color: #075894;

            font-size: 20px;

            font-weight: bold;
        }


        .feature-box h3 {

            color: #075894;

            font-size: 19px;

            margin-bottom: 12px;
        }


        .feature-box p {

            color: #777;

            font-size: 14px;

            line-height: 1.6;
        }


        /* ================= LOGIN SECTION ================= */

        .login-section {

            background-color: white;

            padding: 60px 8%;

            text-align: center;
        }


        .login-section p {

            color: #666;

            margin-bottom: 25px;

            font-size: 15px;
        }


        /* ================= FOOTER ================= */

        footer {

            text-align: center;

            padding: 20px;

            background-color: #075894;

            color: white;

            font-size: 13px;
        }


        /* ================= MOBILE ================= */

        @media (max-width: 700px) {


            nav {

                padding: 0 4%;
            }


            .brand-name {

                font-size: 21px;
            }


            .brand img {

                width: 45px;

                height: 45px;
            }


            .nav-links {

                gap: 8px;
            }


            .nav-links a:not(.login) {

                display: none;
            }


            .hero {

                padding: 70px 7%;

                min-height: 500px;
            }


            .hero h1 {

                font-size: 42px;
            }


            .hero h2 {

                font-size: 21px;
            }


            .hero p {

                font-size: 15px;
            }


            .hero-buttons {

                display: flex;

                flex-direction: column;

                align-items: flex-start;

                gap: 10px;
            }


            .secondary-btn {

                margin-left: 0;
            }


            .features-container {

                flex-direction: column;

                align-items: center;
            }


            .feature-box {

                width: 100%;

                max-width: 400px;
            }


            .section-title {

                font-size: 27px;
            }

        }

    </style>

</head>


<body>


    <!-- ================================================= -->
    <!-- NAVBAR -->
    <!-- ================================================= -->

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


        <!-- NAVIGATION -->

        <div class="nav-links">


            <a href="#home">
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



    <!-- ================================================= -->
    <!-- HOME / HERO -->
    <!-- ================================================= -->

    <section class="hero" id="home">


        <div class="hero-content">


            <div class="tag">

                Hostel Management System

            </div>


            <h1>

                FixNest

            </h1>


            <h2>

                Hostel Complaint Management System

            </h2>


            <p>

                A simple and efficient platform for students
                to report hostel complaints and for staff to
                manage, track and resolve them.

            </p>


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



    <!-- ================================================= -->
    <!-- ABOUT SECTION -->
    <!-- ================================================= -->

    <section class="about" id="about">


        <h2 class="section-title">

            About FixNest

        </h2>


        <div class="section-line"></div>


        <div class="about-content">


            <p>

                FixNest is a hostel complaint management
                system developed to make the process of
                reporting and handling hostel complaints
                easier and more organized.

            </p>


            <p>

                Students can submit complaints related to
                hostel facilities and track their progress.
                Wardens can review complaints, while
                maintenance staff can handle and update
                the complaints until they are resolved.

            </p>


        </div>


    </section>



    <!-- ================================================= -->
    <!-- FEATURES SECTION -->
    <!-- ================================================= -->

    <section class="features" id="features">


        <h2 class="section-title">

            Features

        </h2>


        <div class="section-line"></div>


        <div class="features-container">


            <!-- FEATURE 1 -->

            <div class="feature-box">


                <div class="feature-number">

                    1

                </div>


                <h3>

                    Report Complaints

                </h3>


                <p>

                    Students can easily submit complaints
                    related to hostel rooms, facilities,
                    electrical problems, plumbing and other
                    hostel issues.

                </p>


            </div>



            <!-- FEATURE 2 -->

            <div class="feature-box">


                <div class="feature-number">

                    2

                </div>


                <h3>

                    Track Progress

                </h3>


                <p>

                    Students and staff can check the current
                    status of complaints and follow their
                    progress from submission to resolution.

                </p>


            </div>



            <!-- FEATURE 3 -->

            <div class="feature-box">


                <div class="feature-number">

                    3

                </div>


                <h3>

                    Quick Resolution

                </h3>


                <p>

                    Wardens and maintenance staff can review,
                    assign and update complaints to help
                    complete the work efficiently.

                </p>


            </div>


        </div>


    </section>



    <!-- ================================================= -->
    <!-- LOGIN SECTION -->
    <!-- ================================================= -->

    <section class="login-section">


        <h2 class="section-title">

            Get Started with FixNest

        </h2>


        <div class="section-line"></div>


        <p>

            Login to access the hostel complaint management system.

        </p>


        <a
            href="Index.php"
            class="primary-btn"
        >

            Login to FixNest

        </a>


    </section>



    <!-- ================================================= -->
    <!-- FOOTER -->
    <!-- ================================================= -->

    <footer>

        © 2026 FixNest | Hostel Complaint Management System

    </footer>


</body>

</html>
