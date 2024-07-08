<?php

class timeturkcom extends plugin_base
{
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

	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array(
					'/<div class="align-self-center some-cat">(.*)<\/ul>/Uis',
					'/(<a.*<\/a>)/Uis'
				)
			)
		),
		'section' => array(
			'link' => '/href="([^"]*)"/Uis',
			'name' => '/(?:<\/i>|<a[^<]*>)([^<]*)<\/a>/Uis',
			'append_domain' => false,
			'process_link' => 'filter_sections'
		)
	);


	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<\/div>\s*<div>\s*<div class="pagination-card">\s*<a href="([^"]*)" title=/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<div class="col-12 col-md-6 col-lg-4 col-xl-3">\s*<div class="card">\s*<!-- <a .*<a href="(.*)"/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<\/h1>(.*)<div class="col-12 col-lg-4 col-xl-3 d-none d-lg-block ">/Uis',
			'author' => '/"author":.*"name":\s*"([^"]*)"/Uis',
			'article_date' => '/(?:"uploadDate": "|"datePublished": ")(.*)"/Uis'
		)
	);


	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<div class="swiper-slide [^<]*>\s*<a href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link',
				'ignore_terminal_stop' => true
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<div class="full-card [^<]*>\s*<a href="(.*)"/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link',
				'ignore_terminal_stop' => true
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<\/h1>(.*)<div class="col-12 col-lg-4 col-xl-3 d-none d-lg-block ">/Uis',
			'author' => '/"author":.*"name":\s*"([^"]*)"/Uis',
			'article_date' => '/(?:"uploadDate": "|"datePublished": ")(.*)"/Uis'
		)
	);

	

	protected function process_article_link($link, $referer_link, $logic) {

		if (strpos($link, '/bit.ly/') || strpos($link, '/kizilay-yetim-ustmanset/')) {
			return false;
		}
		return $link;
	}

	public function prepare_home($section_id)
	{
		$this->logic = $this->logic_home;
	}

	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/<div class="news-social-box">\s*<ul>.*<\/ul>\s*<div.*div>/Uis', '', $content);
		$content = preg_replace('/<div class="date-space">.*<div class="news-content"[^>]*>/Uis', '', $content);
		$content = preg_replace('/<div class="share-native">.*<\/div>/Uis', '', $content);
		$content = preg_replace('/(<div class="image-top">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<p>AA\s*<\/div>)/Uis', '', $content);
		$content = preg_replace('/<p style="color:#fff!important;">Bu yazı.*p>/Uis', '', $content);
		$content = preg_replace('/(<div class="post-time">.*<\/p>.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/<div class="tags">(.*)<\/div>/Uis', '', $content);
		return $content;
	}


	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{

		if (preg_match('/(\d+?)-(\d+?)-(\d+?)/Uis', $article_date, $matches)) {

			$date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . '-' . $matches[2] . '-' . $matches[3] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			if ($date_obj instanceof DateTime) {
				$article_date = $date_obj->format('Y-m-d H:i:s');
			}
		}
		return $article_date;
	}
}
