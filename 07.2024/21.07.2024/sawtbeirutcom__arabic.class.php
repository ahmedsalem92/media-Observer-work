<?php

class sawtbeirutcom__arabic extends plugin_base
{

	// ANT settings
	protected $ant_precision = 6;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:123.0) Gecko/20100101 Firefox/123.0';

	// CRAWL settings
	protected $stop_on_article_found = true;
	protected $stop_date_override = true;
	protected $use_headless = true;

	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<a class="next page-numbers"\s*href="(.*)"/Uis',
				'append_domain' => false
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<\/div>\s*<\/div>\s*<a href="(.*)"/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/(<div class="single-description">.*)<div class="heateor_sss_sharing_contain[^<]*>/Uis',
			'author' => false,
			'article_date' => '/"datePublished":"(.*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic)
	{

		return $link;
	}

	protected function process_headline($headline, $article_data)
	{

		$headline = preg_replace('/This site asks for consent to use your data/Uis', '', $headline);
		$headline = preg_replace('/Manage your data/Uis', '', $headline);
		$headline = preg_replace('/Confirm our vendors/Uis', '', $headline);
		return $headline;
	}

	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/<form.*\/form>/Uis', '', $content);

		$content = preg_replace('/(>\s*&nbsp;\s*<)/Uis', '><', $content);
		$content = preg_replace('/(<ins.*<\/ins>)/Uis', '', $content);
		$content = preg_replace('/(<script.*<\/script>)/Uis', '', $content);
		$content = preg_replace('/(<style.*<\/style>)/Uis', '', $content);
		if (preg_match('/(<div class="single-description">)/Uis', $content, $matches)) {
			if (strlen(trim(strip_tags($content))) == 0)
				return 'no content';
		}

		return $content;
	}
	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{

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

	public function pre_get_page(&$page)
	{

		$this->ant->set_wait_for_load(true);
	}
}
