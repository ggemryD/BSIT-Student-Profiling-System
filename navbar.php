<?php
// Start session and include configuration
// session_start();
include 'db_connection.php';

// Default profile picture
//$profile_picture = "image/bsitlogo2.png"; // Default profile picture path

// Check if student is logged in
if (isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];

    // Fetch profile picture
    $profile_picture = 'uploads/default.png'; // Default profile picture
    $query = "SELECT profile_picture FROM students WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $student_id);
    $stmt->execute();
    $stmt->bind_result($db_profile_picture);
    $stmt->fetch();
    $stmt->close();

    if (!empty($db_profile_picture)) {
        $profile_picture = $db_profile_picture; // Use uploaded picture if available
    }
}

?>


<header class="header">
    <ul class="nav-links">
        <!-- Profile Picture in the left corner -->
        <li class="profile-pic">
            <a href="#">
                <img src="<?php echo $profile_picture; ?>" alt="Profile Picture">
            </a>
        </li>
        <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="myProfile.php"><i class="fas fa-user"></i> Student Information</a></li>
        <li><a href="studentAnnouncement.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
        <li class="logout"><a href="index.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</header>

<!-- Add Font Awesome CDN link in your <head> section -->
<head>
   <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>

<style>

    /* Adjust Navbar */
    .nav-links {
        display: flex;
        align-items: center;
        background: #23374b;
        padding: 20px 15px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        position: fixed;
        top: 0;
        justify-content: center;
        width: 100%;
        z-index: 1000; /* Ensure it stays above other content */
        font-family: 'Poppins', sans-serif;
    }

    /* Ensure main content accounts for navbar height */
    body {
        margin: 0;
        overflow: auto; /* Allow scrolling */
        scrollbar-width: none; /* Firefox */
    }

    body::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Edge */
    }

    .profile-container {
        max-width: 800px;
        margin: 8rem auto; /* Adjust margin-top to account for navbar height */
        padding: 2rem;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        line-height: 1.6;
    }

    /* Profile Picture styling */
    .nav-links .profile-pic {
        margin-right: 20px; /* Space between profile pic and other links */
    }

    .nav-links .profile-pic img {
        width: 60px;  /* Set size of profile picture */
        height: 60px;/* height: 40px; Ensure it's a square image */
        border-radius: 50%; /* Make it circular */
        object-fit: cover; /* Ensure the image fits properly */
    }

    /* Navbar Links */
    .nav-links li {
        list-style: none;
        margin: 0 12px;
    }

    .nav-links li a {
        position: relative;
        color: white;
        font-size: 20px;
        font-weight: 500;
        padding: 6px 0;
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    .nav-links li a i {
        margin-right: 8px; /* Space between icon and text */
        font-size: 22px;  /* Icon size */
    }

    /* Underline effect on hover */
    .nav-links li a:before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        width: 0%;
        background: #34efdf;
        border-radius: 12px;
        transition: all 0.4s ease;
    }

    .nav-links li a:hover:before {
        width: 100%;
    }

    .nav-links li.center a:before {
        left: 50%;
        transform: translateX(-50%);
    }

    .nav-links li.upward a:before {
        width: 100%;
        bottom: -5px;
        opacity: 0;
    }

    .nav-links li.upward a:hover:before {
        bottom: 0px;
        opacity: 1;
    }

    .nav-links li.forward a:before {
        width: 100%;
        transform: scaleX(0);
        transform-origin: right;
        transition: transform 0.4s ease;
    }

    .nav-links li.forward a:hover:before {
        transform: scaleX(1);
        transform-origin: left;
    }

    .profile-pic{
    position: absolute;
    padding-right: 1250px;
    pointer-events: none;
    }

</style>
