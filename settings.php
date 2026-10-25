<?php require 'config.php';auth();$title='Settings';$u=$_SESSION['u'];require 'header.php';?>
<div class="card"><h3>Staff profile</h3><dl class="dl"><div><dt>Name</dt><dd><?=e($u['name'])?></dd></div><div><dt>Username</dt><dd><?=e($u['username'])?></dd></div><div><dt>Email</dt><dd><?=e($u['email'])?></dd></div><div><dt>Role</dt><dd><?=e($u['role'])?></dd></div></dl><a class="btn" href="logout.php">Logout</a></div>
<?php require 'footer.php';?>
