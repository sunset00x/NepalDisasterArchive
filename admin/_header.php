<?php
require_once __DIR__.'/../config/auth.php'; require_login();
$me=user(); $flash=get_flash();
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($page_title??'Admin')?> — <?=SITE_NAME?></title><link rel="stylesheet" href="<?=BASE_URL?>/admin/admin.css"></head><body>
<aside class="sidebar"><div class="brand">NEPAL<br><span>DISASTER ARCHIVE</span></div><nav>
<a href="<?=BASE_URL?>/admin/index.php">Dashboard</a><a href="<?=BASE_URL?>/admin/stories.php">Stories</a><a href="<?=BASE_URL?>/admin/story-edit.php">New Story</a><a href="<?=BASE_URL?>/admin/categories.php">Categories</a><a href="<?=BASE_URL?>/admin/users.php">Users</a>
</nav><div class="sidebottom"><small><?=e($me['name'])?> · <?=e($me['role'])?></small><a href="<?=BASE_URL?>/admin/logout.php">Logout</a></div></aside>
<main class="main"><header class="top"><button class="menu" onclick="document.body.classList.toggle('navopen')">☰</button><strong><?=e($page_title??'Dashboard')?></strong><a href="<?=BASE_URL?>/" target="_blank">View site ↗</a></header>
<?php if($flash):?><div class="flash <?=$flash['type']?>"><?=e($flash['message'])?></div><?php endif;?>
