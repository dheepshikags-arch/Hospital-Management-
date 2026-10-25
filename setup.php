<?php // Run once in browser after importing schema.sql, then delete this file.
require 'config.php';
if($pdo->query('SELECT COUNT(*) FROM users')->fetchColumn())die('Already set up.');
$u=$pdo->prepare('INSERT INTO users(name,username,email,password,role) VALUES(?,?,?,?,?)');
foreach([['Admin User','admin','admin@hms.test','admin123','admin'],['Dr. Meera Rao','doctor','doctor@hms.test','doctor123','doctor'],['Front Desk','reception','reception@hms.test','reception123','receptionist']] as $r){$r[3]=password_hash($r[3],PASSWORD_DEFAULT);$u->execute($r);}
$names=[['Arun Kumar','1985-04-12','Male','O+'],['Divya Nair','1992-09-03','Female','O+'],['Karthik S','1978-01-25','Male','B+'],['Lakshmi P','2001-06-18','Female','A+'],['Ravi Shankar','1965-11-30','Male','AB+']];
$p=$pdo->prepare('INSERT INTO patients(full_name,dob,gender,phone,address,blood_group,reg_date) VALUES(?,?,?,?,?,?,?)');
foreach($names as $i=>$n){$p->execute([$n[0],$n[1],$n[2],'98765432'.sprintf('%02d',$i),'Demo Street '.($i+1).', Coimbatore',$n[3],date('Y-m-d',strtotime('-'.($i*20).' days'))]);
 $id=$pdo->lastInsertId();$pdo->prepare('UPDATE patients SET patient_code=? WHERE id=?')->execute(['PT-'.str_pad($id,5,'0',STR_PAD_LEFT),$id]);
 $pdo->prepare('INSERT INTO appointments(patient_id,doctor,appt_date,appt_time,reason,status) VALUES(?,?,?,?,?,?)')->execute([$id,'Dr. Meera Rao',date('Y-m-d',strtotime('+'.$i.' days')),'10:'.($i*10<60?sprintf('%02d',$i*10):'00'),'General checkup','Scheduled']);
 $pdo->prepare('INSERT INTO records(patient_id,visit_date,symptoms,diagnosis,treatment,doctor) VALUES(?,?,?,?,?,?)')->execute([$id,date('Y-m-d'),'Demo symptoms','Demo diagnosis','Demo treatment notes','Dr. Meera Rao']);}
echo 'Done (demo data). Delete setup.php now.';
