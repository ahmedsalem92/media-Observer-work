<?php

class birgunnet extends plugin_base {
	// ANT settings
	protected $ant_precision = 6;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/64.0.3282.167 Chrome/64.0.3282.167 Safari/537.36';
	protected $use_proxies = true;
	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = true;


	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	private $exclude_sections = array(
		'https://www.birgun.net/kategori/birgun-pazar-19' , 'https://www.birgun.net/#' , 'https://www.birgun.net/tv' , 'https://www.birgun.net/kategori/birgun-kitap-21'
		, 'https://www.birgun.net/son-dakika-haberleri' , 'https://www.birgun.net/yazarlar' ,'https://www.birgun.net/kategori/birgun-ege-32',
		'https://www.birgun.net/resmi-ilanlar' , 'https://www.birgun.net/yazi-dizileri'
	);
	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array (
					'/<ul class="navbar-nav navbar-nav-scroll text-nowrap mainmenu">(.*)<div class="nav-item dropdown dropdown-toggle-icon-none nav-search me-3">/Uis',
					'/(<a.*<\/a>)/Uis'
				)
			)
		),
		'section' => array(
			'link' => '/href="(.*)"/Uis',
			'name' => '/<a[^>]*>(.*)<\/a>/Uis',
			'append_domain' => true,
			'process_link' => 'filter_sections'
		)
	);
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp'=> '/<li class=\'page-item active\'>.*<li class=\'page-item\s*\'>.*href=\'(.*)\'/Uis',
				'append_domain' =>	true,
				'process_link' => 'process_article_link'
			),
			1 => array(
				'type' => 'article',
				'regexp'=> '/<h2 class=\'card-title\'><a href=\'(.*)\'/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1>(.*)<\/h1>/Uis',
			'content' => '/<div class=resize>(.*<div class=tags>)/Uis',
			'author' => false,
			'article_date' => '/"datePublished":\s*"([^"]*)"/Uis'
		)
	);


	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => [
					'/<div class="swiper home-top-swiper">(.*)<div class=swiper-button-prev>/Uis',
					'/<div class=swiper-slide>.*<a href=(.*) target/Uis',
				],
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => [
					'/<div class="swiper home-swiper">(.*)<div class=swiper-button-prev>/Uis',
					'/<div class=swiper-slide>.*<a href=(.*) target/Uis',
				],
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			),
			2 => array(
				'type' => 'article',
				'regexp' => [
					'/<main class=home>(.*)<div id=DivResmiIlanlar-2>/Uis',
					'/<div class="col-6 col-lg-3">.*<a href=(.*) target/Uis',
				],
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			),
			3 => array(
				'type' => 'article',
				'regexp' => [
					'/<main class=home>(.*)<div id=DivResmiIlanlar-2>/Uis',
					'/<div class="col-6 col-lg-2">.*<a href=(.*) target/Uis',
				],
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1>(.*)<\/h1>/Uis',
			'content' => '/<div class=resize>(.*<div class=tags>)/Uis',
			'author' => false,
			'article_date' => '/"datePublished":\s*"([^"]*)"/Uis'
		)
	);

	public function prepare_home($section_id) {

		$this->logic = $this->logic_home;

	}




	protected function process_content($content, $article_data){

		$content = preg_replace('/<div class="mb-1 mt-3 latest-articles">(.*)<div class=tags>/Uis', 'photo', $content);
		$content = preg_replace('/(<p>#.*)<\/div>/Uis', '', $content);

		return $content;
	}

	protected function filter_sections($section_link, $section_name, $referer_link, $logic) {

		// exclude these sections
		if (in_array(trim($section_link), $this->exclude_sections)) {
			return '';
		}

		return $section_link;

	}

	protected function process_article_link($link, $referer_link, $logic) {

		if (strpos($link, 'youtube')) {
			return false;
		}
		return $link;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

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
