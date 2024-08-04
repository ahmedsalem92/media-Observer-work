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
				'type' => 'list1',
				'regexp' => '/<span class="last-page first-last-pages">.*<a href="(.*)"/Uis',
				'append_domain' => false
			),
			1 => array(
				'type' => 'article',
				'regexp' => [
					'/<ul id="posts-container".*>(.*)<\/ul>/Uis',
					'/<h2 class="post-title"><a href="(.*)"/Uis',
				],
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="entry-content entry clearfix">(.*)<div id="post-extra-info">/Uis',
			'author' => false,
			'article_date' => '/dateModified":"(.*)"/Uis'
		)
	);

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

		//2024-07-30T08:35:05+00:00 
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
