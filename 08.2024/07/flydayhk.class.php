<?php

class ttgmena extends plugin_base
{
	// ANT settings
	protected $ant_precision = 6;
	protected $stop_on_date = true;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/64.0.3282.167 Chrome/64.0.3282.167 Safari/537.36';
	protected $use_proxies = true; // Proxy 

	// CRAWL settings
	protected $stop_on_article_found = true;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';


	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<div class="older">.*<a href="(.*)"/Uis',
				'append_domain' => false
			),
			1 => array(
				'type' => 'article',
				'regexp' => [
					'/<div class="penci-wrap-masonry">(.*)<div class="penci-pagination">/Uis',
					'/<div class="thumbnail">.*<a href="(.*)"/Uis',
				],
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<h1 class="post-title single-post-title entry-title">(.*)<\/h1>/Uis',
			'content' => '/<div class="post-entry blockquote-style-2">(.*)<div class="penci-single-link-pages">/Uis',
			'author' => false,
			'article_date' => '/dateModified":"(.*)"/Uis'
		)
	);

	protected function process_content($content, $article_data)
	{
		$content = str_replace('資料由美通社提供', '', $content);
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
