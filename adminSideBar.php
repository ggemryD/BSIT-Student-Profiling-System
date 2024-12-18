<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sidebar</title>
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Sidebar styling */
        aside {
            background-color: #23374b;
            color: white;
            padding: 30px 20px;
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            font-family: 'Roboto', sans-serif;
        }

        aside ul {
            list-style-type: none;
            padding: 0;
        }

        aside ul li {
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        aside ul li a {
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        aside ul li a:hover {
            background-color: #0056b3;
            color: white;
        }

        aside ul li a i {
            margin-right: 15px;  /* Space between icon and text */
            font-size: 1.4rem;
        }

        .logo {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo img {
            width: 180px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <aside>
        <!-- Logo can be added here -->
        <!-- <div class="logo">
            <img src="image/bsitLogo2.png" alt="Admin Logo">
        </div> -->

        <ul>
            <li><a href="adminHome.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="adminDashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="studentManagement.php"><i class="fas fa-users"></i> Manage Students</a></li>
            <li><a href="createForm.php"><i class="fas fa-edit"></i> Create Form</a></li>
            <li><a href="adminAnnouncement.php"><i class="fas fa-bullhorn"></i> Post Announcement</a></li>
            <li><a href="adminSettings.php"><i class="fas fa-cogs"></i> Settings</a></li>
            <li><a href="adminLogin.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>
</body>
</html>
