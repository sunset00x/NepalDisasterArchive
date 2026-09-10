<?php
require_once __DIR__.'/_header.php'; require_admin(); $page_title='Users';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $name=trim($_POST['name']??'');$email=trim(mb_strtolower($_POST['email']??''));$pass=$_POST['password']??'';$role=($_POST['role']??'EDITOR')==='ADMIN'?'ADMIN':'EDITOR';
 if(mb_strlen($name)<2 || !filter_var($email,FILTER_VALIDATE_EMAIL) || mb_strlen($pass)<8){flash('error','Invalid user details. Password must be at least 8 characters.');}
 else{try{$s=db()->prepare('INSERT INTO admins(name,email,password_hash,role) VALUES(?,?,?,?)');$s->execute([$name,$email,password_hash($pass,PASSWORD_DEFAULT),$role]);flash('success','User created.');}catch(Throwable $e){flash('error','Email already exists.');}}
 redirect('/admin/users.php');
}
$rows=db()->query('SELECT id,name,email,role,created_at FROM admins ORDER BY created_at DESC')->fetchAll();
?>
<div class="content"><form class="form" method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>"><h2>Create editor</h2><div class="formgrid">
<div class="field"><label>Name</label><input name="name" required></div><div class="field"><label>Email</label><input type="email" name="email" required></div>
<div class="field"><label>Password</label><input type="password" name="password" minlength="8" required></div><div class="field"><label>Role</label><select name="role"><option>EDITOR</option><option>ADMIN</option></select></div>
</div><br><button>Create User</button></form>
<div class="tablewrap" style="margin-top:20px"><table><tr><th>Name</th><th>Email</th><th>Role</th><th>Created</th></tr><?php foreach($rows as $r):?><tr><td><?=e($r['name'])?></td><td><?=e($r['email'])?></td><td><?=e($r['role'])?></td><td><?=e($r['created_at'])?></td></tr><?php endforeach;?></table></div></div>
<?php require_once __DIR__.'/_footer.php';