<?php

class thegulfheraldcom extends plugin_base
{

	// ANT settings
	protected $ant_precision = 6;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';
	protected $use_proxies = true;


	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = false;

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
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/(<div class="tds-button td-fix-index">.*)(?:<div class="td-post-sharing-visible">|<ul class="tdb-tags">)/Uis',
			'author' => false,
			'article_date' => '/datePublished":"(.*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic)
	{

		$temp_link = ''; // https://aviaciondigital.com/post-sitemap21.xml
		if (preg_match_all('/<loc>(https:\/\/aviaciondigital\.com\/post-sitemap\d+?\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>', '', $temp_link);
			$temp_link = str_replace('</loc>', '', $temp_link);
		}

		return $temp_link;
	}

	private $links = array();
	private $array_index = 0;

	protected function process_article_link($link, $referer_link, $logic)
	{

		$temp_link = '';
		if (empty($this->links)) {
			$result = $this->ant->get($referer_link);
			if (preg_match_all('/<loc>(.*)<\/loc>/Uis', $result, $matches)) {
				$this->links = $matches[0];
				$this->array_index = sizeof($this->links);
			}
		}
		$this->array_index--;
		if ($this->array_index > 0 and isset($this->links[$this->array_index])) {
			$temp_link = str_replace('<loc>', '', $this->links[$this->array_index]);
			$temp_link = str_replace('</loc>', '', $temp_link);
			return $temp_link;
		}

		return '';
	}

	protected function process_content($content, $article_data)
	{
		$content = preg_replace('/<div class="wp-block-image">(.*)<\/div>/Uis', '', $content);
		$content = preg_replace('/<div class="wp-block-embed__wrapper">(.*)<\/div>/Uis', '', $content);
		$content = preg_replace('/<div class="tds-button td-fix-index">(.*)<\/div>/Uis', '', $content);
		$content = preg_replace('/<div style="display: inline-block">(.*)<\/div>/Uis', '', $content);
		$content = preg_replace('/<div class="tdb-next-post tdb-next-post-bg tdb-post-prev">(.*)<\/div>/Uis', '', $content);
		$content = preg_replace('/<blockquote class="twitter-tweet">(.*)<\/blockquote>/Uis', '', $content);
		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2024-07-30T06:29:14+00:00 
		if (preg_match('/(.*)T(.*)\+/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
        
		if(strpos($this->settings['site_section_link'], '/columns')){
			return date('Y-m-d H:i:s', time());
		}

		return $article_date;

	}

}
