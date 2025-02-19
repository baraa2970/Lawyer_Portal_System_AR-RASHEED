<?php
session_start();

// Database connection details
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
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
// Handle Registration Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post'])) {
    // Retrieve form data
    $firstName = $_POST['first_Name'];
    $lastName = $_POST['last_Name'];
    $username = $_POST['username'];
    $contactNumber = $_POST['contact_number'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Plain text input from form
    $universityCollege = $_POST['university_College'];
    $fullAddress = $_POST['full_address'];
    $city = $_POST['city'];
    $caseHandle = implode(', ', $_POST['case_handle']); // Convert array to string
    $role = $_POST['role']; // Role: lawyer or client

    // Hash the password using password_hash (using the default algorithm, e.g., bcrypt)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to insert data into the `user` table
    $sql = "INSERT INTO user (f_name, l_name, phone_num, email, user_name, password, University, Address, City, Type_of_cases, role) 
            VALUES (:f_name, :l_name, :phone_num, :email, :user_name, :password, :university, :address, :city, :type_of_cases, :role)";
    
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':f_name', $firstName);
    $stmt->bindParam(':l_name', $lastName);
    $stmt->bindParam(':phone_num', $contactNumber);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':user_name', $username);
    $stmt->bindParam(':password', $hashedPassword); // Store hashed password
    $stmt->bindParam(':university', $universityCollege);
    $stmt->bindParam(':address', $fullAddress);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':type_of_cases', $caseHandle);
    $stmt->bindParam(':role', $role);

    // Execute the statement and redirect upon success
    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        $registrationMessage = "Error during registration.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <!-- Meta tags and CSS -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lawyer Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="customnav bg-success">
        <div class="container">
            <nav class="navbar navbar-expand-lg">
                <a class="navbar-brand text-white" href="#">Lawyer Portal System</a>
            </nav>
        </div>
    </header>

    <section class="registerform">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2>Welcome, Lawyer! <i class="fas fa-gavel"></i></h2>
                    <hr/>
                    <h4>Ready to Join the Legal Revolution? <i class="fas fa-rocket"></i></h4>
                </div>
                <div class="col-md-6">
                    <?php if (isset($registrationMessage)): ?>
                        <div class="alert alert-success">
                            <?php echo $registrationMessage; ?>
                        </div>
                    <?php endif; ?>
                    <form action="" method="post" enctype="multipart/form-data" id="validateForm">
                        <!-- First and Last Name -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="first_Name">First Name</label>
                                <input type="text" class="form-control" id="first_Name" name="first_Name" placeholder="First name" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="last_Name">Last Name</label>
                                <input type="text" class="form-control" id="last_Name" name="last_Name" placeholder="Last name" required>
                            </div>
                        </div>
                        <!-- Username -->
                        <div class="form-group">
                            <label for="Username">Username</label>
                            <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
                        </div>
                        <!-- Contact Number -->
                        <div class="form-group">
                            <label for="contact_number">Contact Number</label>
                            <input type="text" class="form-control" name="contact_number" id="contact_number" placeholder="Contact number" required>
                        </div>
                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email address" required>
                        </div>
                        <!-- Password (will be hashed) -->
                        <div class="form-group">
                            <label for="Password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        </div>
                        <!-- University / College Name -->
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="institute">University / College Name</label>
                                <input type="text" class="form-control" id="institute" name="university_College" placeholder="Institute name" required>
                            </div>
                        </div>
                        <!-- Full Address -->
                        <div class="form-group">
                            <label for="address">Full Address</label>
                            <input type="text" class="form-control" id="address" name="full_address" placeholder="Full address" required>
                        </div>
                        <!-- City -->
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city" placeholder="Enter your city" required>
                        </div>
                        <!-- Types of Cases (Checkboxes) -->
                        <div class="form-group">
                            <label for="speciality">Types of Cases</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="case_handle[]" value="Criminal matter" id="crime">
                                        <label class="form-check-label" for="crime">Criminal matter</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="case_handle[]" value="Civil matter" id="civil">
                                        <label class="form-check-label" for="civil">Civil matter</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="case_handle[]" value="Writ Jurisdiction" id="writ">
                                        <label class="form-check-label" for="writ">Writ Jurisdiction</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="case_handle[]" value="Company law" id="com">
                                        <label class="form-check-label" for="com">Company law</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="case_handle[]" value="Contract law" id="con">
                                        <label class="form-check-label" for="con">Contract law</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="case_handle[]" value="Commercial matter" id="comm">
                                        <label class="form-check-label" for="comm">Commercial matter</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="case_handle[]" value="Construction law" id="cons">
                                        <label class="form-check-label" for="cons">Construction law</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="case_handle[]" value="Information Technology" id="it">
                                        <label class="form-check-label" for="it">Information Technology</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="case_handle[]" value="Family Law" id="fam">
                                        <label class="form-check-label" for="fam">Family Law</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="case_handle[]" value="Religious Matter" id="rel">
                                        <label class="form-check-label" for="rel">Religious Matter</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Role selection -->
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="lawyer">Lawyer</option>
                                <option value="client">Client</option>
                            </select>
                        </div>
                        <!-- Terms and Conditions checkbox -->
                        <div class="form-group">
                            <div class="form-check">
                                <input id="accept" name="agree" type="checkbox" value="y" required />
                                <strong>I Agree with terms & conditions</strong>
                            </div>
                        </div>
                        <input name="post" type="submit" class="btn btn-block btn-success" value="Register" />
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
    <!-- JavaScript libraries --> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>

    
<script>
        $('#validateForm').bootstrapValidator({
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                first_Name: {
                    validators: {
                        stringLength: {
                            min: 3,
                            message: 'Please Enter your First name with minimum 3 letters length',
                        },
                        notEmpty: {
                            message: 'Please Enter your First name'
                        }
                    }
                },
                last_Name: {
                    validators: {
                        stringLength: {
                            min: 3,
                            message: 'Please Enter your Last name with minimum 3 letters length',
                        },
                        notEmpty: {
                            message: 'Please Enter your Last name'
                        }
                    }
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: 'Please Enter your email address'
                        },
                        emailAddress: {
                            message: 'Please Enter a valid email address'
                        }
                    }
                },
                contact_number: {
                    validators: {
                        stringLength: {
                            min: 9,
                            max: 9,
                            message: 'Contract Number Must be 9 Digit',
                        },
                        numeric: {
                            message: 'The phone must be a number'
                        },
                        notEmpty: {
                            message: 'Please Enter your phone number'
                        }
                    }
                },
                university_College: {
                    validators: {
                        notEmpty: {
                            message: 'Please Enter Your University or College'
                        }
                    }
                },
                full_address: {
                    validators: {
                        notEmpty: {
                            message: 'Please Enter Your Full Address'
                        }
                    }
                },
                city: {
                    validators: {
                        notEmpty: {
                            message: 'Please enter your city'
                        },
                        stringLength: {
                            min: 3,
                            message: 'City name must be at least 3 characters long'
                        }
                    }
                },
                agree: {
                    validators: {
                        notEmpty: {
                            message: 'Please Check Terms & Conditions is required'
                        }
                    }
                },
                'case_handle[]': {
                    validators: {
                        notEmpty: {
                            message: 'Please Select Types of Cases Handled'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
