<?php

class picayuneitemcom extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = false;

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
			'headline' => '/<h2 class="headline">(.*)<\/h/Uis',
			'content' => '/(<div class="story_detail">.*)(<div id="comments|>\s*Continued…\.|>\s*#\s*#\s*#\s*<\/)/Uis',
			'author' => false,
			'article_date' => '/datePublished":"(.*)"/Uis'
		)
	);
	protected $logic_press = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list_press_link'

			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<div class="headline-entry article">.*<div class="headline-col[^>]*>\s*<a href="([^"]*)"/Uis',
				'append_domain' => false,
				'process_link' => 'process_press_link'
			)
		),
		'article' => array(
			'headline' => '/<body[^>]*>.*(?:<h1 class="xn-hedline">|<div class="headline-col "><h1>)(.*)<\/h/Uis',
			'content' => '/(<div class="entry-content">.*)(?:<\/main|<ul class="sidebar|>\s*Continued…\.|>\s*#\s*#\s*#\s*<\/)/Uis',
			'author' => false,
			'article_date' => '/","date":"(.*)"/Uis'
		)
	);

	protected $page_count = 1;
	protected function process_list_press_link($link, $referer_link, $logic) {
		$this->page_count = $this->page_count +1;
		return 'https://smb.picayuneitem.com/?&page=' . $this->page_count;
	}
	protected function process_press_link($link, $referer_link, $logic) {
		if (preg_match('/\/contests\//Uis', $link, $matches))  return '';
		return 'https://smb.picayuneitem.com' . $link;
	}
	public function prepare_press($section_id) {

		$this->logic = $this->logic_press;

	}

	protected function process_list1_link($link, $referer_link, $logic) {

		$temp_link =''; // https://www.picayuneitem.com/wp-sitemap-posts-post-22.xml
		if(preg_match_all('/<loc>(https:\/\/www\.picayuneitem\.com\/wp-sitemap-posts-post-\d+\.xml)<\/loc>/Uis', $link, $matches)){
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>' , '' , $temp_link);
			$temp_link = str_replace('</loc>' , '' , $temp_link);
		}

		return $temp_link;
	}


	private $links = array();
	private $array_index;

	protected function process_article_link($link, $referer_link, $logic) {

		$temp_link = '';
		if(empty($this->links)){
			$result = $this->ant->get($referer_link);
			if(preg_match_all('/<loc>(.*)<\/loc>/Uis', $result, $matches)){
				$this->links = $matches[0];
				$this->array_index = sizeof($this->links);
			}
		}
		$this->array_index--;
		$temp_link = str_replace('<loc>' , '' , $this->links[$this->array_index]);
		$temp_link = str_replace('</loc>' , '' , $temp_link);
		if (preg_match('/\/(?:contests|subscriptions)\//Uis', $temp_link, $matches))  return '';
		return $temp_link;
	}

	protected function process_content($content, $article_data) {

		$content = preg_replace('/<div class="gallery_group.*\/div>/Uis', '', $content);
		$content = preg_replace('/<div id="article_info">.*\/div>/Uis', '', $content);
		$content = preg_replace('/(<form.*<\/form>)/Uis', '', $content);
		$content = preg_replace('/(<ins.*<\/ins>)/Uis', '', $content);
		$content = preg_replace('/(<script.*<\/script>)/Uis', '', $content);
		$content = preg_replace('/(<p>\s*<span class="xn-person.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>\s*(?:From Staff Reports|All of these articles can|This content is published on|newsroom\:|To learn more about this|For more information|To learn more about).*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<div class="entry-content">.*<div class="xn-content">)/Uis', '<div class="entry-content">', $content);
		$content = preg_replace('/(<h6[^>]*>\s*<img.*<\/h6>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>SOURCE[^<]*<span class="(?:xn-location|xn-person)".*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<div class=\'widget\'.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<p>The post <a.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*class="author.*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p><strong>Author Bio<\/strong><\/p>\s*<p>.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p class="tags">.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>\s*TAGS<br \/>.*\/p>)/Uis', '', $content);
		$content = preg_replace('/(\[\…\])/Uis', '', $content);
		$content = preg_replace('/(>\s*,\s*<)/Uis', '><', $content);
		$content = preg_replace('/(>\s*-\s*Written by[^<]*<)/Uis', '><', $content);
		$content = preg_replace('/(<pre>.*\/pre>)/Uis', '', $content);
		$content = preg_replace('/(>\s*The following files are available for download[^<]*<\/p>\s*(?:<div>|\s*)*+\s*<table.*\/table>)/Uis', '>', $content);
		$content = preg_replace('/(<a[^>]*rel="category tag"[^>]*>.*\/a>)/Uis', '', $content);
		$content = preg_replace('/(>\s*Read the full report\s*:\s*<a.*\/a>)/Uis', '>', $content);
		$content = preg_replace('/(>For more information:\s*<a.*\/a>)/Uis', '>', $content);
		$content = preg_replace('/(<p[^>]*>\s*<a[^>]*>\s*(?:<strong>|<span>|<em>\s*)*+(?:Read More|CLICK HERE).*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>To view [^>]*<a[^>]*>click here.*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<div class="caption">.*\/div>)/Uis', '', $content);
		$content = preg_replace('/(<\/table><p[^>]*>(?:<b>|<i>)*+By.*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>\s*(?:<strong>|<b>|<span[^>]*>|<em>)*+\s*(?:Originally Posted On|STORY BY|PHOTOS BY|Contributing columnist|Download Free Sample|Follow Us –|Follow us on|Related resources|Hashtags|CANNOT VIEW THIS VIDEO|KEYWORDS|WHAT TO DO NEXT|READ MORE\:|SOURCE\:).*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>\s*(?:\*\*\*|This document is also available at|This content was issued|More AP|More details about the|To join the Coupang|No Class Has Been|Follow us|Attorney Advertising).*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<h3 class="section">.*\/h3>)/Uis', '', $content);
		$content = preg_replace('/(<p id="(?:gnw_attachments|PURL|caption-attachment).*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<ul id="gnw_attachments.*\/ul>)/Uis', '', $content);
		$content = preg_replace('/(<span class="xn-location.*\/span>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>(?:<strong>|<span[^>]*>|<em>|<b>|<i>|\s*)*+(?:View source version on|Related Reports\:|Additional Link\:|For more information|contact\:|Contacts\:|Media Contact|Contact Information|contact Us|Forward-Looking|Forward Looking).*\/p>(?:\s*<p.*\/p>|\s*))/Uis', '', $content);
		$content = preg_replace('/(<div class="p402_premium">\s*<p>\D{0,30}<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>\s*<a href="mailto\:.*\/p>)/Uis', '', $content);
		$content = preg_replace('/(>\s*(?:Tags\:|-\s*END\s*-|,|Image \d+|Attachment|NEWS RELEASE|Contributing writer|Newsletter|Sponsored Content|Media Contact|Press release)\s*<)/Uis', '><', $content);
		$content = preg_replace('/(>\s*(?:This press release may contain forward-looking statements|Related Image|PLEASE CLICK|click here|Contact|SOURCE)[^<]*<)/Uis', '><', $content);


		if (preg_match('/(<div class="story_detail">|<div class="entry-content">)/Uis', $content, $matches)){
			if(strlen(trim(strip_tags($content)))==0)
				return 'no content';
		}

		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2018-05-21T08:20:26+00:00
		if (preg_match('/(\d+?)-(\d+?)-(\d+?)/Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . '-' . $matches[2] . '-' . $matches[3] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');

			return $article_date;
		}


	}

}
