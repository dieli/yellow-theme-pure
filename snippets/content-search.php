<div class="content-wrapper">
<div class="content">
<h1><?php echo $yellow->page->getHtml("title") ?></h1>
<form class="pure-form" action="<?php echo $yellow->page->getLocation() ?>" method="post">
    <input type="text" class="pure-input-rounded" name="query" value="<?php echo htmlspecialchars($_REQUEST["query"]) ?>" />
    <button type="submit" class="pure-button">Search</button>
</form>
<?php if(count($yellow->page->getPages())): ?>
<?php foreach($yellow->page->getPages() as $page): ?>
<div class="entry">
<div class="entry-header"><h2><a href="<?php echo $page->getLocation() ?>"><?php echo $page->getHtml("title") ?></a></h2></div>
<div class="entry-content"><?php echo htmlspecialchars($yellow->toolbox->createTextDescription($page->getContent(), 250)) ?></div>
<div class="entry-location"><a href="<?php echo $page->getLocation() ?>"><?php echo $page->getUrl() ?></a></div>
</div>
<?php endforeach ?>
<?php else: ?>
<p><?php echo $yellow->page->getHtml("searchResults") ?><p>
<?php endif ?>
<?php $yellow->snippet("pagination", $yellow->page->getPages()) ?>
</div>
</div>
