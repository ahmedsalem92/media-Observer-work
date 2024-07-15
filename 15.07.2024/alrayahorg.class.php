<?php

class alrayahorg extends plugin_base
{
	// ANT settings
	protected $ant_precision = 2;
	protected $use_proxies = true;
	protected $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36';

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = false;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<ul class="pager- ul-none">\s*(?:<li.*<\/li>\s*)*<li.*?<a href="(https:\/\/alrayah\.sa\/page\/.*\/\?s)" >.*<\/ul>/Uis',
				'append_domain' =>	false,
				'process_link' => 'next_page_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<li class="clfx"><div class="box-wrap clfx">\s*<span class="thumb"><a href="(.*)"/Uis',
				'process_link' => 'process_article_link',
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/(<h1 class="post-title">.*<\/h1>)/Uis',
			'content' => '/(<div class="block- padd">.*)(?:<div class="post--tags|<div class="at-below-post|<\/iframe>)/Uis',
			'author' => '/^.*<span class="auth-avtar">.*<h4>(.*)<\/h4>/Uis',
			'article_date' => '/(?:<meta property="article:published_time" content=|"datePublished": ")(.*)"/Uis'
		)
	);

	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<div>\s*<h3><a href="(.*)"/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link',
				'ignore_terminal_stop' => true
			)
		),
		'article' => array(
			'headline' => '/(<h1 class="post-title">.*<\/h1>)/Uis',
			'content' => '/(<div class="block- padd">.*)(?:<div class="post--tags|<div class="at-below-post|<div class="post--tags">)/Uis',
			'author' => '/^.*<span class="auth-avtar">.*<h4>(.*)<\/h4>/Uis',
			'article_date' => '/(?:<meta property="article:published_time" content=|"datePublished": ")(.*)"/Uis'
		)
	);

	public function prepare_home($section_id)
	{

		$this->logic = $this->logic_home;
	}

	protected function next_page_link($link, $referer_link)
	{

		return $link;
	}


	protected function process_article_link($link, $referer_link, $logic)
	{
		if (
			$link == 'https://alrayah.org/category/%d9%85%d9%82%d8%a7%d9%84%d8%a7%d8%aa//' ||
			$link == 'https://alrayah.org/vid/%e2%81%a7%e2%80%ab%d8%b0%d8%a7_%d9%84%d8%a7%d9%8a%d9%86%e2%80%ac%e2%81%a9-%d9%85%d8%af%d9%8a%d9%86%d8%a9-%d8%a7%d9%84%d9%85%d8%b3%d8%aa%d9%82%d8%a8%d9%84-%d9%81%d9%8a-%e2%81%a7%e2%80%ab%d9%86%d9%8a%d9%88%d9%85%e2%80%ac%e2%81%a9/' ||
			$link == 'https://alrayah.org/vid/%d8%a7%d9%84%d9%85%d9%88%d8%a7%d8%b7%d9%86-%d8%b9%d8%a8%d8%af%d8%a7%d9%84%d9%84%d9%87-%d8%a7%d9%84%d9%82%d8%ad%d8%b7%d8%a7%d9%86%d9%8a-%d9%8a%d8%ad%d9%83%d9%8a-%d9%82%d8%b5%d8%a9-%d8%aa%d8%b9%d8%a7%d9%81%d9%8a%d9%87-%d9%85%d9%86-%d9%85%d8%b1%d8%b6-%e2%80%ab%d8%a7%d9%84%d8%b3%d8%b1%d8%b7%d8%a7%d9%86%e2%80%ac-%d8%a8%d8%b9%d8%af-%d8%a3%d9%86-%d8%a3%d8%b5%d9%8a%d8%a8-%d8%a8%d9%87-5-%d9%85%d8%b1%d8%a7%d8%aa/' ||
			$link == 'https://alrayah.org/vid/%d8%a7%d9%84%d9%87%d9%84%d8%a7%d9%84%e2%80%ac%e2%81%a9-%d8%a7%d9%84%d8%b3%d8%b9%d9%88%d8%af%d9%8a-%d9%8a%d8%af%d8%b4%d9%86-%d8%a7%d9%84%d9%87%d9%88%d9%8a%d8%a9-%d9%88%d8%a7%d9%84%d8%a3%d8%b7%d9%82%d9%85-%d8%a7%d9%84%d8%ac%d8%af%d9%8a%d8%af%d8%a9-%d9%84%d9%84%d9%86%d8%a7%d8%af%d9%8a/'
		) {
			return false;
		} else {
			return $link;
		}
	}


	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/<span class="postTitle".*\/span>/Uis', '', $content);
		$content = preg_replace('/(<iframe.*<\/iframe>)/Uis', 'video', $content);
		$content = preg_replace('/<span class="ctaText".*\/span>/Uis', '', $content);
		$content = preg_replace('/(<ins.*<\/ins>)/Uis', '', $content);
		$content = preg_replace('/(<script.*<\/script>)/Uis', '', $content);
		$content = preg_replace('/(<div class="vid-iframe">.*<\/iframe>)/Uis', 'video', $content);
		$content = preg_replace('/<h1[^<]*>(.*)<\/h1>/Uis', '', $content);
		$content = preg_replace('/(<span class="auth-avtar">.*<\/ul>)/Uis', '', $content);
		$content = preg_replace('/(<style.*<\/style>)/Uis', '', $content);
		if (preg_match('/(<div class="p-content">)/Uis', $content, $matches)) {
			if (strlen(trim(strip_tags($content))) == 0)
				return 'no content';
		}

		return $content;
	}

	protected function process_headline($headline, $article_data)
	{

		$headline = preg_replace('/(<h1 class="post-title"><\/h1>)/Uis', 'No Headline', $headline);

		return $headline;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{
		//19 سبتمبر
		//2018-05-21T08:20:26+00:00
		if (preg_match('/(\d{4}-\d{1,2}-\d{1,2})T(.*)(?:\+|Z|\.|\"|-)/Uis', $article_date, $matches)) {

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
