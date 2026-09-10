<?php
require_once __DIR__.'/config/config.php';
$q=trim($_GET['q']??'');$cat=trim($_GET['category']??'');$where="WHERE s.status='PUBLISHED'";$params=[];
if($q!==''){$where.=" AND (s.title LIKE ? OR s.location LIKE ? OR s.summary LIKE ? OR s.content LIKE ? OR s.year_label LIKE ?)";for($i=0;$i<5;$i++)$params[]="%$q%";}
if($cat!==''){$where.=' AND c.slug=?';$params[]=$cat;}
$st=db()->prepare("SELECT s.*,c.name category,c.slug category_slug FROM stories s LEFT JOIN categories c ON c.id=s.category_id $where ORDER BY s.featured DESC,COALESCE(s.event_date,'1000-01-01') DESC,s.created_at DESC");$st->execute($params);$stories=$st->fetchAll();
$cats=db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Natural Disasters of Nepal</title>
<style>:root{--ink:#17202a;--paper:#f7f3ed;--red:#9e2b25;--muted:#6c706f;--line:#d9d0c6}*{box-sizing:border-box}body{margin:0;background:var(--paper);color:var(--ink);font-family:Georgia,serif}.nav{position:sticky;top:0;background:#111820;color:#fff;z-index:10;padding:15px 5%;display:flex;align-items:center;gap:25px;font-family:Arial}.logo{font-weight:900;letter-spacing:2px}.navlinks{margin-left:auto;display:flex;gap:18px}.nav a{color:#fff;text-decoration:none;font-size:13px}.hero{padding:100px 7% 70px;background:#18212a;color:white}.hero h1{font-size:clamp(44px,7vw,90px);line-height:.95;margin:0 0 25px;max-width:1000px}.hero p{font-size:21px;max-width:800px;color:#d5d8da;line-height:1.6}.stats{display:flex;gap:35px;flex-wrap:wrap;margin-top:40px;font-family:Arial}.stat strong{display:block;font-size:30px}.wrap{width:min(1200px,90%);margin:auto}.section{padding:70px 0}.section h2{font-size:42px;margin:0 0 15px}.intro{font-size:19px;line-height:1.8;color:#444;max-width:850px}.filters{display:flex;gap:10px;flex-wrap:wrap;margin:30px 0}.filters input,.filters select{padding:13px;border:1px solid var(--line);background:white;border-radius:7px;font:16px Arial}.filters button{padding:13px 20px;background:var(--red);color:white;border:0;border-radius:7px}.cards{display:grid;grid-template-columns:repeat(3,1fr);gap:20px}.card{background:white;border:1px solid var(--line);padding:25px;border-radius:10px}.card h3{font-size:25px;margin:10px 0}.meta{font:12px Arial;color:var(--red);font-weight:800;text-transform:uppercase;letter-spacing:1px}.card p{line-height:1.6;color:#555}.read{color:var(--red);font-family:Arial;font-weight:700;text-decoration:none}.category{font-family:Arial;font-size:12px;color:#555}.why{background:#ece5dc}.columns{display:grid;grid-template-columns:repeat(3,1fr);gap:30px}.columns div{border-top:3px solid var(--red);padding-top:15px}.columns h3{font-size:25px}.columns p{line-height:1.7;color:#555}.disclaimer{font:13px Arial;color:#555;border-left:3px solid var(--red);padding:15px 20px;background:#eee9e2}.footer{background:#111820;color:#d8dde0;padding:45px 7%;font-family:Arial}@media(max-width:850px){.navlinks{display:none}.cards,.columns{grid-template-columns:1fr}.hero{padding:70px 5%}.section{padding:45px 0}}</style></head><body>


<nav class="nav">
    <a class="logo" href="#home">NEPAL DISASTER ARCHIVE</a>
    <div class="navlinks">
        <a href="#home">Home</a>
        <a href="#stories">Stories</a>
        <a href="#hazards">Categories</a>
        
        <a href="<?=BASE_URL?>/admin/login.php">Editor Login</a>
    </div>
</nav>

<header class="hero" id="home">
    <div class="wrap">
        <div class="meta" style="color:#d79b91">Research Archive · Nepal</div>
        <h1>Nepal's History of Natural Disasters</h1>
        <p>Centuries of earthquakes, floods, landslides, avalanches and extreme weather — documented, explained and placed in context.</p>
        <div class="stats">
            <div class="stat">
                <strong>1000+</strong>Years of documented history</div>
                <div class="stat"><strong><?=count($cats)?></strong>Hazard categories</div><div class="stat"><strong>Himalaya → Terai</strong>Geographic range</div></div></div>
            </header>


    <section class="section" id="stories"><div class="wrap"><h2><?=$q?'Search results':'Archive stories'?></h2><form class="filters"><input name="q" value="<?=e($q)?>" placeholder="Search event, year, location..."><select name="category"><option value="">All categories</option><?php foreach($cats as $c):?><option value="<?=e($c['slug'])?>" <?=$cat===$c['slug']?'selected':''?>><?=e($c['name'])?></option><?php endforeach;?></select><button>Search</button></form><div class="cards"><?php foreach($stories as $storyIndex=>$s):?><article class="card story-card"<?=$storyIndex>=3?' hidden':''?>><div class="meta"><?=e($s['year_label']?:'Historical')?></div><div class="category"><?=e($s['category']??'Other')?> · <?=e($s['location']??'Nepal')?></div><h3><?=e($s['title'])?></h3><p><?=e($s['summary']?:mb_substr(strip_tags($s['content']),0,180).'…')?></p><?php if($s['deaths']):?><p><strong>Deaths:</strong> <?=e($s['deaths'])?></p><?php endif;?><a class="read" href="<?=BASE_URL?>/story.php?slug=<?=urlencode($s['slug'])?>">Read full history →</a></article><?php endforeach;?></div><?php if(count($stories)>3):?><a class="more-link" href="#stories" data-show-all=".story-card">View all stories →</a><?php endif;?><?php if(!$stories):?><p>No published stories match your search.</p><?php endif;?></div></section>


<section class="section" id="hazards"><div class="wrap"><h2>Disaster categories</h2><div class="cards"><?php foreach($cats as $categoryIndex=>$c):?><article class="card category-card"<?=$categoryIndex>=3?' hidden':''?>><div class="meta"><?=e($c['name'])?></div><h3><?=e($c['name'])?></h3><p><?=e($c['description'])?></p><a class="read" href="?category=<?=e($c['slug'])?>#stories">Explore →</a></article><?php endforeach;?></div><?php if(count($cats)>3):?><a class="more-link" href="#hazards" data-show-all=".category-card">View all categories →</a><?php endif;?></div></section>


    <section class="section" ><div class="wrap"><h2>A country shaped by natural hazards</h2><p class="intro">Nepal's geography places it at the intersection of active Himalayan tectonics, intense summer monsoon rainfall, steep slopes, major river systems, high-altitude snow and changing climate conditions. The result is a diverse disaster profile ranging from earthquakes and landslides to floods, lightning, drought and wildfire.</p><div class="disclaimer">Historical records, dates, death tolls and magnitudes may vary because many older disasters occurred before modern scientific monitoring. Estimates should therefore be presented with appropriate uncertainty.</div></div></section>
<section class="section why"><div class="wrap"><h2>Why Nepal is vulnerable</h2><div class="columns"><div><h3>Himalayan tectonics</h3><p>The collision of the Indian and Eurasian plates stores enormous strain along structures including the Main Himalayan Thrust, creating significant earthquake hazard.</p></div><div><h3>Monsoon & terrain</h3><p>Seasonal rainfall interacts with steep terrain, fragile geology and river networks, producing floods, flash floods and landslides.</p></div><div><h3>Exposure & change</h3><p>Urban growth, roads, settlement patterns, infrastructure vulnerability and climate change can increase the consequences of natural hazards.</p></div></div></div></section>



    <footer class="footer">
        <strong>NEPAL DISASTER ARCHIVE</strong>
        <p>Documenting Disasters. Building Resilience - An Academix Digital Initiative</p>
        <div class="social-links" aria-label="Social media links">
            <a href="https://www.facebook.com/" target="_blank" rel="noopener noreferrer" aria-label="Facebook" title="Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4h-3c-3.3 0-5 1.8-5 5v3H6v4h3v8h4v-8h3.2l.8-4H13V9c0-.7.3-1 1-1z"/></svg></a>
            <a href="https://www.instagram.com/academix_digital/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" class="icon-fill"/></svg></a>
             </div>
    </footer>

<style>.more-link{display:inline-block;margin-top:25px;color:var(--red);font-family:Arial;font-weight:700;text-decoration:none}.social-links{display:flex;gap:14px;margin-top:20px}.social-links a{display:inline-flex;width:38px;height:38px;align-items:center;justify-content:center;border:1px solid #59636a;border-radius:50%;color:#fff}.social-links a:hover{background:var(--red);border-color:var(--red)}.social-links svg{width:19px;height:19px;fill:currentColor;stroke:currentColor;stroke-width:1.5}.social-links svg rect,.social-links svg circle{fill:none}.social-links svg .icon-fill{fill:currentColor;stroke:none}</style>
<script>document.querySelectorAll('[data-show-all]').forEach(function(link){link.addEventListener('click',function(event){event.preventDefault();var expanded=link.dataset.expanded==='true';document.querySelectorAll(link.dataset.showAll).forEach(function(card,index){card.hidden=expanded&&index>=3});if(!link.dataset.label)link.dataset.label=link.textContent.replace('View all ','');link.dataset.expanded=expanded?'false':'true';link.textContent=expanded?'View all '+link.dataset.label:'View less'})});</script>
</body></html>
