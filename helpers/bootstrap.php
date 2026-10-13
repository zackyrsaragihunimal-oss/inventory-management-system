<?php
session_start(); require_once __DIR__.'/../config/database.php';
function e($v): string { return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8'); }
function csrf(): string { if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function check_csrf(): void { if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){ http_response_code(419); exit('Permintaan tidak valid.'); } }
function require_login(): void { if(empty($_SESSION['user'])) { header('Location: login.php'); exit; } }
function require_admin(): void { require_login(); if(($_SESSION['user']['role']??'')!=='admin'){ http_response_code(403); exit('Akses admin diperlukan.'); } }
function redirect(string $url): never { header("Location: $url"); exit; }
function flash(?string $msg=null): ?string { if($msg!==null){$_SESSION['flash']=$msg;return null;} $x=$_SESSION['flash']??null; unset($_SESSION['flash']); return $x; }
function layout_start(string $title): void { require_login(); $u=$_SESSION['user']; $m=flash(); echo '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).'</title><link rel="stylesheet" href="../assets/css/app.css"></head><body><header><strong>Stockroom</strong><nav><a href="dashboard.php">Dashboard</a><a href="products/index.php">Produk</a><a href="stock/create.php">Stok</a><a href="reports/index.php">Laporan</a>'.($u['role']==='admin'?'<a href="categories/index.php">Kategori</a><a href="suppliers/index.php">Supplier</a><a href="users/index.php">Users</a>':'').'<a href="logout.php">Keluar</a></nav></header><main><p class="muted">Masuk sebagai '.e($u['name']).' · '.e($u['role']).'</p>'.($m?'<div class="flash">'.e($m).'</div>':''); }
function layout_end(): void { echo '</main><footer>Inventory Management System · Web 4</footer></body></html>'; }
