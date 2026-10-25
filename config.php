<?php
session_start();
$pdo=new PDO('mysql:host=localhost;dbname=hms;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function auth(){if(empty($_SESSION['u'])){header('Location: login.php');exit;}}
function canRec(){return in_array($_SESSION['u']['role']??'',['admin','doctor']);}
function csrf(){return $_SESSION['t']??($_SESSION['t']=bin2hex(random_bytes(16)));}
function check(){if($_SERVER['REQUEST_METHOD']==='POST'&&!hash_equals($_SESSION['t']??'',$_POST['t']??''))die('Invalid request');}
function flash($m=null,$t='ok'){if($m){$_SESSION['f']=[$m,$t];return;}$f=$_SESSION['f']??null;unset($_SESSION['f']);return $f;}
function sel($a,$b){return $a==$b?' selected':'';}
function badge($s){return '<span class="badge '.strtolower($s).'">'.e($s).'</span>';}
function validDate($d){$x=DateTime::createFromFormat('Y-m-d',$d);return $x&&$x->format('Y-m-d')===$d;}
