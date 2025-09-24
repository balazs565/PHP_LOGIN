<?php
session_start();
include("connect.php");
include("fx.php");

$user_data = check_login($con);


$upload_error ="";
$avatar_preview = $user_data['avatar'] ?? 'default.png';

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES['avatar'])){
    $file=$_FILES['avatar'];
    $allowed_types=['image/jpeg', 'image/png'];
    $max_size = 2 * 1024 * 1024;
    if($file['error'] === 0){
        if(in_array($file['type'], $allowed_types)){
            if($file['size'] <= $max_size){
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $new_filename = bin2hex(random_bytes(8)) . '.' . $ext;
                $upload_dir = 'uploads/';
                if(!is_dir($upload_dir)){
                    mkdir($upload_dir, 0755, true);
                }
                $target = $upload_dir . $new_filename;
                if(move_uploaded_file($file['tmp_name'], $target)){
                    $stmt = $con->prepare("UPDATE u407hyho_users SET avatar = ? WHERE id = ?");
                    $stmt->bind_param("si", $new_filename, $user_data['id']);
                    $stmt->execute();

                    $avatar_preview = $new_filename;
               } else {
                    $upload_error = "Eroare la upload.";
                }

            } elseif($file['size'] > $max_size){
                $upload_error = "Fișierul depășește 2 MB.";
            }
        } elseif(!in_array($file['type'], $allowed_types)){
            $upload_error = "Doar fișiere JPG sau PNG sunt permise.";
        }
    } elseif($file['error'] !== 0){
        $upload_error = "Nu s-a selectat niciun fișier sau a apărut o eroare.";
    }
}
?>

<html>
<head>
    <title>Profil</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-family: sans-serif;
            background: #f0f2f5;
        }
        h1 {
            margin-bottom: 10px;
        }
        .profile-info {
            margin-bottom: 20px;
            text-align: center;
        }
        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        input[type=file] {
            margin-bottom: 10px;
        }
        button {
            width: 200px;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background: #007bff;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
       .buttons-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px; 
}

.avatar-form,
.logout-btn {
    width: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.upload-btn,
.logout-btn {
    width: 100%; 
}

.services-btn{
    width: 200px;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    background: #28a745; 
    color: white;
    cursor: pointer;
    margin-bottom: 10px;
}
    </style>
</head>
<body>
    <div class="profile-info">
    <h1>Bine ai venit, <?php echo htmlspecialchars($user_data['name']); ?>!</h1>
    <p>Email: <?php echo htmlspecialchars($user_data['email']); ?></p>
    <img src="uploads/<?php echo htmlspecialchars($avatar_preview); ?>" class="avatar-preview" alt="Avatar">
</div>

<div class="buttons-container">
    <form method="POST" enctype="multipart/form-data" class="avatar-form">
        <?php if(!empty($upload_error)) echo "<p class='error'>$upload_error</p>"; ?>
        <input type="file" name="avatar" accept=".jpg,.jpeg,.png" required>
        <br>
        <button type="submit" class="upload-btn">Incarca Avatar</button>
    </form>
    <button class="services-btn" onclick="window.location.href='services.php'">Vezi Servicii</button>


    <button class="logout-btn" onclick="window.location.href='logout.php'">Dezlogare</button>
</div>
</body>
</html>