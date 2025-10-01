<?php
session_start();
include("connect.php");

if(empty($_SESSION['token'])){
$_SESSION['token'] = bin2hex(random_bytes(32));
}
$token=$_SESSION['token'];

// Services Create
if(isset($_POST['create_service'])){
    
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['token'] ){
        die('Autenticare de CSRF');
    }
    $name = trim($_POST['servicenames']);
    $duration = (int)$_POST['duration'];
    $price = (float)$_POST['price'];
    $active = isset($_POST['active']) ? 1 : 0;

    $stmt = $con->prepare("INSERT INTO u407hyho_services (servicenames, duration, price, active) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sddi", $name, $duration, $price, $active);
    if($stmt->execute()){
        $service_msg = "Serviciu creat cu succes!";
    } else {
        $service_msg = "Eroare: " . $stmt->error;
    }
}

// Services Update
$edit_service = null;
if(isset($_POST['edit_service'])){

if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['token'] ){
        die('Autenticare de CSRF');
    }

    $id = (int)$_POST['service_id'];
    $stmt = $con->prepare("SELECT * FROM u407hyho_services WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_service = $stmt->get_result()->fetch_assoc();
}

if(isset($_POST['update_service'])){
    $id = (int)$_POST['service_id'];
    $name = trim($_POST['servicenames']);
    $duration = (int)$_POST['duration'];
    $price = (float)$_POST['price'];
    $active = isset($_POST['active']) ? 1 : 0;

    $stmt = $con->prepare("UPDATE u407hyho_services SET servicenames=?, duration=?, price=?, active=? WHERE id=?");
    $stmt->bind_param("sddii", $name, $duration, $price, $active, $id);
    if($stmt->execute()){
        $service_msg = "Serviciu modificat cu succes!";
        header("Refresh:0");
        exit;
    } else {
        $service_msg = "Eroare: ".$stmt->error;
    }
}
// Services Delete

if(isset($_POST['delete_service'])){

    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['token'] ){
        die('Autenticare de CSRF');
    }
    $id = (int)$_POST['service_id'];
    $stmt = $con->prepare("DELETE FROM u407hyho_services WHERE id=?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        $service_msg = "Serviciu sters cu succes!";
        header("Refresh:0");
        exit;
    } else {
        $service_msg = "Eroare: ".$stmt->error;
    }
}

// Timeslot Create
if(isset($_POST['create_timeslot'])){
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['token'] ){
        die('Autenticare de CSRF');
    }
    $service_id = (int)$_POST['service_id'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $capacity = (int)$_POST['capacity'];
    $status = isset($_POST['status']) && $_POST['status'] == 'open' ? 'open' : 'closed';

    $stmt = $con->prepare("INSERT INTO u407hyho_timeslots (service_id, date, start_time, end_time, capacity, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssis", $service_id, $date, $start_time, $end_time, $capacity, $status);
    if($stmt->execute()){
        $timeslot_msg = "Orar creat cu succes!";
    } else {
        $timeslot_msg = "Eroare: " . $stmt->error;
    }
}

// Timeslot Update
$edit_timeslot = null;
if(isset($_POST['edit_timeslot'])){
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['token'] ){
        die('Autenticare de CSRF');
    }
    $id = (int)$_POST['timeslot_id'];
    $stmt = $con->prepare("SELECT * FROM u407hyho_timeslots WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_timeslot = $stmt->get_result()->fetch_assoc();
}

if(isset($_POST['update_timeslot'])){
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['token'] ){
        die('Autenticare de CSRF');
    }
    $id = (int)$_POST['timeslot_id'];
    $service_id = (int)$_POST['service_id'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $capacity = (int)$_POST['capacity'];
    $status = isset($_POST['status']) && $_POST['status'] == 'open' ? 'open' : 'closed';

    $stmt = $con->prepare("UPDATE u407hyho_timeslots SET service_id=?, date=?, start_time=?, end_time=?, capacity=?, status=? WHERE id=?");
    $stmt->bind_param("isssisi", $service_id, $date, $start_time, $end_time, $capacity, $status, $id);
    if($stmt->execute()){
        $timeslot_msg = "Orar modificat cu succes!";
        header("Refresh:0");
        exit;
    } else {
        $timeslot_msg = "Eroare: ".$stmt->error;
    }
}


