<?php

class wonewsnet__arabic extends plugin_base
{

	// ANT settings
	protected $ant_precision = 2;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;


	// DEFINITIONSa
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
			'content' => '/(?:<div class="separator" style[^<]*>|<div id=\'tocDiv\'>)(.*)(?:<div class=\'hideensa\'>|<div class=\'post-tags\'>)/Uis',
			'author' => false,
			'article_date' => '/"datePublished": "(.*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic)
	{

		$temp_link = '';
		if (preg_match('/<loc>(https:\/\/www.wonews\.net\/sitemap.xml\?page=\d+?)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[1];
			$temp_link = str_replace('<loc>', '', $temp_link);
			$temp_link = str_replace('</loc>', '', $temp_link);
		}

		return $temp_link;
	}

	protected function process_content($content, $article_data)
	{
		$content = preg_replace('/(تعديل المقال)/Uis', '', $content);
		return $content;
	}


	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{

		//2018-05-21T08:20:26+00:00
		if (preg_match('/(.*)T/Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;
	}
}
