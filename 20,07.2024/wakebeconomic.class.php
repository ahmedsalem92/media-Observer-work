<?php

class arqamtmcom extends plugin_base
{

	// ANT settings
	protected $ant_precision = 2;

	// CRAWL settings
	protected $stop_on_article_found = true;
	protected $stop_date_override = true;


	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
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
			'headline' => '/^.*<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="entry-content entry clearfix">(.*)<div id="post-extra-info">/Uis',
			'author' => false,
			'article_date' => '/"datePublished":"(.*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic)
	{

		$temp_link = ''; // https://wakebeconomic.com/post-sitemap22.xml
		if (preg_match_all('/<loc>(https:\/\/wakebeconomic\.com\/post-sitemap\d*?\.xml)<\/loc>/Uis', $link, $matches)) {
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>', '', $temp_link);
			$temp_link = str_replace('</loc>', '', $temp_link);
		}

		return $temp_link;
	}

	private $links = array();
	private $array_index = 0;

	protected function process_article_link($link, $referer_link, $logic)
	{

		$temp_link = '';
		if (empty($this->links)) {
			$result = $this->ant->get($referer_link);
			if (preg_match_all('/<loc>(.*)<\/loc>/Uis', $result, $matches)) {
				$this->links = $matches[0];
				$this->array_index = sizeof($this->links) - 1; // Set index to last element
			}
		}

		if ($this->array_index >= 0 and isset($this->links[$this->array_index])) {
			$temp_link = str_replace('<loc>', '', $this->links[$this->array_index]);
			$temp_link = str_replace('</loc>', '', $temp_link);

			if ($temp_link == 'https://arqam.news/321264/') return false;

			$this->array_index--; // Move to the previous element for next call
			return $temp_link;
		}

		return '';
	}


	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/(<script.*<\/script>)/Uis', '', $content);
		$content = preg_replace('/(<div class="ozuftl9m.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="oajrlxb2.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<p style="color:#E74C3C;">.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<table.*<\/table>)/Uis', '', $content);
		$content = preg_replace('/(<div class="post-bottom-meta.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<span class="tagcloud">.*<\/span>)/Uis', '', $content);
		$content = preg_replace('/(<div class="stjgntxs.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(أخبار ذات صلة)/Uis', '', $content);
		$content = preg_replace('/(<p>واكب.*<\/p>)/Uis', '', $content);

		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{

		//2018-05-21T08:20:26+00:00
		if (preg_match('/(.*)T(.*)\+/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		if (strpos($this->settings['site_section_link'], '/columns')) {
			return date('Y-m-d H:i:s', time());
		}

		return $article_date;
	}
}
