<?php require 'config.php';auth();$title='Reports';
$from=$_GET['from']??'';$to=$_GET['to']??'';$w=['1'];$a=[];
if(validDate($from)){$w[]='reg_date>=?';$a[]=$from;}if(validDate($to)){$w[]='reg_date<=?';$a[]=$to;}$W=implode(' AND ',$w);
if(isset($_GET['csv'])){header('Content-Type: text/csv');header('Content-Disposition: attachment; filename="patients.csv"');$o=fopen('php://output','w');fputcsv($o,['Patient ID','Name','DOB','Gender','Phone','Registered']);
 $s=$pdo->prepare("SELECT patient_code,full_name,dob,gender,phone,reg_date FROM patients WHERE $W ORDER BY reg_date");$s->execute($a);while($r=$s->fetch(PDO::FETCH_NUM))fputcsv($o,$r);exit;}
$s=$pdo->prepare("SELECT COUNT(*) FROM patients WHERE $W");$s->execute($a);$total=$s->fetchColumn();
$s=$pdo->prepare("SELECT DATE_FORMAT(reg_date,'%Y-%m') m,COUNT(*) n FROM patients WHERE $W GROUP BY m ORDER BY m");$s->execute($a);$tr=$s->fetchAll();$max=max([1]+array_column($tr,'n'));
$st=$pdo->query('SELECT status,COUNT(*) n FROM appointments GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);
require 'header.php';?>
<div class="card noprint"><form class="row">From <input type="date" name="from" value="<?=e($from)?>"> To <input type="date" name="to" value="<?=e($to)?>"><button class="btn">Apply</button>
<a class="btn ghost" href="?csv=1&from=<?=e($from)?>&to=<?=e($to)?>">Export CSV</a><button type="button" class="btn ghost" onclick="print()">Print</button></form></div>
<div class="grid2"><div class="card stat"><div><b><?=$total?></b><span>Registered patients<?=($from||$to)?' in selected range':''?></span></div></div>
<div class="card"><h3>Appointment status</h3><?php foreach(['Scheduled','Completed','Cancelled'] as $x):?><p><?=badge($x)?> <?=$st[$x]??0?></p><?php endforeach;?></div></div>
<div class="card"><h3>New registrations by month</h3><?php foreach($tr as $r):?><div class="bar"><span><?=e($r['m'])?></span><i style="width:<?=$r['n']/$max*100?>%"></i><b><?=$r['n']?></b></div><?php endforeach;?><?php if(!$tr):?><p class="muted">No data.</p><?php endif;?></div>
<?php require 'footer.php';?>
