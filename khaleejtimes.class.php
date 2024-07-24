<?php

class khaleejtimes extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $disable_cache_buster = true;
	protected $use_proxies = true;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<url>\s*<loc>(.*)<\/loc>\s*<lastmod>/Uis',
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<meta property="og:title" content="(.*)"/Uis',
			'content' => '/<\/h1>(.*)(?:<div class="(?:author_detail|news_detail_banner|desktopArticleEnd|share_social_icons_bottom)">|<ul class="tags-btm-nf">|<secondpart>|<\!--Content Section End here-->|<\/article)/Uis',
			'author' => '/<meta name="author" content="([^"]*)"/Uis',
			'article_date' => '/(?:"datePublished":\s*"|article_publish_date"\s*:\s*")([^"]*)"/Uis'
		)
	);


	protected function process_content($content, $article_data) {

		$content = preg_replace('/(ALSO READ:\s*<\/strong><\/p>(?:\s*<ul.*\/ul>)*+)/Uis', '', $content);
		$content = preg_replace('/(<p>ALSO READ:<\/p>\s*(?:\s*<ul.*\/ul>\s*)*+)/Uis', '', $content);
		$content = preg_replace('/(<div class="article-lead-img-caption".*\/div>)/Uis', '', $content);
		$content = preg_replace('/<strong>Also read:\s*<a.*a>/Uis', '', $content);
		$content = preg_replace('/<blockquote.*blockquote>/Uis', '', $content);
		$content = preg_replace('/(<strong id="strong.*<\/strong>)/Uis', '', $content);
		$content = preg_replace('/(Published:.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<div class="article-top-author.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="movie-date">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<h2 class="post-title">.*<\/h2>)/Uis', '', $content);
		$content = preg_replace('/(<div class="entry-summary">.*<\/span>)/Uis', '', $content);
		$content = preg_replace('/(<span class="sep">.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<div id="arttopstrywrap.*<\/div>\s*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="google.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<ul class="also.*<\/ul>)/Uis', '', $content);



		return $content;
	}

	protected function process_headline($headline, $article_data) {

		return iconv('WINDOWS-1252', 'UTF-8//TRANSLIT//IGNORE', $headline);

	}

	protected function process_article_date($article_date, $article_data) {

		return iconv('WINDOWS-1252', 'UTF-8//TRANSLIT//IGNORE', $article_date);

	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {


		if (preg_match('/(\d{4}-\d{1,2}-\d{1,2})T(.*)(?:\+|Z|\.|\")/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' '.$matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;

	}

}
