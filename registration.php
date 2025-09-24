<?php
session_start();
include("connect.php");

function generateUserId($length = 8) {
    return bin2hex(random_bytes($length)); 
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $check = $con->prepare("SELECT id FROM u407hyho_users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email-ul exista deja!";
    } else {
        $user_id = generateUserId(6);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $con->prepare("INSERT INTO u407hyho_users (user_id, name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $user_id, $name, $email, $hashed_password);

        if ($stmt->execute()) {
            header("Location: login.php?msg=success");
            exit;
        } else {
            $error = "A aparut o eroare!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inregistrare</title>
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

    .login { 
        background:#fff; 
        padding:2rem; border-radius:8px; 
        box-shadow:0 4px 12px rgba(0,0,0,0.1);
        width:280px;
        text-align:center; 
    }

    .login input {
         width:100%; 
         margin:0.5rem 0; 
         padding:0.6rem; 
         border:1px solid #ccc; 
         border-radius:4px; 
        }
    .login button { 
        width:100%; 
        padding:0.6rem; 
        margin-top:0.8rem; 
        border:none; 
        border-radius:4px; 
        background:#007bff; 
        color:white; 
        cursor:pointer; 
    }

    .login button:hover {
         background:#0056b3; 
        }

    .error { 
        color:red;
         margin-bottom:0.5rem; 
        }
        
  </style>
</head>
<body>
  <form class="login" method="post" action="">
    <h2>Inregistrare</h2>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <input type="text" name="name" placeholder="Nume" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Parola" required>
    <button type="submit">Creeaza cont</button>
    <button type="button" onclick="window.location.href='login.php'">Inapoi la login</button>
  </form>
</body>
</html>
