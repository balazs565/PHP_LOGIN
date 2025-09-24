<?php
session_start();
include("connect.php");
include("fx.php"); 

$user_data = check_login($con);
?>

<html>
<head>
    <title>Pagina Login</title>
</head>
<body>
    <a href="logout.php">Logout</a>
    <h1>Bine ati venit la pagina de index.</h1>
    <br>
    Buna, <?php echo htmlspecialchars($user_data['user_name']); ?>!
</body>
</html>