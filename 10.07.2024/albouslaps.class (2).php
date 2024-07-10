<?php

class albouslaps extends plugin_base {
	// ANT settings
	protected $ant_precision = 2;
	protected $use_proxies = true;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0';

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = false;
	protected $stop_on_date = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<h2 class="post-title"><a href="(.*)"/Uis',
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<h1 class="post-title entry-title">(.*)<\/h1>/Uis',
			'content' => '/(?:<div class="entry-content entry.*>|<div class="clearfix text-formatted.*>|<div class="paragraph-list">|<div class="entry-content entry.*>)(.*)<div id="post-extra-info">/Uis',
			'author' => false,
			'article_date' => '/"datePublished":"(.*)",/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic) {

		$temp_link = ''; // https://newsy.albousla.ps/sitemap.xml
		if(preg_match_all('/<loc>(https:\/\/newsy\.albousla\.ps\/sitemap_\d+?.xml)<\/loc>/Uis', $link, $matches)){
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
			return $temp_link;
		}

		return '';

	}

	protected function process_content($content, $article_data){
		$content = preg_replace('/(<aside.*<\/aside>)/Uis', '', $content);
		$content = preg_replace('/(تابع أحدث الأخبار عبر تطبيق)/Uis', '', $content);
		$content = preg_replace('/(<p class="follow-google"><a.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<a class="share_icon">.*<\/a>)/Uis', '', $content);
		$content = preg_replace('/(<span class="fa f.*\/span>)/Uis', '', $content);
		$content = preg_replace('/(تويتر)/Uis', '', $content);
		$content = preg_replace('/(نعرض لكم زوارنا أهم وأحدث الأخبار فى المقال الاتي:)/Uis', '', $content);
		$content = preg_replace('/(انقر هنا لقراءة الخبر من مصدره\.)/Uis', '', $content);
		$content = preg_replace('/(انقر <a.*<\/a>)/Uis', '', $content);
		$content = preg_replace('/(إقرأ ايضا\.)/Uis', '', $content);
		$content = preg_replace('/(تابع أحدث الأخبار عبر تطبيق\.)/Uis', '', $content);
		$content = preg_replace('/(يمكنك مشاركة الخبر علي صفحات التواصل\.)/Uis', '', $content);


		return $content;
	}



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
