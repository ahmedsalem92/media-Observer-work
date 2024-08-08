		<?php
//https://maroc-diplomatique.net/
//https://maroc-diplomatique.net/?s=// to get the search link 


//https://moc.mediaobserver-me.com/tasks/
//! @author <abdullah fayad/a.fayad@mediaobserver-me.com>
protected $use_proxies = true; // Proxy 
protected $use_headless = true; // when issue in the site // by testing on site
	
	
public function pre_get_page(&$page) {   

	$this->ant->set_wait_for_load(true);

}  // with use less
protected $cloudflare_bypass = true; // to pass from browser checker
protected $allow_failed_date_override = true;
protected $stop_on_date = true;
protected $stop_on_date = false;
protected $private_cookie = false;     
protected $disable_cache_buster = true; // DISable for browser cashe cashe

	
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0';
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/64.0.3282.167 Chrome/64.0.3282.167 Safari/537.36';
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/60.0.3112.113 Chrome/60.0.3112.113 Safari/537.36';
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';
	protected $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36'; 

	// all agent user browser interface


'regexp' => '/^(.*)$/Uis', ---> //catch all of the source code 

date("Y") . '-' . date("m") . '-' . date("d") . ' 16:00:00', // speical date 

date("d",  strtotime('-1 Day'))  // yesterday date 


'process_link' => 'process_next_link'   // process link 


'ignore_terminal_stop' => true   // date will not stop if i add this line // with home page

// 
//return this link with no date

elseif($this->settings['site_section_link'] == 'https://al-lahtha.com/Articles/articles_list'){  
	$article_date = date('Y-m-d H:i:s');
} 


// is i need to return today date in only one section 

"20".$matches[3] . '-' . $matches[2] . '-' . $matches[1] . ' 16:00:00', //24 2024


 // secrch section 
/?s=


  // delete all spaces with regex
\s+?
    
// --------------------------------------------------------------------------------------------------------------------------------//
// 07 skiping zero in date
// make number 2 digits by adding 0 to left

$day = str_pad($matches[3], 2, '0', STR_PAD_LEFT);

// delete space  in arbic month 
$month = $this->arabic_month_to_number(trim($matches[2])); 

// 3 part date 
$matches[3] . '-' . $month . '-' . $matches[1] . ' 16:00:00',  
$matches[3] . '-' . $matches[1] . '-' . $matches[1] . ' 16:00:00', 

// convert month from english alphabetic to number
$month = date("m", strtotime($matches[1])); 

 // Current date     today date
$article_date = date('Y-m-d H:i:s', time());

// convert month from arabic alphabetic to number
$month = $this->arabic_month_to_number($matches[1]); 

// for time H:i:s
$article_date_today = new DateTime();  $article_date_today->format('H:i:s') 

 // hajri date
$article_date = $this->arabic_date_to_gregorian_date('Y-m-d', $year, $month, $day);

// hjri month date 
$month = $this->arabic_hijri_month_to_number($matches[2]); 

// hidden sections
private $force_hidden_sections = array(
	'http://www.albayan.ae/sports/horses' => 'فروسية',
	'http://www.albayan.ae/health/editorial' => 'كلمة العدد'
);
                

// empty headline 
protected function process_headline($headline, $article_data){
	if(strlen(trim(strip_tags($headline)))==0)
		return 'no headline';                                
	return $headline;
}
protected function process_headline($headline, $article_data) {

	if (empty($headline)) {
		return 'no headline';
	}
	else{ return $headline ;}

}

// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2018-05-21T08:20:26+00:00
		if (preg_match('/(.*)T(.*)\+/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
        
		if(strpos($this->settings['site_section_link'], '/columns')){
			return date('Y-m-d H:i:s', time());
		}

		return $article_date;

	}




	// json link 
	// site section with catch link

protected function process_next_link($link, $referer_link, $logic) { 
	$next_link = $this->settings['site_section_link'] . 'ds_'.  $link .'.json' ;
	$next_link =  str_replace('index.htm', '', $next_link);
	return $next_link ;
}  


 //add section name in next page link 
	// site section with catch link 
// when the next page link had nothing except the page number
protected function process_list1_link($link, $referer_link, $logic) {   

return $this->settings['site_section_link'] . $link ;
	
}


protected function process_content($content, $article_data) {

	$pattern = ['/(<div class=the-subtitle>مقالات ذات صلة<\/div><\/div><div class="mag-box-container clearfix"><ul.*\/ul>)/Uis',
				'/<div class="kksr-stars">.*<span class="kksr-muted">\)<\/span><\/div><\/div>/Uis',
				'/(الاطلاع على الكتاب<\/span>)/Uis',
				'/(الاطلاع على الكتاب<\/strong>)/Uis',
				'/(الكلمات المفتاحية:.*<\/p>)/Uis',
				'/(<div id=(?:"|)inline-related-post.*<\/div>)/Uis',
				'/(<div class=(?:"|)kksr-legend.*<\/div>)/Uis',
				'/(<ul class="posts-items posts.*<\/ul>)/Uis',
				'/(<strong>تحميل الدراسة<\/strong>)/Uis',
				'/(<span class=(?:"|)ctaText(?:"|)>.*<\/a>)/Uis',];
	$content = preg_replace($pattern, '', $content);

	return  $content;
}


// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
protected function process_date($article_date) {


	if ($this->settings['site_section_link'] == 'https://web-release.com/events/'){

		return date('Y-m-d H:i:s', time());
	}
	//	2018-08-29T03:23:41+00:00
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

// fix the define name in impeded it into the link
protected $paged = 1 ;
	protected function process_list1_link($link, $referer_link, $logic) {
		if(preg_match('/var category\s*= \'(.*)\'/Uis',$link,$article_link) ){
			
			define("cat1", $article_link[1]);
		}
        
		$fake_link = 'https://alnasnews.com.jo/online/wp-admin/admin-ajax.php?action=categories_load_more_post&paged=' . $this->paged++ .'&posts=10&category=' . cat1 ;
        
			if ($this->paged <= 15){
            
		return $fake_link ;
		}
		else{
            
			return false ;
		}

	}

/////////////////////////////////////////////////////////////////////////

/// fixed section link
protected function section_link($link) {
	return 'https://www.aljazeera.net/sitemap.xml?yyyy=' . date('Y') .  '&mm=' . date('m') . '&dd=' . date('d');
}


// normarl next page
protected function next_link($link) {

	return 'https://www.aljazeera.net/sitemap.xml?yyyy=' . date('Y') .  '&mm=' . date('m') . '&dd=' . date("d", strtotime("yesterday")) ;
}

// increse 30 page in one step 

$this->page+=30   



// no stoping in date
	public function prepare_home_page($section_id) {

		$this->stop_on_date = false;
		$this->logic = $this->logic_home_page;

	}


 //remove link
	protected function process_article_link($link, $referer_link, $logic) {
		if ($link == '') return false;
		return $link;
	}


// skiping articles in array
	private $exclude_articles = array(
		'http://elhiwarpress.com/index.php?option=com_content&view=article&id=9098',
		'http://elhiwarpress.com/index.php?option=com_content&view=article&id=9067',
		'http://elhiwarpress.com/index.php?option=com_content&view=article&id=9063',
		'http://elhiwarpress.com/index.php?option=com_content&view=article&id=9016',
		'http://elhiwarpress.com/index.php?option=com_content&view=article&id=8875',
		'http://elhiwarpress.com/index.php?option=com_content&view=article&id=9374',
		'http://elhiwarpress.com/index.php?option=com_content&view=article&id=9221',
		'http://elhiwarpress.com/index.php?option=com_content&view=article&id=8467'
	);
	protected function process_article_link($link, $referer_link, $logic) {
		if (in_array(rtrim($link), $this->exclude_articles)){
			return false;
		}
		return $link;
	}


// change ing the arabic number to english and calling in date function
	protected function convert2English($string){

		$newNumbers = range(0, 9);
		$arabic = array('٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١','٠');
		return str_replace($arabic, $newNumbers, $string);
	}



 //homepage 


 protected $logic_home = array(
	'list1' => array(
		0 => array(
			'type' => 'article',
			'regexp' => '/<a href="([^"]*)" class="overlay-link">/Uis',
			'append_domain' => false,
			'ignore_terminal_stop' => true
		),
		1 => array(
			'type' => 'article',
			'regexp' => '/<h2 class="entry-title"><a href="([^"]*)"/Uis',
			'append_domain' => false,
			'ignore_terminal_stop' => true
		),
		2 => array(
			'type' => 'article',
			'regexp' => '/<h4 class="mb-0"><a href="([^"]*)"/Uis',
			'append_domain' => false,
			'ignore_terminal_stop' => true
		)
	),
	'article' => array(
		'headline' => '/<h1[^>]*>(.*)<\//Uis',
		'content' => '/<div class="content">(.*)<div class="post-author[^>]*>/Uis',
		'author' => '/<h4 class="fn">.*rel="author">(.*)<\//Uis',
		'article_date' => '/<meta property="article:published_time" content="([^"]*)"/Uis'
	)
);


public function prepare_home($section_id) {

	$this->logic = $this->logic_home;

}


// exclude_sections
	protected $exclude_sections = array( ); // excluded sections array
	
	//Function to filter exclude_sections
	protected function filter_sections($section_link, $section_name, $referer_link, $logic) {
		if (in_array(rtrim($section_name), $this->exclude_sections)) {
			return '';
		}
		return $section_link;
	} 	
	
	// Function if you want to change startup link to get sections
	protected function detect_section_link($link) {
	
		return ''; // put the link of section that you want to redirect here
	}


// remove amp; if found in a link

protected function process_list1_link($link, $referer_link, $logic) {

	$link =  str_replace('amp;', '', $link);

	return $link ;
	
}



	// if link of section contain video
	protected function process_content($content, $article_data) {
	if (strpos($this->settings['site_section_link'], '/video')){
        
        return 'video' ;
	 }
		
		return $content;
	}
 
// if the $content have   youtube 
protected function process_content($content, $article_data) {
	if (strpos($content, 'youtube')){
        
		return 'video' ;
	}

	if (strpos($article_data['link'], 'www.middleeasteye.net/video')) {
		$content = ' Video';
	}
		
		return $content;
	}


protected function process_content($content, $article_data) {

		if (strpos($article_data['link'], '/%d8%a8%d8%a7%d9%84%d9%81%d9%8a%d8%af%d9%8a%d9%88')) {
			$content = $content . ' فيديو';
		}
		return $content;
	}


//stop next page on 40
	protected $page_count = 1;
	protected function process_list_press_link($link, $referer_link, $logic) { 
		$this->page_count = $this->page_count +1;
		if($this->page_count < 40){
			return '' . $this->page_count;
		}
		else{
			return false ;
		}
		
	}
	

	// process_content

protected function process_content($content, $article_data){
		
		$content = preg_replace('//Uis', '', $content);
		$content = preg_replace('//Uis', '', $content);


  /// if we don't have <div class="entry-content">  so contnet is empty 
		if (preg_match('/(<div class="entry-content">)/Uis', $content, $matches)){
			if(strlen(trim(strip_tags($content)))==0)
				return 'no content';
		}
		return $content;
	}




// editng content that contain video

	protected function process_content($content, $article_data) {

		if(strpos($article_data['link'], 'reports/')) $content = $content . ' Video';
		if(strpos($article_data['link'], 'flashes/')) $content = $content . ' Video';
		if(strpos($article_data['link'], 'videos/')) $content = $content . ' Video';
		if(strpos($article_data['link'], '/photojournalism/')) $content = $content . ' photo';
		$content = preg_replace('/<div class="post-metas">.*<i class="fab fa-telegram"><\/li>/Uis', '', $content);
		$content = preg_replace('/(?:<ul class="social-share sharelinks">|<ul class="list-inline">).*ul>/Uis', '', $content);
		$content = preg_replace('/<li><span class="meta-item">.*<\/ul>/Uis', '', $content);
		return $content;
	}


 // logic for next page 
	
	protected $logic_no_next = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp'=> '/<a class="more-link" href="([^"]*)"/Uis',							
				'append_domain' => false
		)			
	),
		'article' => array(
			'headline' => '/<span itemprop="name">(.*)<\/span>/Uis',
			'content' => '/(?:<div class="entry">|<div id="entry-content"[^<]*>)(.*)<span style="display:none"[^>]*>/Uis',
			'author' => false,
			'article_date' => '/<meta property="article:published_time" content="(.*)\/>/Uis'					
		)
	);

    public function prepare_no_next($section_id) {

		$this->logic = $this->logic_no_next;

	}



