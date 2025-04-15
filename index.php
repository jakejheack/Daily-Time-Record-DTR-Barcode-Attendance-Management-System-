<?php
session_start();

include_once("connection/cons.php");
$con = conns();
    

if(!empty($_SESSION['Login']) && !empty($_SESSION['Usernames'])){
    echo header ("Location: in_out.php");
}

if (isset($_POST["submit"])) {
           $username = $_POST["username"];
           $password = $_POST["password"];
            $sql = "SELECT * FROM user WHERE users = '$username'";
            $result = mysqli_query($con, $sql);
            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $row = mysqli_fetch_assoc($result);

            if ($user) {
                if ($password == $user["pw"]) {
                    $_SESSION['Login'] = $user['ID'];
                    $_SESSION['Usernames'] = $user['names'];
                    $_SESSION['Access'] = $user['access'];
                    $_SESSION['Store'] = $user['store'];
                    header("Location: in_out.php");
                    die();
                }else{
                    echo "<div class='alert alert-danger'>Password does not match!</div>";
                }
            }else{
                echo "<div class='alert alert-danger'>Username and Password does not match!</div>";
            }
}
?>
<!DOCTYPE html>
<html lang="eng">

<head>
    <title>Login Form</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8" />
    <meta name="keywords" content="Login Form" />
        <link rel="icon" type="image/x-icon" href="img/logo.png">

    <link rel="stylesheet" href="css/all.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/styling.css" type="text/css" media="all" />

</head>

<body>

    <section class="w3l-mockup-form">
        <div class="container">
            <!-- /form -->
            <div class="workinghny-form-grid">
                <div class="main-mockup">
                    
                    <div class="w3l_form align-self">
                        <div class="left_grid_info">
                            <img src="img/COH.png" alt="">
                        </div>
                    </div>
                    <div class="content-wthree">
                        <center><h2>Employees Attendance Recording System</h2></center>
                        <div><br>
                            <br>
                        <center>Login Here</center>
                        </div>
                        <form action="" method="post">
                            <input type="text" class="username" name="username" placeholder="Username" required>
                            <input type="password" class="password" name="password" placeholder="Password" style="margin-bottom: 2px;" required>
                            <p>`</p>

                            <button name="submit" name="submit" class="btn" type="submit">Login</button>
                        </form>
                        <div class="social-icons">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <script src="js/jquery.min.js"></script>
    <script src="js/all.js"></script>

</body>

</html>