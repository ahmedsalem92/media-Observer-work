<?php

class snabusinesscom extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $use_proxies = true;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(https:\/\/www\.snabusiness\.com\/(?:article|infographic|program)\/.*)<\/loc>/Uis',
				'append_domain' => false
			),
		),
		'article' => array(
			'headline' => '/(?:<h1[^<]*>|<h1 class="ContentHeader[^>]*>)(.*)<\/h/Uis',
			'content' => '/<p class="Article_summary__RYoD9">(.*)(?:<ul class="SocialShare_social__ajsua|<p class="inline-space-paragraph">|<div class="RelatedArticles_box__ZR4H6 ">)/Uis',
			'author' => false,
			'article_date' => '/<main.*<span class="TimeAgo_date[^>]*>(.*)<\//Uis'
		)
	);

	protected function process_content($content, $article_data) {

		$content = preg_replace('/(<ul class="SocialShare.*\/ul>)/Uis', '', $content);
		$content = preg_replace('/(<div class="StoryListItem.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="RelatedArticles_box.*<\/div>)/Uis', '', $content);
		$content = preg_replace('//Uis', '', $content);
		if (preg_match('/((?:<p class="Article_summary|<div class="Infographic_summary)[^>]*>)/Uis', $content, $matches)){
			if(strlen(trim(strip_tags($content)))==0)
				return 'no content';
		}

		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2020-11-19T21:39:00
		if (preg_match('/(\d{2}) (\W*) (\d{4})/Uis', $article_date, $matches)) {
			$month = $this->arabic_month_to_number($matches[2]);
			$date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3].'-'.$month .'-'. $matches[1] . ' 16:00:00' ,
				new DateTimeZone($this->site_timezone)
			);
			if ($date_obj instanceof DateTime) {
				$article_date = $date_obj->format('Y-m-d H:i:s');
			}
		}
		return $article_date;
	}

}