// Timeslot Delete
if(isset($_POST['delete_timeslot'])){
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['token'] ){
        die('Autenticare de CSRF');
    }
    $id = (int)$_POST['timeslot_id'];
    $stmt = $con->prepare("DELETE FROM u407hyho_timeslots WHERE id=?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        $timeslot_msg = "Orar sters cu succes!";
        header("Refresh:0");
        exit;
    } else {
        $timeslot_msg = "Eroare: ".$stmt->error;
    }
}

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'services';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Panou de Admin pentru frizeria</title>
<style>
body { 
font-family: sans-serif; 
background:#f0f2f5; 
padding:20px; 
}

nav a { 
    margin-right:15px; 
    text-decoration:none;
    color:black; 
    padding:5px 10px;
    background:#ddd;
    border-radius:5px;
}

nav a.active {
     background:#007bff;
    color:white;
}

table { 
    width:100%; 
    border-collapse:collapse; 
    margin-top:10px; 
}

th, td { 
    padding:10px;
    border:1px solid #ccc;
}

form input, form select {
     margin-bottom:10px; 
     padding:5px; 
     width:200px; 
     display:block;
}

button { 
    padding:5px 10px;
    margin-right:5px;
    cursor:pointer; 
}


.logoutbtn{
    padding 5px 10px; 
    background:red;
    color:white; 
    border:black; 
    border-radius:5px; 
    cursor:pointer;
}



</style>
</head>
<body>

<h1>Panou de Admin pentru frizeria</h1>
<nav>
    <a href="?tab=services" class="<?= ($current_tab=='services') ? 'active' : '' ?>">Servicii-administrare</a>
    <a href="?tab=timeslots" class="<?= ($current_tab=='timeslots') ? 'active' : '' ?>">Intervale-administrare</a>
    <a href="?tab=bookings" class="<?= ($current_tab=='bookings') ? 'active' : '' ?>">Programari-administrare</a>
</nav>

<div style="position:fixed; top:10px; right:10px;">
    <button onclick="window.location.href='logout.php'" class='logoutbtn'>Dezlogare</button>
</div>

<!-- Services CRUD -->
<?php if($current_tab=='services'): ?>
<h2>Servicii</h2>
<?php if(isset($service_msg)) echo "<p style='color:green;'>$service_msg</p>"; ?>

<?php
$stmt = $con->prepare("SELECT id, servicenames, duration, price, active FROM u407hyho_services");
$stmt->execute();
$result = $stmt->get_result();
?>
<table>
    <tr><th>ID</th><th>Servicii</th><th>Durata</th><th>Pret(RON)</th><th>Active</th><th>Operatiuni</th></tr>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row    ['servicenames']) ?></td>
            <td><?= $row['duration'] ?></td>
            <td><?= $row['price'] ?></td>
            <td><?= $row['active'] ? 'Da' : 'Nu' ?></td>
            <td>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= $token?>">
                    <input type="hidden" name="service_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="edit_service">Editare</button>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= $token?>">
                    <input type="hidden" name="service_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="delete_service" onclick="return confirm('Sigur vrei sa stergi?')">Stergere</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<!-- Create Service -->
<?php if(!$edit_service): ?>
<h3>Adaugare Serviciu Nou</h3>
<form method="POST" action="">
    <input type="text" name="servicenames" placeholder="Numele Serviciului" required>
    <input type="hidden" name="csrf_token" value="<?= $token?>">
    <input type="number" name="duration" placeholder="Durata (minute)" required>
    <input type="number" name="price" placeholder="Pret (RON)" required>
    Activ: <input type="checkbox" name="active" value="1" checked>
    <button type="submit" name="create_service">Creare</button>
</form>
<?php endif; ?>

