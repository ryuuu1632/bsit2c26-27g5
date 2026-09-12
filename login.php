<?php
session_start();

$error = '';

$loginStatus = $_SESSION['login_status'] ?? null;
$loginMessage = $_SESSION['login_message'] ?? '';

// clear it so it doesn't show again on refresh
unset($_SESSION['login_status'], $_SESSION['login_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.min.css">
        
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Group 5 BSIT 2C</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-cream: #fbf9f5;
            --maroon-dark: #4a0e17;
            --maroon-main: #6b1522;
            --maroon-gradient: linear-gradient(135deg, #7c1a29, #4a0e17);
            --gold-gradient: linear-gradient(135deg, #d4a373, #b8860b);
            --text-dark: #2b2b2b;
            --text-muted: #777777;
            --white: #ffffff;
            --card-radius: 18px;
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
            justify-content: space-between;
        }

        /* Top Navigation Bar */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 8%;
            background-color: var(--bg-cream);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-badge {
            width: 42px;
            height: 42px;
            background: var(--maroon-gradient);
            color: #d4a373;
            font-weight: 700;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .logo-text h1 {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: var(--maroon-dark);
            text-transform: uppercase;
        }

        .logo-text p {
            font-size: 10px;
            color: var(--text-muted);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        nav {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        nav a {
            text-decoration: none;
            color: var(--text-dark);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: var(--maroon-main);
        }

        /* Main Section */
        main {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            flex-grow: 1;
        }

       .login-card {
          background: var(--white);
          width: 100%;
          max-width: 900px;
          border-radius: var(--card-radius);
          box-shadow: 0 15px 35px rgba(74, 14, 23, 0.08);
          display: flex;
          overflow: hidden;
          border: 1px solid rgba(0, 0, 0, 0.04);
          position: relative;
       }

        .left-banner {
            flex: 1;
            background: var(--maroon-gradient);
            color: var(--white);
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .left-banner::after {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
        }

        .banner-tag {
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #d4a373;
            font-weight: 600;
        }

        .banner-title {
            font-size: 32px;
            font-weight: 700;
            line-height: 1.2;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .banner-desc {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
            font-weight: 300;
        }

        .banner-footer {
            font-size: 11px;
            letter-spacing: 1px;
            color: #d4a373;
            text-transform: uppercase;
        }

        .right-form {
            flex: 1.2;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--white);
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-size: 24px;
            color: var(--maroon-dark);
            font-weight: 700;
        }

        .form-header p {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e1e1e1;
            border-radius: 10px;
            font-size: 14px;
            background-color: #fafafa;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: var(--maroon-main);
            background-color: var(--white);
            box-shadow: 0 0 0 3px rgba(107, 21, 34, 0.1);
        }

        .error-message {
            background-color: #fde8e8;
            color: #9b1c1c;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #9b1c1c;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--maroon-gradient);
            color: var(--white);
            border: none;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: opacity 0.3s ease, transform 0.2s ease;
            box-shadow: 0 6px 15px rgba(74, 14, 23, 0.2);
        }

        .btn-submit:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        .form-footer {
            margin-top: 25px;
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
        }

        .form-footer a {
            color: var(--maroon-main);
            text-decoration: none;
            font-weight: 600;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px;
            font-size: 11px;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
            }
            .left-banner {
                padding: 30px;
            }
            .right-form {
                padding: 30px;
            }
        }
    </style>
</head>
<body>

  <header>
    <div class="logo-container">
        <div class="logo-badge">TCC</div>
        <div class="logo-text">
            <h1>Group 5 • BSIT 2C</h1>
            <p>Tagoloan Community College</p>
        </div>
    </div>
    <nav>
        <a href="#">Home</a>
        <a href="#">Members</a>
        <a href="#">Group Info</a>
        <a href="#">Contact</a>
    </nav>
</header>

    <main>
       <div class="login-card">
    <a href="index.php" style="position:absolute; top:16px; right:16px; z-index:5; width:34px; height:34px; border-radius:50%; background:rgba(107,21,34,0.08); color:var(--maroon-dark); display:flex; align-items:center; justify-content:center; font-size:14px; text-decoration:none; transition:all 0.3s ease;" onmouseover="this.style.background='var(--maroon-gradient)'; this.style.color='#fff';" onmouseout="this.style.background='rgba(107,21,34,0.08)'; this.style.color='var(--maroon-dark)';" aria-label="Back to site">&#10005;</a>
    <div class="left-banner">
                <div>
                    <span class="banner-tag">BSIT 2C — GROUP 5</span>
                    <h2 class="banner-title">Five students,<br>one build.</h2>
                    <p class="banner-desc">Access the portal to manage portfolio projects, view team roles, and monitor progress.</p>
                </div>
                <div class="banner-footer">— TCC IT Excellence</div>
            </div>

            <div class="right-form">
                <div class="form-header">
                    <h2>Account Login</h2>
                    <p>Enter your credentials to continue</p>
                </div>

                <?php if (!empty($error)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="login2.php" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" class="btn-submit">Sign In</button>
                </form>

                <div class="form-footer">
                    Need assistance? <a href="#">Contact Admin</a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Tagoloan Community College — BSIT 2C Group 5</p>
    </footer>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.5/sweetalert2.all.min.js"></script>
<script>
<?php if ($loginStatus === 'success'): ?>
Swal.fire({
    icon: 'success',
    title: 'Welcome!',
    text: <?php echo json_encode($loginMessage); ?>,
    confirmButtonColor: '#6b1522',
    timer: 1500,
    showConfirmButton: false
}).then(() => {
    window.location.href = 'admin/index.php';
});
<?php elseif ($loginStatus === 'error'): ?>
Swal.fire({
    icon: 'error',
    title: 'Login Failed',
    text: <?php echo json_encode($loginMessage); ?>,
    confirmButtonColor: '#6b1522'
});
<?php endif; ?>
</script>
        

</body>
</html>