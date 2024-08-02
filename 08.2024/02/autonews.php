<?php

class ttgmena extends plugin_base
{
	// ANT settings
	protected $ant_precision = 6;
	protected $stop_on_date = false;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/64.0.3282.167 Chrome/64.0.3282.167 Safari/537.36';
	protected $use_proxies = true;
	protected $use_headless = true;

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
				'append_domain' =>	false,
				'process_link' => 'process_list1_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => [
					'/<div class="all-articles-grid">(.*)<div class="research-results-cards-paginations">/Uis',
					'/<a href="(.*)"/Uis',
				],
				'append_domain' => true,
			)
		),
		'article' => array(
			'headline' => '/<h1 class="single-article-title"><span>(.*)<\/span><\/h1>/Uis',
			'content' => '/<div class="article-single-desc">(.*)<div class="article-single-desc-lower-img">/Uis',
			'author' => false,
			'article_date' => '/<div class="single-article-meta">(*)<\/div>/Uis'
		)
	);

	protected $page = 2; // https://www.autonews.ma/news?model=&brand=&page=1
	function process_list1_link($link, $referer_link, $logic)
	{
		$next_page = "https://www.autonews.ma/news?model=&brand=&page" . $this->page++;
		return $next_page;
	}


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
		//  Publié le : 01 Juillet 2024 par Autonews
		if (preg_match('/.*(\d{2}) (w+?) (\d{4})/', $article_date, $matches)) {
			$day = $matches[1];
			$month = $matches[2];
			$year = $matches[3];
	
			// Convert month name to month number
			$months = [
				'Janvier' => '01',
				'Février' => '02',
				'Mars' => '03',
				'Avril' => '04',
				'Mai' => '05',
				'Juin' => '06',
				'Juillet' => '07',
				'Août' => '08',
				'Septembre' => '09',
				'Octobre' => '10',
				'Novembre' => '11',
				'Décembre' => '12'
			];
	
			if (array_key_exists($month, $months)) {
				$month_number = $months[$month];
				$formatted_date = sprintf('%s-%s-%s 00:00:00', $year, $month_number, $day); // Assuming no time is given
				return $formatted_date; // Return date in YYYY-mm-dd HH:ii:ss format
			}
		}
		
		return null; // Return null if the format is incorrect
	}
	public function pre_get_page(&$page)
	{

		$this->ant->set_wait_for_load(true);
	}
}
