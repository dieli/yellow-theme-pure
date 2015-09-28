<div class="content-wrapper">
      <div class="content">
<?php $yellow->page->set("entryClass", "entry") ?>
<?php if($yellow->page->isExisting("tag")): ?>
<?php foreach(preg_split("/,\s*/", $yellow->page->get("tag")) as $tag) { $yellow->page->set("entryClass", $yellow->page->get("entryClass")." ".$yellow->toolbox->normaliseArgs($tag, false)); } ?>
<?php endif ?>
<div class="<?php echo $yellow->page->getHtml("entryClass") ?>">
<div class="entry-header"><h3><?php echo $yellow->page->getHtml("titleContent") ?></h3></div>
<div class="entry-meta"><?php echo htmlspecialchars($yellow->page->getDate("published")) ?> <?php echo $yellow->text->getHtml("blogBy") ?> <?php $authorCounter = 0; foreach(preg_split("/,\s*/", $yellow->page->get("author")) as $author) { if(++$authorCounter>1) echo ", "; echo "<a href=\"".$yellow->page->getParentTop()->getLocation().$yellow->toolbox->normaliseArgs("author:$author")."\">".htmlspecialchars($author)."</a>"; } ?></div>
<div class="entry-content"><?php echo $yellow->page->getContent() ?></div>
<div class="entry-footer">
<?php if($yellow->page->isExisting("tag")): ?>
<p><?php echo $yellow->text->getHtml("blogTag") ?> <?php $tagCounter = 0; foreach(preg_split("/,\s*/", $yellow->page->get("tag")) as $tag) { if(++$tagCounter>1) echo ", "; echo "<a href=\"".$yellow->page->getParentTop()->getLocation().$yellow->toolbox->normaliseArgs("tag:$tag")."\">".htmlspecialchars($tag)."</a>"; } ?></p>
<?php endif ?>
</div>
</div>
<?php echo $yellow->page->getExtra("comments") ?>
</div>
</div>
