<?php require 'config.php'; check(); $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $l=trim($_POST['login']??'');$pw=$_POST['password']??'';
 if($l===''||$pw==='')$err='Enter your username/email and password.';
 else{$s=$pdo->prepare('SELECT * FROM users WHERE username=? OR email=?');$s->execute([$l,$l]);$u=$s->fetch();
  if($u&&password_verify($pw,$u['password'])){session_regenerate_id(true);unset($u['password']);$_SESSION['u']=$u;header('Location: index.php');exit;}
  $err='Invalid credentials.';}}
?><!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login</title><link rel="stylesheet" href="style.css"></head>
<body class="loginbg"><form method="post" class="card login"><h2>Hospital Management System</h2><p class="muted">Authorized staff only</p>
<?php if($err):?><div class="msg err"><?=e($err)?></div><?php endif;?>
<input type="hidden" name="t" value="<?=csrf()?>">
<label>Username or email<input name="login" value="<?=e($_POST['login']??'')?>" autofocus></label>
<label>Password<input type="password" name="password"></label>
<button class="btn">Login</button><p class="muted small">Demo: admin / admin123 · doctor / doctor123 · reception / reception123</p></form></body></html>
