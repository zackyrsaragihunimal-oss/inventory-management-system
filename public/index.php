<?php require_once __DIR__.'/../helpers/bootstrap.php'; if(!empty($_SESSION['user'])) redirect('dashboard.php'); redirect('login.php');
