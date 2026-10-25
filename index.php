<?php require 'config.php';auth();$title='Dashboard';
$q=fn($s)=>$pdo->query($s)->fetchColumn();
$cards=[['Total Patients',$q('SELECT COUNT(*) FROM patients'),'users'],["Today's Appointments",$q("SELECT COUNT(*) FROM appointments WHERE appt_date=CURDATE() AND status<>'Cancelled'"),'calendar-check'],['Medical Records',$q('SELECT COUNT(*) FROM records'),'file-text'],['Pending Appointments',$q("SELECT COUNT(*) FROM appointments WHERE status='Scheduled' AND appt_date>=CURDATE()"),'clock']];
$recent=$pdo->query('SELECT * FROM patients ORDER BY created_at DESC LIMIT 5')->fetchAll();
$up=$pdo->query("SELECT a.*,p.full_name FROM appointments a JOIN patients p ON p.id=a.patient_id WHERE a.status='Scheduled' AND a.appt_date>=CURDATE() ORDER BY a.appt_date,a.appt_time LIMIT 6")->fetchAll();
$tr=array_reverse($pdo->query("SELECT DATE_FORMAT(reg_date,'%Y-%m') m,COUNT(*) n FROM patients GROUP BY m ORDER BY m DESC LIMIT 6")->fetchAll());$max=max([1]+array_column($tr,'n'));
$act=$pdo->query('SELECT full_name,patient_code,created_at,updated_at FROM patients ORDER BY updated_at DESC LIMIT 5')->fetchAll();
require 'header.php';?>
<div class="row between"><p class="muted small">Figures come from the database; after setup.php they are demo data only.</p><a class="btn" href="patient_form.php">+ Register Patient</a></div>
<div class="grid4"><?php foreach($cards as $c):?><div class="card stat"><i data-lucide="<?=$c[2]?>"></i><div><b><?=$c[1]?></b><span><?=$c[0]?></span></div></div><?php endforeach;?></div>
<div class="grid2"><div class="card"><h3>Recent patients</h3><table><tr><th>ID</th><th>Name</th><th>Registered</th></tr>
<?php foreach($recent as $r):?><tr><td><?=e($r['patient_code'])?></td><td><a href="patient_view.php?id=<?=$r['id']?>"><?=e($r['full_name'])?></a></td><td><?=e($r['reg_date'])?></td></tr><?php endforeach;?></table></div>
<div class="card"><h3>Upcoming appointments</h3><table><tr><th>Patient</th><th>Doctor</th><th>When</th></tr>
<?php foreach($up as $a):?><tr><td><?=e($a['full_name'])?></td><td><?=e($a['doctor'])?></td><td><?=e($a['appt_date'].' '.substr($a['appt_time'],0,5))?></td></tr><?php endforeach;?></table></div>
<div class="card"><h3>Registrations per month</h3><?php foreach($tr as $r):?><div class="bar"><span><?=e($r['m'])?></span><i style="width:<?=$r['n']/$max*100?>%"></i><b><?=$r['n']?></b></div><?php endforeach;?></div>
<div class="card"><h3>Recent activity</h3><ul class="feed"><?php foreach($act as $r):?><li><?=e($r['full_name'])?> (<?=e($r['patient_code'])?>) — <?=$r['updated_at']==$r['created_at']?'added':'updated'?> <span class="muted"><?=e($r['updated_at'])?></span></li><?php endforeach;?></ul></div></div>
<?php require 'footer.php';?>
