<?php
   session_start();
   $name = isset($_SESSION["name"]) ? $_SESSION["name"] : 'Admin';
?>

<div class="header-container">
    <a href="#" class="logo-container">
        <div class="logo-badge">TCC</div>
        <div class="logo-text">
            <h1>Group 5 • BSIT 2C</h1>
            <p>Tagoloan Community College</p>
        </div>
    </a>

    <nav>
        <ul>
            <li class="active"><a href="index.php">Dashboard</a></li>
            
            <li>
                <a href="#">Users <span class="arrow-down">▼</span></a>
                <ul class="submenu">
                    <li><a href="#">Manage Users</a></li>
                    <li><a href="add-user.php">Add New User</a></li>
                    <li><a href="#">Roles & Permissions</a></li>
                </ul>
            </li>

            <li>
                <a href="#">Group Info <span class="arrow-down">▼</span></a>
                <ul class="submenu">
                    <li><a href="#">Project Scope</a></li>
                    <li><a href="#">Tech Stack</a></li>
                    <li><a href="#">Documentation</a></li>
                </ul>
            </li>

            <li>
                <a href="#">System <span class="arrow-down">▼</span></a>
                <ul class="submenu">
                    <li><a href="#">Settings</a></li>
                    <li><a href="#">Database Logs</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <div class="header-actions">
        <div class="user-profile-badge">
            👋 Welcome, <?php echo htmlspecialchars($name); ?>
        </div>

        <a href="../index.php" class="btn-view-site">View Main Site</a>

        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
</div>