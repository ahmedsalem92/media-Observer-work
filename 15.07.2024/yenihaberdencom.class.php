<?php

class yenihaberdencom extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = true;

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
				'regexp' => '/<url>(.*)<\/url>/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1 class="content-title[^>]*>(.*)<\/h1>/Uis',
			'content' => '/<\/h1>(.*)<div class="col-12 col-lg-4">/Uis',
			'author' => '/<meta itemprop="author" content="(.*)"/Uis',
			'article_date' => '/(?:<div class="content-date">\s*<time class="p1" datetime="|<div class="date-info">.*<time datetime="|"datePublished":\s*")([^"]*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic) {
		$temp_link ='';
		if(preg_match_all('/<loc>(https:\/\/www.yenihaberden.com\/sitemap-news-\d+\.xml)<\/loc>/Uis', $link, $matches)){
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('</loc>' , '' , $temp_link);
			$temp_link = str_replace('<loc>' , '' , $temp_link);
		}

		return $temp_link;
	}

	private $links = array();
	private $array_index = 0 ;

	protected function process_article_link($link, $referer_link, $logic) {

		$temp_link = '';
		if(empty($this->links)){
			$result = $this->ant->get($referer_link);
			if(preg_match_all('/<loc>(.*)<\/loc>/Uis', $result, $matches)){
				$this->links = $matches[0];
				$this->array_index = sizeof($this->links);
			}
		}
		$this->array_index--;
		if($this->array_index > 0 and isset($this->links[$this->array_index]) ){
			$temp_link = str_replace('<loc>' , '' , $this->links[$this->array_index]);
			$temp_link = str_replace('</loc>' , '' , $temp_link);
			return $temp_link;
		}

		return '';

	}

	protected function process_content($content, $article_data) {

		$content = preg_replace('/(<div class="author.*\/div>)/Uis', '', $content);
		$content = preg_replace('/(<p class="content-source">.*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<section class="news.*\/section>)/Uis', '', $content);
		$content = preg_replace('/<div><span>(.*)<\/span>/Uis', '', $content);
		$content = preg_replace('/(<time datetime=".*<\/time>)/Uis', '', $content);
		$content = preg_replace('/(İşte o ilanlardan bazıları)/Uis', '', $content);

		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		if (preg_match('/(\d{4}-\d{1,2}-\d{1,2})(?: |T)/Uis', $article_date, $matches)) {

			$date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			if ($date_obj instanceof DateTime) {
				$article_date = $date_obj->format('Y-m-d H:i:s');
			}
		}
		return $article_date;

	}

}
