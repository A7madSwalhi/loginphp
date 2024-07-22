<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h2 class="text-center">Register</h2>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $name = trim($_POST["name"]);
                $email = trim($_POST["email"]);
                $password = trim($_POST["password"]);
                $confirm_password = trim($_POST["confirm_password"]);

                $errors = [];

                // Check if all fields are filled out
                if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
                    $errors[] = "All fields are required.";
                }

                // Validate email format using regex
                if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
                    $errors[] = "Invalid email format.";
                }

                // Check if passwords match
                if ($password !== $confirm_password) {
                    $errors[] = "Passwords do not match.";
                }

                // Validate password strength (example criteria: at least 6 characters, at least one number, one uppercase and one lowercase letter)
                if (!preg_match("/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{6,}$/", $password)) {
                    $errors[] = "Password must be at least 6 characters long and include at least one number, one uppercase letter, and one lowercase letter.";
                }

                // If no errors, insert into database
                if (empty($errors)) {
                    // Database connection
                    include 'config/config.php';

                    // Check connection
                    if (!$connect) {
                        die("Connection failed: " . mysqli_connect_error());
                    }

                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Prepare and execute insert statement
                    $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
                    $stmt = mysqli_prepare($connect, $sql);
                    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);

                    if (mysqli_stmt_execute($stmt)) {
                        echo "<div class='alert alert-success'>Registration successful.</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Error: " . mysqli_stmt_error($stmt) . "</div>";
                    }

                    mysqli_stmt_close($stmt);
                    mysqli_close($connect);
                } else {
                    foreach ($errors as $error) {
                        echo "<div class='alert alert-danger'>$error</div>";
                    }
                }
            }
            ?>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
        </div>
    </div>
</body>

</html>