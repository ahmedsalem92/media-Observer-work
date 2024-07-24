<?php

class lahamag extends plugin_base
{

	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0';

	// CRAWL settings
	protected $stop_on_article_found = true;
	protected $stop_date_override = true;
	protected $stop_on_date = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/(?:<div class="text article-text-container ">|<div class="text main-container more_padding">|<div class="text main-container ">)(.*)(?:<div class="relatedArticles">|<blockquote|<div class="related_articles">)/Uis',
			'author' => false,
			'article_date' => '/"datePublished": "(.*)"/Uis'
		)
	);


	protected function process_article_link($link, $referer_link, $logic)
	{
		// Remove specific link
		if (preg_match('/^https:\/\/www\.lahamag\.com\/article\/\d+--.*/', $link)) {
			return false;
		}
		return $link;
	}


	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/ /Uis', ' ', $content);
		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format

}
