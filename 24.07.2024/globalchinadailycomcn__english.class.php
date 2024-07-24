<?php

class globalchinadailycomcn__english extends plugin_base
{

	// ANT settings
	protected $ant_precision = 2;
	protected $stop_on_date = false;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	private $exclude_sections = array(
		'HOME', 'GLOBAL VIEWS', 'SERVICE', 'NEWSPAPER', 'China Daily PDF', 'China Daily E-paper', 'China Daily Global PDF', 'China Daily Global E-paper', 'Subscribe'
	);
	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'section',
				'regexp' => array(
					'/^.*<ul class="dropdown">(.*)<div id="fsD1" class="focus">/Uis',
					'/(<a.*<\/a>)/Uis'
				),
			),

		),
		'section' => array(
			'link' => '/href="([^"]*)"/Uis',
			'name' => '/<a[^<]*>(.*)<\/a>/Uis',
			'append_domain' => false,
			'process_link' => 'filter_sections'
		)
	);

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<h3><a.*href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<\/a>\s*<span><a target="_blank".*href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			),
			2 => array(
				'type' => 'article',
				'regexp' => '/<div class="tBox">\s*<a.*href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			),
			3 => array(
				'type' => 'article',
				'regexp' => '/<div class="tBox" style="border-bottom:20px;">.*href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			),
			4 => array(
				'type' => 'article',
				'regexp' => '/<span class="tw2_l_t">.*href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			),
			5 => array(
				'type' => 'article',
				'regexp' => '/<div class="tBox2" style="margin-right:0px;">.*href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			),
			6 => array(
				'type' => 'article',
				'regexp' => '/<span class="tw3_01_t">.*href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			),
			7 => array(
				'type' => 'article',
				'regexp' => '/<div class="tBox3">.*href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			),
			8 => array(
				'type' => 'article',
				'regexp' => '/<div class="tBox3" style="margin-right:0px;">.*href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			),
			9 => array(
				'type' => 'article',
				'regexp' => '/<span class="tw3_l_t">.*href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/(?:<div id="Content">|<\/h1>)(.*)<div class="selectpage">/Uis',
			'author' => false,
			'article_date' => '/<meta name="publishdate" content="(.*)"/Uis'
		)
	);
	protected $logic_next = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<a class="pagestyle" href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<a class="a_img" shape="rect"\s*href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			),
			2 => array(
				'type' => 'article',
				'regexp' => '/<div class="twBox_t1">.*href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/(?:<div id="Content">|<\/h1>)(.*)<div class="selectpage">/Uis',
			'author' => false,
			'article_date' => '/<meta name="publishdate" content="(.*)"/Uis'
		)
	);
	protected $logic_sport = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<a class="pagestyle" href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<a class="a_img" shape="rect"\s*href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			),
			2 => array(
				'type' => 'article',
				'regexp' => '/<div class="twBox_t1">.*href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/(?:<div id="Content">|<\/h1>)(.*)<div class="selectpage">/Uis',
			'author' => false,
			'article_date' => '/<meta name="publishdate" content="(.*)"/Uis'
		)
	);
	protected $logic_opinion = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<a target="_blank" shape="rect" href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			),

		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/(?:<div id="Content">|<\/h1>)(.*)<div class="selectpage">/Uis',
			'author' => false,
			'article_date' => '/<meta name="publishdate" content="(.*)"/Uis'
		)
	);
	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<div class="fcon".*href="(.*)"/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<li><a target="_blank" shape="r.*href="(.*)"/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			),
			2 => array(
				'type' => 'article',
				'regexp' => '/<div class="txt txt-small">.*href="(.*)"/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			),
			3 => array(
				'type' => 'article',
				'regexp' => '/<div class="jk">.*href="(.*)"/Uis',
				'append_domain' => true,
				'ignore_terminal_stop' => true,
				'process_link' => 'process_article_link'
			),

		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/(?:<div id="Content">|<\/h1>)(.*)<div class="selectpage">/Uis',
			'author' => false,
			'article_date' => '/<meta name="publishdate" content="(.*)"/Uis'
		)
	);


	protected function process_article_link($link, $referer_link, $logic)
	{
		if ($link === 'https://global.chinadaily.com.cn/a/202407/22/WS669df6fca31095c51c50f3e9') {
			return false;
		}

		$link =  str_replace('http://global.chinadaily.com.cn//global.chinadaily.com.cn/', 'https://global.chinadaily.com.cn/', $link);
		$link =  str_replace('http://global.chinadaily.com.cn//www.chinadaily.com.cn/', 'https://global.chinadaily.com.cn/', $link);

		if (strpos($link, 'c043c4be5') || strpos($link, 'c043c4868') || strpos($link, '043c41d1') || strpos($link, '043c3d27') || strpos($link, '43c540c') || strpos($link, '43bfcc1')) {
			return false;
		}

		return $link;
	}
	protected function process_content($content, $article_data)
	{
		$content = preg_replace('/(<p class="data">.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<h2><\/h2>.*<div id="Content">)/Uis', '', $content);
		$content = preg_replace('/(figure class="image" style="display: table;">\s*<img\s*src[^<]*>\s*<fi[^<]*><\/f[^<]*>\s*<\/figure>\s*<p>.*<\/p>\s*<\/div>)/Uis', 'photo', $content);
		$content = preg_replace('/(<div id="div_currpage">.*<\/div>)/Uis', '', $content);
		return $content;
	}

	public function prepare_next($section_id)
	{

		$this->logic = $this->logic_next;
	}
	public function prepare_opinion($section_id)
	{

		$this->logic = $this->logic_opinion;
	}

	public function prepare_sport($section_id)
	{

		$this->logic = $this->logic_sport;
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


		//$section_link = str_replace('http', 'https', $section_link);

		return $section_link;
	}




	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{

		//2018-05-21T08:20:26+00:00
		if (preg_match('/(\d+?)-(\d+?)-(\d+?)/Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . '-' . $matches[2] . '-' . $matches[3] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;
	}
}
