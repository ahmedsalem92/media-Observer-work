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
				'regexp' => '/<a class="next page-numbers" href="(.*)"/Uis',
				'append_domain' => false
			),
			1 => array(
				'type' => 'article',
				'regexp' => [
					'/<main id="main" class="site-main" role="main">(.*)<div id="secondary" class="widget-area" role="complementary">/Uis',
					'/<article.*href="(.*)"/Uis',
				],
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<h2 class="entry-title">(.*)<\/h2>/Uis',
			'content' => '/<div class="entry-content">(.*)<footer class="entry-footer">/Uis',
			'author' => false,
			'article_date' => '/dateModified":"(.*)"/Uis'
		)
	);

	protected function process_content($content, $article_data)
	{
		$content = preg_replace('/(-RBS-)/Uis', '', $content);
		$content = preg_replace('/(\(RNS\))/Uis', '', $content);
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
		return $article_date;
	}
}
