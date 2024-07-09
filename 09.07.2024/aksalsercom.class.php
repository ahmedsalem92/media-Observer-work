<?php

class aksalsercom extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36';

	// CRAWL settings
	protected $stop_on_date = false;
	protected $stop_on_article_found = true;
	protected $stop_date_override = false;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	private $exclude_sections = array(
		'https://www.aksalser.com/news/terms-and-privacy/',
		'https://www.aksalser.com/news/about-us/',
		'https://www.aksalser.com/news/contact-us/'
	);
	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array (
					'/<ul id="menu-[^<]*>(.*)<ul class="components">/Uis',
					'/(<a.*<\/a>)/Uis'//the whole link
				),
			),

		),
		'section' => array(
			'link' => '/href="([^"]*)"/Uis',//link of section
			'name' => '/<a[^<]*>(.*)<\/a>/Uis',//name of section
			'append_domain' => true,//
			'process_link' => 'filter_sections'
		)
	);
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<li class="the-next-page"><a href="(.*)"/Uis',
				'append_domain' => false,
				'process_link' => 'process_next_link'

			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<h2 class=[^<]*><a href="(.*)">/Uis',
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<h1 class="post-title entry-title">(.*)<\/h1>/Uis',
			'content' => '/(<div class="entry-content[^>]*>.*)(?:<\!-- \.entry-content \/-->|<div id="share-buttons-bottom|<div class="post-shortlink)/Uis',
			'author' => false,
			'article_date' => '/"datepublished":"(.*)"/Uis'
		)
	);
	protected $logic_no_next = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<h2 class=[^<]*><a href="(.*)">/Uis',
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<h1 class="post-title entry-title">(.*)<\/h1>/Uis',
			'content' => '/(<div class="entry-content[^>]*>.*)(?:<\!-- \.entry-content \/-->|<div id="share-buttons-bottom|<div class="post-shortlink)/Uis',
			'author' => false,
			'article_date' => '/"datepublished":"(.*)"/Uis'
		)
	);
	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<h2 class="post-title"><a href="(.*)">/Uis',
				'append_domain' => true,
			)
		),
		'article' => array(
			'headline' => '/<h1 class="post-title entry-title">(.*)<\/h1>/Uis',
			'content' => '/(<div class="entry-content[^>]*>.*)(?:<\!-- \.entry-content \/-->|<div id="share-buttons-bottom|<div class="post-shortlink)/Uis',
			'author' => false,
			'article_date' => '/"datepublished":"(.*)"/Uis'
		)
	);


	public function prepare_home($section_id) {

		$this->logic = $this->logic_home;

	}
	public function prepare_no_next($section_id) {

		$this->logic = $this->logic_no_next;

	}
	protected function filter_sections($section_link, $section_name, $referer_link, $logic) {

		// exclude these sections
		if (in_array(trim($section_link), $this->exclude_sections)) {
			return '';
		}

		return $section_link;

	}
	protected function process_content($content, $article_data){
		$content = preg_replace('/(<div class="entry-content entry clearfix">\s*<div[^<]*>\s*<\/div>\s*<div[^<]*>\s*<div[^<]*>.*<a[^<]*>.*<\/a><\/div>\s*<\/div>\s*<di[^<]*>\s*<div[^<]*>\s*<ins[^<]*><\/ins>\s*<s[^<]*>\s*\(ad.*<\/script>\s*<\/div>)/Uis', 'no content', $content);
		$content = preg_replace('/(<\/p><div class="stream-item stream-item-in-post stream-item-in-post-3"><div class="news-sig">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<h1><strong>شاهد أيضاً.*<div class="tbltmobartcl">)/Uis', '', $content);
		$content = preg_replace('/(تابعوا أبرز و أحدث أخبار ألمانيا أولاً بأول عبر صفحة :.*<\/div>)/Uis', '', $content);

		if (preg_match('/(<div class="entry-content[^<]*>)/Uis', $content, $matches)){
			if(strlen(trim(strip_tags($content)))==0)
				return 'no content';
		}
		return $content;
	}

	protected function process_article_link($link, $referer_link, $logic) {
		if ($link == 'https://www.aksalser.com/news') {return false;}
		if (preg_match('/^https:\/\/www\.aksalser\.com\/news(\/syria-news\/page\/[2-6]\/)?$/Uis', $link)) {	return false;}
		if (preg_match('/^https:\/\/www\.aksalser\.com\/news(\/economics\/page\/[2-6]\/)?$/Uis', $link)) {	return false;}
		if (preg_match('/^https:\/\/www\.aksalser\.com\/news(\/immigrants\/page\/[2-6]\/)?$/Uis', $link)) {	return false;}
		if (preg_match('/^https:\/\/www\.aksalser\.com\/news(\/accidents\/page\/[2-6]\/)?$/Uis', $link)) {	return false;}
		if (preg_match('/^https:\/\/www\.aksalser\.com\/news(\/politics\/page\/[2-6]\/)?$/Uis', $link)) {	return false;}
		if (preg_match('/^https:\/\/www\.aksalser\.com\/news(\/around-the-world\/page\/[2-6]\/)?$/Uis', $link)) {	return false;}
		if (preg_match('/^https:\/\/www\.aksalser\.com\/news(\/flash\/page\/[2-6]\/)?$/Uis', $link)) {	return false;}
		if (preg_match('/^https:\/\/www\.aksalser\.com\/news(\/breaking-news\/page\/[2-6]\/)?$/Uis', $link)) {	return false;}
		if (preg_match('/^https:\/\/www\.aksalser\.com\/news(\/latest-news\/page\/[2-6]\/)?$/Uis', $link)) {	return false;}
		if (preg_match('/^https:\/\/www\.aksalser\.com\/news(\/new-media\/page\/[2-6]\/)?$/Uis', $link)) {	return false;}
		if (preg_match('/^https:\/\/www\.aksalser\.com\/news(\/top-news\/page\/[2-6]\/)?$/Uis', $link)) {	return false;}
		if (preg_match('/^https:\/\/www\.aksalser\.com\/news(\/press-views\/page\/[2-6]\/)?$/Uis', $link)) {	return false;}
		if (preg_match('/^https:\/\/www\.aksalser\.com\/news(\/photo-gallery\/page\/[2-6]\/)?$/Uis', $link)) {	return false;}
		if (preg_match('/^https:\/\/www\.aksalser\.com\/news(\/latest-news\/page\/[2-6]\/)?$/Uis', $link)) {	return false;}
		return $link;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2018-05-21T
		if (preg_match('/(.*)T/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . ' 18:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;

	}



}
