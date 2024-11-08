<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    $username = $_POST['username'];
    $_SESSION['username'] = $username;

    // Database connection
    $con = new mysqli("localhost", "root", "rootpassword", "website");

    // Check connection
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    // Prepare and execute the SQL statement
    $stmt = $con->prepare("INSERT INTO gamesiteONE (username) VALUES (?) ON DUPLICATE KEY UPDATE username = ?");
    $stmt->bind_param("ss", $username, $username);

    if ($stmt->execute()) {
        $_SESSION['username'] = $username;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $con->close();

    // Redirect to main page
    header("Location: mainpage.php");
    exit();
}
?>

<!DOCTYPE html>
<html style="background-color: rgb(46, 46, 46);">
    <head>
            <link rel="stylesheet" href="style.css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
            <script>
                function printError(elemId, hintMsg) {
                    document.getElementById(elemId).innerHTML = hintMsg;
                }
                
                function validateForm() {
                    var name = document.contactForm.name.value;
                    var nameErr = true;

                if(name == "") {
                    printError("nameErr", "Please enter your name");
                } else {
                    var regex = /^[a-zA-Z\s]+$/;                
                    if(regex.test(name) === false) {
                        printError("nameErr", "Please enter a valid name");
                } else {
                    printError("nameErr", "");
                    nameErr = false;
                }
                }

                if(nameErr == true){
                    return false;
                }
            };
            </script>
    </head>
        <body>
            <div class="login-box">
                <form name="contactForm" onsubmit="return validateForm()" action="index.php" method="POST">
                        <div class="row">
                            <input type="text" placeholder="Name" name="username" required="required">
                            <div class="error" id="nameErr"></div>
                        </div>
                        <div class="row"><input type="submit" value="Start Game"></div>
                </form>
            </div>
        </body>
</html>