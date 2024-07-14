<?php

class theinteriorjournalcom extends plugin_base {

	// ANT settings
	protected $ant_precision = 8;
	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = true;

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
			'headline' => '/<h2 class="headline">(.*)<\/h2>/Uis',
			'content' => '/<div id="article_info">(.*)<div class="instory_widget">/Uis',
			'author' => false,
			'article_date' => '/(?:<meta property="article:published_time" content="|<span itemprop="datePublished dateModified">|<div title="[^<]*>)([^"]*)(?:"|<\/span>|<\/div>)|"date":"([^"]*)","(?:feedCode|fileName|headline)|,"feedCode":"[^"]*","date":"([^"]*)"/Uis'
		)
	);
	protected $logic_press = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false,
				'process_link' => 'process_list_press_link'

			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false,
				'process_link' => 'process_press_link'
			)
		),
		'article' => array(
			'headline' => '/<body[^>]*>.*(?:<h1 class="xn-hedline">|<div class="headline-col "><h1>)(.*)<\/h/Uis',
			'content' => '/(<div class="entry-content">.*)(?:<\/main|<ul class="sidebar|>\s*Continued…\.|>\s*#\s*#\s*#\s*<\/)/Uis',
			'author' => false,
			'article_date' => '/(?:<meta property="article:published_time" content="|<span itemprop="datePublished dateModified">|<div title="[^<]*>)([^"]*)(?:"|<\/span>|<\/div>)|"date":"([^"]*)","(?:feedCode|fileName|headline)|,"feedCode":"[^"]*","date":"([^"]*)"/Uis'
		)

	);

	protected $page_count = 1;
	protected function process_list_press_link($link, $referer_link, $logic) {
		$this->page_count = $this->page_count +1;
		if($this->page_count < 40){
			return 'https://smb.theinteriorjournal.com/?&page=' . $this->page_count;
		}
		else{
			return false ;
		}

	}



	protected function process_press_link($link, $referer_link, $logic) {
		return 'https://smb.theinteriorjournal.com' . $link;
	}
	public function prepare_press($section_id) {

		$this->logic = $this->logic_press;

	}


	protected function process_list1_link($link, $referer_link, $logic) {

		$temp_link =''; // https://www.theinteriorjournal.com/wp-sitemap-posts-post-5.xml
		if(preg_match_all('/<loc>(https:\/\/www\.theinteriorjournal\.com\/wp-sitemap-posts-post-\d*?\.xml)<\/loc>/Uis', $link, $matches)){
			$temp_link = $matches[0][sizeof($matches[0]) - 1];
			$temp_link = str_replace('<loc>' , '' , $temp_link);
			$temp_link = str_replace('</loc>' , '' , $temp_link);
		}

		return $temp_link;
	}


	private $links = array();
	private $array_index = 0 ;

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
		if($this->array_index > 0 and isset($this->links[$this->array_index]) ){
			$temp_link = str_replace('<loc>' , '' , $this->links[$this->array_index]);
			$temp_link = str_replace('</loc>' , '' , $temp_link);
			return $temp_link;
		}

		return '';

	}

	protected function process_content($content, $article_data) {
		$content = preg_replace('/<h2 class="headline">(.*)<\/h2>/Uis', '', $content);
		$content = preg_replace('/<h2 class="headline">(.*)<\/h2>/Uis', '', $content);
		$content = preg_replace('/<p class="pubStamp">(.*)<\/p>/Uis', '', $content);
		$content = preg_replace('/<div class="gallery_group.*\/div>/Uis', '', $content);
		$content = preg_replace('/<div id="article_info">.*\/div>/Uis', '', $content);
		$content = preg_replace('/(<form.*<\/form>)/Uis', '', $content);
		$content = preg_replace('/(<ins.*<\/ins>)/Uis', '', $content);
		$content = preg_replace('/(<script.*<\/script>)/Uis', '', $content);
		$content = preg_replace('/(<p>\s*<span class="xn-person.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p id="PURL".*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>\s*<em>Originally Posted On.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>\s*<a[^>]*>\s*<strong>CLICK HERE.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(>This press release may contain forward-looking statements[^<]*<)/Uis', '><', $content);
		$content = preg_replace('/(<p[^>]*>\s*(?:All of these articles can|This content is published on|newsroom\:|To learn more about this|For more information|To learn more about).*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>SOURCE [^<]*<)/Uis', '<', $content);
		$content = preg_replace('/(>Media Contact<)/Uis', '><', $content);
		$content = preg_replace('/(<div class="entry-content">.*<div class="xn-content">)/Uis', '<div class="entry-content">', $content);
		$content = preg_replace('/(<ul>.*<\/ul>)/Uis', '', $content);
		$content = preg_replace('/(<h6[^>]*>\s*<img.*<\/h6>)/Uis', '', $content);
		$content = preg_replace('/(>\s*Contact[^<]*<)/Uis', '><', $content);
		$content = preg_replace('/(>\s*SOURCE[^<]*<)/Uis', '><', $content);
		$content = preg_replace('/(<p>\*\*\*.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<div class=\'widget\'.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<p>The post <a.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p><strong>Author Bio<\/strong><\/p>\s*<p>.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p class="tags">.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(>Sponsored Content<)/Uis', '><', $content);
		$content = preg_replace('/(>\s*(?:Tags:|Read more|\|)\s*<)/Uis', '><', $content);
		//---------------------
		$content = preg_replace('/(\[\…\])/Uis', '', $content);
		$content = preg_replace('/(>\s*-\s*Written by[^<]*<)/Uis', '><', $content);
		$content = preg_replace('/(>\s*-\s*END\s*-\s*<)/Uis', '><', $content);
		$content = preg_replace('/(>\s*,\s*<)/Uis', '><', $content);
		$content = preg_replace('/(>\s*Image \d+\s*<)/Uis', '><', $content);
		$content = preg_replace('/(<pre>.*\/pre>)/Uis', '', $content);
		$content = preg_replace('/(>\s*The following files are available for download[^<]*<\/p>\s*(?:<div>|\s*)*+\s*<table.*\/table>)/Uis', '>', $content);
		$content = preg_replace('/(<a[^>]*rel="category tag"[^>]*>.*\/a>)/Uis', '', $content);
		$content = preg_replace('/(<a[^>]*>\s*(?:YouTube|LinkedIn|Twitter)\s*<\/a>)/Uis', '', $content);
		$content = preg_replace('/(>\s*Related Image[^<]*<)/Uis', '><', $content);
		$content = preg_replace('/(>\s*NEWS RELEASE\s*<)/Uis', '><', $content);
		$content = preg_replace('/(>\s*Attachment\s*<)/Uis', '><', $content);
		$content = preg_replace('/(>\s*PLEASE CLICK[^>]*<)/Uis', '><', $content);
		$content = preg_replace('/(>For more information:\s*<a.*\/a>)/Uis', '>', $content);
		$content = preg_replace('/(>\s*Contributing writer\s*<)/Uis', '><', $content);
		$content = preg_replace('/(>\s*Newsletter\s*<)/Uis', '><', $content);
		$content = preg_replace('/(<div class="caption">.*\/div>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>\s*<a[^>]*>\s*Click here.*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p id="gnw_attachments.*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>\s*<strong>\s*(?:CANNOT VIEW THIS VIDEO|KEYWORDS|WHAT TO DO NEXT|READ MORE\:|SOURCE\:).*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>\s*(?:This content was issued|More AP|More details about the|To join the Coupang|No Class Has Been|Follow us|Attorney Advertising).*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<h3 class="section">.*\/h3>)/Uis', '', $content);
		$content = preg_replace('/(<p[^>]*>\s*About ClaimsFiler<\/p>\s*<p[^>]*>.*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<ul id="gnw_attachments.*\/ul>)/Uis', '', $content);
		$content = preg_replace('/(<span class="xn-location.*\/span>)/Uis', '', $content);
		$content = preg_replace('/(>\s*About ClaimsFiler<\/div>\s*<p>ClaimsFiler has a single.*\/p>)/Uis', '>', $content);
		$content = preg_replace('/(<p[^>]*>(?:<strong>|<span>|<em>|<b>)Contact Information:.*\/p>\s*<p.*\/p>)/Uis', '', $content);

		if (preg_match('/(<div class="story_detail">|<div class="entry-content">)/Uis', $content, $matches)){
			if(strlen(trim(strip_tags($content)))==0)
				return 'no content';
		}
		//---------------------

		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2018-05-21T08:20:26+00:00
		if (preg_match('/(\d{4}-\d{1,2}-\d{1,2})T(.*)(?:\+|Z|\.|\")/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' '.$matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		elseif (preg_match('/(\w+?) (\d+?), (\d+?)/Uis', $article_date, $matches)) {
			$month = date("m", strtotime($matches[1]));
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $month . '-' . $matches[2] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		else{
			$article_date = $this->interval_to_date($article_date);
		}

		return $article_date;
	}

	function interval_to_date($article_date){


		//3 years || منذ 3 سنه || منذ 3 سنة || منذ 3 سنوات || منذ 3 سنين
		if(preg_match('/(\d+) سنه/Uis', $article_date, $matches) || preg_match('/(\d+) سنة/Uis', $article_date, $matches) || preg_match('/(\d+) سنوات/Uis', $article_date, $matches) || preg_match('/(\d+) سنين/Uis', $article_date, $matches) || preg_match('/(\d+) years/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-'. $matches[1] . ' year'));

		}
		// منذ سنتان || منذ سنتين
		else if(preg_match('/(سنتان)/Uis', $article_date, $matches) || preg_match('/(سنتين)/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-2 year'));

		}
		// year || منذ سنه || منذ سنة
		else if(preg_match('/(سنه)/Uis', $article_date, $matches) || preg_match('/(سنة)/Uis', $article_date, $matches) || preg_match('/(year)/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-1 year'));

		}
		// 3 months || منذ 3 شهر || منذ 3 شهور
		else if(preg_match('/(\d+) شهر/Uis', $article_date, $matches) || preg_match('/(\d+) شهور/Uis', $article_date, $matches) || preg_match('/(\d+)months/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-'. $matches[1] . ' month'));

		}
		// منذ شهرين || منذ شهران
		else if(preg_match('/(شهرين)/Uis', $article_date, $matches) || preg_match('/(شهران)/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-2 month'));

		}
		// month ago || منذ شهر
		else if(preg_match('/(شهر)/Uis', $article_date, $matches) || preg_match('/(month)/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-1 month'));

		}
		// 3 weeks || منذ 3 اسبوع || منذ 3 أسبوع || منذ 3 اسابيع || منذ 3 أسابيع
		else if(preg_match('/(\d+) اسبوع/Uis', $article_date, $matches) || preg_match('/(\d+) أسبوع/Uis', $article_date, $matches) || preg_match('/(\d+) اسابيع/Uis', $article_date, $matches) || preg_match('/(\d+) أسابيع/Uis', $article_date, $matches) || preg_match('/(\d+) weeks/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-'. $matches[1] . ' Week'));

		}
		// منذ اسبوعين || منذ أسبوعين || منذ اسبوعان || منذ أسبوعان
		else if(preg_match('/(اسبوعان)/Uis', $article_date, $matches) || preg_match('/(اسبوعين)/Uis', $article_date, $matches) || preg_match('/(أسبوعان)/Uis', $article_date, $matches) || preg_match('/(أسبوعين)/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-2 Week'));

		}
		// week || منذ اسبوع || منذ أسبوع
		else if(preg_match('/(اسبوع)/Uis', $article_date, $matches) || preg_match('/(أسبوع)/Uis', $article_date, $matches) || preg_match('/(week)/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-1 Week'));

		}
		// 3 days || منذ 3 يوم || منذ 3 ايام || منذ 3 أيام
		else if(preg_match('/(\d+) يوم/Uis', $article_date, $matches) || preg_match('/(\d+) أيام/Uis', $article_date, $matches) || preg_match('/(\d+) ايام/Uis', $article_date, $matches) || preg_match('/(\d+) days/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-'. $matches[1] . ' Day'));

		}
		// منذ يومين || منذ يومان
		else if(preg_match('/(يومين)/Uis', $article_date, $matches) || preg_match('/(يومان)/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-2 Day'));

		}
		// day || منذ يوم
		else if(preg_match('/(يوم)/Uis', $article_date, $matches) || preg_match('/(day)/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-1 Day'));

		}
		// 4 hours || منذ 4 ساعة || منذ 4 ساعه || منذ 4 ساعات
		else if(preg_match('/(\d+) ساعة/Uis', $article_date, $matches) || preg_match('/(\d+) ساعه/Uis', $article_date, $matches) || preg_match('/(\d+) ساعات/Uis', $article_date, $matches) || preg_match('/(\d+) hours/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-'. $matches[1] . ' Hour'));

		}
		// منذ ساعتين || منذ ساعتان
		else if(preg_match('/(ساعتين)/Uis', $article_date, $matches) || preg_match('/(ساعتان)/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-2 Hour'));

		}
		// hour || منذ ساعة || منذ ساعه
		else if(preg_match('/(ساعة)/Uis', $article_date, $matches) || preg_match('/(ساعه)/Uis', $article_date, $matches) || preg_match('/(hour)/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-1 Hour'));

		}
		// 24 minutes || منذ 24 دقيقة || منذ 24 دقيقه || منذ 24 دقائق
		else if(preg_match('/(\d+) دقيقة/Uis', $article_date, $matches) || preg_match('/(\d+) دقيقه/Uis', $article_date, $matches) || preg_match('/(\d+) دقائق/Uis', $article_date, $matches) || preg_match('/(\d+) minutes/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-'. $matches[1] . ' Minute'));

		}
		// منذ دقيقتين || منذ دقيقتان
		else if(preg_match('/(دقيقتين)/Uis', $article_date, $matches) || preg_match('/(دقيقتان)/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-2 Minute'));

		}
		// minute || منذ دقيقة || منذ دقيقه
		else if(preg_match('/(دقيقة)/Uis', $article_date, $matches) || preg_match('/(Today)/Uis', $article_date, $matches)  || preg_match('/(دقيقه)/Uis', $article_date, $matches) || preg_match('/(minute)/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-1 Minute'));

		}

		return $article_date;
	}
}
