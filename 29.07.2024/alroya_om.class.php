<?php

class alroya_om extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0';

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	private $exclude_sections = array(
		'أخبار', 'اقتصاد', 'رياضة', 'المقالات', 'ملاحق' , 'فيديو', 'مبادرات الرؤية'
	);
	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array(
					'/<nav class="main-menu[^>]*>(.*)<\/nav>/Uis',
					'/(<a.*a>)/Uis'
				)
			)
		),
		'section' => array(
			'link' => '/href="([^#]*)"/Uis',
			'name' => '/(?:<a[^>]*><span[^>]*>|<a[^>]*>)(.*)(?:<\/a>|<\/span>)/Uis',
			'append_domain' => false,
			'process_link' => 'filter_sections'
		)
	);
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<a class="page-link" href="([^"]*)" rel="next"/Uis',
				'append_domain' => false
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<h3 class="entry-title font-size-18 mb-0"><a href="([^"]*)"/Uis',
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' =>'/<h1 class="post-title font-size-28 font-weight-bold">(.*)<\/h1>/Uis',
			'content' => '/<div class="post-text-container">(.*)<div class="post-tags/Uis',
			'author' =>false,
			'article_date' => '/<meta property="article:published_time" content="([^"]*)"/Uis'
		)
	);
	protected $logic_no_next = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<div class="item">\s*<a href="([^"]*)"/Uis',
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' =>'/<h1 class="post-title font-size-28 font-weight-bold">(.*)<\/h1>/Uis',
			'content' => '/<div class="post-text-container">(.*)<div class="post-tags/Uis',
			'author' =>false,
			'article_date' => '/<meta property="article:published_time" content="([^"]*)"/Uis'
		)
	);
	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<h3 class="entry-title[^>]*>\s*<a[^>]*href="([^"]*)"/Uis',
				'append_domain' => false,
				'ignore_terminal_stop' => true
			)
		),
		'article' => array(
			'headline' =>'/<h1 class="post-title font-size-28 font-weight-bold">(.*)<\/h1>/Uis',
			'content' => '/<div class="post-text-container">(.*)<div class="post-tags/Uis',
			'author' =>false,
			'article_date' => '/<meta property="article:published_time" content="([^"]*)"/Uis'
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
		if (in_array(trim($section_name), $this->exclude_sections)) {
			return '';
		}

		return $section_link;

	}

	protected function process_content($content, $article_data){

		$content = preg_replace('/(<div class="youtube-subscribe">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="short-link mb20">.*<\/div>)/Uis', '', $content);
		$content = str_replace('مسقط - الرؤية', '', $content);
		$content = str_replace('مسقط - العمانية', '', $content);
		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {
		//2020-04-26T18:11:00+04:00

		if (preg_match('/(.*)T(.*)(?:\+|Z|\.)/Uis', $article_date, $matches)) {

			$date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);
			if ($date_obj instanceof DateTime) {
				$article_date = $date_obj->format('Y-m-d H:i:s');
			}
		}
		return $article_date;

	}

}
