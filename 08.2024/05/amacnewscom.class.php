<?php

class amacnewscom extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $stop_on_date = true;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';

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
				'ignore_terminal_stop' => true
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<li><a href=\'(.*)\'/Uis',
				'append_domain' => false,
				'ignore_terminal_stop' => true
			),
			2 => array(
				'type' => 'article',
				'regexp' => '/<div class=\'en-title2 vizhe_title matchsize \'><a href=\'(.*)\'/Uis',
				'append_domain' => false,
				'ignore_terminal_stop' => true
			),
			4 => array(
				'type' => 'article',
				'regexp' => '/<div class=\'option_last_news  \'><a href=\'(.*)\'/Uis',
				'append_domain' => false,
				'ignore_terminal_stop' => true
			),
			5 => array(
				'type' => 'article',
				'regexp' => '/<div class=\'third-title \'><a href=\'(.*)\'/Uis',
				'append_domain' => false,
				'ignore_terminal_stop' => true
			),
			6 => array(
				'type' => 'article',
				'regexp' => '/<h3 class=\'third-title \'><a href=\'(.*)\'/Uis',
				'append_domain' => false,
				'ignore_terminal_stop' => true
			),
			7 => array(
				'type' => 'article',
				'regexp' => '/<div class=\'en-title2 vizhe_title matchsize \'><a href=\'(.*)\'/Uis',
				'append_domain' => false,
				'ignore_terminal_stop' => true
			),
			8 => array(
				'type' => 'article',
				'regexp' => '/<div class=\'en-title2 model2_title\'><a href=\'(.*)\'/Uis',
				'append_domain' => false,
				'ignore_terminal_stop' => true
			)
		),
		'article' => array(
			'headline' => '/<title>(.*)(?:<\/title>)/Uis',
			'content' => '/(?:<div class=\'clearfix\'><\/div>|<article.*>)(.*)(?:<div class=\'clearfix\'>|<\/article>)/Uis',
			'author' => false,
			'article_date' => '/<span>Publish date<\/span>(.*)<\/div>/Uis'
		)
	);

	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/(<div class="container signup.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="form-button.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="newsletter.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="newsletter-subscribe">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<iframe.*<\/iframe>)/Uis', 'VIDEO', $content);
		$content = preg_replace('/<a href=\'\/tag\/Fertiglobe\'>(.*)<\/a>/Uis', '', $content);
		$content = preg_replace('/<div id=\'feedback_form_parent1\'.*>(.*<\/form>)/Uis', '', $content);
		if (empty($content)) {
			$content = 'no content';
		}
		return $content;
	}

	public function prepare_home($section_id)
	{

		$this->logic = $this->logic_home;
	}




	protected function filter_sections($section_link, $section_name, $referer_link, $logic)
	{

		// exclude these sections
		if (in_array(trim($section_name), $this->exclude_sections)) {
			return '';
		}

		return $section_link;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{
		if (empty($article_date)) {
			return date('Y-m-d H:i:s');
		} else {
			// Define the input date format
			$input_format = 'l d F Y - H:i';

			// Create a DateTime object from the input date string
			$article_date_obj = DateTime::createFromFormat($input_format, $article_date, new DateTimeZone($this->site_timezone));

			// Check for parsing errors
			if ($article_date_obj) {
				$article_date = $article_date_obj->format('Y-m-d H:i:s');
			} else {
				// Handle the error if the date string could not be parsed
				$errors = DateTime::getLastErrors();
				error_log(print_r($errors, true)); // Log the errors for debugging
				$article_date = 'Invalid date';
			}
		}

		return $article_date;
	}
}
