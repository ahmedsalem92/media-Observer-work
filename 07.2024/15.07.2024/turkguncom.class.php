<?php

class turkguncom extends plugin_base
{
	// ANT settings
	protected $ant_precision = 2;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;


	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link',
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<h2 class="post3-head-description content[^<]*>(.*)<div class="d-flex align-items-center justify-content-lg-center[^<]*>/Uis',
			'author' => false,
			'article_date' => '/^.*"datePublished":"(.*)"/Uis'
		)
	);


	protected function process_list1_link($link, $referer_link, $logic)
	{

		$temp_link = ''; //https://www.turkgun.com/sitemap/news-2024-07.xml
		if (preg_match('/<loc>(https:\/\/www\.turkgun\.com\/sitemap\/news-\d+?-\d+?\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[0];
			$temp_link = str_replace('<loc>', '', $temp_link);
			$temp_link = str_replace('</loc>', '', $temp_link);
		}

		return $temp_link;
	}

	protected function process_article_link($link, $referer_link, $logic)
	{
		if ($link === 'https://www.turkgun.com/resmi-ilanlar/tc-istanbul-anadolu-49-asliye-ceza-mahkemesinden/244114') {
			return false;
		} elseif ($link === 'https://www.turkgun.com/resmi-ilanlar/erdemli-belediye-baskanligi-15-temmuz-ilani/244230') {
			return false;
		}
		if (strpos($link, 'copte-goruntulendi-') || strpos($link, 'baskanligindan-ihale-ilani')) {
			return false;
		}
		return $link;
	}


	protected function process_content($content, $article_data)
	{
		$content = preg_replace('/<span class="me-3"><i class="fa fa-calendar me-1">(.*)<\/span>/Uis', '', $content);
		$content = preg_replace('/<span class="post-flash__h4">(.*)<\/span>/Uis', '', $content);
		$content = preg_replace('/(<i class="fab fa.*<\/a>)/Uis', '', $content);
		$content = preg_replace('/<i class="fas fa-link text-dark me-2">(.*)<\/a>/Uis', '', $content);

		$content = preg_replace('/(A<sup>\+<\/sup>)/Uis', '', $content);
		$content = preg_replace('/<span class="mini-title d-block text-uppersize">(.*)<\/span>/Uis', '', $content);
		$content = preg_replace('/<span class="me-3"><i class="fa fa-calendar me-1">(.*)<\/span>/Uis', '', $content);
		$content = preg_replace('/<i class="fa fa-clock me-1">(.*)<\/span>/Uis', '', $content);
		$content = preg_replace('/(A<sup>-<\/sup>)/Uis', '', $content);
		$content = preg_replace('/<span class="me-3"><i class="fa fa-calendar me-1">(.*)<\/span>/Uis', '', $content);
		$content = preg_replace('/<span class="me-3"><i class="fa fa-edit me-1">(.*)<\/span>/Uis', '', $content);
		$content = preg_replace('/<span class="me-3"><i class="fa fa-share-alt me-1">(.*)<\/span>/Uis', '', $content);
		$content = preg_replace('/<div class="fw-normal text-dark fs-12 text-nowrap">.*<\/div>/Uis', '', $content);
		$content = preg_replace('/<div class="reading-time fw-normal text-dark fs-12 text-nowrap family-regular me-2 text-center">.*<\/div>\s*<\/div>/Uis', '', $content);
		$content = preg_replace('/<div class="source-name fw-semibold fs-11 text-darkestgray family-regular d-flex me-2">.*<\/div>/Uis', '', $content);
		$content = preg_replace('/<button data-operation="increase" class="news-operation-button border border-secondary-subtle fs-20" title=.*<\/button>/Uis', '', $content);
		$content = preg_replace('/<button data-operation="decrease" class="news-operation-button border border-secondary-subtle fs-20".*<\/button>/Uis', '', $content);
		$content = preg_replace('/<div class="fs-14 bg-lavender fw-semibold py-2[^<]*>\s*<a/Uis', '', $content);
		$content = preg_replace('/ <div class="fs-14 bg-lavender fw-semibold py-2[^<]*>\s*<a/Uis', '', $content);
		$content = preg_replace('/<div class="d-flex flex-lg-row flex-md-row flex-wrap mb-3 border-bottom border-ghost pb-3 detail-page-px-7">.*<div class="col-12 position-relative detail-sort-item " >/Uis', '', $content);
		//$this->log('$content=> '.htmlentities($content));


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
}
