<?php
require_once __DIR__.'/config.php';

function user(): ?array { return $_SESSION['admin_user'] ?? null; }
function require_login(): void { if (!user()) redirect('/admin/login.php'); }
function require_admin(): void {
    require_login();
    if ((user()['role'] ?? '') !== 'ADMIN') { http_response_code(403); exit('Administrator access required.'); }
}
function login_user(array $u): void {
    session_regenerate_id(true);
    $_SESSION['admin_user']=['id'=>(int)$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']];
}

function login_is_throttled(string $email): bool {
    $key='login_attempts_'.hash('sha256',strtolower(trim($email)));$attempt=$_SESSION[$key]??['count'=>0,'at'=>0];
    return $attempt['count']>=5 && time()-$attempt['at']<900;
}
function record_login_failure(string $email): void {
    $key='login_attempts_'.hash('sha256',strtolower(trim($email)));$attempt=$_SESSION[$key]??['count'=>0,'at'=>time()];
    if(time()-$attempt['at']>=900)$attempt=['count'=>0,'at'=>time()];$attempt['count']++;$attempt['at']=time();$_SESSION[$key]=$attempt;
}
function clear_login_failures(string $email): void { unset($_SESSION['login_attempts_'.hash('sha256',strtolower(trim($email)))]); }
function logout_user(): void {
    $_SESSION=[];
    if (ini_get('session.use_cookies')) {
        $p=session_get_cookie_params();
        setcookie(session_name(),'',
            time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']);
    }
    session_destroy();
}
