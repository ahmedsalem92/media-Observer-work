<?php

class ttgmena extends plugin_base
{
	// ANT settings
	protected $ant_precision = 2;
	protected $stop_on_date = true;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/64.0.3282.167 Chrome/64.0.3282.167 Safari/537.36';
	protected $use_headless = true;
	protected $use_proxies = true;
	// CRAWL settings
	protected $stop_on_article_found = true;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';


	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<a class="page_nav next".*href="(.*)"/Uis',
				'append_domain' => false
			),
			1 => array(
				'type' => 'article',
				'regexp' => [
					'/<div class="jeg_posts jeg_load_more_flag">(.*)<div class="jeg_navigation jeg_pagination/Uis',
					'/<article class="jeg_post jeg_pl_md_2 format-standard">.*href="(.*)"/Uis',
				],
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<\/h1>(.*)<div class="jeg_ad jeg_article jnews_content_bottom_ads ">/Uis',
			'author' => false,
			'article_date' => '/dateModified":"(.*)"/Uis'
		)
	);


	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/<div class="jeg_post_meta jeg_post_meta_1">(.*)<div class="entry-content no-share">/Uis', '', $content);
		$content = preg_replace('/(<div class="ads-text">ADVERTISEMENT<\/div>)/Uis', '', $content);
		$content = preg_replace('/<p><b>(READ MORE:.*<\/span>)<\/b><\/p>/Uis', '', $content);
		return $content;
	}


	protected function process_date($article_date)
	{
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

	public function pre_get_page(&$page) {   
   
		$this->ant->set_wait_for_load(true);

	} 
}
