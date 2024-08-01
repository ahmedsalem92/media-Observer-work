<?php

class alnar extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $use_proxies = true;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';
	
	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = true;
	
	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link',
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1 class="post-title[^>]*>(.*)<\/h1>/Uis',
			'content' => '/(<div class="entry-content[^>]*>.*)(?:<div class="stream-item stream-item-below-post-content|<\!-- \.entry-content -->|<div id="post-extra-info|<\/article|<footer)/Uis',
			'author' => false,
			'article_date' => '/<meta property="article:published_time" content="([^"]*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic) {

		$temp_link ='';
		if(preg_match_all('/<loc>((?:https|http):(?:\/\/|\/\/www\.)technologianews\.com\/post-sitemap\d{0,100}\.xml)<\/loc>/Uis', $link, $matches)){
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>' , '' , $temp_link);
			$temp_link = str_replace('</loc>' , '' , $temp_link);
		}

		return $temp_link;
	}
	
	private $links = array();
	private $array_index = 0 ;

	protected function process_article_link($link, $referer_link, $logic) {

		$temp_link = '';
		if(empty($this->links)){
			$result = $this->ant->get($referer_link);
			if(preg_match_all('/<loc>(.*)<\/loc>/Uis', $result, $matches)){
				$this->links = $matches[0];
				$this->array_index = sizeof($this->links);
			}
		}
		$this->array_index--;
		if($this->array_index > 0 and isset($this->links[$this->array_index]) ){
			$temp_link = str_replace('<loc>' , '' , $this->links[$this->array_index]);
			$temp_link = str_replace('</loc>' , '' , $temp_link);
			return $temp_link;
		}

		return '';
	}

	protected function process_content($content, $article_data) {

		
		$content = preg_replace('//Uis', '', $content);
		
		$content = preg_replace('/(>\s*&nbsp;\s*<)/Uis', '><', $content);
		$content = preg_replace('/(<ins.*<\/ins>)/Uis', '', $content);
		$content = preg_replace('/(<script.*<\/script>)/Uis', '', $content);
		$content = preg_replace('/(<style.*<\/style>)/Uis', '', $content);
		if (preg_match('/(<div class="entry-content[^>]*>)/Uis', $content, $matches)){
			if(strlen(trim(strip_tags($content)))==0)
				return 'no content';
		}
		
		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2018-05-21T08:20:26+00:00
		if (preg_match('/(\d{4}-\d{1,2}-\d{1,2})T(.*)(?:\+|Z|\.|\")/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' '.$matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;
	}

}
