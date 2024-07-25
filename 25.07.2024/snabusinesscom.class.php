<?php

class snabusinesscom extends plugin_base {

	// ANT settings
	protected $ant_precision = 6;
	protected $use_proxies = true;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/60.0.3112.113 Chrome/60.0.3112.113 Safari/537.36';


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
			'content' => '/<p class="Article_summary__.*">(.*)<ul class="SocialShare_social/Uis',
			'author' => false,
			'article_date' => '/<main.*<span class="TimeAgo_date[^>]*>(.*)<\//Uis'
		)
	);

	protected function process_content($content, $article_data) {

		$content = preg_replace('/(<ul class="SocialShare.*\/ul>)/Uis', '', $content);
		$content = preg_replace('/(<div class="StoryListItem.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="RelatedArticles_box.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="RelatedArticles_box__ZR4H6 ">.*)<p>/Uis', '', $content);
		if (empty($content)) {
			return 'video';
		} else {
			return $content;
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