// side map 
// date and artilce link

protected function process_list2_link($link, $referer_link, $logic) {

	$date_stop = date('Y-m-d H:i:s',strtotime("-2 days"));
	$temp_link = '';
	if(preg_match('/<lastmod>(.*)<\/lastmod>/Uis', $link, $match)){
		if($match[1] > $date_stop){
			if(preg_match('/<loc>(.*)<\/loc>/Uis', $link, $matchlink)){
				$temp_link = $matchlink[1];
			}
		}
	}

	return $temp_link;
}


 // using defin to make it constant 
protected $page = 1;
	
	protected function process_list1_link($link, $referer_link) {
		if(preg_match('/load-more-categories\/(.*)\//Uis',$link,$article_link)){
		
		define("cat1", $article_link[1]);
            
		}
		
		$this->page++;
        
		$fake_link = 'https://marieclairearabia.com/load-more-categories/' . cat1 .'/'. $this->page;
		
		return $fake_link;
	}


// encode the sections // skiping the arabic langauge

protected function section_link($link) {

		$link_parts = explode('/', $link);
		$mixed_part = array_pop($link_parts);
		if (trim($mixed_part) == '') {
			$mixed_part = array_pop($link_parts);
		}
		$link_parts[] = urlencode($mixed_part);
		$result_link = implode('/', $link_parts);

		return $result_link;

	}

	// 

