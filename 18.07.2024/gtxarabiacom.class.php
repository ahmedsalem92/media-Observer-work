<?php

class gtxarabiacom extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36';

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = false;
	protected $stop_on_date = false;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';


	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<a class="next page-numbers" href="(.*)"/Uis',
				'append_domain' => false,
				'process_link' => 'next_page_link'
			),
			1 => array(
				'type' => 'article',
				'regexp' => array(
					'/<div class="entry-media penci_mobj__img">.*href="(.*)"/Uis'
				),
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/(?:<h1.*>|<h2 class="headline">|<div class="headline-entry article"><div class="headline-col "><h1>|<div class="headline-entry article"><div>.*<div class="headline-col "><h1>)(.*)(?:<\/h1>|<\/h2>|<\/h[^<]*>)/Uis',
			'content' => '/(?:<div class="penci-entry-content entry-content">|<div class="gallery_group">|<div class="p402_premium">|<div class="entry-content">|<div class="xn-content">)(.*)(?:<!-- .entry-content -->|<aside|<div class="share_buttons_group">|<div class="table-responsive">|<p id="PURL">|<div id="sup_nav" class="sup_nav single">|<\/main>|<p dir="ltr">###<\/p>)/Uis',
			'article_date' => '/(?:"datePublished": "|"datePublished":"|"createdAt":")(.*)"/Uis'
		)
	);


	protected $logic_sitemap = array(
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
			'headline' => '/<h2 class="headline">(.*)<\/h2>/Uis',
			'content' => '/<p class="author">.*<\/p>(.*)<div class="instory_widget">/Uis',
			'article_date' => '/(?:"datePublished":"|"createdAt":")(.*)"/Uis'
		)
	);

	protected function next_page_link($link, $referer_link)
	{

		return $link;
	}

	protected function process_list1_link($link, $referer_link, $logic)
	{

		$temp_link = ''; // https://www.amnews.com/wp-sitemap-posts-post-15.xml
		if (preg_match_all('/<loc>(https:\/\/www\.amnews\.com\/wp-sitemap-posts-post-\d*?\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>', '', $temp_link);
			$temp_link = str_replace('</loc>', '', $temp_link);
		}

		return $temp_link;
	}
	private $links = array();
	private $array_index;

	protected function process_article_link($link, $referer_link, $logic)
	{

		$temp_link = '';
		if (empty($this->links)) {
			$result = $this->ant->get($referer_link);
			if (preg_match_all('/<loc>(.*)<\/loc>/Uis', $result, $matches)) {
				$this->links = $matches[0];
				$this->array_index = sizeof($this->links);
			}
		}
		$this->array_index--;
		$temp_link = str_replace('<loc>', '', $this->links[$this->array_index]);
		$temp_link = str_replace('</loc>', '', $temp_link);
		return $temp_link;
	}



	public function prepare_sitemap($section_id)
	{

		$this->logic = $this->logic_sitemap;
	}
	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/(<div class="xn-newslines">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<section id="block-757700".*<\/section>)/Uis', '', $content);
		$content = preg_replace('/(Sign up for.*<\/label>)/Uis', '', $content);
		$content = preg_replace('/(<h3 class="section">.*<\/h3>)/Uis', '', $content);
		$content = preg_replace('/(Original Press Release)/Uis', '', $content);
		$content = preg_replace('/للمزيد من التفاصيل/Uis', '', $content);
		$content = preg_replace('/يرجى زيارة/Uis', '', $content);
		$content = preg_replace('/يرجى زيارة/Uis', '', $content);
		$content = preg_replace('/للمزيد من المعلومات يمكنكم زيارة الموقع الرسمي من هنا/Uis', '', $content);

		return $content;
	}

	protected function process_article2_link($link, $referer_link, $logic)
	{

		return str_replace('https://www.', 'https://smb.', $link);
	}

	protected $page_count = 1;
	protected function process_list_press_link($link, $referer_link, $logic)
	{
		$this->page_count = $this->page_count + 1;
		if ($this->page_count < 41) {
			return 'https://smb.amnews.com/?&page=' . $this->page_count;
		} else {
			return false;
		}
	}


	protected function filter_sections($section_link, $section_name, $referer_link, $logic)
	{

		// exclude these sections
		if (in_array(trim($section_name), $this->exclude_sections)) {
			return '';
		}

		return $section_link;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{
		if (preg_match('/(\d{4})-(\d{2})-(\d{2})/Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . '-' . $matches[2]  . '-' . $matches[3] . ' 00:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		} elseif (preg_match('/(.*)T/Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d\TH:i:s',
				$matches[1],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		return $article_date;
	}
}
