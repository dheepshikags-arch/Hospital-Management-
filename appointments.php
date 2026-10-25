<?php require 'config.php';auth();check();$title='Appointments';$ST=['Scheduled','Completed','Cancelled'];
if(isset($_POST['cancel'])){$pdo->prepare("UPDATE appointments SET status='Cancelled' WHERE id=?")->execute([(int)$_POST['cancel']]);flash('Appointment cancelled.');header('Location: appointments.php');exit;}
$eid=(int)($_GET['edit']??0);$d=['patient_id'=>'','doctor'=>'','appt_date'=>date('Y-m-d'),'appt_time'=>'09:00','reason'=>'','status'=>'Scheduled'];
if($eid){$s=$pdo->prepare('SELECT * FROM appointments WHERE id=?');$s->execute([$eid]);$d=$s->fetch()?:$d;}
if(isset($_POST['save'])){$id=(int)$_POST['id'];$v=[(int)$_POST['patient_id'],trim($_POST['doctor']),$_POST['appt_date'],$_POST['appt_time'],trim($_POST['reason']),$_POST['status']];
 if(!$v[0]||$v[1]===''||!validDate($v[2])||!preg_match('/^\d\d:\d\d/',$v[3])||!in_array($v[5],$ST)){flash('Patient, doctor, valid date and time are required.','err');header('Location: appointments.php'.($id?"?edit=$id":''));exit;}
 if($id){$pdo->prepare('UPDATE appointments SET patient_id=?,doctor=?,appt_date=?,appt_time=?,reason=?,status=? WHERE id=?')->execute([...$v,$id]);flash('Appointment updated.');}
 else{$pdo->prepare('INSERT INTO appointments(patient_id,doctor,appt_date,appt_time,reason,status) VALUES(?,?,?,?,?,?)')->execute($v);flash('Appointment scheduled.');}
 header('Location: appointments.php');exit;}
$q=trim($_GET['q']??'');$fd=$_GET['date']??'';$fs=$_GET['status']??'';$w=['1'];$a=[];
if($q!==''){$w[]='(p.full_name LIKE ? OR a.doctor LIKE ?)';array_push($a,"%$q%","%$q%");}
if($fd!==''){$w[]='a.appt_date=?';$a[]=$fd;}if($fs!==''){$w[]='a.status=?';$a[]=$fs;}
$s=$pdo->prepare('SELECT a.*,p.full_name FROM appointments a JOIN patients p ON p.id=a.patient_id WHERE '.implode(' AND ',$w).' ORDER BY a.appt_date DESC,a.appt_time');$s->execute($a);$rows=$s->fetchAll();
$pts=$pdo->query('SELECT id,full_name,patient_code FROM patients ORDER BY full_name')->fetchAll();
require 'header.php';?>
<div class="card"><h3><?=$eid?'Edit':'Schedule'?> appointment</h3><form method="post" class="form"><input type="hidden" name="t" value="<?=csrf()?>"><input type="hidden" name="id" value="<?=$eid?>">
<label>Patient<select name="patient_id"><option value="">Select</option><?php foreach($pts as $p):?><option value="<?=$p['id']?>"<?=sel($d['patient_id'],$p['id'])?>><?=e($p['full_name'].' ('.$p['patient_code'].')')?></option><?php endforeach;?></select></label>
<label>Doctor<input name="doctor" value="<?=e($d['doctor'])?>"></label><label>Date<input type="date" name="appt_date" value="<?=e($d['appt_date'])?>"></label><label>Time<input type="time" name="appt_time" value="<?=e(substr($d['appt_time'],0,5))?>"></label>
<label>Reason<input name="reason" value="<?=e($d['reason'])?>"></label><label>Status<select name="status"><?php foreach($ST as $x):?><option<?=sel($d['status'],$x)?>><?=$x?></option><?php endforeach;?></select></label>
<div class="row full"><button name="save" value="1" class="btn">Save</button><?php if($eid):?><a class="btn ghost" href="appointments.php">Cancel</a><?php endif;?></div></form></div>
<div class="card"><form class="row"><input name="q" placeholder="Patient or doctor" value="<?=e($q)?>"><input type="date" name="date" value="<?=e($fd)?>"><select name="status"><option value="">All statuses</option><?php foreach($ST as $x):?><option<?=sel($fs,$x)?>><?=$x?></option><?php endforeach;?></select><button class="btn">Filter</button></form>
<div class="scroll"><table><tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Reason</th><th>Status</th><th></th></tr>
<?php foreach($rows as $r):?><tr><td>A-<?=$r['id']?></td><td><?=e($r['full_name'])?></td><td><?=e($r['doctor'])?></td><td><?=e($r['appt_date'])?></td><td><?=e(substr($r['appt_time'],0,5))?></td><td><?=e($r['reason'])?></td><td><?=badge($r['status'])?></td>
<td class="acts"><a href="?edit=<?=$r['id']?>">Edit</a><?php if($r['status']=='Scheduled'):?><form method="post" data-confirm="Cancel this appointment?"><input type="hidden" name="t" value="<?=csrf()?>"><button name="cancel" value="<?=$r['id']?>" class="link danger">Cancel</button></form><?php endif;?></td></tr><?php endforeach;?></table></div></div>
<?php require 'footer.php';?>