protected function section_link($link) {

		return $this->url_econding($link);

	}



// return incoding

	protected function section_link($link) {
		if($link == 'http://www.dbmena.com'){

			return $link;
		}else{
    
			return $this->url_econding($link);
		}
            
	}
	
	protected function section_link($link) {

		return $this->url_econding($link);

	}


// for home page 



   protected function section_link($link) {
		if($link == 'http://www.dbmena.com'){

			return $link;
		}else{
    
			return $this->url_econding($link);
		}
            
	}
////  when i found video   return nothing


protected function process_article_link($link, $referer_link, $logic) {

		if (strpos($link, 'breaking#')) {
			return false;
		}
		return $link;
	}




	protected function process_article_date($article_date, $article_dat){

		if (strpos($article_dat['link'], '/instruction/')) {
			return date('Y-m-d H:i:s', time());
		}
		return $article_date;

	}

 // encode article link
    protected function article_link($link) {

		$parts = parse_url($link);
		$path = explode('/', $parts['path']);

		foreach($path as &$param) {
			$param = urlencode($param);
		}
		$parts['path'] = implode('/', $path);

		return unparse_url($parts);

	}


protected function process_article_link($link, $referer_link, $logic) {

		$link_path = array();
		$parts = parse_url($link);
		if (isset($parts['path']) && trim($parts['path'], '/') != '') {
			$path = explode('/', $parts['path']);

			while(($path_crumb = array_shift($path)) !== null) {
				if (strlen(trim($path_crumb))) {
					$link_path[] = urlencode($path_crumb);
				}
			}
			$parts['path'] = '/' . implode('/', $link_path);

			return unparse_url($parts);
		}
		else {
			return '';
		}

	}




//*********************************************************************************************************************************//

