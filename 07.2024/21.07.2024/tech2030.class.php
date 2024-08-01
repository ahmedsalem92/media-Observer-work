<?php

class ttgmena extends plugin_base
{

	// ANT settings
	protected $ant_precision = 2;
	protected $stop_on_date = false;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	private $exclude_sections = array();

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<div class="pages-nav"><a data-url="(.*)"/Uis',
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
			'content' => '/<div class="entry-content entry clearfix">(.*)(?:للمزيد من المعلومات|<span class="s20">|<div id="post-extra-info">)/Uis',
			'author' => false,
			'article_date' => '/published_time" content="(.*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic)
	{

		$link =  str_replace('&', '&#038;', $link);

		return $link;
	}

	protected function process_article_link($link, $referer_link, $logic)
	{
		if (isset($logic['list1']) && is_array($logic['list1'])) {
			foreach ($logic['list1'] as $item) {
				if (isset($item['type']) && $item['type'] === 'list1') {
					$logic['article']['process_link'] = $item['process_link'];
					break;
				}
			}
		}

		$link = str_replace('&', '&#038;', $link);

		return $link;
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
