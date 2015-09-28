<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Blog plugin
class YellowBlog
{
	const Version = "0.5.6";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("blogLocation", "/blog/");
		$this->yellow->config->setDefault("blogWithSidebar", "0");
		$this->yellow->config->setDefault("blogPaginationLimit", "5");
	}
	
	// Handle page meta data parsing
	function onParseMeta($page)
	{
		if(substru($page->get("template"), 0, 4)=="blog" && $this->yellow->config->get("blogWithSidebar"))
		{
			if(!$page->isExisting("sidebar")) $page->set("sidebar", "sidebar");
		}
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = NULL;
		if($name=="blogarchive" && $shortcut)
		{
			list($location) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($location)) $location = $this->yellow->config->get("blogLocation");
			$blog = $this->yellow->pages->find($location);
			$pages = $blog ? $blog->getChildren(!$blog->isVisible()) : $this->yellow->pages->clean();
			$pages->filter("template", "blog");
			$page->setLastModified($pages->getModified());
			$months = array();
			foreach($pages as $page) if(preg_match("/^(\d+\-\d+)\-/", $page->get("published"), $matches)) ++$months[$matches[1]];
			if(count($months))
			{
				uksort($months, strnatcasecmp);
				$months = array_reverse($months);
				$output = "<div class=\"".htmlspecialchars($name)."\">\n";
				$output .= "<ul>\n";
				foreach($months as $key=>$value)
				{
					$output .= "<li><a href=\"".$blog->getLocation().$this->yellow->toolbox->normaliseArgs("published:$key")."\">";
					$output .= htmlspecialchars($this->yellow->text->normaliseDate($key))."</a></li>\n";
				}
				$output .= "</ul>\n";
				$output .= "</div>\n";
			} else {
				$page->error(500, "Blogarchive '$location' does not exist!");
			}
		}
		if($name=="blogrecent" && $shortcut)
		{
			list($location, $pagesMax) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($location)) $location = $this->yellow->config->get("blogLocation");
			if(empty($pagesMax)) $pagesMax = 10;
			$blog = $this->yellow->pages->find($location);
			$pages = $blog ? $blog->getChildren(!$blog->isVisible()) : $this->yellow->pages->clean();
			$pages->filter("template", "blog")->sort("published", false)->limit($pagesMax);
			$page->setLastModified($pages->getModified());
			if(count($pages))
			{
				$output = "<div class=\"".htmlspecialchars($name)."\">\n";
				$output .= "<ul>\n";
				foreach($pages as $page)
				{
					$output .= "<li><a href=\"".$page->getLocation()."\">".$page->getHtml("titleNavigation")."</a></li>\n";
				}
				$output .= "</ul>\n";
				$output .= "</div>\n";
			} else {
				$page->error(500, "Blogrecent '$location' does not exist!");
			}
		}
		if($name=="blogrelated" && $shortcut)
		{
			list($location, $pagesMax) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($location)) $location = $this->yellow->config->get("blogLocation");
			if(empty($pagesMax)) $pagesMax = 4;
			$blog = $this->yellow->pages->find($location);
			$pages = $blog ? $blog->getChildren(!$blog->isVisible()) : $this->yellow->pages->clean();
			$pages->filter("template", "blog")->similar($page->getPage("main"))->limit($pagesMax);
			$page->setLastModified($pages->getModified());
			if(count($pages))
			{
				$output = "<div class=\"".htmlspecialchars($name)."\">\n";
				$output .= "<ul>\n";
				foreach($pages as $page)
				{
					$output .= "<li><a href=\"".$page->getLocation()."\">".$page->getHtml("titleNavigation")."</a></li>\n";
				}
				$output .= "</ul>\n";
				$output .= "</div>\n";
			} else {
				$page->error(500, "Blogrelated '$location' does not exist!");
			}
		}
		if($name=="blogtags" && $shortcut)
		{
			list($location) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($location)) $location = $this->yellow->config->get("blogLocation");
			$blog = $this->yellow->pages->find($location);
			$pages = $blog ? $blog->getChildren(!$blog->isVisible()) : $this->yellow->pages->clean();
			$pages->filter("template", "blog");
			$page->setLastModified($pages->getModified());
			$tags = array();
			foreach($pages as $page) if($page->isExisting("tag")) foreach(preg_split("/,\s*/", $page->get("tag")) as $tag) ++$tags[$tag];
			if(count($tags))
			{
				uksort($tags, strnatcasecmp);
				$output = "<div class=\"".htmlspecialchars($name)."\">\n";
				$output .= "<ul>\n";
				foreach($tags as $key=>$value)
				{
					$output .= "<li><a href=\"".$blog->getLocation().$this->yellow->toolbox->normaliseArgs("tag:$key")."\">";
					$output .= htmlspecialchars($key)."</a></li>\n";
				}
				$output .= "</ul>\n";
				$output .= "</div>\n";
			} else {
				$page->error(500, "Blogtags '$location' does not exist!");
			}
		}
		return $output;
	}
	
	// Handle page parsing
	function onParsePage()
	{
		if($this->yellow->page->get("template") == "blogpages")
		{
			$pages = $this->yellow->page->getChildren(!$this->yellow->page->isVisible());
			$pagesFilter = array();
			if($_REQUEST["tag"])
			{
				$pages->filter("tag", $_REQUEST["tag"]);
				array_push($pagesFilter, $pages->getFilter());
			}
			if($_REQUEST["author"])
			{
				$pages->filter("author", $_REQUEST["author"]);
				array_push($pagesFilter, $pages->getFilter());
			}
			if($_REQUEST["published"])
			{
				$pages->filter("published", $_REQUEST["published"], false);
				array_push($pagesFilter, $this->yellow->text->normaliseDate($pages->getFilter()));
			}
			$pages->sort("published")->filter("template", "blog");
			$pages->pagination($this->yellow->config->get("blogPaginationLimit"));
			if(!$pages->getPaginationNumber()) $this->yellow->page->error(404);
			if(!empty($pagesFilter))
			{
				$title = implode(' ', $pagesFilter);
				$this->yellow->page->set("titleHeader", $title." - ".$this->yellow->page->get("sitename"));
				$this->yellow->page->set("titleBlog", $this->yellow->text->get("blogFilter")." ".$title);
			}
			$this->yellow->page->setPages($pages);
			$this->yellow->page->setLastModified($pages->getModified());
			$this->yellow->page->setHeader("Cache-Control", "max-age=60");
		}
	}

	// Handle page extra HTML data
	function onExtra($name)
	{
		return $this->onParseContentBlock($this->yellow->page, $name, "", true);
	}
}

$yellow->plugins->register("blog", "YellowBlog", YellowBlog::Version);
?>