<!-- Edit Service -->
<?php if($edit_service): ?>
<h3>Modificare Serviciu</h3>
<form method="POST" action="">
    <input type="hidden" name="service_id" value="<?= $edit_service['id'] ?>">
    <input type="hidden" name="csrf_token" value="<?= $token?>">
    <input type="text" name="servicenames" value="<?= htmlspecialchars($edit_service['servicenames']) ?>" required>
    <input type="number" name="duration" value="<?= $edit_service['duration'] ?>" required>
    <input type="number" name="price" value="<?= $edit_service['price'] ?>" required>
    Activ: <input type="checkbox" name="active" value="1" <?= $edit_service['active'] ? 'checked' : '' ?>>
    <button type="submit" name="update_service">Salveaza</button>
</form>
<?php endif; ?>
<?php endif; ?>

<!-- Timeslots CRUD -->
<?php if($current_tab=='timeslots'): ?>
<h2>Orar</h2>
<?php if(isset($timeslot_msg)) echo "<p style='color:green;'>$timeslot_msg</p>"; ?>

<?php
$stmt2 = $con->prepare("SELECT t.id, t.service_id, t.date, t.start_time, t.end_time, t.capacity, t.status 
                        FROM u407hyho_timeslots t 
                        LEFT JOIN u407hyho_services s ON t.service_id = s.id");
$stmt2->execute();
$result2 = $stmt2->get_result();
?>
<table>
    <tr><th>ID</th><th>Data</th><th>Inceput</th><th>Final</th><th>Capacitate</th><th>Status</th><th>Operatiuni</th></tr>
    <?php while($row = $result2->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['date']) ?></td>
            <td><?= $row['start_time'] ?></td>
            <td><?= $row['end_time'] ?></td>
            <td><?= $row['capacity'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <form method="POST" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= $token?>">
                    <input type="hidden" name="timeslot_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="edit_timeslot">Editare</button>
                </form>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="timeslot_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="delete_timeslot" onclick="return confirm('Sigur vrei sa stergi?')">Stergere</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
<!-- Create Timeslot -->
<?php if(!$edit_timeslot): ?>
<h3>Adaugare Orar Nou</h3>
<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= $token?>">
    <label>Serviciu:
        <select name="service_id" required>
            <?php
            $services = $con->query("SELECT id, servicenames FROM u407hyho_services");
            while($s = $services->fetch_assoc()){
                echo "<option value='{$s['id']}'>" . htmlspecialchars($s['servicenames']) . "</option>";
            }
            ?>
        </select>
    </label>
    <input type="date" name="date" required>
    <input type="time" name="start_time" required>
    <input type="time" name="end_time" required>
    <input type="number" name="capacity" placeholder="Capacitate" required>
    <label>Status:
        <select name="status">
            <option value="open">Deschis</option>
            <option value="closed">Inchis</option>
        </select>
    </label>
    <button type="submit" name="create_timeslot">Creare</button>
</form>
<?php endif; ?>

<!-- Edit Timeslot -->
<?php if($edit_timeslot): ?>
<h3>Modificare Orar</h3>
<form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= $token?>">
    <input type="hidden" name="timeslot_id" value="<?= $edit_timeslot['id'] ?>">
    <label>Serviciu:
        <select name="service_id" required>
            <?php
            $services = $con->query("SELECT id, servicenames FROM u407hyho_services");
            while($s = $services->fetch_assoc()){
                $selected = ($s['id']==$edit_timeslot['service_id']) ? 'selected' : '';
                echo "<option value='{$s['id']}' $selected>" . htmlspecialchars($s['servicenames']) . "</option>";
            }
            ?>
        </select>
    </label>
    <input type="datetime-local" name="start_time" value="<?= formatDateTimeLocal($edit_timeslot['start_time']) ?>" required>
    <input type="datetime-local" name="end_time" value="<?= formatDateTimeLocal($edit_timeslot['end_time']) ?>" required>
    Capacitate: <input type="number" name="capacity" value="<?= $edit_timeslot['capacity'] ?>" required>
    Status: <input type="checkbox" name="status" value="open" <?= $edit_timeslot['status']=='open' ? 'checked' : '' ?>>
    <button type="submit" name="update_timeslot">Salveaza</button>
</form>
<?php endif; ?>
<?php endif; ?>


<?php if($current_tab=='bookings'): ?>

<?php endif; ?>
<?php


$stmt = $con->prepare("
    SELECT a.id AS appointment_id, u.name AS user_name, s.servicenames, t.date, t.start_time, t.end_time, a.status, a.note
    FROM U407hYho_appointments a
    JOIN u407hyho_users u ON a.user_id = u.id
    JOIN u407hyho_services s ON a.service_id = s.id
    JOIN u407hyho_timeslots t ON a.timeslot_id = t.id
    ORDER BY t.date, t.start_time
");
$stmt->execute();
$result = $stmt->get_result();


if(isset($_POST['change_status'])){

if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['token'] ){
        die('Autenticare de CSRF');
    }

    $appointment_id = (int)$_POST['appointment_id'];
    $new_status = $_POST['status'];
    $note=$_POST['note'];

    
     if($new_status === 'canceled'){
      
        $stmt = $con->prepare("SELECT timeslot_id FROM U407hYho_appointments WHERE id=?");
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();
        $timeslot_id = $stmt->get_result()->fetch_assoc()['timeslot_id'];

        
        $stmt = $con->prepare("DELETE FROM U407hYho_appointments WHERE id=?");
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();

       
        $stmt = $con->prepare("UPDATE u407hyho_timeslots SET capacity = capacity + 1, status='open' WHERE id=?");
        $stmt->bind_param("i", $timeslot_id);
        $stmt->execute();
    } else {

        $stmt = $con->prepare("UPDATE U407hYho_appointments SET status=?, note=? WHERE id=?");
        $stmt->bind_param("ssi", $new_status, $note, $appointment_id);
        $stmt->execute();
    }

    header("Location: admin.php?tab=bookings"); 
    exit;
}

?>
<!DOCTYPE html>
<html lang="ro">
<head>
<meta charset="UTF-8">
<title>Admin - Appointments</title>
<style>
    table { border-collapse:collapse; margin:20px auto; background:#fff; box-shadow:0 4px 12px rgba(0,0,0,0.1); border-radius:8px; }
    th,td { padding:12px; border-bottom:1px solid #ddd; }
    th { background:#007bff; color:#fff; }
    select, button { padding:6px 12px; border-radius:4px; margin:2px; }
</style>
</head>
<body>
    <?php if($current_tab=='bookings'): ?>
<h1>Gestionare Rezervari</h1>
<table>
<tr>
    <th>User</th>
    <th>Serviciu</th>
    <th>Data</th>
    <th>Start</th>
    <th>End</th>
    <th>Status</th>
    <th>Actiune</th>
</tr>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['user_name']) ?></td>
    <td><?= htmlspecialchars($row['servicenames']) ?></td>
    <td><?= htmlspecialchars($row['date']) ?></td>
    <td><?= htmlspecialchars($row['start_time']) ?></td>
    <td><?= htmlspecialchars($row['end_time']) ?></td>
    <td><?= htmlspecialchars($row['status']) ?></td>
    <td>
        <form method="POST">
            <?php $note=$row['note']; ?>
            <input type="hidden" name="csrf_token" value="<?= $token?>">
            <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($row['appointment_id']) ?>">
            <input type="text" name="note" value="<?= htmlspecialchars($row[$note] ?? '') ?>">
            <select name="status">
                <option value="pending" <?= $row['status']=='pending' ? 'selected' : '' ?>>In asteptare</option>
                <option value="confirmed" <?= $row['status']=='confirmed' ? 'selected' : '' ?>>Confirmat</option>
                <option value="canceled" <?= $row['status']=='canceled' ? 'selected' : '' ?>>Anulat</option>
            </select>
            <button type="submit" name="change_status">Update</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php endif; ?>
</body>
</html>


<?php
function formatDateTimeLocal($dt) {
    return $dt ? date('Y-m-d\TH:i', strtotime($dt)) : '';
}
?>

</body>
</html>



