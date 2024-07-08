<?php

class timeturkcom extends plugin_base {
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
				'type' => 'list1',
				'regexp'=> '/<div class="pagination-card">\s*<a href="([^"]*)" title=/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			),
			1 => array(
				'type' => 'article',
				'regexp'=> '/<div class="card">\s*<!--.*?-->\s*<a href="(.*)">/Uig',
				'append_domain' => false
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
		if ($link == 'https://www.timeturk.com/') return false;
		return $link;
	}

	protected function process_content($content, $article_data) {

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
	protected function process_date($article_date) {

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
