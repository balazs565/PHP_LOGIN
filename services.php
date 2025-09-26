<?php
session_start();
include("connect.php");
include("fx.php");

$user_data = check_login($con);

$stmt = $con->prepare("SELECT id,servicenames,duration,price FROM u407hyho_services WHERE active = 1");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Servicii</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f0f2f5;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        h1 {
            margin-bottom: 20px;
        }
        table {
            border-collapse: collapse;
            width: 500px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
        }
        .back-btn {
            margin-top: 20px;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background: #6c757d;
            color: white;
            cursor: pointer;
        }
        .back-btn:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <h1>Servicii Active</h1>

    <?php
    $stmt=$con->prepare("SELECT id,servicenames, duration, price FROM u407hyho_services WHERE active = 1");
    $stmt->execute();
    $result = $stmt->get_result();
echo"<table>";
echo"<tr><th>Serviciu</th><th>Durata</th><th>Pret</th></tr>";

    while($row = $result->fetch_assoc()){
        echo "<tr onclick=\"window.location.href='timeslots.php?id=".$row['id']."'\" style=\"cursor:pointer;\">";
        echo "<td>".$row['servicenames']."</td>";
        echo "<td>".$row['duration']."</td>";
        echo "<td>".$row['price']."</td>";
        echo"</tr>";
        }
        echo "</table>";
   ?>

    <button class="back-btn" onclick="window.location.href='mainpage.php'">Inapoi la Profil</button>
</body>
</html>