<?php
// Copyright (c) 2013-2015 Datenstrom, http://datenstrom.se
// This file may be used and distributed under the terms of the public license.

// Search plugin
class YellowSearch
{
	const Version = "0.5.2";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		$this->yellow->config->setDefault("searchPaginationLimit", "5");		
	}
	
	// Handle page parsing
	function onParsePage()
	{
		if($this->yellow->page->get("template") == "search")
		{
			if(PHP_SAPI == "cli") $this->yellow->page->error(500, "Static website not supported!");
			$query = trim($_REQUEST["query"]);
			$tokens = array_slice(array_unique(array_filter(explode(' ', $query), "strlen")), 0, 10);
			if(!empty($tokens))
			{
				$this->yellow->page->set("titleHeader", $query." - ".$this->yellow->page->get("sitename"));
				$this->yellow->page->set("title", $this->yellow->text->get("searchQuery")." ".$query);
				$this->yellow->page->set("searchResults", $this->yellow->text->get("searchResultsEmpty"));
				$pages = $this->yellow->pages->clean();
				foreach($this->yellow->pages->index(false, false) as $page)
				{
					$searchScore = 0;
					$searchTokens = array();
					foreach($tokens as $token)
					{
						$score = substr_count(strtoloweru($page->getContent(true)), strtoloweru($token));
						if($score) { $searchScore += $score; $searchTokens[$token] = true; }
						if(stristr($page->getLocation(), $token)) { $searchScore += 20; $searchTokens[$token] = true; }
						if(stristr($page->get("title"), $token)) { $searchScore += 10; $searchTokens[$token] = true; }
						if(stristr($page->get("tag"), $token)) { $searchScore += 5; $searchTokens[$token] = true; }
						if(stristr($page->get("author"), $token)) { $searchScore += 2; $searchTokens[$token] = true; }
					}
					if(count($tokens) == count($searchTokens))
					{
						$page->set("searchscore", $searchScore);
						$pages->append($page);
					}
				}
				$pages->sort("searchscore");
				$pages->pagination($this->yellow->config->get("searchPaginationLimit"));
				if($_REQUEST["page"] && !$pages->getPaginationNumber()) $this->yellow->page->error(404);
				$this->yellow->page->setPages($pages);
				$this->yellow->page->setLastModified($pages->getModified());
				$this->yellow->page->setHeader("Cache-Control", "max-age=60");
			} else {
				$this->yellow->page->set("searchResults", $this->yellow->text->get("searchResultsNone"));
			}
		}
	}
}

$yellow->plugins->register("search", "YellowSearch", YellowSearch::Version);
?>