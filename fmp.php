<?php
date_default_timezone_set('Europe/Bucharest');
session_start();

if(empty($_SESSION['token'])){
$_SESSION['token'] = bin2hex(random_bytes(32));
}
$token=$_SESSION['token'];

include("connect.php");

$message = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){

    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['token'] ){
        die('Autenticare de CSRF');
    }
    $email = trim($_POST['email']);

    $stmt = $con->prepare("SELECT id FROM u407hyho_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($row = $result->fetch_assoc()){
        $user_id = $row['id'];

        $token = bin2hex(random_bytes(16));
        $expires = date("Y-m-d H:i:s", time() + 900);

        $stmt = $con->prepare("INSERT INTO U407hYho_password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $token, $expires);
        $stmt->execute();

        $reset_link = "http://localhost:8080/Login/reset_password.php?token=$token";
        $message = "Reset link: <a href='$reset_link'>$reset_link</a>";

    } else {
        $message = "Email inexistent!";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperarea Parola</title>
    <style>
        body {
            margin:0; 
            height:100vh; 
            display:flex; 
            justify-content:center; 
            align-items:center; 
            font-family:sans-serif; 
            background:#f0f2f5; 
        }
        .reset-container { 
            background:#fff; 
            padding:2rem; 
            border-radius:8px; 
            box-shadow:0 4px 12px rgba(0,0,0,0.1); 
            width:300px; 
            text-align:center; 
        }
        .reset-container input { 
            width:100%; 
            margin:0.5rem 0; 
            padding:0.6rem; 
            border:1px solid #ccc; 
            border-radius:4px; 
        }
        .reset-container button { 
            width:100%; 
            padding:0.6rem; 
            margin-top:0.8rem; 
            border:none; 
            border-radius:4px; 
            background:#007bff; 
            color:white; 
            cursor:pointer;
        }
        .reset-container button:hover { 
            background:#0056b3;
        }
        .reset-container h2 {
            margin-bottom:1rem;
        }
        .reset-container .back-button {
            background:red;
            margin-top:10px;
        }

        .reset-container .back-button:hover {
            background:#780000;
            margin-top:10px;
        }
        .message {
            margin:0.5rem 0;
        }
        .message a {
            color:#007bff;
            text-decoration:none;
        }
        .message a:hover {
            text-decoration:underline;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h2>Recuperarea Parola</h2>
        <?php if(!empty($message)) echo "<p class='message'>$message</p>"; ?>
        <form method="POST" action="">
             <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="email" id="email" name="email" placeholder="Email" required>
            <button type="submit">Trimite</button>
            <button type="button" class="back-button" onclick="window.location.href='login.php'">Inapoi la Logare</button>
        </form>
    </div>
</body>
</html>
