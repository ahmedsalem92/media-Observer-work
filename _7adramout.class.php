<?php

class _7adramout extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $use_proxies = true;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';
	

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;

	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<h3 class="post-title"><a\s+href="(.*?)">/Uis',
				'append_domain' => false
			),
		),
		'article' => array(
			'headline' => '/<h1 class="post-title entry-title">(.*)<\/h1>/Uis',
			'content' => '/(<div class="entry-content entry clearfix">)(.*?)(?:<\/div><div id="post-extra-info"|<\/article|<footer)/Uis',
			'author' => '/<span class="post-source">\s*<a[^>]*>(.*)<\/a>/Uis',
			'article_date' => '/<meta property="og:updated_time" content="([^"]*)"/Uis'
		)
	);
	protected function section_link($link) {

		return 'https://7adramout.net/yemen-news/2024/07/03/%d9%88%d8%a7%d8%b4%d9%86%d8%b7%d9%86-%d8%aa%d8%aa%d8%ad%d8%af%d8%ab-%d8%b9%d9%86-%d8%a7%d8%b5%d8%b7%d9%8a%d8%a7%d8%af-%d9%87%d8%af%d9%81%d8%a7%d9%8b-%d8%ad%d9%88%d8%ab%d9%8a%d8%a7%d9%8b/';
	}
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
