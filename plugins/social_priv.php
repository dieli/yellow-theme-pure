<?php

// Social Privacy plugin

class YellowSocialPriv
{
	const Version = "0.1.0";
	var $yellow;			//access to API
	
	// Handle initialisation
	function onLoad($yellow)
	{
		$this->yellow = $yellow;
	}
	
	// Handle page content parsing of custom block
	function onParseContentBlock($page, $name, $text, $shortcut)
	{
		$output = NULL;
		if($name=="social_priv" && $shortcut)
		{
			// Get current page
			$url_for_share = $this->yellow->config->get("serverName").$yellow->page->base.$page->getLocation();
			$title = "";
			$description = "";
			$output = "<ul class=\"share_class\">";
			
			// Get facebook url		
			if ($this->yellow->config->get("social_priv.enable_facebook")==1) {
				$url_facebook = $this->yellow->config->get("social_priv.url_facebook");
				if (empty($url_facebook)) $url_facebook = "https://www.facebook.com/sharer/sharer.php?u=";
				// get share text
				$url_text = $this->yellow->config->get("social_priv.text_facebook");
				if (empty($url_text)) $url_text = "share";
				// Build share link
				$url_facebook = $url_facebook.$url_for_share;
				// Facebook
				$output .=  "<li class=\"share_button facebook\"><a href=\"".$url_facebook."\" target=\"_blank\"><span class=\"fa icon-facebook-squared\"></span><span class=\"share_text\">".$url_text."</span>";
				//$output .=  "<span class=\"share_count\">".$this->get_fb($url_for_share)."</span>";
				$output .=  "</a></li>";
			}
			
			// Get twitter url
			if ($this->yellow->config->get("social_priv.enable_twitter")==1) {
				$url_twitter = $this->yellow->config->get("social_priv.url_twitter");
				if (empty($url_twitter)) $url_twitter = "https://twitter.com/intent/tweet/?text=";
				// get share text
				$url_text = $this->yellow->config->get("social_priv.text_twitter");
				if (empty($url_text)) $url_text = "tweet";
				// Build share link
				$url_twitter = $url_twitter.$description.$url_for_share."&amp;url=".$url_for_share;
				// Twitter
				$output .=  "<li class=\"share_button twitter\"><a href=\"".$url_twitter."\" target=\"_blank\"><span class=\"fa icon-twitter\"></span><span class=\"share_text\">".$url_text."</span>";
//				$output .=  "<span class=\"share_count\">".$this->get_tweets($url_for_share)."</span>";
				$output .=  "</a></li>";
			}
			// Get google url		
			if ($this->yellow->config->get("social_priv.enable_google")==1) {
				$url_google = $this->yellow->config->get("social_priv.url_google");
				if (empty($url_google)) $url_google = "https://plus.google.com/share?url=";
				// get share text
				$url_text = $this->yellow->config->get("social_priv.text_google");
				if (empty($url_text)) $url_text = "+1";
				// Build share link
				$url_google = $url_google.$url_for_share;
				// Google plusones
				$output .=  "<li class=\"share_button google\"><a href=\"".$url_google."\" target=\"_blank\"><span class=\"fa icon-gplus\"></span><span class=\"share_text\">".$url_text."</span>";
//				$output .=  "<span class=\"share_count\">".$this->get_plusones($url_for_share)."</span>";
				$output .=  "</a></li>";
			}
			
			// Get linkedin url		
			if ($this->yellow->config->get("social_priv.enable_linkedin")==1) {
				$url_linkedin = $this->yellow->config->get("social_priv.url_linkedin");
				if (empty($url_linkedin)) $url_linkedin = "https://www.linkedin.com/shareArticle?mini=true&url=";
				// get share text
				$url_text = $this->yellow->config->get("social_priv.text_linkedin");
				if (empty($url_text)) $url_text = "LinkedIn";
				// Build share link
				$url_linkedin = $url_linkedin.$url_for_share."%2F&title=".$title."&source=".$url_for_share."%2F&summary=".$description;
				// LinkedIn
				$output .=  "<li class=\"share_button linkedin\"><a href=\"".$url_linkedin."\" target=\"_blank\"><span class=\"fa icon-linkedin\"></span><span class=\"share_text\">".$url_text."</span>";
//				$output .=  "<span class=\"share_count\">".$this->get_linkedin($url_for_share)."</span>";
				$output .=  "</a></li>";
			}
			
			// Get reddit url		
			if ($this->yellow->config->get("social_priv.enable_reddit")==1) {
				$url_reddit = $this->yellow->config->get("social_priv.url_reddit");
				if (empty($url_reddit)) $url_reddit = "http://www.reddit.com/submit/?url=";
				// get share text
				$url_text = $this->yellow->config->get("social_priv.text_reddit");
				if (empty($url_text)) $url_text = "Reddit";
				// Build share link
				$url_reddit = $url_reddit.$url_for_share;
				// Reddit
				$output .=  "<li class=\"share_button reddit\"><a href=\"".$url_reddit."\" target=\"_blank\"><span class=\"fa icon-reddit\"></span><span class=\"share_text\">".$url_text."</span></li>";
			}
			
			// Get pinterest url		
			if ($this->yellow->config->get("social_priv.enable_pinterest")==1) {
				$url_pinterest = $this->yellow->config->get("social_priv.url_pinterest");
				if (empty($url_pinterest)) $url_pinterest = "https://www.pinterest.com/pin/create/button/?url=";
				// get share text
				$url_text = $this->yellow->config->get("social_priv.text_pinterest");
				if (empty($url_text)) $url_text = "pinterest";
				// Build share link
				$url_pinterest = $url_pinterest.$url_for_share."%2F&media=".$url_for_share."%2Ficon.png&description=".$description;
				// Pinterest
				$output .=  "<li class=\"share_button pinterest\"><a href=\"".$url_pinterest."\" target=\"_blank\"><span class=\"fa icon-pinterest\"></span><span class=\"share_text\">".$url_text."</span>";
//				$output .=  "<span class=\"share_count\">".$this->get_pinterest($url_for_share)."</span>";
				$output .=  "</a></li>";
			}
			$output .= "</ul>";
		}
		return $output;
	}

