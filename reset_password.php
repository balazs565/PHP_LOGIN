<?php
session_start();
include("connect.php");

if(empty($_SESSION['csrf_token'])){
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf_token=$_SESSION['csrf_token'];


$message = "";

if(!isset($_GET['token'])){
    die("Link invalid!");
}


$reset_token = $_GET['token'];

$stmt = $con->prepare("SELECT user_id FROM u407hYho_password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->bind_param("s", $reset_token);
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()){
    $user_id = $row['user_id'];

    if($_SERVER['REQUEST_METHOD'] == "POST"){

        if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'] ){
            die('Autenticare de CSRF');
        }
     
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if($new_password !== $confirm_password){
            $message = "Parolele nu se potrivesc!";
            $message_class="message";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $con->prepare("UPDATE u407hyho_users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);
            $stmt->execute();

            $stmt = $con->prepare("DELETE FROM u407hYho_password_resets WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $message_class="success";

            $message = "Parola a fost resetata cu succes! Te vei redirectiona la login in cateva secunde.";
            echo '<meta http-equiv="refresh" content="3;url=login.php">';
        }
    }
} else {
    die("Link expirat sau invalid!");
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Resetare Parola</title>
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
        .message {
            margin:0.5rem 0;
            color:red;
        }
        .success {
            color:green;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h2>Resetare Parola</h2>
        <?php if(!empty($message)) echo "<p class='".$message_class."'>".$message."</p>"; ?>
        <?php if(isset($user_id)): ?>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <input type="password" name="new_password" placeholder="Parola Noua" required>
            <input type="password" name="confirm_password" placeholder="Confirma Parola" required>
            <button type="submit">Reseteaza Parola</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
