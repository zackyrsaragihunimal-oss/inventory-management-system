<?php
function db(): PDO { static $pdo; if ($pdo) return $pdo; $host=getenv('DB_HOST')?:'127.0.0.1'; $name=getenv('DB_NAME')?:'inventory_db'; $user=getenv('DB_USER')?:'root'; $pass=getenv('DB_PASS')?:''; $pdo=new PDO("mysql:host=$host;dbname=$name;charset=utf8mb4",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]); return $pdo; }
