<div class="content-wrapper">
<?php if($yellow->page->isExisting("titleBlog")): ?>
<h2><?php echo $yellow->page->getHtml("titleBlog") ?></h2>
<?php endif ?>
<?php foreach($yellow->page->getPages() as $page): ?>
<?php $page->set("entryClass", "entry") ?>
<?php if($page->isExisting("tag")): ?>
<?php foreach(preg_split("/,\s*/", $page->get("tag")) as $tag) { $page->set("entryClass", $page->get("entryClass")." ".$yellow->toolbox->normaliseArgs($tag, false)); } ?>
<?php endif ?>
<div class="content">
<div class="entry-header"><h3><a href="<?php echo $page->getLocation() ?>"><?php echo $page->getHtml("title") ?></a></h3></div>
<div class="entry-meta"><?php echo htmlspecialchars($page->getDate("published")) ?> <?php echo $yellow->text->getHtml("blogBy") ?> <?php $authorCounter = 0; foreach(preg_split("/,\s*/", $page->get("author")) as $author) { if(++$authorCounter>1) echo ", "; echo "<a href=\"".$yellow->page->getLocation().$yellow->toolbox->normaliseArgs("author:$author")."\">".htmlspecialchars($author)."</a>"; } ?></div>
<div class="entry-content"><?php echo $yellow->toolbox->createTextDescription($page->getContent(), 1024, false, "<!--more-->", " <a href=\"".$page->getLocation()."\">".$yellow->text->getHtml("blogMore")."</a>") ?></div>
</div>
<?php endforeach ?>
<?php $yellow->snippet("pagination", $yellow->page->getPages()) ?>
</div>
