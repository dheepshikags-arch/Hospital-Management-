<?php require 'config.php';auth();$title='Patient Profile';$id=(int)($_GET['id']??0);
$s=$pdo->prepare('SELECT *,TIMESTAMPDIFF(YEAR,dob,CURDATE()) age FROM patients WHERE id=?');$s->execute([$id]);$p=$s->fetch();if(!$p)die('Patient not found');
$ap=$pdo->prepare('SELECT * FROM appointments WHERE patient_id=? ORDER BY appt_date DESC');$ap->execute([$id]);
if(canRec()){$rc=$pdo->prepare('SELECT * FROM records WHERE patient_id=? ORDER BY visit_date DESC');$rc->execute([$id]);}
require 'header.php';?>
<div class="card"><div class="row between"><h3><?=e($p['full_name'])?> <span class="muted"><?=e($p['patient_code'])?></span></h3><a class="btn ghost" href="patient_form.php?id=<?=$id?>">Edit</a></div>
<dl class="dl"><?php foreach(['dob'=>'Date of birth','age'=>'Age','gender'=>'Gender','phone'=>'Phone','email'=>'Email','address'=>'Address','blood_group'=>'Blood group','emergency_contact'=>'Emergency contact','reg_date'=>'Registered'] as $k=>$l):?><div><dt><?=$l?></dt><dd><?=e($p[$k]?:'—')?></dd></div><?php endforeach;?></dl></div>
<div class="card"><h3>Appointments</h3><table><tr><th>Date</th><th>Time</th><th>Doctor</th><th>Status</th></tr><?php foreach($ap as $a):?><tr><td><?=e($a['appt_date'])?></td><td><?=e(substr($a['appt_time'],0,5))?></td><td><?=e($a['doctor'])?></td><td><?=badge($a['status'])?></td></tr><?php endforeach;?></table></div>
<?php if(canRec()):?><div class="card"><h3>Medical records</h3><table><tr><th>Visit</th><th>Diagnosis</th><th>Treatment</th><th>Doctor</th></tr><?php foreach($rc as $r):?><tr><td><?=e($r['visit_date'])?></td><td><?=e($r['diagnosis'])?></td><td><?=e($r['treatment'])?></td><td><?=e($r['doctor'])?></td></tr><?php endforeach;?></table></div><?php endif;?>
<?php require 'footer.php';?>
