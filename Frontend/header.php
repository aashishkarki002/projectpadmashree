<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        :root {
            --topbar-bg: #ffffff;
            --text-color: #333333;
            --border-color: #e0e4e8;
            --dropdown-shadow: rgba(0, 0, 0, 0.1);
            --hover-bg: #f5f7fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 4rem;
            padding: 0 1.5rem;
            background-color: var(--topbar-bg);
            border-bottom: 0.0625rem solid var(--border-color);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo .img {
            height: 2.5rem;
            width: auto;
        }

        .profile {
            position: relative;
        }

        .profile-trigger {
            display: flex;
            align-items: center;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            transition: background-color 0.2s ease;
        }

        .profile-trigger:hover {
            background-color: var(--hover-bg);
        }

        .profile-logo {
            width: 2rem;
            height: 2rem;
            margin-right: 0.5rem;
            overflow: hidden;
            border-radius: 50%;
        }

        .profile-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile h1 {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-color);
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 3.5rem;
            background-color: white;
            min-width: 15rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem var(--dropdown-shadow);
            border: 0.0625rem solid var(--border-color);
            z-index: 101;
        }

        .dropdown-content.show {
            display: block;
        }

        .profile-info {
            padding: 1rem;
            border-bottom: 0.0625rem solid var(--border-color);
        }

        .profile-info-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .profile-info-header img {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-info-header span {
            font-weight: 600;
            font-size: 0.9375rem;
            color: var(--text-color);
        }

        .dropdown-item {
            display: block;
            padding: 0.75rem 1rem;
            text-decoration: none;
            color: var(--text-color);
            transition: background-color 0.2s ease;
            font-size: 0.875rem;
        }

        .dropdown-item:first-of-type {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .dropdown-item:last-of-type {
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }

        .dropdown-item:hover {
            background-color: var(--hover-bg);
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="logo">
            <img src="img/img.png" alt="Company Logo" class="img">
        </div>
        <div class="profile">
            <button class="profile-trigger" onclick="toggleDropdown()">
                <div class="profile-logo">
                    <img src="./img/profile.jpg" alt="profile">
                </div>
                <h1>Profile</h1>
            </button>
            <div class="dropdown-content" id="myDropdown">
                <div class="profile-info">
                    <div class="profile-info-header">
                        <img src="./img/profile.jpg" alt="profile">
                        <span><?php echo strtoupper(htmlspecialchars($_SESSION['firstname'])); ?></span>
                    </div>
                </div>
                <a href="setting.php" class="dropdown-item">Settings & privacy</a>
                <a href="logout.php" class="dropdown-item">Log Out</a>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            document.getElementById("myDropdown").classList.toggle("show");
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.profile-trigger') && 
                !event.target.matches('.profile-trigger *')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</body>
</html>