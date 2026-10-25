<?php require 'config.php';auth();check();$title='Register Patient';$id=(int)($_GET['id']??0);
$F=['full_name','dob','gender','phone','email','address','blood_group','emergency_contact','reg_date'];$p=array_fill_keys($F,'');$p['reg_date']=date('Y-m-d');
if($id){$s=$pdo->prepare('SELECT * FROM patients WHERE id=?');$s->execute([$id]);$row=$s->fetch();if(!$row)die('Patient not found');$p=array_intersect_key($row,$p);$title='Edit Patient';}
$err=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
 foreach($F as $k)$p[$k]=trim($_POST[$k]??'');
 if(strlen($p['full_name'])<2)$err['full_name']='Full name is required.';
 if(!validDate($p['dob'])||$p['dob']>date('Y-m-d'))$err['dob']='Enter a valid past date of birth.';
 if(!in_array($p['gender'],['Male','Female','Other']))$err['gender']='Select a gender.';
 if(!preg_match('/^[0-9+\-\s]{7,15}$/',$p['phone']))$err['phone']='Enter a valid phone number.';
 if($p['email']!==''&&!filter_var($p['email'],FILTER_VALIDATE_EMAIL))$err['email']='Invalid email.';
 if($p['address']==='')$err['address']='Address is required.';
 if($p['blood_group']!==''&&!in_array($p['blood_group'],['A+','A-','B+','B-','AB+','AB-','O+','O-']))$err['blood_group']='Invalid blood group.';
 if($p['emergency_contact']!==''&&!preg_match('/^[0-9+\-\s]{7,15}$/',$p['emergency_contact']))$err['emergency_contact']='Invalid number.';
 if(!validDate($p['reg_date']))$err['reg_date']='Invalid date.';
 if(!$err){$v=array_values($p);
  if($id){$pdo->prepare('UPDATE patients SET '.implode('=?,',$F).'=? WHERE id=?')->execute([...$v,$id]);flash('Patient updated.');}
  else{$pdo->prepare('INSERT INTO patients('.implode(',',$F).') VALUES('.implode(',',array_fill(0,9,'?')).')')->execute($v);$n=$pdo->lastInsertId();
   $pdo->prepare('UPDATE patients SET patient_code=? WHERE id=?')->execute(['PT-'.str_pad($n,5,'0',STR_PAD_LEFT),$n]);flash('Patient registered.');}
  header('Location: patients.php');exit;}}
$nextCode=$id?$row['patient_code']:'Auto-generated';
function fld($k,$l,$p,$err,$type='text'){echo '<label>'.$l.'<input type="'.$type.'" name="'.$k.'" value="'.e($p[$k]).'">'.(isset($err[$k])?'<small class="ferr">'.e($err[$k]).'</small>':'').'</label>';}
require 'header.php';?>
<form method="post" class="card form"><input type="hidden" name="t" value="<?=csrf()?>">
<label>Patient ID<input value="<?=e($nextCode)?>" disabled></label>
<?php fld('full_name','Full name *',$p,$err);fld('dob','Date of birth *',$p,$err,'date');?>
<label>Gender *<select name="gender"><option value="">Select</option><?php foreach(['Male','Female','Other'] as $x):?><option<?=sel($p['gender'],$x)?>><?=$x?></option><?php endforeach;?></select><?=isset($err['gender'])?'<small class="ferr">'.$err['gender'].'</small>':''?></label>
<?php fld('phone','Phone *',$p,$err);fld('email','Email (optional)',$p,$err,'email');fld('address','Address *',$p,$err);?>
<label>Blood group<select name="blood_group"><option value="">—</option><?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $x):?><option<?=sel($p['blood_group'],$x)?>><?=$x?></option><?php endforeach;?></select></label>
<?php fld('emergency_contact','Emergency contact',$p,$err);fld('reg_date','Registration date *',$p,$err,'date');?>
<div class="row full"><button class="btn">Save Patient</button><button type="reset" class="btn ghost" onclick="document.querySelectorAll('.form input:not([disabled]),.form select').forEach(i=>i.value='');return false">Clear Form</button><a class="btn ghost" href="patients.php">Cancel</a></div></form>
<?php require 'footer.php';?>
