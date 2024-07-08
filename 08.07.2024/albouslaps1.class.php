<?php

class albouslaps extends plugin_base {
	// ANT settings
	protected $ant_precision = 2;
	protected $use_proxies = true;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = false;
	protected $stop_on_date = false;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' =>	false,
				'process_link' => 'process_list1_link'
			),
			1 => array(
				'type' => 'article',
				'regexp'=> '/<a class="d-flex flex-column flex-grow post-link" href="([^<]*)"/Uis',
				'append_domain' =>false
			)
		),
		'article' => array(
			'headline' => '/<h1 class="page-title entry-title">(.*)<\/h1>/Uis',
			'content' => '/<div class="entry-content-inner">(.*)<div class="share-buttons share-buttons-bottom">/Uis',
			'author' => false,
			'article_date' => '/<time class="post-date post-date-published published" datetime="([^<]*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic) {

		$temp_link =''; // https://www.albousla.ps/sitemap_1.xml
		if(preg_match_all('/<loc>(https:\/\/www\.albousla\.ps\/sitemap_\d+\.xml)<\/loc>/Uis', $link, $matches)){
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>' , '' , $temp_link);
			$temp_link = str_replace('</loc>' , '' , $temp_link);
		}

		return $temp_link;
	}

	protected function process_content($content, $article_data){
		$content = preg_replace('/(<a href=".*<\/a>)/Uis', '', $content);
		$content = preg_replace('/(<p class="follow-google"><a.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<a class="share_icon">.*<\/a>)/Uis', '', $content);
		$content = preg_replace('/(<span class="fa f.*\/span>)/Uis', '', $content);
		$content = preg_replace('/<span>(.*)<\/span>/Uis', '', $content);
		$content = preg_replace('/(تويتر)/Uis', '', $content);
		$content = preg_replace('/(نعرض لكم زوارنا أهم وأحدث الأخبار فى المقال الاتي:)/Uis', '', $content);
		$content = preg_replace('/(انقر هنا لقراءة الخبر من مصدره\.)/Uis', '', $content);
		$content = preg_replace('/(انقر <a.*<\/a>)/Uis', '', $content);
		return $content;
	}

	// private $page_no = 2;
	// protected function process_list1_link($link, $referer_link, $logic) {
	// 	return $this->settings['site_section_link'] .'/?page=' . $this->page_no++;
	// }

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {
	//2020-06-14T06:04:48+02:00
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
