<?php
require_once __DIR__.'/_header.php'; $page_title='Stories';
if(isset($_GET['delete']) && user()['role']==='ADMIN'){
 $s=db()->prepare('DELETE FROM stories WHERE id=?');$s->execute([(int)$_GET['delete']]);flash('success','Story deleted.');redirect('/admin/stories.php');
}
$q=trim($_GET['q']??'');$where='';$params=[];
if($q!==''){$where='WHERE s.title LIKE ? OR s.location LIKE ? OR s.year_label LIKE ?';$params=["%$q%","%$q%","%$q%"];}
$s=db()->prepare("SELECT s.*,c.name category FROM stories s LEFT JOIN categories c ON c.id=s.category_id $where ORDER BY s.updated_at DESC");$s->execute($params);$rows=$s->fetchAll();
?>
<div class="content"><div class="toolbar"><form><input class="search" name="q" value="<?=e($q)?>" placeholder="Search title, location, year..."> <button>Search</button></form><a class="btn" href="<?=BASE_URL?>/admin/story-edit.php">+ New Story</a></div>
<div class="tablewrap"><table><thead><tr><th>Title</th><th>Category</th><th>Year</th><th>Status</th><th>Updated</th><th>Actions</th></tr></thead><tbody>
<?php foreach($rows as $r):?><tr><td><?=e($r['title'])?></td><td><?=e($r['category']??'—')?></td><td><?=e($r['year_label'])?></td><td><span class="badge <?=strtolower($r['status'])?>"><?=e($r['status'])?></span></td><td><?=e($r['updated_at'])?></td><td><a class="btn secondary" href="<?=BASE_URL?>/admin/story-edit.php?id=<?=$r['id']?>">Edit</a> <?php if(user()['role']==='ADMIN'):?><a class="btn danger" data-confirm="Delete this story?" href="?delete=<?=$r['id']?>">Delete</a><?php endif;?></td></tr><?php endforeach;?>
<?php if(!$rows):?><tr><td colspan="6">No stories found.</td></tr><?php endif;?></tbody></table></div></div>
<?php require_once __DIR__.'/_footer.php';