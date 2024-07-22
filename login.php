<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            margin-top: 50px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="form-container card">
                    <h2 class="text-center">Login</h2>
                    <?php
                    // Initialize variables
                    $email = $password = "";
                    $errors = [];

                    // Process the form when it's submitted
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $email = trim($_POST["email"]);
                        $password = trim($_POST["password"]);

                        // Check if both fields are filled out
                        if (empty($email) || empty($password)) {
                            $errors[] = "Both fields are required.";
                        }

                        // Validate email format using regex
                        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
                            $errors[] = "Invalid email format.";
                        }

                        // If no errors, check credentials
                        if (empty($errors)) {
                            // Database connection
                            include 'config/config.php';  // Make sure this file contains the correct connection settings

                            // Check connection
                            if (!$connect) {
                                die("Connection failed: " . mysqli_connect_error());
                            }

                            // Prepare and execute select statement
                            $sql = "SELECT password FROM users WHERE email = ?";
                            $stmt = mysqli_prepare($connect, $sql);

                            if ($stmt) {
                                mysqli_stmt_bind_param($stmt, "s", $email);
                                mysqli_stmt_execute($stmt);
                                mysqli_stmt_bind_result($stmt, $hashed_password);
                                mysqli_stmt_fetch($stmt);

                                // Check if the email exists
                                if (!$hashed_password) {
                                    $errors[] = "Invalid email or password.";
                                } else {
                                    // Verify password
                                    if (password_verify($password, $hashed_password)) {
                                        echo "<div class='alert alert-success'>Login successful.</div>";
                                        // Start session or redirect to another page here
                                    } else {
                                        $errors[] = "Invalid email or password.";
                                    }
                                }

                                mysqli_stmt_close($stmt);
                            } else {
                                $errors[] = "Database query error: " . mysqli_error($connect);
                            }

                            mysqli_close($connect);
                        }

                        // Display errors
                        foreach ($errors as $error) {
                            echo "<div class='alert alert-danger'>$error</div>";
                        }
                    }
                    ?>
                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>