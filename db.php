<?php
// db.php - Database connection and common UI elements

$host = 'localhost';
$dbname = 'rslc7_rslc7_01';
$username = 'rslc7_rslc7_01';
$password = '123456';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

session_start();

// Common Header Function
function renderHeader($title = "Blogger Clone") {
    $isLoggedIn = isset($_SESSION['user_id']);
    $username = $isLoggedIn ? htmlspecialchars($_SESSION['username']) : '';
    
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$title | Blogger</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #00f2fe;
            --secondary: #4facfe;
            --accent: #f093fb;
            --bg-dark: #0f172a;
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --neon-shadow: 0 0 15px rgba(0, 242, 254, 0.4);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
            color: var(--text-main);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 48%, rgba(0, 242, 254, 0.05) 50%, transparent 52%);
            background-size: 200% 200%;
            animation: wave 10s infinite linear;
            z-index: -1;
        }

        @keyframes wave {
            0% { background-position: -100% -100%; }
            100% { background-position: 100% 100%; }
        }

        /* Navbar */
        nav {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-decoration: none;
            letter-spacing: -1px;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-main);
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: 0.3s;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .btn-neon {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.4s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-neon:hover {
            background: var(--primary);
            color: #000;
            box-shadow: var(--neon-shadow);
            transform: translateY(-2px);
        }

        .container {
            max-width: 1000px;
            margin: 3rem auto;
            padding: 0 1.5rem;
        }

        /* Hero */
        .hero {
            text-align: center;
            margin-bottom: 4rem;
            animation: fadeIn 1s ease;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .hero p {
            color: var(--text-muted);
            font-size: 1.2rem;
        }

        /* Glass Cards */
        .card {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            transition: 0.4s;
            position: relative;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-10px);
            border-color: rgba(0, 242, 254, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .card h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .card .meta {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 15px;
        }

        .card .content {
            color: #cbd5e1;
            margin-bottom: 1.5rem;
        }

        /* Forms */
        .glass-form {
            background: var(--glass);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 3rem;
            width: 100%;
            max-width: 500px;
            margin: 2rem auto;
        }

        .input-group {
            margin-bottom: 1.5rem;
        }

        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-muted);
        }

        .input-group input, .input-group textarea {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 0.8rem 1rem;
            border-radius: 12px;
            color: white;
            outline: none;
            transition: 0.3s;
        }

        .input-group input:focus, .input-group textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 10px rgba(0, 242, 254, 0.2);
        }

        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            animation: slideDown 0.5s ease;
        }

        .alert-error { background: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #fca5a5; }
        .alert-success { background: rgba(34, 197, 94, 0.2); border: 1px solid #22c55e; color: #86efac; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideDown { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        /* Mobile */
        @media (max-width: 768px) {
            .nav-links { gap: 1rem; }
            .hero h1 { font-size: 2.5rem; }
            nav { padding: 1rem 1rem; }
            .nav-links a { display: none; }
            .nav-links a.btn-neon { display: inline-block; }
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.php" class="logo">Blogger.</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
HTML;
    if ($isLoggedIn) {
        echo <<<HTML
            <a href="create_post.php">Write Post</a>
            <span style="color: var(--primary); font-weight: 600;">$username</span>
            <a href="logout.php" class="btn-neon">Logout</a>
HTML;
    } else {
        echo <<<HTML
            <a href="login.php">Login</a>
            <a href="signup.php" class="btn-neon">Sign Up</a>
HTML;
    }
    echo <<<HTML
        </div>
    </nav>
    <div class="container">
HTML;
}

// Common Footer Function
function renderFooter() {
    echo <<<HTML
    </div>
    <footer style="text-align: center; padding: 3rem; color: var(--text-muted); border-top: 1px solid var(--glass-border); margin-top: 5rem;">
        <p>&copy; 2024 Blogger Clone. Modern Neon Design.</p>
    </footer>
</body>
</html>
HTML;
}
?>
