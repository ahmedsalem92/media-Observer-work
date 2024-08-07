<?php

class swiftnewzcom extends plugin_base
{

	// ANT settings
	protected $ant_precision = 6;
	protected $use_proxies = true;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = false;
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
			'headline' => '/<title>(.*) - Platinum<\/title>/Uis',
			'content' => '/<div id="text_block-10-305".*>.*<figure.*>(.*)<\/span><\/div>/Uis',
			'article_date' => '/datePublished":"(.*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic)
	{
		$temp_link = '';  // https://platinumarabia.net/post-sitemap.xml
		if (preg_match_all('/<loc>(https:\/\/platinumarabia\.net\/post-sitemap\.xml)<\/loc>/Uis', $link, $matches)) {
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

		$content = preg_replace('/(>\s*- Advertisement -\s*<)/Uis', '><', $content);
		$content = preg_replace('/(<span class="number.*<\/span>)/Uis', '', $content);
		$content = preg_replace('/(<p>دكا .*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>.* سويفت نيوز:<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>.* سويفت نيوز :<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>.*واس:<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>.* واس :<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>المصدر:.*<\/p>)/Uis', '', $content);

		$content = preg_replace('/<p class=\'qnfc-caption qnfc-caption-below\'>- Advertisement -<\/p>/Uis', '', $content);
		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{

		//2019-11-10T15:06:56+03:00
		if (preg_match('/(\d{4}-\d{1,2}-\d{1,2})T(.*)(?:\+|Z|\.|\")/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		return $article_date;
	}
}