	// Get social stuff, from http://99webtools.com/blog/php-script-to-get-social-share-count/
	function get_tweets($social_url) { 
		$json_string = $this->file_get_contents_curl('http://urls.api.twitter.com/1/urls/count.json?url=' . $social_url);
		$json = json_decode($json_string, true);
		return isset($json['count'])?intval($json['count']):0;
	}
	
	function get_linkedin($social_url) { 
		$json_string = $this->file_get_contents_curl("http://www.linkedin.com/countserv/count/share?url=$social_url&format=json");
		$json = json_decode($json_string, true);
		return isset($json['count'])?intval($json['count']):0;
	}
	function get_fb($social_url) {
		$json_string = $this->file_get_contents_curl('http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls='.$social_url);
		$json = json_decode($json_string, true);
		return isset($json[0]['total_count'])?intval($json[0]['total_count']):0;
	}
	function get_plusones($social_url)  {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"'.rawurldecode($social_url).'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		$curl_results = curl_exec ($curl);
		curl_close ($curl);
		$json = json_decode($curl_results, true);
		return isset($json[0]['result']['metadata']['globalCounts']['count'])?intval( $json[0]['result']['metadata']['globalCounts']['count'] ):0;
	}
	function get_pinterest($social_url) {
		$return_data = $this->file_get_contents_curl('http://api.pinterest.com/v1/urls/count.json?url='.$social_url);
		$json_string = preg_replace('/^receiveCount\((.*)\)$/', "\1", $return_data);
		$json = json_decode($json_string, true);
		return isset($json['count'])?intval($json['count']):0;
	}
	private function file_get_contents_curl($url){
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		$cont = curl_exec($ch);
		if(curl_error($ch))
		{
		die(curl_error($ch));
		}
		return $cont;
	}
	
	// Handle page extra HTML data
	function onExtra($name)
	{
		return $this->onParseContentBlock($this->yellow->page, $name, "", true);
	}

}

$yellow->plugins->register("social_priv", "YellowSocialPriv", YellowSocialPriv::Version);
?>