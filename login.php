<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'lawyer') {
        header("Location: dashboard.php");
    } else {
        header("Location: client.php");
    }
    exit();
}

$host = '127.0.0.1';
$db   = 'jms';
$user = 'root'; // Replace with your database username
$pass = '';     // Replace with your database password
$charset = 'latin1';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Create a PDO instance (connect to the database)
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle Login Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Trim input values to remove extra spaces
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch user from the database using email
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    // Verify the password using password_verify
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['user_name'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'lawyer') {
            header("Location: dashboard.php");
        } else {
            header("Location: client.php");
        }
        exit();
    } else {
        // Invalid credentials
        $loginMessage = "Invalid email or password.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Meta tags and CSS includes -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="customnav bg-success">
        <div class="container">
            <nav class="navbar navbar-expand-lg">
            

       
            <a class="navbar-brand text-white" href="#">Lawyer Portal System</a>
            <div class="ml-auto">
                <a href="register.php" class="btn btn-light">Register</a>
            </div>
        </nav>
    </div>
</header>
                <a class="navbar-brand text-white" href="#">Lawyer Portal System</a>
            </nav>
        </div>
    </header>

    <section class="registerform">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2>Welcome Back! <i class="fas fa-gavel"></i></h2>
                    <hr/>
                    <h4>Login to Access Your Account <i class="fas fa-rocket"></i></h4>
                </div>
                <div class="col-md-6">
                    <?php if (isset($loginMessage)): ?>
                        <div class="alert alert-danger">
                            <?php echo $loginMessage; ?>
                        </div>
                    <?php endif; ?>
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                        </div>
                        <input name="login" type="submit" class="btn btn-block btn-success" value="Login" />
                    </form>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-success">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h5>All rights reserved. 2025</h5>
                </div>
            </div>
        </div>
    </footer>

    <!-- Optional JavaScript -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
</body>
</html>
