<?php

class albawaba_ar extends plugin_base {

	// ANT settings
	protected $ant_precision = 6;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';
	protected $private_cookie = true;


	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';

	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => array(
								'/<ul class="menu menu--arabic-main-navigation nav navbar-nav">(.*)<\/nav>/Uis',
								'/href="([^"]*)"/Uis'
							),
				'append_domain' => true,
				'process_link' => 'section_link'
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'section',
				'regexp' => '/<h2[^>]*class=[^>]*block-title[^>]*>(<a.*<\/h2>)/Uis',
				'append_domain' => false,
			)
		),
		'section' => array(
			'name' => '/<a[^>]*>([^<]*)<\/a/Uis',
			'link' => '/href="([^"]*)"/Uis',
			'append_domain' => true,
			'process_link' => 'section_link'
		)
	);

	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<a class="button" href="([^"]*)" title="الذهاب إلى الصفحة التالية"/Uis',
				'append_domain' => false,
				'process_link' => 'process_next_link'

			),
			1 => array(
				'type' => 'article',
				'regexp'=> '/<h3>\s*<a href="([^"]*)"/Uis',
				'process_link' => 'process_article_link',
				'append_domain' => true
			)
		),
		'article' => array(
			'headline' => '/<h1 class="page-header">(?:<span>|)(.*)(?:<\/span|<\/h)/Uis',
			'content' => '/(?:<section class="block block-layout-builder block-field-(?:blocknodearticlebody|blocknodepagebody) clearfix">|<div class="field field--name-field-highlights field--type-string field--label-above">|<section class="block block-ctools block-entity-viewnode clearfix">)(.*)<\/section/Uis',
			'author' => false,
			'article_date' => '/(?:"datePublished"\s*:\s*"|<section class="block block-layout-builder block-field-blocknodearticlecreated clearfix">.*<span>)(.*)(?:<\/span>|")/Uis'
		)
	);
	protected $logic_no_next = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp'=> '/<h3>\s*<a href="([^"]*)"/Uis',
				'process_link' => 'process_article_link',
				'append_domain' => true
			)
		),
		'article' => array(
			'headline' => '/<h1 class="page-header">(?:<span>|)(.*)(?:<\/span|<\/h)/Uis',
			'content' => '/(?:<section class="block block-layout-builder block-field-(?:blocknodearticlebody|blocknodepagebody) clearfix">|<div class="field field--name-field-highlights field--type-string field--label-above">|<section class="block block-ctools block-entity-viewnode clearfix">)(.*)<\/section/Uis',
			'author' => false,
			'article_date' => '/(?:"datePublished"\s*:\s*"|<section class="block block-layout-builder block-field-blocknodearticlecreated clearfix">.*<span>)(.*)(?:<\/span>|")/Uis'
		)
	);

	protected $logic_home = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<div class="field field--name-node-title[^<]*><h3>\s*<a href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link',
				'ignore_terminal_stop' => true
			)
		),
		'article' => array(
			'headline' => '/<h1 class="page-header">(?:<span>|)(.*)(?:<\/span|<\/h)/Uis',
			'content' => '/(?:<section class="block block-layout-builder block-field-(?:blocknodearticlebody|blocknodepagebody) clearfix">|<div class="field field--name-field-highlights field--type-string field--label-above">|<section class="block block-ctools block-entity-viewnode clearfix">)(.*)<\/section/Uis',
			'author' => false,
			'article_date' => '/(?:"datePublished"\s*:\s*"|<section class="block block-layout-builder block-field-blocknodearticlecreated clearfix">.*<span>)(.*)(?:<\/span>|")/Uis'
		)
	);

	public function prepare_no_next($section_id) {

		$this->logic = $this->logic_no_next;

	}
	public function prepare_home($section_id) {

		$this->logic = $this->logic_home;

	}

	protected function url_econding($link){

		$parts = parse_url($link);
		$path = explode('/', $parts['path']);

		foreach($path as &$param) {
			$param = urlencode($param);
		}
		$parts['path'] = implode('/', $path);

		$link = strtolower (unparse_url($parts));

		return $link;
	}
	protected function detect_section_link($link) {

		return 'https://www.albawaba.com/ar'. '?c_b=' . rand();

	}

	protected function section_link($link) {

		$temp_link = $this->url_econding($link);
		if(in_array($temp_link,[
		"https://www.albawaba.com/ar/%25d9%2581%25d9%258a%25d8%25af%25d9%258a%25d9%2588",
		"https://www.albawaba.com/ar/term/339/archive/ar/%25d8%25aa%25d8%25b1%25d9%2581%25d9%258a%25d9%2587/%25d8%25a7%25d8%25ae%25d8%25aa%25d8%25b1%25d9%2586%25d8%25a7-%25d9%2584%25d9%2583%25d9%2585",
		"https://www.albawaba.com/ar/term/341/archive/ar/%25d8%25b5%25d8%25ad%25d8%25aa%25d9%2583%25d9%2590-%25d9%2588%25d8%25ac%25d9%2585%25d8%25a7%25d9%2584%25d9%2583%25d9%2590/%25d8%25a5%25d8%25b7%25d9%2584%25d8%25a7%25d9%2584%25d8%25a7%25d8%25aa-%25d8%25a7%25d9%2584%25d9%2585%25d8%25b4%25d8%25a7%25d9%2587%25d9%258a%25d8%25b1",
		]))
			return'';
		if(strpos($temp_link,'albawaba.com/ar'))
			return $temp_link  . '?c_b=' . rand();

	}


	protected function process_next_link($link, $referer_link, $logic) {

		$link = $this->settings['site_section_link'] . $link;
		$link = str_replace('&amp;', '&', $link);
		$link = str_replace('#038;', '', $link);

		// Match the first occurrence of c_b and remove subsequent ones
		$url = preg_replace('/(\?|&)c_b=[^&]*/', '$1c_b=NEXT_PAGE', $link, 1);
		$new_url = preg_replace('/&c_b=[^&]*/', '', $url);
		$new_url = preg_replace('/NEXT_PAGE/', preg_replace('/.*?(\?|&)c_b=([^&]*).*/', '$2', $url), $new_url);

		return $new_url;
	}
	protected function process_article_link($link, $referer_link, $logic) {

		$link_parts = explode('/', $link);
		$mixed_part = array_pop($link_parts);
		if (trim($mixed_part) == '') {
			$mixed_part = array_pop($link_parts);
		}
		$link_parts[] = urlencode(html_entity_decode($mixed_part));
		$result_link = implode('/', $link_parts);

		if(strpos($result_link,'/ar/author/'))
			return '';
		return $result_link;
	}

	protected function process_content($content, $article_data){

		$content = preg_replace(
			'/(<div class="image-container"><div class="article-image-block"><div class="article-image">.*<div class="article-image-description">[^<]*<\/div><\/div><\/div>)/Uis',
			'',
			$content
		);
		// remove advertisement title
		$content = preg_replace('/(>View this post on Instagram<)/Uis', '><', $content);
		$content = preg_replace('/(>A post shared by[^<]*<)/Uis', '><', $content);
		$content = preg_replace('/(<h2[^>]*>\s*Advertisement\s*<\/h2>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>\s*<strong>\s*<a[^<]*target="_blank">.*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>\s*<strong>للمزيد من.*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>\s*<a[^>]*>[^<]*<\/a>\s*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<h2[^>]*>\s*إعلان\s*<\/h2>)/Uis', '', $content);
		$content = preg_replace('/(<div class="related_articles.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="facetweet">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<section[^>]*>\s*<p[^>]*>.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>(?:\s*<strong[^>]*>|\s*<span[^>]*>)*+(?:أقرا|إقرا|اقرا|اقرأ|أقرأ|إقرأ|إقرأ|يهمك|شاهدي|شاهد)\s*(?:أيضًا|ايضا|أيضا|أيضاً|أيضَا|أيض&#1611;ا|ايضاً|أىضاً|المزيد).*\/p>\s*(?:\s*<p><a.*\/a>\s*<\/p>\s*)*+)/Uis', '', $content);
		$content = preg_replace('/(توقعات الأبراج لعام 2023)/Uis', '', $content);
		$content = preg_replace('/(توقعات نجلاء قباني للأبراج لعام 2023)/Uis', '', $content);
		// remove alerts container
		$content = preg_replace('/(<div id="underimagecontainer">.*<!-- \/block\.tpl\.php -->\s*<\/div>)/Uis', '', $content);

		return $content;
	}


	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2019-02-27T23:30:18+00:00
		if (preg_match('/(.*)T(.*)\+/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		elseif (preg_match('/(\d+?).*\/ (\W+?) (\d{4})/Uis', $article_date, $matches)) {
			$month = $this->arabic_month_to_number(trim($matches[2]));
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $month . '-' . $matches[1] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		return $article_date;
	}

}
