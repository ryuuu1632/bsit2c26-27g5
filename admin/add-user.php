<?php
session_start();
if (!isset($_SESSION['username'])) { 
        echo '<script type = "text/javascript">';
        echo 'alert("Cannot Go Back! Already Logged-out");' ;
        echo 'window.location="../index.php"; ' ;
        echo '</script>' ;
}

$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'BSIT2C-Group5-Admin';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname      = htmlspecialchars(trim($_POST['firstname'] ?? ''));
    $middle         = htmlspecialchars(trim($_POST['middle'] ?? ''));
    $lastname       = htmlspecialchars(trim($_POST['lastname'] ?? ''));
    $address        = htmlspecialchars(trim($_POST['address'] ?? ''));
    $gender         = htmlspecialchars(trim($_POST['gender'] ?? ''));
    $birthday       = htmlspecialchars(trim($_POST['birthday'] ?? ''));
    $contact        = htmlspecialchars(trim($_POST['contact'] ?? ''));
    $marital_status = htmlspecialchars(trim($_POST['marital_status'] ?? ''));

    $message = "Registration successful for $firstname $lastname!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration - Group 5 BSIT 2C</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-cream: #fbf9f5;
            --maroon-dark: #4a0e17;
            --maroon-main: #6b1522;
            --maroon-gradient: linear-gradient(135deg, #7c1a29, #4a0e17);
            --gold-accent: #d4a373;
            --text-dark: #2b2b2b;
            --text-muted: #777777;
            --white: #ffffff;
            --border-light: #e8e3dc;
            --card-radius: 20px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--bg-cream);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Accent Dark Bar */
        .top-accent-bar {
            height: 8px;
            background: #2b2b2b;
            width: 100%;
        }

        /* Top Header Container */
        header {
            background: var(--white);
            border-bottom: 1px solid var(--border-light);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 4%;
            max-width: 1600px;
            margin: 0 auto;
            gap: 20px;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            flex-shrink: 0;
        }

        .logo-badge {
            width: 44px;
            height: 44px;
            background: var(--maroon-dark);
            color: var(--gold-accent);
            font-weight: 700;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            box-shadow: 0 4px 10px rgba(74, 14, 23, 0.25);
        }

        .logo-text h1 {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: var(--maroon-dark);
            text-transform: uppercase;
            line-height: 1.1;
        }

        .logo-text p {
            font-size: 9px;
            color: var(--text-muted);
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Navigation Links */
        nav {
            flex-shrink: 0;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 10px;
            align-items: center;
        }

        nav li {
            position: relative;
        }

        nav a {
            text-decoration: none;
            color: var(--text-dark);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 8px 16px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            transition: all 0.25s ease;
        }

        /* Active Pill State */
        nav li.active > a {
            color: var(--white);
            background: var(--maroon-main);
            padding: 8px 20px;
        }

        nav a:hover {
            color: var(--maroon-main);
        }

        nav li.active > a:hover {
            color: var(--white);
        }

        .arrow-down {
            font-size: 7px;
            transition: transform 0.3s ease;
        }

        nav li:hover .arrow-down {
            transform: rotate(180deg);
        }

        /* Submenu Dropdown */
        .submenu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 200px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border-light);
            padding: 8px 0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
            list-style: none;
        }

        nav li:hover .submenu {
            opacity: 1;
            visibility: visible;
            transform: translateY(5px);
        }

        .submenu li a {
            color: var(--text-dark);
            padding: 10px 18px;
            font-size: 11px;
            border-radius: 0;
            text-transform: none;
            letter-spacing: 0;
            font-weight: 500;
        }

        .submenu li a:hover {
            background: rgba(107, 21, 34, 0.06);
            color: var(--maroon-main);
        }

        /* Header Right Action Buttons */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .user-profile-badge {
            background: #fff8ee;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            color: var(--maroon-dark);
            border: 1px solid #f3e5d8;
            white-space: nowrap;
        }

        .btn-view-site {
            background: #f3f3f3;
            color: var(--text-dark);
            border: 1px solid var(--border-light);
            padding: 8px 18px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            transition: background 0.3s ease;
        }

        .btn-view-site:hover {
            background: #e8e8e8;
        }

        .btn-logout { 
            background: var(--maroon-dark); 
            color: var(--white); 
            padding: 8px 22px; 
            border-radius: 20px; 
            text-decoration: none; 
            font-size: 11px; 
            font-weight: 600; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
            white-space: nowrap;
            transition: opacity 0.3s ease;
        } 

        .btn-logout:hover { 
            opacity: 0.9; 
        }

        /* Main Content & Registration Split Layout */
        main {
            max-width: 1150px;
            width: 100%;
            margin: 0 auto;
            padding: 40px 4%;
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .registration-card {
            background: var(--white);
            border-radius: var(--card-radius);
            box-shadow: 0 15px 35px rgba(74, 14, 23, 0.08);
            border: 1px solid var(--border-light);
            display: flex;
            width: 100%;
            overflow: hidden;
        }

        .card-sidebar {
            background: var(--maroon-gradient);
            color: var(--white);
            width: 310px;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            flex-shrink: 0;
        }

        .card-sidebar::after {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-brand .brand-badge {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.15);
            color: var(--gold-accent);
            font-weight: 700;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .brand-title {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .brand-subtitle {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .sidebar-body h2 {
            font-size: 24px;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 12px;
        }

        .sidebar-body p {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.6;
        }

        .sidebar-footer {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
        }

        .card-form-area {
            flex: 1;
            padding: 35px 40px;
        }

        .form-top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: var(--maroon-main);
            font-size: 12px;
            font-weight: 600;
            background: rgba(107, 21, 34, 0.06);
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: var(--maroon-dark);
            color: var(--white);
            transform: translateX(-3px);
        }

        .alert-success {
            background: #e6f4ea;
            color: #137333;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 20px;
            border: 1px solid rgba(19, 115, 51, 0.2);
        }

        .form-section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: var(--maroon-main);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-light);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 22px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.col-span-3 {
            grid-column: span 3;
        }

        .form-group label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border-light);
            border-radius: 10px;
            font-size: 13px;
            background: #faf8f5;
            color: var(--text-dark);
            transition: all 0.25s ease;
            outline: none;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 65px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--maroon-main);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(107, 21, 34, 0.08);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 10px;
        }

        .btn-reset {
            background: transparent;
            border: 1px solid var(--border-light);
            color: var(--text-muted);
            padding: 10px 22px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-reset:hover {
            background: #f0ede8;
            color: var(--text-dark);
        }

        .btn-submit {
            background: var(--maroon-dark);
            color: var(--white);
            border: none;
            padding: 10px 28px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-submit:hover {
            opacity: 0.9;
        }

        footer {
            background: var(--white);
            border-top: 1px solid var(--border-light);
            padding: 20px 6%;
            margin-top: auto;
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: var(--text-muted);
        }

        @media (max-width: 850px) {
            .registration-card {
                flex-direction: column;
            }
            .card-sidebar {
                width: 100%;
                padding: 30px;
                gap: 20px;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-group.col-span-3 {
                grid-column: span 1;
            }
        }
    </style>
</head>
<body>

    <!-- Dark Top Stripe Accent -->
    <div class="top-accent-bar"></div>

    <!-- Header Navigation Matching Image -->
    <header>
        <div class="header-container">
            <a href="add-user.php" class="logo-container">
                <div class="logo-badge">TCC</div>
                <div class="logo-text">
                    <h1>Group 5 • BSIT 2C</h1>
                    <p>Tagoloan Community College</p>
                </div>
            </a>

            <nav>
                <ul>
                    <li class="active"><a href="add-user.php">Dashboard</a></li>
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
                        </ul>
                    </li>
                </ul>
            </nav>

            <div class="header-actions">
                <span class="user-profile-badge">👋 Welcome, <?php echo htmlspecialchars($name); ?></span>
                <a href="../index.php" class="btn-view-site">View Main Site</a>
                <a href="../logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </header>

    <main>
        <div class="registration-card">
            <!-- Sidebar -->
            <div class="card-sidebar">
                <div class="sidebar-brand">
                    <div class="brand-badge">TCC</div>
                    <div>
                        <div class="brand-title">Group 5 • BSIT 2C</div>
                        <div class="brand-subtitle">Registration Portal</div>
                    </div>
                </div>

                <div class="sidebar-body">
                    <h2>Create Account</h2>
                    <p>Complete the user details form to save new credentials into the database system.</p>
                </div>

                <div class="sidebar-footer">
                    Tagoloan Community College &copy; <?php echo date('Y'); ?>
                </div>
            </div>

            <!-- Form Body Area -->
            <div class="card-form-area">
                <div class="form-top-bar">
                    <a href="javascript:history.back()" class="btn-back">
                        ← Back to Dashboard
                    </a>
                </div>

                <?php if (!empty($message)): ?>
                    <div class="alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <form action="add-user2.php" method="POST" enctype="multipart/form-data">
                    <!-- Personal Info Section -->
                    <div class="form-section-title">Personal Details</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="firstname">First Name</label>
                            <input type="text" id="firstname" name="sfname" placeholder="Enter first name" required>
                        </div>

                        <div class="form-group">
                            <label for="middle">Middle Name</label>
                            <input type="text" id="middle" name="smname" placeholder="Enter middle name">
                        </div>

                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" id="lastname" name="slname" placeholder="Enter last name" required>
                        </div>

                        <div class="form-group col-span-3">
                            <label for="address">Address</label>
                            <textarea id="address" name="saddress" rows="2" placeholder="Enter complete address" required></textarea>
                        </div>
                    </div>

                    <!-- Additional Demographics Section -->
                    <div class="form-section-title">Additional Details</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="sgender" required>
                                <option value="" disabled selected>Select</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="lgbt">LGBT</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="birthday">Birthday</label>
                            <input type="date" id="birthday" name="sbday" required>
                        </div>

                        <div class="form-group">
                            <label for="marital_status">Marital Status</label>
                            <select id="marital_status" name="smstatus" required>
                                <option value="" disabled selected>Select</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Separated">Separated</option>
                            </select>
                        </div>

                        <div class="form-group col-span-3">
                            <label for="contact">Contact Number</label>
                            <input type="tel" id="contact" name="scontact" placeholder="e.g. 09123456789" required>
                        </div>
                    </div>

                    <!-- Action Controls -->
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">Register</button>
                    </div>

                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <div>
                &copy; <?php echo date('Y'); ?> <strong>Tagoloan Community College</strong> • BSIT 2C Group 5
            </div>
        </div>
    </footer>

</body>
</html>