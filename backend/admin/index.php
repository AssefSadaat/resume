<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_connect.php';
require_once 'auth.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit();
}

// Fetch data for dashboard
try {
    // Fetch counts
    $aboutCount = $pdo->query("SELECT COUNT(*) FROM about")->fetchColumn();
    $educationCount = $pdo->query("SELECT COUNT(*) FROM education")->fetchColumn();
    $experienceCount = $pdo->query("SELECT COUNT(*) FROM experience")->fetchColumn();
    $contactCount = $pdo->query("SELECT COUNT(*) FROM contact_info")->fetchColumn();
} catch (PDOException $e) {
    error_log("Error fetching data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #00b4b3;
            --primary-dark: #009e9d;
            --dark: #1f2937;
            --darker: #111827;
            --light: #f3f4f6;
            --purple: #8b5cf6;
            --blue: #3b82f6;
            --danger: #ef4444;
            --success: #10b981;
        }

        body {
            background: linear-gradient(135deg, var(--darker) 0%, #1e1b4b 100%);
            color: var(--light);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            margin: 0;
            min-height: 100vh;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="rgba(255,255,255,0.03)"/></svg>') repeat;
            pointer-events: none;
            z-index: -1;
        }

        .dashboard {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 1rem;
            padding: 1.5rem 2rem;
            margin-bottom: 3rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.875rem;
            font-weight: 600;
            background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        .admin-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        .menu-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 1rem;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: 0 10px 30px -10px rgba(0, 180, 179, 0.2);
        }

        .menu-card h3 {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0 0 1.5rem 0;
            font-size: 1.25rem;
            color: white;
        }

        .menu-card h3 i {
            font-size: 1.5rem;
            background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .btn {
            background: linear-gradient(135deg, var(--primary) 0%, #00d4d3 100%);
            color: white;
            padding: 0.875rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 180, 179, 0.3);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .btn-preview {
            background: linear-gradient(135deg, var(--purple) 0%, #6d28d9 100%);
        }

        .btn-preview:hover {
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
        }

        .btn-danger:hover {
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .stats {
            color: #94a3b8;
            font-size: 0.875rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stats p {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stats i {
            color: var(--primary);
        }

        @media (max-width: 768px) {
            .dashboard {
                padding: 1rem;
            }

            .header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .header-left {
                flex-direction: column;
                gap: 1rem;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 88%;
                justify-content: center;
            }

            .menu-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <div class="header">
            <div class="header-left">
                <h1>Dashboard</h1>
                <a href="../../frontend/index.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> View Resume
                </a>
            </div>
            <a href="logout.php" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>

        <div class="admin-menu">
            <!-- About Section -->
            <div class="menu-card">
                <h3><i class="fas fa-user"></i> About</h3>
                <div class="button-group">
                    <a href="about.php" class="btn">
                        <i class="fas fa-plus"></i> Add New
                    </a>
                    <a href="./about/edit.php" class="btn btn-secondary">
                        <i class="fas fa-edit"></i> Update
                    </a>
                    <a href="about/preview.php" class="btn btn-preview">
                        <i class="fas fa-eye"></i> Preview
                    </a>
                </div>
                <div class="stats">
                    <p><i class="fas fa-chart-bar"></i> <?php echo $aboutCount; ?> Entries</p>
                </div>
            </div>

            <!-- Education Section -->
            <div class="menu-card">
                <h3><i class="fas fa-graduation-cap"></i> Education</h3>
                <div class="button-group">
                    <a href="education.php" class="btn">
                        <i class="fas fa-plus"></i> Add New
                    </a>
                    <a href="./education/edit.php" class="btn btn-secondary">
                        <i class="fas fa-edit"></i> Update
                    </a>
                    <a href="./education/preview.php" class="btn btn-preview">
                        <i class="fas fa-eye"></i> Preview
                    </a>
                </div>
                <div class="stats">
                    <p><i class="fas fa-chart-bar"></i> <?php echo $educationCount; ?> Entries</p>
                </div>
            </div>

            <!-- Experience Section -->
            <div class="menu-card">
                <h3><i class="fas fa-briefcase"></i> Experience</h3>
                <div class="button-group">
                    <a href="experience.php" class="btn">
                        <i class="fas fa-plus"></i> Add New
                    </a>
                    <a href="./experience/edit.php" class="btn btn-secondary">
                        <i class="fas fa-edit"></i> Update
                    </a>
                    <a href="./experience/preview.php" class="btn btn-preview">
                        <i class="fas fa-eye"></i> Preview
                    </a>
                </div>
                <div class="stats">
                    <p><i class="fas fa-chart-bar"></i> <?php echo $experienceCount; ?> Entries</p>
                </div>
            </div>
            <!-- Contact Section -->
            <div class="menu-card">
                <h3><i class="fas fa-address-book"></i> Contact</h3>
                <div class="button-group">
                    <a href="contact/contact.php" class="btn"> <!-- Changed from contact.php -->
                        <i class="fas fa-plus"></i> Add New
                    </a>
                </div>
                <div class="stats">
                    <p><i class="fas fa-chart-bar"></i> <?php echo $contactCount; ?> Entries</p>
                </div>
            </div>

            <!-- Admin Settings -->
            <div class="menu-card">
                <h3><i class="fas fa-cog"></i> Settings</h3>
                <div class="button-group">
                    <a href="register.php" class="btn">
                        <i class="fas fa-user-cog"></i> Account
                    </a>
                    <a href="change_password.php" class="btn btn-secondary">
                        <i class="fas fa-key"></i> Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>