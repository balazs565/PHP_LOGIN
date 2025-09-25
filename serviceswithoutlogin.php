<?php
session_start();
include("connect.php");

$stmt= $con->prepare("SELECT servicenames,price FROM U407hYho_services");
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        ul {
            list-style-type: none;
            padding: 0;
            width: 300px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        li {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }
        li:last-child {
            border-bottom: none;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background: #6c757d;
            color: white;
            cursor: pointer;
        }

    </style>
</head>
<body>
    <h1>Servicii disponibile</h1>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li><?php echo htmlspecialchars($row['servicenames']); ?> - <?php echo htmlspecialchars($row['price']); ?> RON</li>
        <?php endwhile; ?>
    </ul>

    <button type="button" onclick="window.location.href='login.php'">Inapoi la Login</button>
</body>
</html>
