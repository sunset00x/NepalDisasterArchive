<?php
require_once __DIR__.'/config/config.php';
$slug=trim($_GET['slug']??'');
$s=db()->prepare("SELECT s.*,c.name category FROM stories s LEFT JOIN categories c ON c.id=s.category_id WHERE s.slug=? AND s.status='PUBLISHED' LIMIT 1");$s->execute([$slug]);$story=$s->fetch();
if(!$story){http_response_code(404);exit('Story not found.');}
$paragraphs=preg_split("/\R{2,}/",trim($story['content']));
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($story['title'])?> — Nepal Disaster Archive</title>
<style>body{margin:0;background:#f7f3ed;color:#17202a;font-family:Georgia,serif}.nav{background:#111820;color:#fff;padding:18px 7%;font:700 14px Arial}.nav a{color:#fff;text-decoration:none}.article{max-width:850px;margin:70px auto;padding:0 25px}.meta{color:#9e2b25;font:800 13px Arial;text-transform:uppercase;letter-spacing:1px}.article h1{font-size:clamp(42px,7vw,72px);line-height:1;margin:15px 0}.dek{font-size:21px;color:#555;line-height:1.6}.facts{display:flex;flex-wrap:wrap;gap:12px;margin:30px 0}.fact{background:#fff;border:1px solid #ddd0c5;padding:15px;border-radius:8px;font-family:Arial}.fact b{display:block;font-size:12px;color:#777}.body{font-size:19px;line-height:1.85}.body p{margin:0 0 24px}.sources{border-top:2px solid #9e2b25;margin-top:45px;padding-top:20px;font:15px Arial;white-space:pre-line;color:#555}</style></head><body>
<nav class="nav"><a href="<?=BASE_URL?>/">← NEPAL DISASTER ARCHIVE</a></nav><article class="article"><div class="meta"><?=e($story['category']??'Natural Disaster')?> · <?=e($story['year_label'])?></div><h1><?=e($story['title'])?></h1><p class="dek"><?=e($story['summary'])?></p>
<div class="facts"><?php foreach([['Date',$story['event_date']],['Location',$story['location']],['Magnitude',$story['magnitude']],['Deaths',$story['deaths']],['Injuries',$story['injuries']]] as $f):if($f[1]):?><div class="fact"><b><?=e($f[0])?></b><?=e($f[1])?></div><?php endif;endforeach;?></div>
<div class="body"><?php foreach($paragraphs as $p):?><p><?=nl2br(e($p))?></p><?php endforeach;?></div>
<?php if($story['impact']):?><h2>Impact</h2><div class="body"><p><?=nl2br(e($story['impact']))?></p></div><?php endif;?>
<?php if($story['sources']):?><div class="sources"><strong>Sources / references</strong><br><br><?=e($story['sources'])?></div><?php endif;?>
</article></body></html>