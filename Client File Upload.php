<?php
session_start();

// Database connection details
$host = '127.0.0.1'; // Localhost
$dbname = 'jms';     // Your database name
$username = 'root';  // Default username for local MySQL/MariaDB
$password = '';      // Default password for local MySQL/MariaDB (empty if no password is set)

// Connect to the database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Ensure only clients can access this page
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'client') {
    header("Location: login.php"); // Redirect if not a client
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['file'])) {
    $client_id = $_SESSION['user_id'];
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_type = pathinfo($file_name, PATHINFO_EXTENSION);
    
    // Allow only PDF and Word documents
    $allowed_types = ['pdf', 'doc', 'docx'];
    if (!in_array($file_type, $allowed_types)) {
        $error = "Only PDF and Word documents are allowed.";
    } else {
        $upload_dir = "uploads/clients/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_path = $upload_dir . uniqid() . "_" . basename($file_name);
        move_uploaded_file($file_tmp, $file_path);
        
        // Insert into the document table
        try {
            $stmt = $conn->prepare("INSERT INTO document (case_id, title, up_date, format) VALUES (?, ?, NOW(), ?)");
            $stmt->bindParam(1, $case_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $file_name, PDO::PARAM_STR);
            $stmt->bindParam(3, $file_type, PDO::PARAM_STR);
            $stmt->execute();
            
            $success = "File uploaded successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Files</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        /* Header Styles */
        header {
            background-color: #28a745; /* Green */
            padding: 15px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }

        nav ul li {
            margin: 0 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        nav ul li a:hover {
            background-color: #218838; /* Darker Green */
        }

        /* Container Styles */
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #28a745; /* Green */
            text-align: center;
            margin-bottom: 20px;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input[type="number"],
        input[type="file"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            padding: 10px;
            background-color: #28a745; /* Green */
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #218838; /* Darker Green */
        }

        /* Messages */
        p {
            text-align: center;
            margin-bottom: 15px;
        }

        p[style*="color:red"] {
            color: red;
        }

        p[style*="color:green"] {
            color: #28a745; /* Green */
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="lawyer Info.php">Lawyers</a></li>
                <li><a href="Booking Page.php">Book Appointment</a></li>
                <li><a href="Client File Upload.php">Upload Files</a></li>
               
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="container">
        <h2>Upload Files</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <label>Select Case ID:</label>
            <input type="number" name="case_id" required>
            <label>Select File (PDF or Word):</label>
            <input type="file" name="file" required>
            <button type="submit">Upload</button>
        </form>
    </div>
</body>
</html>