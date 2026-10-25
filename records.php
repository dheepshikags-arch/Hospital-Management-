<?php require 'config.php';auth();check();$title='Medical Records';
if(!canRec()){http_response_code(403);require 'header.php';echo '<div class="msg err">Access restricted to doctors and administrators.</div>';require 'footer.php';exit;}
$eid=(int)($_GET['edit']??0);$d=['patient_id'=>'','visit_date'=>date('Y-m-d'),'symptoms'=>'','diagnosis'=>'','treatment'=>'','doctor'=>$_SESSION['u']['role']=='doctor'?$_SESSION['u']['name']:'','follow_up'=>''];
if($eid){$s=$pdo->prepare('SELECT * FROM records WHERE id=?');$s->execute([$eid]);$d=$s->fetch()?:$d;}
if(isset($_POST['save'])){$id=(int)$_POST['id'];$fu=$_POST['follow_up']!==''?$_POST['follow_up']:null;
 $v=[(int)$_POST['patient_id'],$_POST['visit_date'],trim($_POST['symptoms']),trim($_POST['diagnosis']),trim($_POST['treatment']),trim($_POST['doctor']),$fu];
 if(!$v[0]||!validDate($v[1])||($fu&&!validDate($fu))){flash('Patient and valid dates are required.','err');header('Location: records.php');exit;}
 if($id){$pdo->prepare('UPDATE records SET patient_id=?,visit_date=?,symptoms=?,diagnosis=?,treatment=?,doctor=?,follow_up=? WHERE id=?')->execute([...$v,$id]);flash('Record updated.');}
 else{$pdo->prepare('INSERT INTO records(patient_id,visit_date,symptoms,diagnosis,treatment,doctor,follow_up) VALUES(?,?,?,?,?,?,?)')->execute($v);flash('Record added.');}
 header('Location: records.php');exit;}
$rows=$pdo->query('SELECT r.*,p.full_name,p.patient_code FROM records r JOIN patients p ON p.id=r.patient_id ORDER BY r.visit_date DESC,r.id DESC')->fetchAll();
$pts=$pdo->query('SELECT id,full_name,patient_code FROM patients ORDER BY full_name')->fetchAll();
require 'header.php';?>
<div class="card"><h3><?=$eid?'Edit':'Add'?> record</h3><form method="post" class="form"><input type="hidden" name="t" value="<?=csrf()?>"><input type="hidden" name="id" value="<?=$eid?>">
<label>Patient<select name="patient_id"><option value="">Select</option><?php foreach($pts as $p):?><option value="<?=$p['id']?>"<?=sel($d['patient_id'],$p['id'])?>><?=e($p['full_name'].' ('.$p['patient_code'].')')?></option><?php endforeach;?></select></label>
<label>Visit date<input type="date" name="visit_date" value="<?=e($d['visit_date'])?>"></label><label>Doctor<input name="doctor" value="<?=e($d['doctor'])?>"></label><label>Follow-up date<input type="date" name="follow_up" value="<?=e($d['follow_up'])?>"></label>
<label>Symptoms<textarea name="symptoms"><?=e($d['symptoms'])?></textarea></label><label>Diagnosis<textarea name="diagnosis"><?=e($d['diagnosis'])?></textarea></label><label>Treatment / prescription<textarea name="treatment"><?=e($d['treatment'])?></textarea></label>
<div class="row full"><button name="save" value="1" class="btn">Save Record</button><?php if($eid):?><a class="btn ghost" href="records.php">Cancel</a><?php endif;?></div></form></div>
<div class="card scroll"><table><tr><th>ID</th><th>Patient</th><th>Visit</th><th>Symptoms</th><th>Diagnosis</th><th>Treatment</th><th>Doctor</th><th>Follow-up</th><th></th></tr>
<?php foreach($rows as $r):?><tr><td>R-<?=$r['id']?></td><td><?=e($r['patient_code'].' '.$r['full_name'])?></td><td><?=e($r['visit_date'])?></td><td><?=e($r['symptoms'])?></td><td><?=e($r['diagnosis'])?></td><td><?=e($r['treatment'])?></td><td><?=e($r['doctor'])?></td><td><?=e($r['follow_up']?:'—')?></td><td class="acts"><a href="?edit=<?=$r['id']?>">Edit</a></td></tr><?php endforeach;?></table></div>
<?php require 'footer.php';?>
