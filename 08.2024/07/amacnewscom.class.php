<?php

class amacnewscom extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $stop_on_date = false;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';
	protected $use_headless = true;
	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	private $exclude_sections = array(
		'Home', 'media', 'Video', 'Photo'
	);

	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array(
					'/<div class="navbar-collapse collapse">(.*)a href=\'\/en\/media\'/Uis',
					'/(<a.*<\/a>)/Uis'
				),
				'append_domain' => true
			)
		),
		'section' => array(
			'link' => '/a href=\'(.*)\'/Uis',
			'name' => '/a.*>(.*)<\/a>/Uis',
			'append_domain' => true,
			'process_link' => 'filter_sections'
		)
	);

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<td style=\'padding-left:5px;\'><a href=\'(.*)\'/Uis',
				'append_domain' => true,
				'process_link' => 'process_list1_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => [
					'/<div id=\'bargozidehcont\'.*>(.*)<div class=\'clearfix\'>/Uis',
					'/<div class=\'special-img2 \'><a href=\'(.*)\'/Uis',
				],
				'append_domain' => true,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<article.*>(.*)<\/article>/Uis',
			'author' => false,
			'article_date' => '/<span>Publish date<\/span>(.*)<\/div>/Uis'
		)
	);

	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<div class=\'sidebar_title\'><a href=\'(.*)\'/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<li><a href=\'(.*)\'/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			),
			2 => array(
				'type' => 'article',
				'regexp' => '/<div class=\'en-title2 vizhe_title matchsize \'><a href=\'(.*)\'/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			),
			4 => array(
				'type' => 'article',
				'regexp' => '/<div class=\'option_last_news  \'><a href=\'(.*)\'/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			),
			5 => array(
				'type' => 'article',
				'regexp' => '/<div class=\'third-title \'><a href=\'(.*)\'/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			),
			6 => array(
				'type' => 'article',
				'regexp' => '/<h3 class=\'third-title \'><a href=\'(.*)\'/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			),
			7 => array(
				'type' => 'article',
				'regexp' => '/<div class=\'en-title2 vizhe_title matchsize \'><a href=\'(.*)\'/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			),
			8 => array(
				'type' => 'article',
				'regexp' => '/<div class=\'en-title2 model2_title\'><a href=\'(.*)\'/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<title>(.*)(?:<\/title>)/Uis',
			'content' => '/<article.*>(.*)<span class=\'share_title\'>/Uis',
			'author' => false,
			'article_date' => '/<span>Publish date<\/span>(.*)<\/div>/Uis'
		)
	);

	protected function filter_sections($section_link, $section_name, $referer_link, $logic)
	{

		if (in_array(trim($section_name), $this->exclude_sections)) {
			return '';
		}

		return $section_link;
	}

	public function prepare_home($section_id)
	{

		$this->logic = $this->logic_home;
	}

	private $exclude_articles = array(
		'https://www.amacnews.com/en/news/1103/research-and-development-center-dewa-published-their-transaction-report',
		'https://www.amacnews.com/en/news/1025/the-wpf-advisory-council-published-its-report',
		'https://www.amacnews.com/en/sound/80/uae-national-anthem'
	);
	protected function process_article_link($link, $referer_link, $logic) {
		if (in_array(rtrim($link), $this->exclude_articles)){
			return false;
		}
		return $link;
	}


	protected function process_content($content, $article_data)
	{
		$content = preg_replace('/<div class=\'clearfix\'>(.*)<\/a>/Uis', '', $content);
		$content = preg_replace('/(<div class="container signup.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="form-button.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="newsletter.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="newsletter-subscribe">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<iframe.*<\/iframe>)/Uis', '', $content);
		$content = preg_replace('/<span id=\'shortlink2\'>(.*)<\/span>/Uis', '', $content);
		$content = preg_replace('/<figcaption.*>(.*)<\/figcaption>/Uis', '', $content);
		$content = str_replace('photo: Social media', '', $content);
		if (empty($content)) {
			$content = 'no content';
		}
		return $content;
	}

	protected function process_date($article_date)
	{
		if (empty($article_date)) {
			return date('Y-m-d H:i:s');
		} else {
			$input_format = 'l d F Y - H:i';
			$article_date_obj = DateTime::createFromFormat($input_format, $article_date, new DateTimeZone($this->site_timezone));
			if ($article_date_obj) {
				$article_date = $article_date_obj->format('Y-m-d H:i:s');
			} else {
				$errors = DateTime::getLastErrors();
				error_log(print_r($errors, true)); // Log the errors for debugging
				$article_date = 'Invalid date';
			}
		}

		return $article_date;
	}
}
