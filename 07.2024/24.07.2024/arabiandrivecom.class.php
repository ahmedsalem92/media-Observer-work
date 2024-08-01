<?php

class futurenewsnet extends plugin_base {

	// ANT settings
	protected $ant_precision = 8;
	protected $use_proxies = true;

	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = false;
	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/href="([^"]*)"><div>الصفحة التالية<\/div>/Uis',
				'append_domain' => false,
				'process_link' => 'process_next_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<div class="text-right text-black xl:text-2xl[^>]*>(.*)<\/div>/Uis',
			'content' => '/(<main[^>]*>.*<\/main>)/Uis',
			'author' => false,
			'article_date' => '/"writtenBy":":\s*"[^"]*",\s*"createdAt":\s*"([^"]*)",\s*"slug":\s*"[^"]*",\s*"slug_en":\s*"[^"]*",\s*"__v":\s*0\s*},\s*"trendingArticles"/Uis'
		)
	);
	
	private $links = array();
	private $array_index = 0 ;

	protected function process_next_link($link, $referer_link, $logic) {
		$this->links = array ();
		$this->array_index = 0;
		$new_link = str_replace("/ar/", "/", $link);
		return "https://arabiandrive.com/ar" . $new_link;

	}

	
	protected function process_article_link($link, $referer_link, $logic) {
		
		$temp_link = '';
		if(empty($this->links)){
			$result = $this->ant->get($referer_link);
			if (preg_match('/اخر الاخبار<\/div>(.*)$/Uis', $result, $result2)) {
				if(preg_match_all('/href="([^"]*)"[^>]*>\s*<div class="relative aspect-\[16\/10\]/Uis', $result2[1], $matches)){
					$this->links = $matches[1];
					$this->log( count($matches[1]));
				}
			}
		}
		$temp_link = $this->links[$this->array_index];
		$this->log('index:' . $this->array_index);
		$this->log('link:' . $this->links[$this->array_index]);
		$this->array_index++;
		
		$new_link = str_replace("/ar/", "/", $temp_link);
		return "https://arabiandrive.com/ar" . $new_link;

	}

	protected function process_content($content, $article_data){
		$content = preg_replace('/إقرأ أيضًا.*$/Uis', '<', $content);
		
		return $content;
	}


	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {
		
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
