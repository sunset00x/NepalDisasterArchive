<?php
require_once __DIR__.'/_header.php';$id=(int)($_GET['id']??0);$page_title=$id?'Edit Story':'New Story';
$cats=db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$row=['title'=>'','slug'=>'','category_id'=>'','event_date'=>'','year_label'=>'','location'=>'','summary'=>'','content'=>'','deaths'=>'','injuries'=>'','magnitude'=>'','impact'=>'','sources'=>'','featured'=>0,'status'=>'DRAFT'];
if($id){$s=db()->prepare('SELECT * FROM stories WHERE id=?');$s->execute([$id]);$found=$s->fetch();if(!$found)exit('Story not found');$row=$found;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $data=[
 'title'=>trim($_POST['title']??''),'slug'=>slugify($_POST['slug']??$_POST['title']??''),
 'category_id'=>($_POST['category_id']??'')?:null,'event_date'=>($_POST['event_date']??'')?:null,
 'year_label'=>trim($_POST['year_label']??''),'location'=>trim($_POST['location']??''),
 'summary'=>trim($_POST['summary']??''),'content'=>trim($_POST['content']??''),
 'deaths'=>trim($_POST['deaths']??''),'injuries'=>trim($_POST['injuries']??''),
 'magnitude'=>trim($_POST['magnitude']??''),'impact'=>trim($_POST['impact']??''),
 'sources'=>trim($_POST['sources']??''),'featured'=>isset($_POST['featured'])?1:0,
 'status'=>($_POST['status']??'DRAFT')==='PUBLISHED'?'PUBLISHED':'DRAFT'
 ];
 if($data['title']===''||$data['content']===''){flash('error','Title and article content are required.');}
 else {
  try {
   if($id){
    $sql='UPDATE stories SET title=?,slug=?,category_id=?,event_date=?,year_label=?,location=?,summary=?,content=?,deaths=?,injuries=?,magnitude=?,impact=?,sources=?,featured=?,status=?,updated_by=? WHERE id=?';
    db()->prepare($sql)->execute([...array_values($data),user()['id'],$id]);
   } else {
    $sql='INSERT INTO stories(title,slug,category_id,event_date,year_label,location,summary,content,deaths,injuries,magnitude,impact,sources,featured,status,created_by,updated_by) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
    db()->prepare($sql)->execute([...array_values($data),user()['id'],user()['id']]);
   }
   flash('success',$id?'Story updated.':'Story created.');redirect('/admin/stories.php');
  } catch(Throwable $e){flash('error','Could not save. Check that the slug is unique.');}
 }
 $row=array_merge($row,$data);
}
?>
<div class="content"><form class="form" method="post"><input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<div class="formgrid">
<div class="field full"><label>Story title *</label><input name="title" value="<?=e($row['title'])?>" required></div>
<div class="field"><label>Slug</label><input name="slug" value="<?=e($row['slug'])?>"></div>
<div class="field"><label>Category</label><select name="category_id"><option value="">Uncategorized</option><?php foreach($cats as $c):?><option value="<?=$c['id']?>" <?=$row['category_id']==$c['id']?'selected':''?>><?=e($c['name'])?></option><?php endforeach;?></select></div>
<div class="field"><label>Event date</label><input type="date" name="event_date" value="<?=e($row['event_date'])?>"></div>
<div class="field"><label>Year / historical label</label><input name="year_label" value="<?=e($row['year_label'])?>"></div>
<div class="field"><label>Location</label><input name="location" value="<?=e($row['location'])?>"></div>
<div class="field"><label>Magnitude / severity</label><input name="magnitude" value="<?=e($row['magnitude'])?>"></div>
<div class="field"><label>Deaths</label><input name="deaths" value="<?=e($row['deaths'])?>" placeholder="Use estimated/range if uncertain"></div>
<div class="field"><label>Injuries</label><input name="injuries" value="<?=e($row['injuries'])?>"></div>
<div class="field full"><label>Summary</label><textarea style="min-height:110px" name="summary"><?=e($row['summary'])?></textarea></div>
<div class="field full"><label>Article content *</label><textarea class="editor" name="content" required><?=e($row['content'])?></textarea><div class="help">Use blank lines between paragraphs. Plain text is safest.</div></div>
<div class="field full"><label>Impact / damage</label><textarea name="impact"><?=e($row['impact'])?></textarea></div>
<div class="field full"><label>Sources / references</label><textarea name="sources" placeholder="One source per line"><?=e($row['sources'])?></textarea></div>
<div class="field"><label>Status</label><select name="status"><option value="DRAFT" <?=$row['status']==='DRAFT'?'selected':''?>>Draft</option><option value="PUBLISHED" <?=$row['status']==='PUBLISHED'?'selected':''?>>Published</option></select></div>
<div class="field"><label><input type="checkbox" name="featured" <?=$row['featured']?'checked':''?>> Featured story</label></div>
</div><div class="actions"><button>Save Story</button><a class="btn secondary" href="<?=BASE_URL?>/admin/stories.php">Cancel</a><?php if($id):?><a class="btn secondary" target="_blank" href="<?=BASE_URL?>/story.php?slug=<?=urlencode($row['slug'])?>">Preview</a><?php endif;?></div></form></div>
<?php require_once __DIR__.'/_footer.php';