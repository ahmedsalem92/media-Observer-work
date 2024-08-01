<?php

class shaabjocom extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $stop_on_date = true;



	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	private $exclude_sections = array(
		'الرئيسية' , 'تلفزيون صدى الشعب'
	);
	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array (
					'/^.*<div class="jeg_mainmenu_wrap">(.*)<\/ul>/Uis',
					'/(<a.*<\/a>)/Uis'
				),
				'append_domain' => false
			),

		),
		'section' => array(
			'link' => '/href="([^"]*)"/Uis',
			'name' => '/<a href=".*">(.*)<\/a>/Uis',
			'append_domain' => false,
			'process_link' => 'filter_sections'
		)
	);

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<a class="page_nav next" .*href="(.*)"/Uis',
				'append_domain' => false
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<div class="jeg_post_excerpt">.*<h3 class="jeg_post_title">.*href="(.*)"/Uis',
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/(?:<div class="jeg_ad jeg_ad_article[^<]*>|<div class="content-inner ">)(.*)<div class="jeg_share_bottom_container">/Uis',
			'author' => false,
			'article_date' => '/<meta property="article:published_time" content="(.*)"/Uis'
		)
	);

	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<h3 class="jeg_post_title">\s*<a href="(.*)"/Uis',
				'append_domain' => false,
				'ignore_terminal_stop' => true
			),

		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/(?:<div class="jeg_ad jeg_ad_article[^<]*>|<div class="content-inner ">)(.*)<div class="jeg_share_bottom_container">/Uis',
			'author' => false,
			'article_date' => '/<meta property="article:published_time" content="(.*)"/Uis'
		)
	);


	public function prepare_home($section_id) {

		$this->logic = $this->logic_home;

	}


	protected function process_content($content, $article_data){

		$content = preg_replace('/(<p><a.*><img[^<]*><noscript>)/Uis', 'NO Content', $content);
		$content = preg_replace('/(<figure class="wp-block-image size-large">.*<\/a>)/Uis', 'image', $content);
		return $content;
	}


	protected function filter_sections($section_link, $section_name, $referer_link, $logic) {

		// exclude these sections
		if (in_array(trim($section_name), $this->exclude_sections)) {
			return '';
		}

		return $section_link;

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
