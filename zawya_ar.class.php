<?php

class zawya_ar extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0';

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = false;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/(?:<guid>|<loc>)([^<]*)(?:<\/loc>|<\/guid>)/Uis',
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<h1 class="article-title">(.*)<\/h1>/Uis',
			'content' => '/^.*"articleBody"(.*)<meta name=[^<]*>/Uis',
			'article_date' => '/"datePublished": "(.*)"/Uis'
		)
	);


	protected function process_content($content, $article_data){

		$content = preg_replace('/("cssSelector" : "\.paywall")/Uis', '', $content);
		$content = preg_replace('/("isAccessibleForFree": "true",)/Uis', '', $content);
		$content = preg_replace('/("hasPart":)/Uis', '', $content);
		$content = preg_replace('/("@type": "WebPageElement")/Uis', '', $content);
		$content = preg_replace('/(})/Uis', '', $content);
		$content = preg_replace('/({)/Uis', '', $content);
		$content = preg_replace('/(,)/Uis', '', $content);
		$content = preg_replace('/(\")/Uis', '', $content);
		$content = preg_replace('/(u202F)/Uis', '', $content);
		return $content;
	}


	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2022-03-08T09:10:53.408Z
		if (preg_match('/(.*)T(.*)(?:\.|Z)/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;

	}

	protected function encoding($link) {

		$parts = parse_url($link);
		$path = explode('/', $parts['path']);

		foreach($path as &$param) {
			$param = urlencode($param);
		}
		$parts['path'] = implode('/', $path);


		$link = strtolower (unparse_url($parts));

		return $link;

	}

	protected function pre_get_page(&$link) {

		if (in_array($link, $this->article_links_only)) {
			$link = 'https://app.scrapingbee.com/api/v1/?api_key=E4BW8I0UGGZMX1CLD4LJL9KRE1PE0MVTTN2HOH58FO55O0KO53F2AIF8IFJANAKACGG3QSVQCC57XBAJ&premium_proxy=true&country_code=ae&render_js=false&url=' . $this->encoding($link);
		}
		else {
			$link = 'https://app.scrapingbee.com/api/v1/?api_key=E4BW8I0UGGZMX1CLD4LJL9KRE1PE0MVTTN2HOH58FO55O0KO53F2AIF8IFJANAKACGG3QSVQCC57XBAJ&premium_proxy=true&render_js=false&country_code=ae&url=' . $link;
		}
	}

}
