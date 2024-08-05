<?php

class almasryalyoum extends plugin_base
{
	// ANT settings
	protected $ant_precision = 7;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/64.0.3282.167 Chrome/64.0.3282.167 Safari/537.36';
	protected $use_proxies = true;
	protected $use_headless = true;


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
				'process_link' => 'process_list1_link'
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
			'headline' => '/(?:<h1.*>|<h1.*<span>)(.*)(?:<\/h1>|<\/span>)/Uis',
			'content' => '/<div id="NewsStory">(.*)<div class="tags-u">/Uis',
			'author' => '/"author": {.*"name": "(.*)"/Uis',
			'article_date' => '/"datePublished":\s*"([^"]*)"/Uis'
		)
	);

	protected $logic_opinion = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_opinion_link'
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<url>\s*<loc>(.*)<\/loc>/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/(?:<h1.*>|<h1.*<span>)(.*)(?:<\/h1>|<\/span>)/Uis',
			'content' => '/<div id="NewsStory">(.*)<div class="tags-u">/Uis',
			'author' => '/"author": {.*"name": "(.*)"/Uis',
			'article_date' => '/"datePublished":\s*"([^"]*)"/Uis'
		)
	);



	public function prepare_opinion($section_id)
	{

		$this->logic = $this->logic_opinion;
	}

	protected function process_headline($headline, $article_data)
	{

		$headline = preg_replace('/(<span class="date">.*<\/span>)/Uis', '', $headline);
		return $headline;
	}
	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/(<strong>.*<\/strong>)/Uis', '', $content);
		$content = preg_replace('/(<div class="min_related">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div id="ad-container">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/Error loading media/Uis', '', $content);
		$content = preg_replace('/(Nigeria the most popular African football team from 90s)/Uis', '', $content);
		$content = preg_replace('/(unstick)/Uis', '', $content);
		$content = preg_replace('/(Share this video)/Uis', '', $content);
		$content = preg_replace('/(Copy)/Uis', '', $content);
		$content = preg_replace('/(Pause Play)/Uis', '', $content);
		$content = preg_replace('/(00:00)/Uis', '', $content);
		$content = preg_replace('/(% Buffered 1.4472931639022204)/Uis', '', $content);
		$content = preg_replace('/(Previous Pause Play Next)/Uis', '', $content);
		$content = preg_replace('/(Live)/Uis', '', $content);
		$content = preg_replace('/(00:00 \/ 01:20)/Uis', '', $content);
		$content = preg_replace('/(Unmute Mute)/Uis', '', $content);
		$content = preg_replace('/(Settings Exit fullscreen Fullscreen)/Uis', '', $content);
		$content = preg_replace('/(Copy video url)/Uis', '', $content);
		$content = preg_replace('/(Play \/ Pause)/Uis', '', $content);
		$content = preg_replace('/(Mute \/ Unmute)/Uis', '', $content);
		$content = preg_replace('/(Report a problem)/Uis', '', $content);
		$content = preg_replace('/(Language)/Uis', '', $content);
		$content = preg_replace('/(Back)/Uis', '', $content);
		$content = preg_replace('/(Default)/Uis', '', $content);
		$content = preg_replace('/(English)/Uis', '', $content);
		$content = preg_replace('/(Español)/Uis', '', $content);
		$content = preg_replace('/(Українська)/Uis', '', $content);
		$content = preg_replace('/(Русский)/Uis', '', $content);
		$content = preg_replace('/(Share)/Uis', '', $content);
		$content = preg_replace('/(Back)/Uis', '', $content);
		$content = preg_replace('/(Facebook)/Uis', '', $content);
		$content = preg_replace('/(Twitter)/Uis', '', $content);
		$content = preg_replace('/(Linkedin)/Uis', '', $content);
		$content = preg_replace('/(Email)/Uis', '', $content);
		$content = preg_replace('/(Vidverto Player)/Uis', '', $content);


		return $content;
	}

	protected function process_list1_link($link, $referer_link, $logic)
	{
		$m = date("m");
		$m = str_replace('01', '1', $m);
		$m = str_replace('02', '2', $m);
		$m = str_replace('03', '3', $m);
		$m = str_replace('04', '4', $m);
		$m = str_replace('05', '5', $m);
		$m = str_replace('06', '6', $m);
		$m = str_replace('07', '7', $m);
		$m = str_replace('08', '8', $m);
		$m = str_replace('09', '9', $m);

		$link =  'https://www.almasryalyoum.com/sitemapxmls/news/news-' . date('Y') . '-' . $m . '.xml';
		return $link;
	}

	protected function process_list1_opinion_link($link, $referer_link, $logic)
	{
		$m = date("m");
		$m = str_replace('01', '1', $m);
		$m = str_replace('02', '2', $m);
		$m = str_replace('03', '3', $m);
		$m = str_replace('04', '4', $m);
		$m = str_replace('05', '5', $m);
		$m = str_replace('06', '6', $m);
		$m = str_replace('07', '7', $m);
		$m = str_replace('08', '8', $m);
		$m = str_replace('09', '9', $m);
		return 'https://www.almasryalyoum.com/sitemapxmls/opinions/opinions-' . date('Y') . '-' . $m . '.xml';
	}

	protected function process_list1_stories_link($link, $referer_link, $logic)
	{
		$m = date("m");
		$m = str_replace('01', '1', $m);
		$m = str_replace('02', '2', $m);
		$m = str_replace('03', '3', $m);
		$m = str_replace('04', '4', $m);
		$m = str_replace('05', '5', $m);
		$m = str_replace('06', '6', $m);
		$m = str_replace('07', '7', $m);
		$m = str_replace('08', '8', $m);
		$m = str_replace('09', '9', $m);

		return 'https://www.almasryalyoum.com/sitemapxmls/stories/stories-' . date('Y') . '-' . $m . '.xml';
	}


	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{

		//14-06-2021
		if (preg_match('/(\d+?)-(\d+?)-(\d+?)/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $matches[2] . '-' . $matches[1] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		} elseif (preg_match('/(\d+?)\/(\d+?)\/(\d+?)/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $matches[1] . '-' . $matches[2] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;
	}

	public function pre_get_page(&$page)
	{

		$this->ant->set_wait_for_load(true);
	}
}
