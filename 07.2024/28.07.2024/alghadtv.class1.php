<?php

class alghadtv extends plugin_base
{

	// ANT settings
	protected $ant_precision = 6;
	protected $stop_on_date = true;
	protected $use_headless = true;
	protected $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36';

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;


	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<a class="next page-numbers" href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_list1_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => [
					'/<div class="listing listing-blog listing-blog-3 clearfix ">(.*)<div class="pagination bs-numbered-pagination"/Uis',
					'/<a href="(.*)"/Uis',
				],
				'append_domain' => true,
			)
		),
		'article' => array(
			'headline' => '/<h1 class="single-post-title">.*<span class="post-title" itemprop="headline">(.*)<\/span>/Uis',
			'content' => '/<div class="entry-content.*>(.*)(?:<div class="entry-terms post-tags clearfix">|<div class="post-share single-post-share)/Uis',
			'author' => '/<span class="post-author-name">[^<]*<b>(.*)<\/b>/Uis',
			'article_date' => '/<div class="post-header-title">.*<time class="post-published updated"\s*datetime="([^"]*)">/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic)
	{
		return $link;
	}

	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/<div class="panel-body small_font">\s*<img[^>]*>\s*<div[^>]*>[^<]*<\/div>/Uis', '', $content);
		$content = preg_replace('/(<iframe.*<\/iframe>)/Uis', 'video', $content);
		$content = preg_replace('/(<div id="infinix">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="entry vert-offset.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<h4 class="secondary_titl.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/<p><img[^>]*><\/p>/Uis', 'IMAGE', $content);
		$content = preg_replace('/(?:<div class="panel panel-default"|<div class="statistics[^<]*>)(.*)<\/div>/Uis', '', $content);

		if (preg_match('/(^\s*<\/div>\s*$)/Uis', $content, $matches)) {
			return 'no content';
		}

		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{

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

	public function pre_get_page(&$page)
	{

		$this->ant->set_wait_for_load(true);
	}
}
