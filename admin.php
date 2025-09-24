<?php
session_start();
include("connect.php");




// Services Create
if(isset($_POST['create_service'])){
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
?>