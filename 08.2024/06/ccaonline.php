<?php

class ttgmena extends plugin_base
{
	// ANT settings
	protected $ant_precision = 2;
	protected $stop_on_date = false;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/64.0.3282.167 Chrome/64.0.3282.167 Safari/537.36';

	// CRAWL settings
	protected $stop_on_article_found = true;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';


	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list_press_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => [
					'/<ul class="b2_gap ">(.*)<div class="b2-pagenav post-nav box b2-radius/Uis',
					'/<li class="post-list-item.*<a.*href="(.*)"/Uis',
				],
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="entry-content">(.*)<div class="content-footer/Uis',
			'author' => false,
			'article_date' => '/updated_time" content="(.*)"/Uis'
		)
	);

	protected $date;
	protected $page_count = 1;
	protected function process_list_press_link($link, $referer_link, $logic)
	{
		if ($this->page_count) { // http://www.ccaonline.cn/page/2?s&amp;type
			$this->page_count++;
			return 'http://www.ccaonline.cn/page/' . $this->page_count . '?s&amp;type';
		} else {
			return false;
		}
	}

	protected function process_date($article_date) {

		if (preg_match('/(.*)T(.*)\+/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;

	}
}
