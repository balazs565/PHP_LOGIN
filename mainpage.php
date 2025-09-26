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
                    $old_avatar = $user_data['avatar'] ?? '';
                    if($old_avatar && $old_avatar !== 'default.png' && file_exists($upload_dir . $old_avatar)){
                        unlink($upload_dir . $old_avatar);
                    }
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

$stmt = $con->prepare("
    SELECT a.note, a.status, s.servicenames 
    FROM u407hyho_appointments a
    JOIN U407hYho_services s ON a.service_id = s.id
    WHERE a.user_id = ? AND a.note IS NOT NULL
    ORDER BY a.created_at DESC
    
");
$stmt->bind_param("i", $user_data['id']);
$stmt->execute();
$note_result = $stmt->get_result()->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Profil</title>
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
    .profile-container {
        background:#fff;
        padding:2rem;
        border-radius:8px;
        box-shadow:0 4px 12px rgba(0,0,0,0.1);
        width:320px;
        text-align:center;
    }
    .profile-container h1 {
        margin-bottom:10px;
    }
    .avatar-preview {
        width:120px;
        height:120px;
        border-radius:50%;
        object-fit:cover;
        margin-bottom:10px;
    }
    input[type=file] {
        margin:0.5rem 0;
        width:100%;
    }
    button {
        width:100%;
        padding:0.6rem;
        margin-top:0.5rem;
        border:none;
        border-radius:4px;
        background:#007bff;
        color:white;
        cursor:pointer;
    }
    button:hover { 
        background:#0056b3; 
    }
    .services-btn { 
        background:#28a745; 
        margin-top:0.5rem;
    }
    .services-btn:hover { 
        background:#00780D;
    }
    .logout-btn { 
        background:red; 
        margin-top:0.5rem;
    }
    .logout-btn:hover { 
        background:#b30000; 
    }
    .error { 
        color:red; 
        margin:0.5rem 0; 
    }
    .note {
        background:#f0f0f0;
        padding:0.5rem;
        margin-top:1rem;
        border-radius:5px;
        font-size:0.9rem;
    }
</style>
</head>
<body>
    <div class="profile-container">
        <h1>Bine ai venit, <?= htmlspecialchars($user_data['name']); ?>!</h1>
        <p>Email: <?= htmlspecialchars($user_data['email']); ?></p>
        <img src="uploads/<?= htmlspecialchars($avatar_preview); ?>" class="avatar-preview" alt="Avatar">

        <?php if($note_result): ?>
            <div class="note">
                <strong>Rezervare mea cea mai recenta: </strong><br>
                Serviciu: <?= htmlspecialchars($note_result['servicenames']); ?><br>
                Status: <?= htmlspecialchars($note_result['status']); ?><br>
                Admin: <?= htmlspecialchars($note_result['note']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <?php if($upload_error) echo "<p class='error'>$upload_error</p>"; ?>
            <input type="file" name="avatar" accept=".jpg,.jpeg,.png" required>
            <button type="submit">Încarcă Avatar</button>
            <button type="button" onclick="window.location.href='myreservations.php'">Programarile Mele</button>
        </form>

        <button class="services-btn" onclick="window.location.href='services.php'">Rezerva</button>
        <button class="logout-btn" onclick="window.location.href='logout.php'">Dezlogare</button>
    </div>
</body>
</html>