<?php
session_start();

// إعداد رؤوس HTTP لتعزيز الأمان
header("X-Frame-Options: DENY");             // منع تضمين الصفحة ضمن إطار (clickjacking)
header("X-Content-Type-Options: nosniff");     // منع المتصفح من تخمين نوع المحتوى
header("X-XSS-Protection: 1; mode=block");     // تمكين الحماية من هجمات XSS

// تأكد من تسجيل دخول المحامي، وإلا يتم إعادة التوجيه إلى صفحة تسجيل الدخول
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lawyer') {
    header("Location: login.php");
    exit();
}

// الاتصال بقاعدة البيانات (تأكد من أن ملف DB/database.php يُنشئ متغير $conn باستخدام MySQLi)
$host = '127.0.0.1'; // Localhost
$dbname = 'jms';     // Your database name
$username = 'root';  // Default username for local MySQL/MariaDB
$password = '';      // Default password for local MySQL/MariaDB (empty if no password is set)

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$lawyer_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['file'])) {
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_type = pathinfo($file_name, PATHINFO_EXTENSION);
    
    // Allow only PDF and Word documents
    $allowed_types = ['pdf', 'doc', 'docx'];
    if (!in_array($file_type, $allowed_types)) {
        $error = "Only PDF and Word documents are allowed.";
    } else {
        $upload_dir = "uploads/lawyers/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_path = $upload_dir . uniqid() . "_" . basename($file_name);
        move_uploaded_file($file_tmp, $file_path);
        
        // Insert into the document table
        $stmt = $conn->prepare("INSERT INTO document (case_id, title, up_date, format) VALUES (?, ?, NOW(), ?)");
        $stmt->bind_param("iss", $case_id, $file_name, $file_type);
        $stmt->execute();
        
        $success = "File uploaded successfully.";
    }
}

// Fetch lawyer's files
$stmt = $conn->prepare("SELECT title, up_date, format FROM document WHERE lawyer_id = ?");
$stmt->bind_param("i", $lawyer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload and View Files</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <style>
        /* Green Design */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .navbar {
            background-color: #28a745; /* Green */
        }

        .navbar-brand, .nav-link {
            color: white !important;
        }

        .nav-link:hover {
            background-color: #218838; /* Darker Green */
        }

        .container {
            margin-top: 20px;
        }

        .btn-primary {
            background-color: #28a745; /* Green */
            border-color: #28a745;
        }

        .btn-primary:hover {
            background-color: #218838; /* Darker Green */
            border-color: #218838;
        }

        .text-muted {
            color: #6c757d !important;
        }
    </style>
</head>
<body>
    <!-- Header (Navbar) -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <a class="navbar-brand" href="dashboard.php">Lawyer Portal</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
           <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
               
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    
    <!-- Main content -->
    <div class="container">
        <h2>Upload and View Files</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <label>Select File (PDF or Word):</label>
            <input type="file" name="file" required>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
        
        <h3>Your Uploaded Files</h3>
        <ul>
            <?php while ($file = $result->fetch_assoc()): ?>
                <li>
                    <a href="<?php echo htmlspecialchars($file['file_path']); ?>" target="_blank">
                        <?php echo htmlspecialchars($file['title']); ?> (Uploaded on: <?php echo htmlspecialchars($file['up_date']); ?>)
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
    
    <!-- JavaScript libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
</body>
</html>