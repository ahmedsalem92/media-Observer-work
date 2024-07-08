<?php

class dubaistandardcom extends plugin_base {

	// ANT settings
	protected $stop_date_override = true;
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0';

	// CRAWL settings

	protected $stop_on_article_found = false;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp'=> '/<span class="current">.*<a href="([^<]*)"/Uis',
				'append_domain' => false
			)
		),
		'list2'=>array(
			0=>array(
				'type' => 'article',
				'regexp'=> '/<div class="td-module-thumb"><a href="(.*)"/Uis',
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div id="tdi_103" class="tdc-row stretch_row_1200 td-stretch-content">(.*)<div id="tdi_124" class="tdc-row stretch_row_1400 td-stretch-content">/Uis',
			'author' => false,
			'article_date' => '/<meta property="article:published_time" content="([^"]*)"/Uis',
		)
	);
	protected function process_content($content, $article_data) {

		$content = preg_replace('/(>Also.*\/p>\s*<p[^>]*>\s*<a.*\/a>)/Uis', '>', $content);
		$content = preg_replace('/(>Previous interviews.*\/p>\s*<p[^>]*>\s*<a.*\/a>)/Uis', '>', $content);
		$content = preg_replace('/(>Excerpts in translation.*\/p>\s*<p[^>]*>\s*<a.*\/a>)/Uis', '>', $content);

		return $content;
	}
	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {
		//2018-05-21T08:20:26+00:00
		if (preg_match('/(.*)T(.*)(?:\+|Z|\.)/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' '.$matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;
	}

}
