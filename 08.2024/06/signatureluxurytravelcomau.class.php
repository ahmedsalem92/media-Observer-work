<?php

class signatureluxurytravelcomau extends plugin_base
{
	// ANT settings
	protected $ant_precision = 6;
	protected $stop_on_date = false;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/64.0.3282.167 Chrome/64.0.3282.167 Safari/537.36';

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
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="eltdf-post-content">(.*)(?:placeholder="Subscribe to our newsletter"|<div class="eltdf-bnl-holder eltdf-pl-eight-holder|<div id="block-\d+?" class="widget widget_block">|<p><strong>Read more:|<div class="eltdf-single-tags-share-holder">|<p class="eltdf-pt-three-title">|<\/article>)/Uis',
			'author' => false,
			'article_date' => '/dateModified":.*"(.*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic)
	{

		$temp_link = ''; // https://www.signatureluxurytravel.com.au/post-sitemap3.xml
		if (preg_match_all('/<loc>(https:\/\/www\.signatureluxurytravel\.com\.au\/post-sitemap\d+?\.xml)<\/loc>/Uis', $link, $matches)) {
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
				$this->array_index = sizeof($this->links);
			}
		}
		$this->array_index--;
		if ($this->array_index > 0 and isset($this->links[$this->array_index])) {
			$temp_link = str_replace('<loc>', '', $this->links[$this->array_index]);
			$temp_link = str_replace('</loc>', '', $temp_link);
			return $temp_link;
		}

		return '';
	}

	protected function process_content($content, $article_data)
	{
		$content = preg_replace('/(<button[^>]*>Subscribe<\/button>)/Uis', '', $content);
		$content = preg_replace('/(<strong>Read more:<\/strong>.*)<\/div>/Uis', '', $content);
		$content = preg_replace('/(<form id="wpforms-form-58331".*<\/form>)/Uis', '', $content);
		$content = preg_replace('/target="_self">( Safari of a lifetime at andBeyond&#8217;s Phinda Private.*Game Reserve )<\/a><\/h4>/Uis', '', $content);
		$content = preg_replace('/(<span class="eltdf-btn-text">uniworld.com<\/span>)/Uis', '', $content);
		$content = preg_replace('/>(Win an opulent 12-day Egyptian cruise for two)<\/a><\/h3>/Uis', '', $content);
		$content = preg_replace('/>(Win an opulent 12-day Egyptian cruise for two)<\/a><\/h3>/Uis', '', $content);
		$content = preg_replace('/target="_self">( Save on your French Polynesia cruise with Aranui )<\/a><\/h4>/Uis', '', $content);
		$content = preg_replace('/target="_self">(11 places to visit in Sri Lanka)<\/a><\/h4>/Uis', '', $content);
		$content = preg_replace('/<p>(This article is a <em>Signature Luxury Travel.*)<div class="eltdf-single-tags-share-holder">/Uis', '', $content);
		$content = preg_replace('/>(Please enable JavaScript in your browser to complete.*form\.)</Uis', '', $content);
		$content = preg_replace('/target="_self">(Safari of a lifetime at and.*Phinda Private Game Reserve)<\/a>/Uis', '', $content);
		$content = preg_replace('/target="_self">(Save on your French Polynesia cruise with Aranui)<\/a><\/h4>/Uis', '', $content);
		$content = preg_replace('/(<div class="wpb_text_column wpb_content_element " >.*)<div class="eltdf-single-tags-share-holder">/Uis', '', $content);
		$content = preg_replace('/(<div class="wpb_text_column wpb_content_element " >.*)<div class="wpforms-container wpforms-container-full".*>/Uis', '', $content);
		$content = preg_replace('/(<div class="wpb_text_column wpb_content_element " >.*)<div class="wpforms-container wpforms-container-full".*>/Uis', '', $content);
		$content = preg_replace('/<div class="wpb_text_column wpb_content_element " ><div class="wpb_wrapper">(<p>This article is a <em>.*<\/em>.*<a.*>.*<\/a>.*<\/p>)<\/div><\/div><\/div>/Uis', '', $content);
		$content = preg_replace('/<label class="wpforms-field-label" for="wpforms-58331-field_2">(Want more travel inspiration delivered directly to your inbox\?)<\/label>/Uis', '', $content);
		$content = preg_replace('/<div class="m_-8269437048825882457gmail-wpb_wrapper">(<p><em>This article is a <\/em>.*<a.*<\/a>.*<\/em><\/p>)<\/div>/Uis', '', $content);
		$content = preg_replace('/<span.*>(VISIT WEBSITE)<\/span>/Uis', '', $content);
		$content = preg_replace('/>(Subscribe to the latest issue today)<\/a>(\.)</Uis', '', $content);
		$content = str_replace('click here', '', $content);
		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{
		//2024-07-31T07:28:13+00:00
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
