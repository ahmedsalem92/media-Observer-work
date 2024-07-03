<?php

class aksamcomtr extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	//protected $use_proxies = true;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';

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
				'regexp' => '/<loc><\!\[CDATA\[ (.*)\]\]><\/loc>/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/(?:<h1 class="title-1 color-primary[^<]*>|<h1 class="main-title[^<]*>)(.*)<\/h1>/Uis',
			'content' => '/(?:<div id="text">|<div class="news-text[^<]*>)(.*)(?:<div class="flx fbw mbm">|<div id="ENGAGEYA_WIDGET[^<]*>|<div class="other-news">|<ul class="tags[^<]*>|<\/article)/Uis',
			'author' => false,
			'article_date' => '/"datePublished":\s*"([^"]*)"/Uis'
		)
	);
	protected $logic_haber = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/(?:<h1 class="title-1 color-primary[^<]*>|<h1 class="main-title[^<]*>)(.*)<\/h1>/Uis',
			'content' => '/(?:<div id="text">|<div class="news-text[^<]*>)(.*)(?:<div class="flx fbw mbm">|<div id="ENGAGEYA_WIDGET[^<]*>|<div class="other-news">|<ul class="tags[^<]*>|<\/article)/Uis',
			'author' => false,
			'article_date' => '/"datePublished":\s*"([^"]*)"/Uis'
		)
	);

	public function prepare_haber($section_id) {

		$this->logic = $this->logic_haber;

	}

	protected function process_article_link($link, $referer_link, $logic) {

		if(preg_match('/foto-galeri\//Uis', $link, $matches)){
			return '';
		}

		return $link;
	}
	protected function process_list1_link($link, $referer_link, $logic) {

		$temp_link ='';
		if(preg_match('/<loc>((?:https|http):(?:\/\/|\/\/www\.)aksam\.com\.tr\/sitemaps\/posts-\d{0,100}-\d{0,100}\.xml)<\/loc>/Uis', $link, $matches)){
			$temp_link = $matches[1];
		}

		return $temp_link;
	}

	protected function process_content($content, $article_data){

		$content = preg_replace('/(<a target="_blank".*<\/a>)/Uis', '', $content);
		return $content;
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
