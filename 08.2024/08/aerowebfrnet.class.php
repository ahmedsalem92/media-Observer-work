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
				'regexp' => '/<a href="([^"]+)">[^<]*<\/a>(?!.*<a href)/Uis',
				'append_domain' => false
			),
			1 => array(
				'type' => 'article',
				'regexp' => [
					'/<div class="yui-u first">(.*)<div class="paginate">/Uis',
					'/<div class="content-item separator">.*<a href="(.*)"/Uis',
				],
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<p class="subtitle">.*<\/p>(.*)<div class="space separator">/Uis',
			'author' => false,
			'article_date' => '/<p class="subtitle">(.*)<\/p>/Uis'
		)
	);


	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/<figure.*>(.*)<\/figure>/Uis', '', $content);
		$content = preg_replace('/<div id="freeMembershipOverlay"(.*)<\/script>/Uis', '', $content);
		$content = preg_replace('/<section>(.*)<\/section>/Uis', '', $content);

		return $content;
	}



	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{

		//2024-07-30 09:24:22
		if (preg_match('/(.*?)(?:\s*\+.*)?$/', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				trim($matches[1]),
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;
	}
}
