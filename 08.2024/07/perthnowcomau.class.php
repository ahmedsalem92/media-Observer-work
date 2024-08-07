<?php

class thegulfheraldcom extends plugin_base
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
			'content' => '/<div class="css-czxz1x-StyledArticleContent ell6x8x3">(.*)<div class="css-i2ml8v-StyledSharing e1sjvqe43">/Uis',
			'author' => false,
			'article_date' => '/dateModified":"(.*)"/Uis'
		)
	);


	protected function process_list1_link($link, $referer_link, $logic)
	{

		$temp_link = ''; // https://www.perthnow.com.au/sitemap/2024/32/0/sitemap.xml
		if (preg_match_all('/<loc>(https:\/\/www\.perthnow\.com\.au\/sitemap\/.*sitemap\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>', '', $temp_link);
			$temp_link = str_replace('</loc>', '', $temp_link);
		}

		return $temp_link;
	}

	private $exclude_articles = array(
		'https://www.nbr.co.nz/economics/rbnz-unlikely-to-panic-and-slash-ocr-this-month-rabobank/',
		'https://www.nbr.co.nz/back-in-business/back-in-business-1-the-du-val-story/',
		'https://www.nbr.co.nz/edwards-on-politics/political-tactics-distract-from-substance-of-policies/'
	);

	private $links = array();
	private $array_index = 0;

	protected function process_article_link($link, $referer_link, $logic)
	{
		if (in_array(rtrim($link), $this->exclude_articles)){
			return false;
		}
		return $link;

		$temp_link = '';
		if (empty($this->links)) {
			$result = $this->ant->get($referer_link);
			if (preg_match_all('/<loc>(.*)<\/loc>/Uis', $result, $matches)) {
				$this->links = $matches[0];
				$this->array_index = sizeof($this->links);
			}
		}
		$this->array_index--;
		if ($this->array_index > 0 and isset($this->links[$this->array_index])) {
			$temp_link = str_replace('<loc>', '', $this->links[$this->array_index]);
			$temp_link = str_replace('</loc>', '', $temp_link);
			return $temp_link;
		}

		return '';
	}

	protected function process_date($article_date) {
		// 2024-08-06T10:49:27.589Z
		if (preg_match('/(.*)T(.*)Z/Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d\TH:i:s.u',
				$matches[1] . 'T' . $matches[2]
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
	
		return $article_date;
	}

}
