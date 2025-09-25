<?php
session_start();
include("connect.php");
include("fx.php");

$user_data = check_login($con);

if(!isset($_GET['id'])){
    die("Serviciu invalid!");
}

$service_id = (int)$_GET['id'];
$msg = "";

$stmt_service = $con->prepare("SELECT servicenames FROM u407hyho_services WHERE id=? AND active=1");
$stmt_service->bind_param("i", $service_id);
$stmt_service->execute();
$service_result = $stmt_service->get_result()->fetch_assoc();
$service_name = $service_result ? $service_result['servicenames'] : 'Serviciu necunoscut';




if(isset($_POST['book_timeslot'])){
    $timeslot_id = (int)$_POST['timeslot_id'];

    
    $stmt = $con->prepare("SELECT id FROM U407hYho_appointments WHERE user_id=? AND timeslot_id=? AND status!='canceled'");
    $stmt->bind_param("ii", $user_data['id'], $timeslot_id);
    $stmt->execute();
    $already = $stmt->get_result()->fetch_assoc();

    if($already){
        $msg = "Ai deja o rezervare pentru acest interval.";
    } else {
     
        $stmt = $con->prepare("
            SELECT t.id, t.capacity, t.status,
                   (SELECT COUNT(*) FROM U407hYho_appointments a WHERE a.timeslot_id=t.id AND a.status='confirmed') AS confirmed_count
            FROM u407hyho_timeslots t
            WHERE t.id=? AND t.service_id=? AND t.status='open'
        ");
        $stmt->bind_param("ii", $timeslot_id, $service_id);
        $stmt->execute();
        $slot = $stmt->get_result()->fetch_assoc();

        if($slot && $slot['confirmed_count'] < $slot['capacity']){
            
            $stmt = $con->prepare("INSERT INTO U407hYho_appointments (user_id, service_id, timeslot_id, status) VALUES (?, ?, ?, 'pending')");
            $stmt->bind_param("iii", $user_data['id'], $service_id, $timeslot_id);
            if($stmt->execute()){
                $msg = "Rezervarea a fost trimisa spre confirmare!";
            } else {
                $msg = "Eroare la rezervare.";
            }
        } else {
            $msg = "Acest interval nu mai este disponibil.";
        }
    }
}


$stmt = $con->prepare("
    SELECT t.id, t.date, t.start_time, t.end_time, t.capacity, t.status,
           (SELECT COUNT(*) FROM U407hYho_appointments a WHERE a.user_id=? AND a.timeslot_id=t.id AND a.status!='canceled') AS already_booked,
           (SELECT COUNT(*) FROM U407hYho_appointments a WHERE a.timeslot_id=t.id AND a.status='confirmed') AS confirmed_count
    FROM u407hyho_timeslots t
    WHERE t.service_id=? AND t.status='open'
    ORDER BY t.date, t.start_time
");
$stmt->bind_param("ii", $user_data['id'], $service_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Rezervari</title>
<style>
body {
    font-family:sans-serif; 
    background:#f0f2f5; 
    padding:20px; 
    text-align:center; 
}

table { 
    border-collapse:collapse; 
    margin:20px auto; 
    background:#fff; 
    box-shadow:0 4px 12px rgba(0,0,0,0.1); 
    border-radius:8px; 
}

th,td { 
    padding:12px; 
    border-bottom:1px solid #ddd;
 }

th {
     background:#007bff;
      color:#fff; 
    }
button { 
    padding:6px 12px; 
    background:#28a745; 
    color:#fff; 
    border:none; 
    border-radius:4px; 
    cursor:pointer; 
}

button:hover { 
    background:#218838; 
}
button:disabled { 
    background:#aaa; 
    cursor:not-allowed; 
}

.msg { 
    margin:10px; 
    color:green; 
    }

</style>
</head>
<body>

<h1>Rezervare pentru serviciul: <?= htmlspecialchars($service_name) ?></h1>

<?php if($msg) echo "<p class='msg'>$msg</p>"; ?>

<?php if($result && $result->num_rows > 0): ?>
<table>
    <tr><th>Data</th><th>Inceput</th><th>Final</th><th>Locuri Ramase</th><th>Actiune</th></tr>
    <?php while($row = $result->fetch_assoc()): 
        $remaining = $row['capacity'] - $row['confirmed_count'];
    ?>
        <tr>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><?= htmlspecialchars($row['start_time']) ?></td>
            <td><?= htmlspecialchars($row['end_time']) ?></td>
            <td><?= $remaining ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="timeslot_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="book_timeslot" <?= $row['already_booked'] || $remaining<=0 ? 'disabled' : '' ?>>
                        <?= $row['already_booked'] ? 'Rezervat' : 'Rezerva' ?>
                    </button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
    <p>Nu sunt intervale disponibile.</p>
<?php endif; ?>

<button onclick="window.location.href='services.php'">Inapoi</button>

</body>
</html>
