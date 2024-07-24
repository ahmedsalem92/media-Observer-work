<?php

class tech2030net extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = false;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => true,
				'process_link' => 'process_list1_link',
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<a class="post-title the-subtitle" href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link',
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="entry-content entry clearfix">(.*)(?:<div class="share-title">|<div class="clearfix"><\/div>|<div id="post-extra-info">|<figure class="wp-block-image|للمزيد من المعلومات|<span class="s20">|<div id="post-extra-info">)/Uis',
			'author' => false,
			'article_date' => '/published_time" content="(.*)"/Uis'
		)
	);
	

	protected $next_page = 2; 
	protected function process_list1_link($link, $referer_link, $logic)
	{
		return 'https://tech2030.net/?s=&paged=' . $this->next_page++;
	} 

	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/(<div class="container signup.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="form-button.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="newsletter.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="newsletter-subscribe">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<iframe.*<\/iframe>)/Uis', 'VIDEO', $content);
		return $content;
	}


	protected function process_headline($headline, $article_data)
	{

		$headline = preg_replace('/(<span class="post-title" itemprop="headline"><\/span>)/Uis', 'No Headline', $headline);
		return $headline;
	}
	
	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
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
