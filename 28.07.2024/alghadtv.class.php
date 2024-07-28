<?php

class alghadtv extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0';

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
			'headline' => '/<h1 class="single-post-title">.*<span class="post-title" itemprop="headline">(.*)<\/span>/Uis',
			'content' => '/<div class="entry-content.*>(.*)(?:<div class="entry-terms post-tags clearfix">|<div class="post-share single-post-share)/Uis',
			'author' => '/<span class="post-author-name">[^<]*<b>(.*)<\/b>/Uis',
			'article_date' => '/<div class="post-header-title">.*<time class="post-published updated"\s*datetime="([^"]*)">/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic) {

		$temp_link ='';
		if(preg_match_all('/<loc>(https:\/\/www.alghad.tv\/wp-sitemap-posts-post-\d+.xml)<\/loc>/Uis', $link, $matches)){
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>' , '' , $temp_link);
			$temp_link = str_replace('</loc>' , '' , $temp_link);
		}

		return $temp_link;
	}
	private $links = array();
	private $array_index;

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
		$temp_link = str_replace('<loc>' , '' , $this->links[$this->array_index]);
		$temp_link = str_replace('</loc>' , '' , $temp_link);
		return $temp_link;
	}

	protected function process_content($content, $article_data) {

		$content = preg_replace('/<div class="panel-body small_font">\s*<img[^>]*>\s*<div[^>]*>[^<]*<\/div>/Uis', '', $content);
		$content = preg_replace('/(<iframe.*<\/iframe>)/Uis', 'video', $content);
		$content = preg_replace('/(<div id="infinix">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="entry vert-offset.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<h4 class="secondary_titl.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/<p><img[^>]*><\/p>/Uis', 'IMAGE', $content);
		$content = preg_replace('/(?:<div class="panel panel-default"|<div class="statistics[^<]*>)(.*)<\/div>/Uis', '', $content);

		if (preg_match('/(^\s*<\/div>\s*$)/Uis', $content, $matches)){
			return 'no content';
		}

		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		if (preg_match('/(.*)T/Uis', $article_date, $matches)) {

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
