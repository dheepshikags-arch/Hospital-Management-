<?php $nav=['index'=>['Dashboard','layout-dashboard'],'patients'=>['Patients','users'],'patient_form'=>['Register Patient','user-plus'],'appointments'=>['Appointments','calendar'],'records'=>['Medical Records','file-text'],'reports'=>['Reports','bar-chart-3'],'settings'=>['Settings','settings'],'logout'=>['Logout','log-out']];
$cur=basename($_SERVER['SCRIPT_NAME'],'.php');$act=$cur;if($cur=='patient_view'||($cur=='patient_form'&&isset($_GET['id'])))$act='patients';
$today=$pdo->query("SELECT COUNT(*) FROM appointments WHERE appt_date=CURDATE() AND status='Scheduled'")->fetchColumn();$f=flash();?>
<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($title)?> · HMS</title>
<link rel="stylesheet" href="style.css"><script src="https://unpkg.com/lucide@latest"></script></head><body>
<aside class="side"><div class="brand"><i data-lucide="hospital"></i> HMS</div>
<?php foreach($nav as $k=>$n):?><a href="<?=$k?>.php" class="<?=$k==$act?'on':''?>"><i data-lucide="<?=$n[1]?>"></i><?=$n[0]?></a><?php endforeach;?></aside>
<main><header class="top"><h1><?=e($title)?></h1>
<form action="patients.php" class="search"><i data-lucide="search"></i><input name="q" placeholder="Search patient ID or name"></form>
<a href="appointments.php?date=<?=date('Y-m-d')?>" class="icon" title="<?=$today?> appointment(s) today"><i data-lucide="bell"></i><?php if($today):?><em><?=$today?></em><?php endif;?></a>
<span class="muted"><?=date('D, d M Y')?></span><span class="user"><i data-lucide="user-circle"></i> <?=e($_SESSION['u']['name'])?> <small>(<?=e($_SESSION['u']['role'])?>)</small></span></header>
<?php if($f):?><div class="msg <?=$f[1]?>"><?=e($f[0])?></div><?php endif;?>
