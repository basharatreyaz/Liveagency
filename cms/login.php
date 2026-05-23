<?php
// 1. Initiate secure session tracking channels
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Redirect to dashboard if session token exists
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin-dashboard.php");
    exit;
}

$error_message = "";

require_once __DIR__ . '/../config.php';

// 3. Process Login Submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db_file = DB_FILE;
    
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $error_message = "Please complete all field requirements.";
    } else if (!file_exists($db_file)) {
        $error_message = "System Engine Core Configuration Error: Database Missing.";
    } else {
        try {
            $pdo = new PDO("sqlite:" . $db_file);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Look up matching administration users securely via prepared statements
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE LOWER(username) = LOWER(?) LIMIT 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // Perform robust cryptographic verification check
            if ($user && password_verify($password, $user['password'])) {
                // Set explicit secure authentication session matrices
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id']        = $user['id'];
                $_SESSION['admin_username']  = $user['username'];
                
                // Regenerate session ID to mitigate session fixation attacks
                session_regenerate_id(true);

                header("Location: admin-dashboard.php");
                exit;
            } else {
                $error_message = "Invalid access authentication credentials.";
            }
        } catch (PDOException $e) {
            $error_message = "System Interface Error: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Dynamically define the global base path
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_url = rtrim($protocol . '://' . $host . $script_dir, '/') . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo $base_url; ?>">
    <title>CMS Secure Access Port - WP Site Doctors</title>
    <!-- Core Icon and Typography CDNs -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Outfit:400,500,600,700,800,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/login.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="login-card">
    <div class="brand-meta">
        <i class="fa-solid fa-shield-halved"></i>
        <h1>CMS Access Port</h1>
        <p style="margin-bottom: 0;">Provide admin credentials to establish session hooks.</p>
    </div>

    <?php if (!empty($error_message)): ?>
        <div class="alert-error">
            <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username or Email</label>
            <div class="input-icon-wrapper">
                <i class="fa-solid fa-user"></i>
                <input type="text" id="username" name="username" placeholder="Username or Email" required autocomplete="username">
            </div>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="input-icon-wrapper">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
            </div>
        </div>

        <button type="submit" class="btn-submit">Authenticate Terminal</button>
    </form>
</div>

</body>
</html>