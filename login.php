<?php
session_start();
include("connect.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $con->prepare("SELECT id, password, is_admin FROM u407hyho_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['is_admin'] = $row['is_admin'];
            
            if ($_SESSION['is_admin'] == 1) {
              
                header("Location: admin.php");
                exit;
            }
            else{
                header("Location: mainpage.php");
              exit;
            }
        } else {
            $error = "Parola incorecta!";
        }
    } else {
        $error = "Email inexistent!";
    }
}
?>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
  <p style="color: green; text-align:center;">Contul a fost creat cu succes! Te poti autentifica acum.</p>
<?php endif; ?>
<?php if (!empty($error)) echo "<p style='color:red;text-align:center;'>$error</p>"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
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
        padding:2rem; 
        border-radius:8px; 
        box-shadow:0 4px 12px
         rgba(0,0,0,0.1); 
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

    .login button:hover
     { 
        background:#0056b3;
     }
  </style>
</head>
<body>
  <form class="login" method="POST" action="">
    <h2>Logare</h2>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Parola" required>
    <button type="submit">Logare</button>
    <button type="button" onclick="window.location.href='registration.php'">Inregistrare</button>
  </form>
</body>
</html>
