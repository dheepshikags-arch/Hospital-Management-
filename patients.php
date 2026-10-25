<?php require 'config.php';auth();check();$title='Patients';
if(isset($_POST['del'])){if($_SESSION['u']['role']==='admin'){$pdo->prepare('DELETE FROM patients WHERE id=?')->execute([(int)$_POST['del']]);flash('Patient deleted.');}else flash('Only admin can delete patients.','err');header('Location: patients.php');exit;}
$q=trim($_GET['q']??'');$g=$_GET['gender']??'';$w=['1'];$a=[];
if($q!==''){$w[]='(patient_code LIKE ? OR full_name LIKE ?)';$a[]="%$q%";$a[]="%$q%";}
if($g!==''){$w[]='gender=?';$a[]=$g;}
$s=$pdo->prepare('SELECT *,TIMESTAMPDIFF(YEAR,dob,CURDATE()) age FROM patients WHERE '.implode(' AND ',$w).' ORDER BY id DESC');$s->execute($a);$rows=$s->fetchAll();
require 'header.php';?>
<div class="card"><form class="row"><input name="q" placeholder="ID or name" value="<?=e($q)?>"><select name="gender"><option value="">All genders</option><?php foreach(['Male','Female','Other'] as $x):?><option<?=sel($g,$x)?>><?=$x?></option><?php endforeach;?></select><button class="btn">Search</button><a class="btn ghost" href="patient_form.php">+ Register</a></form>
<div class="scroll"><table><tr><th>ID</th><th>Name</th><th>Age</th><th>Gender</th><th>Phone</th><th>Registered</th><th>Actions</th></tr>
<?php foreach($rows as $r):?><tr><td><?=e($r['patient_code'])?></td><td><?=e($r['full_name'])?></td><td><?=$r['age']?></td><td><?=e($r['gender'])?></td><td><?=e($r['phone'])?></td><td><?=e($r['reg_date'])?></td>
<td class="acts"><a href="patient_view.php?id=<?=$r['id']?>">View</a><a href="patient_form.php?id=<?=$r['id']?>">Edit</a>
<form method="post" data-confirm="Delete this patient and all linked records?"><input type="hidden" name="t" value="<?=csrf()?>"><button name="del" value="<?=$r['id']?>" class="link danger">Delete</button></form></td></tr><?php endforeach;?>
<?php if(!$rows):?><tr><td colspan="7" class="muted">No patients found.</td></tr><?php endif;?></table></div></div>
<?php require 'footer.php';?>