//  06/06/2018
		if (preg_match('/(\d+)\/(\d+)\/(\d+?)/Uis', $article_date, $matches)) {
			
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $matches[2] . '-' . $matches[1] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		return $article_date;


//*************************************************************************************************************************************///


// when we have a date but we don't have a  the day 

	protected function process_date($article_date) {


		if (preg_match('/(\w+)\s*(\d+?)/Uis', $article_date, $matches)) {

			$month = date("m", strtotime($matches[1]));
			$article_date_today = new DateTime();
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				date("Y") . '-' . date("m") . '-' . date("d") . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		return $article_date;
	}






// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2018-05-21T08:20:26+00:00
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





// 2018-05-06T00:17:24+00:00
		if (preg_match('/(.*)T/Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' 16:00:00', 
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		return $article_date;


//---------------------------------------------------------------------------------------------------------------------------------------//
 //June 30, 2018
		if (preg_match('/(\w+) (\d+), (\d+?)/Uis', $article_date, $matches)) {

			$month = date("m", strtotime($matches[1]));
			$day = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
			$article_date_today = new DateTime(); 
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $month . '-' . $day . ' ' . $article_date_today->format('H:i:s'),
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
//----------------------------------------------------------------------------------------------------------------//

// if article date from with the link

	private $date_article;

	protected function process_article_link($link, $referer_link, $logic) {

		if(
			preg_match('/<h3 class="loop-title">\s*<a href="([^"]*)"/Uis',$link,$article_link) &&
			preg_match('/<p class="meta">(.*)\/\//Uis',$link,$matche)
		){
			$link = $article_link[1];
			$this->date_article = $matche[1];
		

		return $link;

	}

	protected function process_article_date($article_date,$article_data){

		return $this->date_article;
	}

//-----------------------------------------------------------------------------------------------------------------------------//


//-------------------------------------------------------------------------------------------------------------------//

// &nbsp;07-12-2018 06:30 صباحًا
		if (preg_match('/(\d+)-(\d+)-(\d+) (\d+):(\d+) (\W)/Uis', $article_date, $matches)) {
			
			if($matches[6] == 'م'){
				$matches[4] += 12;
			}
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $matches[2] . '-' . $matches[1] . ' ' . $matches[4] . ':' . $matches[5] . ':00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		return $article_date;
//----------------------------------------------------------------------------------------------------------------------------------//


		
//-------------------------------------------------------------------------------------------------------------------------------------------//
//الأربعاء 01 أغسطس 2018 الساعة 10:15
if (preg_match('/(\d+) (\W+) (\d+?).*(\d+):(\d+?)/Uis', $article_date, $matches)) {

			$month = $this->arabic_month_to_number($matches[2]);
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $month . '-' . $matches[1] . ' ' . $matches[4] . ':' . $matches[5] . ':00',
				new DateTimeZone($this->site_timezone)
			);

			$article_date = $article_date_obj->format('Y-m-d H:i:s');

		}

		return $article_date;
		

//------------------------------------------------------------------------------------------------------------------------------//
//  18 يوليو 2018 01:06م
		if (preg_match('/(\d+) (\W+) (\d+?) (\d+):(\d+?)\s*(\W)/Uis', $article_date, $matches)) {

			$month = $this->arabic_month_to_number($matches[2]);
			if($matches[6] == 'م'){
				$matches[4] += 12;
			}
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $month . '-' . $matches[1] . ' ' . $matches[4] . ':' . $matches[5] . ':00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
//-------------------------------------------------------------------------------------------------------------------------//
// 15 May 2014
		if (preg_match('/(\d+) (\w+) (\d+?)/Uis', $article_date, $matches))
		{

			$month = date("m", strtotime($matches[2]));
			$day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
			$article_date_today = new DateTime(); 
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $month . '-' . $day . ' ' . $article_date_today->format('H:i:s'),
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
//------------------------------------------------------------------------------------------------------------------------------------//
//  1437-03-13 05:44 مساءً
		if (preg_match('/(\d+)-(\d+)-(\d+) (\d+):(\d+) (\W)/Uis', $article_date, $matches)) {
			
			if($matches[6] == 'م'){
				$matches[4] += 12;
			}
			$date = $this->arabic_date_to_gregorian_date('Y-m-d', $matches[3], $matches[2], $matches[1]);
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$date . ' ' . $matches[4] . ':' . $matches[5] . ':00', 
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		
//=============================================================================================================================//

    //to catch something from source_code
    
    if(
			preg_match('/<div class="fc_pag" id=".*-.*-(.*)">/Uis',$link,$article_link) &&
			preg_match('/<div class="fc_pag" id=".*-(.*)-.*">/Uis',$link,$matche)
		){
			$link = $article_link[1];
			$link2 = $matche[1];
		}
    //////////////////////////////////////////////////////////////////////////////////////////////




// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//منذ: 2018-10-13 11:28:11
		if (preg_match('/(\d+?)-(\d+?)-(\d+?) (\d+?):(\d+?):(\d+?)/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . '-'. $matches[2] . '-' . $matches[3] . ' ' . $matches[4] . ':' . $matches[5]. ':' . $matches[6],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		//منذ ساعه
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
		else if(preg_match('/(دقيقة)/Uis', $article_date, $matches) || preg_match('/(دقيقه)/Uis', $article_date, $matches) || preg_match('/(minute)/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-1 Minute'));

		}

		return $article_date;
	}

}





/***************************************************************************************************************************/




// process the date of the article, return in YYYY-MM-DD HH:ii:ss format

/// if the date of articale are same and have a unnormal arabic word or regualer
	protected function process_date($article_date) {


		$article_date = $this->interval_to_date($article_date);
        
		return  $article_date ;

	}
    
	function interval_to_date($article_date){


		//3 years || منذ 3 سنه || منذ 3 سنة || منذ 3 سنوات || منذ 3 سنين
		if(preg_match('/(\d+) سنه/Uis', $article_date, $matches) || preg_match('/(\d+?) années/Uis', $article_date, $matches) || preg_match('/(\d+) سنوات/Uis', $article_date, $matches) || preg_match('/(\d+) سنين/Uis', $article_date, $matches) || preg_match('/(\d+) years/Uis', $article_date, $matches))
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
		else if(preg_match('/(\d+) شهر/Uis', $article_date, $matches) || preg_match('/(\d+?) mois/Uis', $article_date, $matches) || preg_match('/(\d+)months/Uis', $article_date, $matches))
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
		else if(preg_match('/(\d+) يوم/Uis', $article_date, $matches) || preg_match('/(\d+?) jours/Uis', $article_date, $matches) || preg_match('/(\d+) ايام/Uis', $article_date, $matches) || preg_match('/(\d+) days/Uis', $article_date, $matches))
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
		else if(preg_match('/(\d+) ساعة/Uis', $article_date, $matches) || preg_match('/(\d+?) heures/Uis', $article_date, $matches) || preg_match('/(\d+) ساعات/Uis', $article_date, $matches) || preg_match('/(\d+) hours/Uis', $article_date, $matches))
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
		else if(preg_match('/(دقيقة)/Uis', $article_date, $matches) || preg_match('/(دقيقه)/Uis', $article_date, $matches) || preg_match('/(minute)/Uis', $article_date, $matches))
		{

			$article_date = date("Y-m-d H:i:s",  strtotime('-1 Minute'));

		}

		return $article_date;
	}

}





///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// arbic date 

// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//السبت ٢٧  أبريل  ٢٠١٩
		if (preg_match('/\S+? (\W+?) \s+?(\W+?)\s+? (\W+?) -/Uis', $article_date, $matches)) {
			$year = $this->number_arabic_to_english($matches[3]);
			$month = $this->arabic_month_to_number($matches[2]);
			$day = $this->number_arabic_to_english($matches[1]);
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$year . '-' . $month . '-' . $day . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;

	}
	
	function number_arabic_to_english($str) {
		$arabic_eastern = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
		$arabic_western = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
		return str_replace($arabic_eastern, $arabic_western, $str);
        
	}

// franch date 

// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//Rédigé par S.J le Dimanche 12 Juillet 2020
		if (preg_match('/(\d+?) (.*) (\d+?)/Uis', $article_date, $matches)) {
			$month = $this->convert(trim($matches[2]));
			$day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $month . '-' . $day . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		return $article_date;

	}

	function convert($string) {
		$franch = array('Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre' ,'Novembre','Décembre');
		$english = array('1', '2', '3', '4', '5', '6', '7', '8', '9', '10' ,'11' ,'12');
		$english_month = str_replace($franch, $english, $string);
		return $english_month;
	}



/*----------------------------------------------------------------------------------------------------------------------------------*/


// sites the UTF-8 if it not found
protected function filter_sections($section_link, &$section_name, $referer_link, $logic) {

		// exclude these sections
		if (in_array(trim($section_link), $this->exclude_sections)) {
			return '';
		}
		
		$section_link = iconv('Windows-1256 ', 'UTF-8//TRANSLIT//IGNORE', $section_link);


		return $section_link;

	}

	protected function process_headline($headline , $article_data){

		return iconv('Windows-1256 ', 'UTF-8//TRANSLIT//IGNORE', $headline);
	}

	protected function process_content($content , $article_data){

		return iconv('Windows-1256 ', 'UTF-8//TRANSLIT//IGNORE', $content);
	}

	protected function process_article_date($article_date , $article_data){

		return iconv('Windows-1256 ', 'UTF-8//TRANSLIT//IGNORE', $article_date);
	}

    
    
    
	protected function pre_get_page(&$page) {

		$this->ant->set_custom_headers(
			array(
				'Content-Type: text/html; charset=UTF-8',
				'Accept-Encoding: gzip, deflate'
			)
		);

	}

//     



// ajex converter link
	protected function process_list1_link($regexp_result, $referer_link, $logic_type) {

		$result = '';
		$params = array();

		foreach($this->ajax_params_ids as $name => $id) {
			if (preg_match('/<input[^>]*id="' . $id . '"[^>]*value="([^"]*)"[^>]*>/Uis', $regexp_result, $matches) ||
					preg_match('/\$\("#' . $id . '"\)\.val\("([^"]*)"\);/Uis', $regexp_result, $matches)) {
				$params[$name] = $matches[1];
				$this->ajax_params[$name] = $matches[1];
			}
			elseif ($name === 'StartIndex' && isset($this->ajax_params['EndIndex'])) {
				$params[$name] = $this->ajax_params['EndIndex'] + 1;
			}
			elseif ($name === 'EndIndex' && isset($params['StartIndex'])) {
				$params[$name] = $params['StartIndex'] + 4;
			}
			elseif (isset($this->ajax_params[$name])) {
				$params[$name] = $this->ajax_params[$name];
			}
			else {
				break;
			}
		}

		if (!empty($params) && $this->current_page < $this->max_page) {
			// adding dummy GET param to trick crawler
			$result = $this->combine_link($this->ajax_url . '?p=' . $this->current_page++);
			$this->ajax_params[$result] = $params;
		}

		return $result;

	}


                 
                // process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//نشر بتاريخ: السبت، 10 تشرين2/نوفمبر 2018 22:56
		if (preg_match('/(\d+?).*\/(\W+?) (\d{4}) (\d+?):(\d+?)/Uis', $article_date, $matches)) {
			$month = $this->arabic_month_to_number($matches[2]);
			$day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-'. $month . '-' . $day . ' ' . $matches[4] . ':' . $matches[5] . ':00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		//نشر بتاريخ: الإثنين، 30 تشرين2/نوفمبر -0001 00:00
		else if (preg_match('/(\d+?).*\/(\W+?) -(\d{4}) (\d+?):(\d+?)/Uis', $article_date, $matches)) {
			$month = $this->arabic_month_to_number($matches[2]);
			$day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-'. $month . '-' . $day . ' ' . $matches[4] . ':' . $matches[5] . ':00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		elseif($this->settings['site_section_link'] == 'https://www.annasronline.com/index.php/2014-09-30-11-05-07/2014-08-24-14-17-03'){

			$article_date = date('Y-m-d H:i:s');
		}

		return $article_date;

	}



	// start from the first link not from the last link 

	protected function process_article_link($link, $referer_link, $logic)
	{
		$temp_link = '';
		
		if (empty($this->links)) {
			$result = $this->ant->get($referer_link);
			if (preg_match_all('/<loc>(.*)<\/loc>/Uis', $result, $matches)) {
				$this->links = $matches[0];
				$this->array_index = 0; // Start from the first link
			}
		}
	
		// Check if the current index is within bounds
		if ($this->array_index < sizeof($this->links)) {
			$temp_link = str_replace('<loc>', '', $this->links[$this->array_index]);
			$temp_link = str_replace('</loc>', '', $temp_link);
			
			// Increment array_index for the next call
			$this->array_index++;
			
			return $temp_link;
		}
	
		return '';
	}


// Returned date format :  2024-08-05T15:42:19.697-04:00
	protected function process_date($article_date) {
		// Example input: 2024-08-05T15:42:19.697-04:00
		if (preg_match('/^(.*)T(.*)\-(\d{2}:\d{2})$/', $article_date, $matches)) {
			// Convert the timezone offset into a DateTimeZone object
			$date_time = DateTime::createFromFormat(
				'Y-m-d\TH:i:s.uP', // 'u' for microseconds and 'P' for timezone with offset
				$article_date
			);
	
			if ($date_time) {
				$article_date = $date_time->format('Y-m-d H:i:s');
			}
		}
		return $article_date;
	}

	// skip section by link
	protected function filter_sections($section_link, $section_name, $referer_link, $logic)
	{
		if (in_array(trim($section_link), $this->exclude_sections)) {
			return '';
		}
		return $section_link;
	}


	/// prosses date by format  August 6, 2024

	protected function process_date($article_date) {
		// August 6, 2024
		if (preg_match('/(\w+) (\d+), (\d+)/', $article_date, $matches)) {
			$month = date('m', strtotime($matches[1]));
			$day = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
			$year = $matches[3];
			
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				"$year-$month-$day 00:00:00"
			);
			
			if ($article_date_obj) {
				$article_date = $article_date_obj->format('Y-m-d H:i:s');
			}
		}
		
		return $article_date;
	}


	/// to make loop for link number 

	'list1' => array(
		0 => array(
			'type' => 'list1',
			'regexp' => '/^(.*)$/Uis',
			'append_domain' => false,
			'process_link' => 'process_list_press_link'
		),
	)

	protected $date;
	protected $page_count = 1;
	protected function process_list_press_link($link, $referer_link, $logic)
	{
		if ($this->page_count) { // http://www.ccaonline.cn/page/2?s&amp;type
			$this->page_count++;
			return 'http://www.ccaonline.cn/page/' . $this->page_count . '?s&amp;type';
		} else {
			return false;
		}
	}


	/// new prosses_date 2024-08-06T10:49:27.589Z
	protected function process_date($article_date) {
		if (preg_match('/(.*)T(.*)\.(\d+)(Z)/i', $article_date, $matches)) {
			$formatted_date = $matches[1] . ' ' . $matches[2];
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$formatted_date,
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
	
		return $article_date;
	}


	/// select the time from the link of sitemap

	protected function process_article_link($link, $referer_link, $logic) {

		if(
			preg_match('/<loc>(.*)<\/loc>/Uis',$link,$article_link) &&
			preg_match('/<lastmod>(.*)<\/lastmod>/Uis',$link,$matches)

		){

			$link = $article_link[1];
			
			$this->date_article = $matches[1];
		}

		
		
		return $link;

	}

	protected function process_article_date($article_date,$article_data){
		return $this->date_article;
	}



	// another process_date 

	protected function process_date($article_date) {


		if (preg_match('/(.*)T/Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1]. ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		return $article_date;
	}


	// another process_date 

	protected function process_date($article_date) {

		if (preg_match('/\s*(\d{4}-\d{2}-\d{2})T(\d{2}:\d{2}:\d{2})(?:\.|Z)/Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' ' . $matches[2],
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;

	}