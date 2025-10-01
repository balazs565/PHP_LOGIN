<?php
$dbhost="localhost";
$dbuser="root";
$dbpass="";
$dbname="login_db";

$con=mysqli_connect($dbhost, $dbuser, $dbpass);

if(!$con){
    die("Nu se poate conecta: " . mysqli_connect_error());
}

$sql = "CREATE DATABASE IF NOT EXISTS `$dbname`
DEFAULT CHARACTER SET utf8mb4
DEFAULT COLLATE utf8mb4_general_ci";

if(!mysqli_query($con, $sql)){
    die("Eroare la crearea bazei de date: " . mysqli_error($con));
}

mysqli_select_db($con, $dbname);


$creator = [

    "CREATE TABLE IF NOT EXISTS `u407hyho_users`(
    `id` bigint NOT NULL AUTO_INCREMENT,
    `user_id` bigint NOT NULL ,
    `name` varchar(255) NOT NULL ,
    `email` varchar(40) NOT NULL,
    `password` varchar(255) NOT NULL,
    `avatar` varchar(255) DEFAULT 'default.png',
    `is_admin` tinyint(1) DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `name` (`name`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;",

    "CREATE TABLE IF NOT EXISTS `u407hyho_password_resets`(
    `id` bigint NOT NULL AUTO_INCREMENT,
    `user_id` bigint NOT NULL ,
    `token` varchar(64) NOT NULL,
    `expires_at` datetime NOT NULL,
    `used` tinyint(1) DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8mb4;",


    "CREATE TABLE IF NOT EXISTS `u407hyho_services`(
    `id` bigint NOT NULL AUTO_INCREMENT,
    `servicenames` varchar(255),
    `price` int NOT NULL, 
    `duration` int NOT NULL,
    `active` tinyint(1) DEFAULT 1 NOT NULL,
    PRIMARY KEY(`id`)
    ) ENGINE=MYISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci",


    "CREATE TABLE IF NOT EXISTS `u407hyho_timeslots`(
    `id` bigint NOT NULL AUTO_INCREMENT,
    `service_id` int NOT NULL,
    `date` date NOT NULL,
    `start_time` time NOT NULL,
    `end_time` time NOT NULL,
    `capacity` int NOT NULL DEFAULT 1,
    `status` enum ('open','closed') DEFAULT 'open',
    PRIMARY KEY(`id`),
    KEY `service_id` (`service_id`),
    KEY `date` (`date`),
    KEY `start_time` (`start_time`),
    KEY `end_time` (`end_time`)
    ) ENGINE=MYISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci",


    "CREATE TABLE IF NOT EXISTS `u407hyho_appointments` (
    `id` bigint NOT NULL AUTO_INCREMENT,
    `user_id` varchar(50) NOT NULL,
    `service_id` int NOT NULL,
    `timeslot_id` int NOT NULL,
    `status` enum ('pending','confirmed','canceled') DEFAULT 'pending',
    `note` varchar(255) DEFAULT '-',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(`id`),
    KEY `user_id` (`user_id`),
    KEY `service_id` (`service_id`),
    KEY `timeslot_id` (`timeslot_id`) 
    ) ENGINE=MYISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci",

    "CREATE TABLE IF NOT EXISTS `u407hyho_uploads` (
    `id` bigint NOT NULL AUTO_INCREMENT,
    `user_id` varchar(50) NOT NULL,
    `appointment_id` bigint NULL,
    `path` varchar(255) NOT NULL,
    `original_name` varchar(255) NOT NULL,
    `mime` varchar(255) NOT NULL,
    `size_bytes` int NOT NULL,
    `created_at` varchar(255) NOT NULL,
    PRIMARY KEY(`id`)
    ) ENGINE=MYISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci"
];

foreach($creator as $table_sql){
    if(!mysqli_query($con, $table_sql)){
        die("Eroare la crearea tabelei: " . mysqli_error($con));
    }
}

$admin_pass= password_hash("root", PASSWORD_DEFAULT);
$user_pass= password_hash("user", PASSWORD_DEFAULT);

$inserts=[
    "INSERT IGNORE INTO `u407hyho_services`(`id`,`servicenames`,`duration`,`price`,`active`) VALUES 
    (1,'Tuns Par',20,60,1),
    (2,'Vopsit Par',90,150,1),
    (3,'Tuns Barba',15,50,1);",

    "INSERT IGNORE INTO `u407hyho_users`(`id`,`user_id`,`name`,`email`,`password`,`avatar`,`is_admin`) VALUES 
    (1,1,'Admin','root@root.com', '$admin_pass', 'default.png',1),
    (2,2,'User','user@user.com', '$user_pass', 'default.png',0);"
];

foreach($inserts as $insert_sql){
    mysqli_query($con, $insert_sql);
    }


?>