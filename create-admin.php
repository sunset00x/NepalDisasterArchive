<?php
require_once __DIR__.'/config/config.php';
$error=''; $success='';
try { $count=(int)db()->query('SELECT COUNT(*) FROM admins')->fetchColumn(); }
catch(Throwable $e){ exit('Database error. Import database/schema.sql first.<br>'.e($e->getMessage())); }
if($count>0) exit('<h2>Administrator already exists.</h2><p>Delete or rename create-admin.php.</p><a href="'.BASE_URL.'/admin/login.php">Login</a>');
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $name=trim($_POST['name']??'');
 $email=trim(mb_strtolower($_POST['email']??''));
 $password=$_POST['password']??'';
 if(mb_strlen($name)<2 || !filter_var($email,FILTER_VALIDATE_EMAIL) || mb_strlen($password)<8){
  $error='Enter a valid name, email and password of at least 8 characters.';
 } else {
  $s=db()->prepare("INSERT INTO admins(name,email,password_hash,role) VALUES(?,?,?,'ADMIN')");
  $s->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
  $success='Administrator created. Delete create-admin.php now.';
 }
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Create Admin</title>
<style>body{font-family:Arial;background:#101820;color:#fff;display:grid;place-items:center;min-height:100vh}.box{background:#fff;color:#111;padding:35px;border-radius:16px;width:min(420px,90%)}input{width:100%;padding:12px;margin:7px 0 15px;box-sizing:border-box}button{padding:12px;background:#942b26;color:#fff;border:0;border-radius:8px;width:100%}.err{color:#a00}.ok{color:#087443}</style></head>
<body><div class="box"><h1>Create Administrator</h1>
<?php if($error):?><p class="err"><?=e($error)?></p><?php endif;?>
<?php if($success):?><p class="ok"><?=e($success)?></p><a href="<?=BASE_URL?>/admin/login.php">Go to login</a><?php else:?>
<form method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<label>Name</label><input name="name" required><label>Email</label><input type="email" name="email" required>
<label>Password</label><input type="password" name="password" minlength="8" required><button>Create Admin</button></form><?php endif;?>
</div></body></html>
