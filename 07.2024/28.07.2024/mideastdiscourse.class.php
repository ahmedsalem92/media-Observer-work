<?php

class alghadtv extends plugin_base
{

	// ANT settings
	protected $ant_precision = 6;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/60.0.3112.113 Chrome/60.0.3112.113 Safari/537.36';
	protected $use_headless = true;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;


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
			)
		),
		'article' => array(
			'headline' => '/<title>(.*)-/Uis',
			'content' => '/<div class="post-content cf entry-content content-spacious">(.*)<div class="the-post-tags">/Uis',
			'author' => '/<p>Source: (.*)<\/p>/Uis',
			'article_date' => '/published_time" content="(.*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic)
	{

		$temp_link = '';  // https://mideastdiscourse.com/post-sitemap24.xml
		if (preg_match_all('/<loc>(https:\/\/mideastdiscourse.com\/post-sitemapW\d+.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>', '', $temp_link);
			$temp_link = str_replace('</loc>', '', $temp_link);
		}

		return $temp_link;
	}

	protected function process_content($content, $article_data)
	{
		$content = preg_replace('/(<p>Source:.*<\/p>)/Uis', '', $content);
		return $content;
	}

	protected function process_date($article_date)
	{

		//2018-05-21T08:20:26+00:00
		if (preg_match('/(.*)T(.*)\+/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		if (strpos($this->settings['site_section_link'], '/columns')) {
			return date('Y-m-d H:i:s', time());
		}

		return $article_date;
	}

}
