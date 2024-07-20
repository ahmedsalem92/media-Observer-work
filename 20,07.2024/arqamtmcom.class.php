<?php

class arqamtmcom extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;

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
			'headline' => '/^.*<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="elementor-element elementor-element-[a-zA-Z0-9]*? single-post-in elementor-widget elementor-widget-theme-post-content".*>(.*)<div class="elementor-element/Uis',
			'author' => false,
			'article_date' => '/^.*<\/h1>.*<span class="elementor-icon-list-text elementor-post-info__item elementor-post-info__item--type-date">\s*?(.*)\s*?<\/span>/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic) {

		$temp_link ='';
		if(preg_match_all('/<loc>(http:\/\/arqam\.news\/post-sitemap\d*?\.xml)<\/loc>/Uis', $link, $matches)){
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>' , '' , $temp_link);
			$temp_link = str_replace('</loc>' , '' , $temp_link);
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

			if ($temp_link == 'https://arqam.news/321264/') return false;

			return $temp_link;
		}

		return '';

	}


	protected function process_content($content, $article_data){

		$content = preg_replace('/(<script.*<\/script>)/Uis', '', $content);
		$content = preg_replace('/(<div class="ozuftl9m.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="oajrlxb2.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<p style="color:#E74C3C;">.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<table.*<\/table>)/Uis', '', $content);
		$content = preg_replace('/(<div class="post-bottom-meta.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<span class="tagcloud">.*<\/span>)/Uis', '', $content);
		$content = preg_replace('/(<div class="stjgntxs.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(أخبار ذات صلة)/Uis', '', $content);

		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2018-05-21T08:20:26+00:00
		if (preg_match('/(\d+?)\/(\d+?)\/(\d+?)/Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $matches[2] . '-' . $matches[1] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;

	}

}
