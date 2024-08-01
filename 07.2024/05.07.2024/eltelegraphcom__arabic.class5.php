<?php

class eltelegraphcom__arabic extends plugin_base {

	// ANT settings
	protected $ant_precision = 6;
	protected $use_proxies = true;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = false;

	protected $site_timezone = 'Asia/Amman';

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<a class="next page-numbers" href="(.*)">/Uis',
				'append_domain' => false
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<h5 class="post-title dt-mb-3 dt-mt-3"><a href="([^"]+)"/Uis',
				'append_domain' => false
			),
		),
		'article' => array(
			'headline' => '/<h1 class="title[^<]*>(.*)<\/h1>/Uis',
			'content' => '/(?:<div class="clearfix">|<div class="featured-image">\s*<img[^>]*>)(.*?)<div class="addtoany_share_save_container/Uis',
			'author' => '/<h4 class="name"><a[^>]*>([^<]*)<\/a><\/h4>/Uis',
			'article_date' => '/,"datePublished":\s*"([^"]*)"/Uis'
		)
	);


	protected function process_content($content, $article_data) {

		$content = preg_replace('/<div class="bdaia-post-featured-image">.*diV>/Uis', 'post featured image', $content);
		$content = preg_replace('/<div class="jnews_prev_next.*diV>/Uis', '', $content);
		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {
		//2018-05-21T08:20:26+00:00
		if (preg_match('/(\d{4}-\d{1,2}-\d{1,2})T(.*)(?:\+|Z|\.|\")/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' '.$matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		return $article_date;

	}

}
