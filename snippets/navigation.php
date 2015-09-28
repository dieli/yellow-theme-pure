<?php $pages = $yellow->pages->top() ?>
<?php $yellow->page->setLastModified($pages->getModified()) ?>
<div class="nav-wrapper container">
<ul class="right hide-on-med-and-down">
<?php foreach($pages as $page): ?>
<li><a<?php echo $page->isActive() ? " class=\"active\"" : "" ?> href="<?php echo $page->getLocation() ?>"><?php echo $page->getHtml("titleNavigation") ?></a></li>
<?php endforeach ?>
</ul>
</div>
<div class="navigation-banner"></div>
