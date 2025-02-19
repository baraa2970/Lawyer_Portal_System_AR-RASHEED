<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cases - Lawyer Portal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Global Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        /* Navigation */
        .navbar {
            background-color: #004085;
        }

        .navbar-brand, .nav-link {
            color: white !important;
            font-weight: bold;
        }

        .navbar-nav .nav-link:hover {
            color: #f8f9fa;
            text-decoration: underline;
        }

        /* Cases Section */
        .cases-container {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .case-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .case-card:hover {
            transform: translateY(-5px);
        }

        .case-header {
            font-size: 1.4rem;
            font-weight: bold;
            color: #004085;
        }

        .case-info {
            font-size: 1rem;
            color: #555;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="#">Lawyer Portal</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active" href="#">Cases</a></li>
                <li class="nav-item"><a class="nav-link" href="appointment.php">Appointments</a></li>
                
                <li class="nav-item"><a class="nav-link" href="#">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Cases Section -->
    <div class="cases-container">
        <h2 class="text-center mb-4">Assigned Cases</h2>

        <div class="case-card">
            <div class="case-header">Smith vs Jones</div>
            <div class="case-info"><strong>Type:</strong> Civil</div>
            <div class="case-info"><strong>Status:</strong> Open</div>
            <div class="case-info"><strong>Lawyer:</strong> Ao</div>
            <div class="case-info"><strong>Client:</strong> Ali</div>
            <div class="case-info"><strong>Court:</strong> Supreme Court - Room 12</div>
            <div class="case-info"><strong>Notes:</strong> Initial hearing scheduled for March 5th</div>
        </div>

    

    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
