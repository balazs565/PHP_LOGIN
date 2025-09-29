<?php
include 'connect.php';
include 'fx.php';
session_start();


if(empty($_SESSION['token'])){
$_SESSION['token'] = bin2hex(random_bytes(32));
}
$token=$_SESSION['token'];


$user_data = check_login($con);


$stmt_user = $con->prepare("SELECT id,servicenames,duration,price FROM u407hyho_services WHERE active = 1");
$stmt_user->execute();
$result = $stmt_user->get_result();

if(isset($_POST['cancel'])){

    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['token'] ){
        die('Autenticare de CSRF');
    }

    $appointment_id=(int)$_POST['appointment_id'];
    $stmt_info = $con->prepare("SELECT s.servicenames, a.note FROM u407hyho_appointments a JOIN U407hYho_services s ON a.service_id = s.id WHERE a.id = ? AND a.user_id = ?");
    $stmt_info->bind_param("ii", $appointment_id,$user_data['id']);
    $stmt_info->execute();
    $canceled_result = $stmt_info->get_result();
    $canceled_row=$canceled_result->fetch_assoc();

    $stmt_cancel = $con->prepare("DELETE FROM u407hyho_appointments WHERE id= ? AND user_id = ?");
    $stmt_cancel->bind_param("ii", $appointment_id,$user_data['id']);
    $stmt_cancel->execute();

    $_SESSION['last_canceled'][]=[
        'servicenames' => $canceled_row['servicenames'],
        'note' => $canceled_row['note'],
        'status' => 'canceled'
    ];
}

$stmt = $con->prepare("
    SELECT a.id AS appointment_id, a.note, a.status, s.servicenames 
    FROM u407hyho_appointments a
    JOIN U407hYho_services s ON a.service_id = s.id
    WHERE a.user_id = ? AND a.note IS NOT NULL
    ORDER BY a.created_at DESC
");
$stmt->bind_param("i", $user_data['id']);
$stmt->execute();
$result = $stmt->get_result();



?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        table {
            width: 80%;
            margin: 0 auto 30px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        th {
            background: #3498db;
            color: #fff;
            padding: 15px;
            text-align: left;
            font-size: 16px;
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        tr:hover {
            background: #f1f9ff;
        }

        button {
            display: block;
            margin: 0 auto;
            padding: 12px 25px;
            font-size: 16px;
            background: #3498db;
            border: none;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: background 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        button:hover {
            background: #2980b9;
        }
        .cancel-btn{
            margin-left: -13px;
            display:block;
            padding :6px 12px;
            background:red;
        }
    </style>
</head>
<body>
    
    <h1>Rezervarile Mele</h1>
    <table>
        <tr>
            <th>Serviciu</th>
            <th>Notă</th>
            <th>Status</th>
            <th>Anulare</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['servicenames']) ?></td>
            <td><?= htmlspecialchars($row['note']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <?php if($row['status'] !== 'canceled') : ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= $token ?>">
                        <input type="hidden" name="appointment_id" value="<?= $row['appointment_id'] ?>">
                        <button type="submit" name="cancel" class="cancel-btn">Anuleaza</button>
                </form>
                <?php else: ?>
                    <span style="color:red;">Anulat</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        <?php if(isset($_SESSION['last_canceled'])): ?>
            <?php foreach($_SESSION['last_canceled'] as $canceled): ?>
                <tr>
                    <td><?= htmlspecialchars($canceled['servicenames']) ?></td>
                    <td><?= htmlspecialchars($canceled['note']) ?></td>
                    <td style="color:red; font-weight:bold;"><?= htmlspecialchars($canceled['status']) ?></td>
                    <td><span style="color:red;">Anulat</span></td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        
    </table>
    <button onclick="window.location.href='mainpage.php'">Înapoi la Profil</button>
</body>
</html>
