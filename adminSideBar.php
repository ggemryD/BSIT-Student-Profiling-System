<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sidebar</title>
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Reset and Variables */
        :root {
            --sidebar-width: 280px;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #2563eb;
            /* --text-primary: #f8fafc; */
            /* --text-secondary: #94a3b8; */
            --transition-speed: 0.3s;
        }

        /* Ensure content doesn't overlap with sidebar */
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            padding-left: 6px;
            background: #f1f5f9;
        }

        /* Sidebar base styling */
        .admin-sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--sidebar-bg);
            color: #f8fafc;
            padding: 1.5rem;
            box-sizing: border-box;
            overflow-y: auto;
            z-index: 1000;
            transition: all var(--transition-speed) ease;
        }

        /* Hide scrollbar but maintain functionality */
        .admin-sidebar::-webkit-scrollbar {
            width: 0;
            background: transparent;
        }

        /* Logo section */
        .sidebar-logo {
            text-align: center;
            padding: 1rem 0 2rem;
            border-bottom: 1px solid rgba(248, 250, 252, 0.1);
            margin-bottom: 2rem;
        }

        .sidebar-logo img {
            width: 120px;
            height: auto;
            margin-bottom: 1rem;
            transition: transform var(--transition-speed);
        }

        .sidebar-logo img:hover {
            transform: scale(1.05);
        }

        .sidebar-logo h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #f8fafc;
            margin: 0;
            opacity: 0.9;
        }

        /* Navigation menu */
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav li {
            margin-bottom: 0.5rem;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 0.875rem 1rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 8px;
            transition: all var(--transition-speed);
            position: relative;
            overflow: hidden;
        }

        .sidebar-nav a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: var(--sidebar-hover);
            transform: scaleY(0);
            transition: transform var(--transition-speed);
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            color: #f8fafc;
            background: rgba(37, 99, 235, 0.1);
        }

        .sidebar-nav a:hover::before,
        .sidebar-nav a.active::before {
            transform: scaleY(1);
        }

        .sidebar-nav i {
            width: 24px;
            font-size: 1.2rem;
            margin-right: 1rem;
            text-align: center;
        }

        .sidebar-nav span {
            font-size: 0.9375rem;
            font-weight: 500;
        }

        /* Logout button special styling */
        .sidebar-nav li:last-child a {
            margin-top: 2rem;
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .sidebar-nav li:last-child a:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        /* Active state for current page */
        .sidebar-nav a.active {
            background: rgba(37, 99, 235, 0.15);
            color: #f8fafc;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            :root {
                --sidebar-width: 240px;
            }
            
            .sidebar-nav span {
                font-size: 0.875rem;
            }
        }

        /* Animation for page transitions */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .admin-sidebar {
            animation: fadeIn var(--transition-speed) ease-out;
        }
    </style>
</head>
<body>
<aside class="admin-sidebar">
        <div class="sidebar-logo">
            <img src="image/bsitLogo2.png" alt="Admin Logo">
            <h2>Admin Panel</h2>
        </div>
        
        <ul class="sidebar-nav">
            <li>
                <a href="adminDashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'adminDashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="studentManagement.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'studentManagement.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Manage Students</span>
                </a>
            </li>
            <li>
                <a href="createForm.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'createForm.php' ? 'active' : ''; ?>">
                    <i class="fas fa-edit"></i>
                    <span>Create Form</span>
                </a>
            </li>
            <li>
                <a href="adminAnnouncement.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'adminAnnouncement.php' ? 'active' : ''; ?>">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>
            </li>
            <li>
                <a href="adminSettings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'adminSettings.php' ? 'active' : ''; ?>">
                    <i class="fas fa-cogs"></i>
                    <span>Settings</span>
                </a>
            </li>
            <li>
                <a href="index.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>
</body>
</html>
