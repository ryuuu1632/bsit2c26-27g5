<?php
session_start();
if (!isset($_SESSION['username'])) { 
        echo '<script type = "text/javascript">';
        echo 'alert("Cannot Go Back! Already Logged-out");' ;
        echo 'window.location="../index.php"; ' ;
        echo '</script>' ;
}
        
        
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Student Admin';

$dataFile = __DIR__ . '/../data/members.json';
$members = json_decode(file_get_contents($dataFile), true);
if (!is_array($members)) { $members = []; }
$dbStatus = 'Offline';
$conn = @mysqli_connect("fdb1027.125mb.com", "4780824_tcc", "Admin@tcc_2026", "4780824_tcc");
if ($conn) {
    $dbStatus = 'Online';
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Group 5 BSIT 2C</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-cream: #fbf9f5;
            --maroon-dark: #4a0e17;
            --maroon-main: #6b1522;
            --maroon-light: #8d1c2d;
            --maroon-gradient: linear-gradient(135deg, #7c1a29, #4a0e17);
            --gold-accent: #d4a373;
            --text-dark: #2b2b2b;
            --text-muted: #777777;
            --white: #ffffff;
            --border-light: rgba(0, 0, 0, 0.06);
            --card-radius: 16px;
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

        /* Single-Line Top Navigation Header */
        header {
            background: var(--white);
            border-bottom: 1px solid var(--border-light);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 2%;
            max-width: 100%;
            margin: 0 auto;
            gap: 10px;
            flex-wrap: nowrap; /* Keeps everything forced to a single row */
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            flex-shrink: 0;
        }

        .logo-badge {
            width: 34px;
            height: 34px;
            background: var(--maroon-gradient);
            color: var(--gold-accent);
            font-weight: 700;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            box-shadow: 0 4px 10px rgba(74, 14, 23, 0.2);
        }

        .logo-text h1 {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: var(--maroon-dark);
            text-transform: uppercase;
            white-space: nowrap;
        }

        .logo-text p {
            font-size: 8px;
            color: var(--text-muted);
            letter-spacing: 0.8px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* Menu Navigation & Submenus */
        nav {
            flex-shrink: 0;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 2px;
            align-items: center;
        }

        nav li {
            position: relative;
        }

        nav a {
            text-decoration: none;
            color: var(--text-dark);
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            padding: 6px 10px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 3px;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        nav a:hover, nav li.active > a {
            color: var(--white);
            background: var(--maroon-gradient);
        }

        .arrow-down {
            font-size: 7px;
            transition: transform 0.3s ease;
        }

        nav li:hover .arrow-down {
            transform: rotate(180deg);
        }

        /* Dropdown Submenu */
      .submenu {
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 150px;
        background: var(--white);
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border-light);
        padding: 6px 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(8px);
        transition: all 0.25s ease;
        list-style: none;
        z-index: 999; /* Ensures it floats cleanly on top of other content */
       }

        nav li:hover .submenu {
            opacity: 1;
            visibility: visible;
            transform: translateY(5px);
        }

        .submenu li a {
            color: var(--text-dark);
            padding: 8px 14px;
            font-size: 10.5px;
            border-radius: 0;
            text-transform: none;
            letter-spacing: 0;
            font-weight: 500;
        }

        .submenu li a:hover {
            background: rgba(107, 21, 34, 0.06);
            color: var(--maroon-main);
        }

        /* Header Action Controls */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .user-profile-badge {
            background: #fdf5e6;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            color: var(--maroon-dark);
            border: 1px solid rgba(212, 163, 115, 0.3);
            white-space: nowrap;
        }

        .btn-view-site {
            background: #f4f4f4;
            color: var(--text-dark);
            border: 1px solid var(--border-light);
            padding: 5px 10px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
            transition: background 0.3s ease;
        }

        .btn-view-site:hover {
            background: #e9e9e9;
        }

        .btn-logout { 
            background: var(--maroon-gradient); 
            color: var(--white); 
            padding: 6px 12px; 
            border-radius: 20px; 
            text-decoration: none; 
            font-size: 10px; 
            font-weight: 600; 
            text-transform: uppercase; 
            letter-spacing: 0.3px; 
            white-space: nowrap;
        } 

        .btn-logout:hover { opacity: 0.85; }

        /* Dashboard Layout */
        main {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 35px 6%;
            flex-grow: 1;
        }

        .dashboard-header {
            margin-bottom: 25px;
        }

        .dashboard-title h2 {
            font-size: 26px;
            color: var(--maroon-dark);
            font-weight: 700;
        }

        .dashboard-title p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* SINGLE MAIN CONTAINER */
        .main-dashboard-container {
            background: var(--white);
            border-radius: var(--card-radius);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border-light);
            overflow: hidden;
        }

        /* Hero Banner */
        .hero-banner {
            background: var(--maroon-gradient);
            padding: 40px;
            color: var(--white);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero-banner::after {
            content: '';
            position: absolute;
            right: -60px;
            bottom: -60px;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 50%;
        }

        .hero-text {
            max-width: 600px;
            z-index: 1;
        }

        .hero-tag {
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--gold-accent);
            font-weight: 600;
        }

        .hero-text h3 {
            font-size: 28px;
            font-weight: 700;
            margin: 10px 0;
            line-height: 1.3;
        }

        .hero-text p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.75);
            font-weight: 300;
            line-height: 1.6;
        }

        .btn-banner {
            z-index: 1;
            background: var(--gold-accent);
            color: var(--maroon-dark);
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: opacity 0.3s ease;
        }

        .btn-banner:hover {
            opacity: 0.9;
        }

        .container-content {
            padding: 35px;
        }

        /* Cards Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 35px;
        }

        .card-button {
            display: block;
            text-decoration: none;
            background: var(--bg-cream);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--border-light);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .card-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(74, 14, 23, 0.08);
            border-color: rgba(107, 21, 34, 0.2);
        }

        .card-button:hover .card-icon {
            background: var(--maroon-gradient);
            color: var(--white);
        }

        .card-button:hover .card-arrow {
            color: var(--maroon-main);
            transform: translateX(4px);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .card-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .card-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: rgba(107, 21, 34, 0.08);
            color: var(--maroon-main);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .card-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--maroon-dark);
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }

        .card-desc {
            font-size: 11px;
            color: var(--text-muted);
        }

        .card-arrow {
            font-size: 14px;
            color: var(--text-muted);
            transition: transform 0.3s ease, color 0.3s ease;
        }

        /* Collapsible Sections */
        .collapsible-title {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            user-select: none;
            font-size: 16px;
            font-weight: 700;
            color: var(--maroon-dark);
        }

        .collapsible-title .arrow-down {
            font-size: 11px;
            transition: transform 0.3s ease;
        }

        .collapsible-title.open .arrow-down {
            transform: rotate(180deg);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-light);
        }

        td {
            padding: 15px;
            font-size: 13px;
            border-bottom: 1px solid var(--border-light);
            color: var(--text-dark);
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status-pill {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pill.active {
            background: #e6f4ea;
            color: #137333;
        }

        .status-pill.pending {
            background: #fef7e0;
            color: #b06000;
        }

        /* Footer */
        footer {
            background: var(--white);
            border-top: 1px solid var(--border-light);
            padding: 25px 6%;
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

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            margin-left: 20px;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--maroon-main);
        }

        /* Team Members Editor styles */
        .members-edit-grid {
            display: none;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
            margin-top: 20px;
        }
        .members-edit-grid.open {
            display: grid;
        }
        .tasks-table-wrap {
            display: none;
            margin-top: 20px;
        }
        .tasks-table-wrap.open {
            display: block;
        }    
        .member-edit-card {
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 20px;
            background: var(--bg-cream);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 14px;
            transition: box-shadow 0.3s ease;
        }
        .member-edit-card:hover {
            box-shadow: 0 8px 20px rgba(74, 14, 23, 0.08);
        }
        .member-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: var(--maroon-gradient);
            color: var(--gold-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
            overflow: hidden;
        }
        .member-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .member-edit-info {
            flex: 1;
            min-width: 120px;
            display: flex;
            flex-direction: column;
        }
        .member-edit-info strong {
            font-size: 14px;
            color: var(--maroon-dark);
        }
        .member-edit-info span {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }
        .edit-toggle-btn {
            background: rgba(107, 21, 34, 0.08);
            color: var(--maroon-main);
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .edit-toggle-btn:hover {
            background: var(--maroon-gradient);
            color: var(--white);
        }
        .edit-toggle-btn .arrow-down {
            font-size: 8px;
            display: inline-block;
            transition: transform 0.3s ease;
        }
        .edit-toggle-btn.open .arrow-down {
            transform: rotate(180deg);
        }
        .edit-panel {
            display: none;
            width: 100%;
            margin-top: 14px;
            padding-top: 16px;
            border-top: 1px dashed var(--border-light);
        }
        .edit-panel.open { display: block; }
        .edit-panel label {
            display: block;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: var(--text-muted);
            margin: 12px 0 6px;
        }
        .edit-panel input[type="text"],
        .edit-panel input[type="url"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            font-size: 13px;
            background: var(--white);
        }
        .photo-inputs {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .photo-inputs input[type="url"] { flex: 1; }
        .file-btn {
            background: var(--white);
            border: 1px solid #e1e1e1;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 11px;
            cursor: pointer;
            white-space: nowrap;
        }
        .file-btn input[type="file"] { display: none; }
        .edit-panel-actions {
            display: flex;
            gap: 8px;
            margin-top: 16px;
        }
        .btn-save-member {
            background: var(--maroon-gradient);
            color: var(--white);
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
        }
        .btn-cancel-member {
            background: transparent;
            border: 1px solid var(--border-light);
            color: var(--text-muted);
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 12px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- Header Navigation in a Single Line -->
    <header>
            <?php include('menu.php'); ?>        
    </header>

    <!-- Main Content Area -->
    <main>
        <div class="dashboard-header">
            <div class="dashboard-title">
                <h2>Overview Dashboard</h2>
                <p>Welcome back! Select a module below to get started.</p>
            </div>
        </div>

        <!-- SINGLE MAIN CONTAINER -->
        <div class="main-dashboard-container">
            
            <!-- Hero Feature Banner -->
            <div class="hero-banner">
                <div class="hero-text">
                    <span class="hero-tag">BSIT 2C — SLICE #03</span>
                    <h3>Five students, one build.</h3>
                    <p>Representing BSIT 2C with clean code, sharp design, and steady delivery. Monitor active roles, system modules, and tasks from one central place.</p>
                </div>
                <a href="#" class="btn-banner">View Team</a>
            </div>

            <!-- Inner Container Content -->
            <div class="container-content">
                
                <!-- Action Cards Grid -->
                <div class="metrics-grid">
                    <a href="../index.php#members" class="card-button">
                        <div class="card-header">
                            <span class="card-title">Team Members</span>
                            <div class="card-icon">05</div>
                        </div>
                        <div class="card-value"><?php echo count($members); ?> Active</div>
                        <div class="card-footer">
                            <span class="card-desc">Manage team profiles</span>
                            <span class="card-arrow">→</span>
                        </div>
                    </a>

                    <a href="#projects" class="card-button">
                        <div class="card-header">
                            <span class="card-title">Project Progress</span>
                            <div class="card-icon">⚡</div>
                        </div>
                        <div class="card-value">85%</div>
                        <div class="card-footer">
                            <span class="card-desc">View sprint timeline</span>
                            <span class="card-arrow">→</span>
                        </div>
                    </a>

                    <a href="#modules" class="card-button">
                        <div class="card-header">
                            <span class="card-title">System Modules</span>
                            <div class="card-icon">📂</div>
                        </div>
                        <div class="card-value">12 Built</div>
                        <div class="card-footer">
                            <span class="card-desc">Explore codebase</span>
                            <span class="card-arrow">→</span>
                        </div>
                    </a>

                    <a href="#logs" class="card-button">
                        <div class="card-header">
                            <span class="card-title">System Logs</span>
                            <div class="card-icon"><?php echo $dbStatus === 'Online' ? '✓' : '✕'; ?></div>
                        </div>
                        <div class="card-value"><?php echo $dbStatus; ?></div>
                        <div class="card-footer">
                            <span class="card-desc">Check DB connections</span>
                            <span class="card-arrow">→</span>
                        </div>
                    </a>
                </div>

                <!-- Collapsible Tasks Section -->
                <div class="collapsible-title" id="tasksToggle">
                    Recent Group Tasks <span class="arrow-down">▼</span>
                </div>
                <div class="tasks-table-wrap" id="tasksSection">
                    <table>
                        <thead>
                            <tr>
                                <th>Task Name</th>
                                <th>Assigned Member</th>
                                <th>Module</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Authentication API Implementation</td>
                                <td>Team Lead</td>
                                <td>Login System</td>
                                <td><span class="status-pill active">Completed</span></td>
                            </tr>
                            <tr>
                                <td>Responsive Dashboard Layout</td>
                                <td>UI/UX Developer</td>
                                <td>Frontend</td>
                                <td><span class="status-pill active">Completed</span></td>
                            </tr>
                            <tr>
                                <td>Database Schema Optimization</td>
                                <td>Backend Developer</td>
                                <td>MySQL DB</td>
                                <td><span class="status-pill pending">In Progress</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Collapsible Team Members Section -->
                <div class="collapsible-title" id="membersToggle" style="margin-top: 30px;">
                    Team Members <span class="arrow-down">▼</span>
                </div>
                <div class="members-edit-grid" id="membersSection">
                    <?php foreach ($members as $idx => $m): ?>
                    <div class="member-edit-card">
                        <div class="member-avatar">
                            <?php if (!empty($m['photo'])): ?>
                                <img src="../<?php echo htmlspecialchars($m['photo']); ?>" alt="<?php echo htmlspecialchars($m['name']); ?>">
                            <?php else: ?>
                                <?php
                                    $initials = '';
                                    foreach (array_slice(preg_split('/\s+/', trim($m['name'])), 0, 2) as $p) {
                                        $initials .= mb_strtoupper(mb_substr($p, 0, 1));
                                    }
                                    echo $initials;
                                ?>
                            <?php endif; ?>
                        </div>
                        <div class="member-edit-info">
                            <strong><?php echo htmlspecialchars($m['name']); ?></strong>
                            <span><?php echo htmlspecialchars($m['role']); ?></span>
                        </div>
                        <button type="button" class="edit-toggle-btn" data-edit="<?php echo $idx; ?>">Edit ✎</button>

                        <div class="edit-panel" id="edit-<?php echo $idx; ?>">
                            <form method="post" action="../save_member.php" enctype="multipart/form-data">
                                <input type="hidden" name="index" value="<?php echo $idx; ?>">
                                <label>Full name</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($m['name']); ?>">
                                <label>Role</label>
                                <input type="text" name="role" value="<?php echo htmlspecialchars($m['role']); ?>">
                                <label>Skills (comma separated)</label>
                                <input type="text" name="skills" value="<?php echo htmlspecialchars(implode(', ', (array)$m['skills'])); ?>">
                                <label>About / Bio</label>
                                <input type="text" name="bio" value="<?php echo htmlspecialchars($m['bio'] ?? ''); ?>" placeholder="Short description shown on their profile">
                                <label>Email</label>
                                <input type="text" name="email" value="<?php echo htmlspecialchars($m['email'] ?? ''); ?>" placeholder="name@example.com">
                                <label>Photo</label>
                                <div class="photo-inputs">
                                    <label class="file-btn">Upload<input type="file" name="photoFile" accept="image/*"></label>
                                    <input type="url" name="photoUrl" placeholder="or paste image URL">
                                </div>
                                <div class="edit-panel-actions">
                                    <button type="submit" class="btn-save-member">Save Changes</button>
                                    <button type="button" class="btn-cancel-member" data-cancel="<?php echo $idx; ?>">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-container">
            <div>
                &copy; <?php echo date('Y'); ?> <strong>Tagoloan Community College</strong> • BSIT 2C Group 5
            </div>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">System Documentation</a>
                <a href="#">Support</a>
            </div>
        </div>
    </footer>

<script>
document.getElementById('tasksToggle').addEventListener('click', () => {
    document.getElementById('tasksSection').classList.toggle('open');
    document.getElementById('tasksToggle').classList.toggle('open');
});
document.getElementById('membersToggle').addEventListener('click', () => {
    document.getElementById('membersSection').classList.toggle('open');
    document.getElementById('membersToggle').classList.toggle('open');
});
document.querySelectorAll('.edit-toggle-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('edit-' + btn.dataset.edit).classList.toggle('open');
        btn.classList.toggle('open');
    });
});
document.querySelectorAll('.btn-cancel-member').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('edit-' + btn.dataset.cancel).classList.remove('open');
    });
});
</script>
</body>
</html>