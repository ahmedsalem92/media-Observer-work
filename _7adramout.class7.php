<?php

class _7adramout extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link',
			),
		),
		'list2'=>array(
			0=>array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/(?:<div class="articleBody">|<div class="entry-content entry clearfix">)(.*)(?:<div class="inside-article-ad">|<p style="color:#c62828")/Uis',
			'author' => '/<meta name="author" content="(.*)" \/>/Uis',
			'article_date' => '/datePublished":"(.*)"/Uis'
		)
	);
	protected function process_list1_link($link, $referer_link, $logic) {
		$temp_link ='';//https://7adramout.net/post-sitemap3.xml
		if(preg_match_all('/<loc>(https:\/\/7adramout\.net\/post-sitemap\d+?\.xml)<\/loc>/Uis', $link, $matches)){
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
				$this->array_index = sizeof($this->links);                            // new way 
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
	//https://7adramout.net/sitemap_index.xml
	//https://7adramout.net/post-sitemap.xml
	protected function process_content($content, $article_data) {
		$content = preg_replace('/<p><strong>اقرأ أيضًا..<\/strong>[^<]*<\/p>/Uis','',$content);
		$content = preg_replace('/<p[^>]*>اذا كنت تعتقد أن المقال يحوي معلومات خاطئة أو لديك تفاصيل إضافية\s*<b[^>]*>أرسل تصحيحًا<\/b>\s*<\/p>/Uis','',$content);
		$content = preg_replace('/<span>\s*<i class="la la-comments">\s*<\/i>(.*)<\/span>/Uis','',$content);
		$content = preg_replace('/<span>\s*<i class="fa fa-clock-o">\s*<\/i>(.*)<\/span>/Uis','',$content);
		$content = preg_replace('/(<div class="article-tags">.*div>)/Uis','',$content);
		$content = preg_replace('/(<aside class="aside-post">.*aside>)/Uis','',$content);
		$content = preg_replace('/(<h3 class="sd-title">.*<\/div>)/Uis','',$content);
		$content = preg_replace('/(<p>عزيزي الزائر لقد قرأت خبر.*<\/p>)/Uis','',$content);
		$content = preg_replace('/(<div class="a2a_kit">\s*<center>.*<\/center>)/Uis','',$content);
		$content = preg_replace('/(<span>النقرات <\/span>)/Uis','',$content);
		$content = preg_replace('/(<span>0<\/span>)/Uis','',$content);
		$content = preg_replace('/(<footer>.*<\/footer>)/Uis','',$content);
		$content = preg_replace('/(<header.*\/header>)/Uis','',$content);
		$content = preg_replace('/(<(?:span|p|a)>\s*شارك\s*<\/(?:span|p|a)>)/Uis','',$content);
		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		// 2018-06-01T22:47:07+03:00
		if (preg_match('/(.*)T(.*)\+/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;

	}

}
