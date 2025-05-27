<?php
$pdo = new PDO("mysql:host=localhost;dbname=project_task_db", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
