<?php
$service = isset($_GET['service']) ? $_GET['service'] : '';
echo "<h1>Disponibilitate pentru serviciul: " . htmlspecialchars($service) . "</h1>";

?>