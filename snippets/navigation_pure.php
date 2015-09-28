<?php list($name, $pages, $level) = $yellow->getSnippetArgs() ?>
<?php if(!$pages) $pages = $yellow->pages->top() ?>
<?php $yellow->page->setLastModified($pages->getModified()) ?>
<?php if(!$level): ?>
<!-- Logo/HEadline -->
<div class="menu-header">
<h1><a href="<?php echo $yellow->page->base."/" ?>"><span class="sitename-logo"></span><?php echo $yellow->page->getHtml("sitename") ?></a></h1>
 <label for="navbar-checkbox" class="navbar-handle"></label>
</div>
<input type="checkbox" id="navbar-checkbox" class="navbar-checkbox">
<div class="home-menu" id="menu"> <!-- Top level -->
<ul>
<?php endif ?>
<?php foreach($pages as $page): ?>
<?php $children = $page->getChildren() ?>
<li class="menu-list <?php if(($children->count()) && ($page->getHtml("titleNavigation") != "Blog")) { echo "menu-has-children "; }?> 
<?php echo $page->isActive() ? "menu-selected " : "" ?>
"><a class="menu-link"  href="<?php echo $page->getLocation() ?>">
<?php echo $page->getHtml("titleNavigation") ?></a>
<?php if(($children->count()) && ($page->getHtml("titleNavigation") != "Blog")) { echo "<ul class='menu-children'>\n"; $yellow->snippet($name, $children, $level+1); echo "</ul>";} ?></li>
<?php endforeach ?>
<?php if(!$level): ?>
<li class="menu-list search">
<form action="<?php echo $yellow->page->base ?>/search/" target="_self" method="post">
	<span class="fa icon-search"></span>
    <input type="search" class="search_bar" name="query" placeholder="Search" />
</form>
</li>
</ul>
</div>
<?php endif ?>
