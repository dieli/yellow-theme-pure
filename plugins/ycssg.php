<?php
// ycssg plugin
class YellowCSSGallery
{
	const Version = "0.1.0";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
		// placeholder
		$this->yellow->config->setDefault("CSSGallery", "0");
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = NULL;
		if($name=="ycssg" && $shortcut)
		{
			list($pattern, $style, $nav, $autoplay) = $this->yellow->toolbox->getTextArgs($text);
			if(empty($pattern))
			{
				$files = $page->getFiles(true);
			} else {
				$images = $this->yellow->config->get("imageDir");
				$files =  $this->yellow->files->index(true, true)->match("#$images$pattern#");
			}
			$iNoFiles = count($files);
			$output ="";
			if(iNoFiles)
			{
				$page->setLastModified($files->getModified());
				// Generate Lightbox
				$iFileCnt = 1;
				foreach($files as $file)
				{
					if ($iFileCnt == 1) {
						$hash_imag = hash('ripemd160', htmlspecialchars($file->getLocation()));
						$prev = $iNoFiles;
					} else {
						$prev = $iFileCnt-1;
					}
					if ($iFileCnt == $iNoFiles) {
						$next = 1;
					} else {
						$next = $iFileCnt+1;
					}
					$prev = $prev.$hash_imag;
					$next = $next.$hash_imag;
					$output .= "<div class=\"lightbox\" id=\"img".$iFileCnt.$hash_imag."\">";
					$output .= "<a href=\"#_\" >";
					$output .= "<img src=\"".htmlspecialchars($file->getLocation())."\">";
					$output .= "</a>";
					$output .= "<div class=\"slide__caption\">";
					$output .= "<a href=\"#img".$prev."\" class=\"lb_left\">&#x2039;</a>";
					// Add Exif Data Image 1: Aperture: ; F-Stop:
					$output .= "<a href=\"#img".$next."\" class=\"lb_right\">&#x203a;</a>";
					$output .= "</div>";
					$output .= "</div>";
					$iFileCnt = $iFileCnt +1;
				}
				// The slideshow
				$iFileCnt = 1;
				$output .= "<div class=\"slides\">";
				foreach($files as $file)
				{
					if ($iFileCnt == 1) {
						$prev = $iNoFiles;
					} else {
						$prev = $iFileCnt-1;
					}
					if ($iFileCnt == $iNoFiles) {
						$next = 1;
					} else {
						$next = $iFileCnt+1;
					}
					$prev = $prev.$hash_imag;
					$next = $next.$hash_imag;
					if ($iFileCnt == 1) {
						$output .= "<input type=\"radio\" name=\"radio".$hash_imag."\" id=\"img-".$iFileCnt.$hash_imag."\" checked />";
					}else {
						$output .= "<input type=\"radio\" name=\"radio".$hash_imag."\" id=\"img-".$iFileCnt.$hash_imag."\"/>";
					}
					$output .= "<div class=\"slide\"  id=\"img-".$iFileCnt.$hash_imag."\">";
					$output .= "<label for=\"img-".$prev."\" class=\"nav prev\">&#x2039;</label>";
					$output .= "<label for=\"img-".$next."\" class=\"nav next\">&#x203a;</label>";
					$output .= "<a href=\"#img".$iFileCnt.$hash_imag."\"><img src=\"".htmlspecialchars($file->getLocation())."\"></a>";
					$output .= "</div>\n";
					$iFileCnt = $iFileCnt +1;
				}
				// The navigation 
				$output .= "<div class=\"quicknav\">";
				for ($iFileCnt = 1; $iFileCnt <= $iNoFiles; $iFileCnt++) {
					$output .= "<label for=\"img-".$iFileCnt.$hash_imag."\" class=\"nav-dots\" id=\"dot-img-".$iFileCnt.$hash_imag."\"></label>";
				}
				$output .= "</div>";
				$output .= "</div>\n";
				// Dynamic style for checked stuff
				$output .= "<style>\n";
				for ($iFileCnt = 1; $iFileCnt < $iNoFiles; $iFileCnt++) {
					$output .= "input[type=\"radio\"]#img-".$iFileCnt.$hash_imag.":checked ~ .quicknav label#dot-img-".$iFileCnt.$hash_imag.",\n";
				}
				$output .= "input[type=\"radio\"]#img-".$iNoFiles.$hash_imag.":checked ~ .quicknav label#dot-img-".$iNoFiles.$hash_imag."\n";
				$output .= "{ background: rgba(0, 0, 0, 0.8);}";
				$output .= "</style>\n";
			} else {
				$page->error(500, "YCSSGallery '$pattern' does not exist!");
			}
		}
		return $output;
	}
	
	// Handle page extra HTML data
	function onExtra($name)
	{
		$output = NULL;
		if($name == "header")
		{
			$pluginLocation = $this->yellow->config->get("serverBase").$this->yellow->config->get("themeLocation");
//			$output = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".$this->$yellow->config->get("serverBase").$this->$yellow->config->get("themeLocation")."ycssg.css\" />\n";
			$output = "<link rel=\"stylesheet\" property=\"stylesheet\"  type=\"text/css\" media=\"all\" href=\"".$pluginLocation."ycssg.min.css\" />\n";
		}
		return $output;
	}
}

$yellow->plugins->register("ycssg", "YellowCSSGallery", YellowCSSGallery::Version);
?>