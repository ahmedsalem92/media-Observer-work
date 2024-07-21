<?php

/**
* INSTRUCTIONS:
* 1. replace <plugin_template> with the plugin class name which should be the same as the filename (<plugin_template>.class.php)
* 		these are not very important as they will be overriden by default
* 2. configure process logic (when to stop) with stop_on_article_found, stop_on_date, etc
* 3. define the sections array and set the logic members and expressions and then the "section" fixed type expressions (sections are the
* 		different areas/categories of articles in a website structure)
* 4. define the logic array definition and set the "logic" members and expressions and then the "article" fixed type expressions
* 5. define the process_date function to return a proper article date
* 6. remove any unnecessary comments/documentation, please keep only comments that are relevant to the specific code written
*
*
* A. USABLE (and useful) PROPERTIES:
* 	settings      - array containing all crawl settings
*		@member running                string  The current type of crawl: sections for section detection, empty or crawl for article crawl
*		@member id_site                int     The current site ID
*		@member site_link              string  The current site base link
*		@member id_site_section        int     The current site section ID or -1 for section detection
*		@member site_section_link      string  The start link for the crawl
*		@member site_section_link_type string  The start link logic type (usually the first defined logic type in sections or logic)
*		@member logic                  array   The current crawl logic chain
*	current_link  - array containing the link for which result processing is currently done
*		@member link string The current URL for which processing is done
*		@member type string The current URL logic type
*	section_links - array containing the detected section links, array keys are the links, array values are the section names
*	ant           - the page request class
*		@method set_post(array $parameters)
*					instructs the page request to perform a POST request with the given parameters
*					parameters array must contain as key the parameter name and as value the parameter value
*					NOTE: must be set before the page is requested, typically in the pre_get_page method
*		@method unset_post()
*					instructs the page request class to return to regular GET requests so that the next request is not a POT
*					NOTE: must be called always when set_post is used in the post_get_page method
*		@method set_cookie_jar(string $cookie)
*					sets a custom cookie to the absolute path provided as parameter
*					NOTE: use only if you require the cookie in a specific file/path, otherwise you the private_cookie setting
*		@method set_cookie_session(bool $session = true)
*					instructs the page request class to ignore all previous set cookies and start a new cookie session (when
*					parameter is true, when false previous cookies are not ignored)
*		@method get_effective_url()
*					returns the final URL for which the page request returns results
*					NOTE: useful when the URL requested performs redirects or rewriting
*		@method set_log(string $log_file)
*					sets the log file for the page request class, absolute path
*					NOTE: only use when specific log files are needed, otherwise the crawler sets the appropriate logs
*		@method set_agent(string $agent)
*					sets the user agent for the page request class
*					NOTE: only use when specific user agent is needed for a specific request,
*					otherwise the crawler sets the agents as defined in the plugin
*		@method set_proxy(string $proxy)
*					instructs the page request class to use a proxy for the requests
*					NOTE: if a website requires proxy usage, set this either in the constructor or any functions that are called
*					before collecting begins: pre_process, pre_detect_sections, pre_collect, and so on
*
*
* B. USABLE (and useful) METHODS, FINAL (cannot be overriden):
* 		arabic_month_to_number(string $text)          - converts a textual arabic month to the month number
* 		get_sections()                                - returns an array with the sections found (keys are the links, values are the names)
* 		log(string $text)                             - adds a message to the log
* 		remap_article(string $link, string $new_link) - moves an article from one link to another (use in pre_article when captured article link is different than actual link)
* 		combine_link(string $link)                    - returns link with domain and scheme added to the sent link
* 		get_process_data()                            - returns an array with data about the crawl process (pages parsed, article ids created, etc)
* 		get_log_messages()                            - returns the array with all log messages
* 		get_sections_logic()                          - returns the sections logic definition
* 		get_crawl_logic()                             - returns the article crawling logic definition
* 		set_log(string $path)                         - sets the path to the log file, absolute path
* 		set_agent(string $agent)                      - sets the crawler agent
*
*
* C. INTERNAL unusable properties, DO NOT DEFINE OR USE:
* 			db
* 			keywords_db
* 			article_keywords_db
* 			process_data
* 			log_messages
* 			on_demand
* 			arabic_months_extended
* 			collection_links
* 			collection_links_only
* 			article_links_only
* 			articles
* 			article_ids
* 			keywords
* 			stop_collecting
* 			processes
* 			child
* 			mode
* 			development_run
* 			site_id
* 			site
* 			sites_db
* 			site_sections_db
*
*
* D. INTERNAL unusable methods, DO NOT DEFINE OR USE:
* 			set_on_demand
* 			set_dryrun
* 			set_development_run
* 			set_child_crawl
* 			process
* 			process_match_data
* 			collect
* 			xml_content
* 			article
* 			add
* 			add_article_keywords
* 			get_match_keywords
* 			get_page
* 			json_extract
* 			xml_extract
* 			match
* 			json_content
* 			xml_content
* 			match_content
* 			cloudflare_resolve
* 			detect_sections
* 			prepare
*
*/

class plugin_template extends plugin_base {

	/**
	* SYSTEM settings
	*/

	/**
	* @var int ant_precision Maximum timer between sequential requests to website source (in seconds)
	* This will be a random number between 0 and the defined value
	* NOTE: default value is 2, define this only if you require a different value, though 2 should be
	* good for the majority of websites, others may be very slow so raising this value may be a good idea
	* @access protected
	*/
	protected $ant_precision = 2;

	/**
	* @var string log_file Log filename for the plugin
	* NOTE: default value is the class name followed by '<plugin_template>.log', define this only if you
	* require a different log file
	* @access protected
	*/
	protected $log_file = '';

	/**
	* @var string agent User agent identifier for the requests made to the website source
	* NOTE: default value is defined by the USE_AGENT constant, define this only if you have issues with the
	* plugin accessing the website source; when defined use a full valid UA string, for example:
	* 		Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0
	* @access protected
	*/
	protected $agent = USE_AGENT;

	/**
	* @var bool private_cookie Use private website cookie
	* NOTE: default value is false, but in some cases it will be required that the plugin uses an individual
	* cookie (vs the global one) so that the site works properly
	* @access protected
	*/
	protected $private_cookie = false;

	/**
	* @var string cookie_file Cookie filename where to store cookie data from the website
	* NOTE: default value is null, which when private_cookie is false will determine usage of global cookie,
	* and when private_cookie is true will populate the value as '<plugin_template>.cookie'
	* @attention DO NOT DEFINE THIS, IT WILL BE OVERRIDDEN IN THE PARENTS
	* @access protected
	*/
	protected $cookie_file = null;

	/**
	* @var bool use_proxies Use proxies to access the site links
	* NOTE: default value is false, but in cases where the site is blocking access to MOC IPs
	* it can be defined to true to make use of proxies to bypass the IP restriction
	* @access protected
	*/
	protected $use_proxies = false;
	/**
	* @var bool disable_cache_buster Disable use of cache buster
	* NOTE: default value is false, so it won't disable cache buster, but in some cases the server
	* won't accept query parameters so the cache buster won't work, so it can be manually disabled
	* (it does so automatically too), to save 2 requests
	* @access protected
	*/
	protected $disable_cache_buster = false;

	/**
	* CRAWLING settings
	*/

	/**
	* @var int stop_date The stop date of an article post date (unix timestamp integer)
	* NOTE: default value is 0 and it will be populated by the parents based on the last run of the plugin.
	* when articles before this date are encountered the crawl process will finish
	* @attention DO NOT DEFINE OR USE THIS, IT WILL BE OVERRIDDEN IN THE PARENTS, unless you define it in pre_collect
	* @access protected
	*/
	protected $stop_date = 0;

	/**
	* @var bool stop_date_override If crawling should use the last plugin run date or not
	* NOTE: default value is false, so the system will use the last plugin run date, however some websites publish
	* articles with dates older than the publishing date (publish today with the date of yesterday)
	* you will have to determine if the website listings are always sequential or not
	* @attention Please be responsible when overriding this, overriding without proper research will cause the
	* system to waste resources parsing unnecessary pages
	* @access protected
	*/
	protected $stop_date_override = false;

	/**
	* @var bool stop_on_date If crawling should stop when it encounters an article with a date previous to stop_date
	* NOTE: default value is true, so the system will stop when an article with the date previous to the defined
	* stop_date is found
	* @attention DO NOT OVERRIDE THIS, unless you want to parse MANY MANY pages
	* @access protected
	*/
	protected $stop_on_date = true;

	/**
	* @var bool stop_on_article_found If crawling should stop when it encounters an already grabbed article
	* NOTE: default value is true, so the system will stop when it encounters an article already in the database,
	* however some sites do not have chronological article listings so in that case you can define it as false;
	* you will have to determine if the website listings are always sequential or not
	* @attention Please be responsible when overriding this, overriding without proper research will cause the
	* system to waste resources parsing unnecessary pages
	* @access protected
	*/
	protected $stop_on_article_found = true;

	/**
	* @var bool allow_failed_date_override If crawling will automatically set today date when the article date is invalid
	* NOTE: default value is false, so the article date is not touched, when set to true the article date will be set with
	* today's date when an invalid date is encountered
	* @attention DO NOT OVERRIDE THIS, unless the website has no article dates or they are seriously inconsistent
	* @access protected
	*/
	protected $allow_failed_date_override = false;

	/**
	* @var bool stop_on_error If crawling should stop, when it encounters an invalid article date
	* NOTE: default value is true, which will cause the crawling process to abort with an error when an invalid article
	* date is found, this is useful for debugging and finding alternative article date patterns
	* @attention DO NOT OVERRIDE THIS, unless the website has no article dates or they are seriously inconsistent
	* @access protected
	*/
	protected $stop_on_error = true;

	/**
	* @var bool cloudflare_bypass If the system should attempt to bypass a cloudflare protection before collecting
	* NOTE: default value is false, so no attempt is made
	* @attention Do not set to true unless you are certain that the site uses CloudFlare protection
	* @access protected
	*/
	protected $cloudflare_bypass = false;

	/**
	* PLUGIN website settings
	*/

	/**
	* @var string site_timezone The timezone of the website dates
	* NOTE: default value is Asia/Amman, set this appropriately for the website dates timezone using one of
	* the timezones defined here: http://php.net/manual/en/timezones.php
	* @access protected
	*/
	protected $site_timezone = CORE_CURRENT_TIMEZONE;

	/**
	* @var array section Definition of the website article sections/categories
	* general definition:
	* @member TYPE string = [
	* 	@member int iteration = [
	* 		@member @required type                  string        TYPE|section
	* 			string defining the logic TYPE will point to next
	* 		@member @required regexp                string|array
	* 			array of or single regexp expression(s) for extracting the link to process next
	* 		@member @required append_domain         bool
	* 			boolean for appending domain to the link or not
	* 		@member @optional process_link          string         process_sections_TYPE_link
	* 			function to be called to process the result link
	* 			only when current logic type is not section
	* 			parameters:  $regexp_result, $referrer_link, $logic_type
	* 		@member @optional process_result        string         process_sections_TYPE_result
	* 			function to be called to process the regexp result content
	* 			only when current logic type is section
	* 			parameters: $regex_result, $referrer_link
	* 	]
	* ],
	* @member section = [
	* 	@member @required link                  string|array
	* 		array of or single regexp expression(s) for extracting section link
	* 	@member @required name                  string|array
	* 		array of or single regexp expression(s) for extracting section name
	* 	@member @required append_domain         bool
	* 		boolean for appending domain to the link or not
	* 	@member @optional process_link          string         process_sections_TYPE_link
	* 		function to be called to process the result link
	* 		parameters:  $section_link, $section_name, $referrer_link, $logic_type
	* ]
	* @access protected
	*/
	protected $sections = array(
		// definition for the content of pages of type list1
		'list1' => array(
			// the first type of data retrieved from the page sources of type list1
			0 => array(
				// the data (links) retrieved by this are of the following type
				// this will put the respective link in the collection links array with the defined type
				// this is chaining the link into the logic
				// here it is pointing to another page which contains further section links
				'type' => 'listX',
				// the regular expression that returns the link to process next
				// it can be a single regular expression or an array of regular expressions
				// if it's a single one than the result of that expression must be the link
				// if it's an array then the regular expressions are applied sequentially
				// and the result of the LAST expression must be the link
				'regexp' => '/<li id="links">\s+<a href="([^"]*)*" class="label-link">/Uis',
				// if the retrieved link is NOT a true and accessible link (f.e. it's a javascript link)
				// then you can set a function to process the link and transform it into an actual link
				// the system can retrieve
				// the function MUST be defined in the current plugin class and must always use
				// the following pattern: process_sections_[logic-type]_link
				'process_link' => 'process_sections_list1_link',
				// if the link is relative define as true to add the domain to it
				// if the link is absolute define as false to not add the domain to it
				'append_domain' => true
			),
			//! ...
			// the second type of data retrieved from the page sources of type list1
			1 => array(
				// this is the actual section link from the homepage
				// this will chain to the FIXED section type and will end with content grab
				'type' => 'section',
				'regexp' => array(
					0 => '/<div class="menu".*<ul class="menu">([^<]*)<\/ul>/Uis',
					1 => '/(<a href="latest_news[^"]*"[^>]*>[^<]*<\/a>)/Uis',
				),
				// if the retrieved content is NOT containing section name and section link
				// so then they cannot be extracted simple, use this function to put together
				// a new content string that contains both
				// the function MUST be defined in the current plugin class and must always use
				// the following pattern: process_sections_[logic-type]_result
				'process_result' => 'process_sections_list1_result',
				'append_domain' => false
			)
		),
		//! ...
		// definition for the content of pages of type listX
		'listX' => array(
			// this is the actual section link
			0 => array(
				// in this case the link will be to the sections found on another page
				// this will chain to the FIXED section type and will end with content grab
				'type' => 'section',
				'regexp' => '/(<a class="categories" href=".*">.*<\/a>)/Uis',
				'append_domain' => true
			)
		),
		// this type is separated from parse logic and is fixed and always has
		// to be defined; this contains the single or arrays of regular expressions which will extract
		// the actual name and link of the section
		'section' => array(
			// the regular expression for the section link, MANDATORY
			'link' => '/<a[^>]*href=[\'|"]([^"\']+)[\'|"][^>]*>/Uis',
			// the regular expression for the section name, MANDATORY
			'name' => '/<a[^>]*href=[\'|"]([^"\']+)[\'|"][^>]*>/Uis',
			// use this function to filter unwanted sections
			'process_result' => 'filter_sections',
			'append_domain' => false
		)
	);

	/**
	* @var array logic Definition of the parse logic chains
	* general definition:
	* @member TYPE string = [
	* 	@member int iteration = [
	* 		@member @required type                  string        TYPE|article
	* 			string defining the logic TYPE will point to next
	* 		@member @required regexp                string|array
	* 			array of or single regexp expression(s) for extracting the link to process next
	* 		@member @required append_domain         bool
	* 			boolean for appending domain to the link or not
	* 		@member @optional process_link          string         process_TYPE_link
	* 			function to be called to process the result link
	* 			parameters: $regex_result, $referrer_link, $logic_type
	* 		@member @optional data_type             string         html|json|xml default: html
	* 			string defining the page content type
	* 		@member @optional data_iterate          string         PARENT_NODE > ... > ARRAY_NODE
	* 			string defining the path the articles array/iteration within the content
	* 			only when data_type is JSON or XML
	* 		@member @optional data_field            string         ARTICLE_NODE > ... > LINK_NODE
	* 			string defining the path to the article link within the article member
	* 			only when data_type is JSON or XML
	* 		@member @optional article_type          string         html|json|xml default: html
	* 			string defining the type of data the article link extracted from data_field will return
	* 			only when current logic type is article
	* 		@member @optional contains_article_data bool           default: false
	* 			boolean defining if system should extract articles data from the current page content
	* 			when current page content contains all (f.e. JSON or XML)
	* 	]
	* ],
	* @member article = [
	* 	@member @required headline     string|array
	* 		array of or single regexp expression(s) for extracting headline
	* 	@member @required content      string|array
	* 		array of or single regexp expression(s) for extracting content
	* 	@member @required author       string|array|bool
	* 		array of or single regexp expression(s) for extracting author, false when missing
	* 	@member @required article_date string|array|bool
	* 		array of or single regexp expression(s) for extracting article date, false when missing
	* 	@member @required post_date    string|array|bool
	* 		array of or single regexp expression(s) for extracting article post date, false when missing
	* 	@attention article_date OR post_date must be defined
	* ]
	* @access protected
	*/
	protected $logic = array(
		// definition for the content of pages of type list1
		'list1' => array(
			// the first type of data retrieved from the page sources of type list1
			0 => array(
				// the data (links) retrieved by this are of the following type
				// this will put the respective link in the collection links array with the defined type
				// this is chaining the link into the logic
				// in this case the link will be of the same type as the current logic
				// (f.e. in pagination, page 1, page 2, page 3 are of the same type when similar)
				'type' => 'list1',
				// the regular expression that returns the link to process next
				// it can be a single regular expression or an array of regular expressions
				// if it's a single one than the result of that expression must be the link
				// if it's an array then the regular expressions are applied sequentially
				// and the result of the LAST expression must be the link
				'regexp' => '/<li id="links">\s+<a href="([^"]*)*" class="label-link">/Uis',
				// if the link is relative define as true to add the domain to it
				// if the link is absolute define as false to not add the domain to it
				'append_domain' => true
			),
			//! ...
			// the second type of data retrieved from the page sources of type list1
			1 => array(
				// in this case the link will be of a different type than the current logic
				// (f.e. in pagination the links are to a list of 3-4 subarticles, which actually point to the article)
				'type' => 'listX',
				'regexp' => array(
					0 => '/<div class="images".*<ul class="pager">([^<]*)<\/ul>/Uis',
					1 => '/<a href="(latest_news[^"]*)" onclick="return link_to_ajax/Uis',
				),
				// if the retrieved link is NOT a true and accessible link (f.e. it's a javascript link)
				// then you can set a function to process the link and transform it into an actual link
				// the system can retrieve
				// the function MUST be defined in the current plugin class and must always use
				// the following pattern: process_[logic-type]_link
				'process_link' => 'process_listX_link',
				'append_domain' => false
			)
		),
		//! ...
		// definition for the content of pages of type listX
		'listX' => array(
			// this is the actual article link
			0 => array(
				// in this case the link will be to the article page where the content data is
				// this will chain to the FIXED article type and will end with content grab
				'type' => 'article',
				'regexp' => '/<a class="latestNewstitle" href="(.*)">/Uis',
				'process_link' => 'remove_amp',
				'append_domain' => true
			),
			1 => array(
				// in this case the link will be of the same type as the current logic
				// (f.e. links to the next subset of 3-4 subarticles)
				'type' => 'listX',
				'regexp' => array(
					0 => '/<div class="images".*<ul class="pager">([^<]*)<\/ul>/Uis',
					1 => '/<a href="(latest_news[^"]*)" onclick="return link_to_ajax/Uis',
				),
				'process_link' => 'process_listX_link',
				'append_domain' => false
			)
		),
		// definition for the content of JSON data pages of type listA
		'listA' => array(
			// chaining back to itself (pagination)
			0 => array(
				'type' => 'listA',
				// this will extract data from the JSON content
				// (f.e. the total number of articles to limit offset per section)
				'regexp' => '/\[\{.*["|\']total["|\']:(\d+),/Uis',
				'process_link' => 'process_listA_link'
			),
			1 => array(
				// the JSON data contains article links
				'type' => 'article_json',
				// define that the content processing now is JSON not HTML (default)
				'data_type' => 'json',
				 // which JSON path contains articles array, when empty it means the articles are returned as array,
				 // when set "content > articles", means there is a member in JSON data named "content" which
				 // contains another member named "articles" where are all articles links
				 // NOTE: enter 'inline' here when the article data is the returned array (no additional paths exist)
				'data_iterate' => '',
				// within the path to the articles array above, for each article define the path to the article link
				// cannot be empty, when "link" it means that the article contains a member "link" with the article link
				// when "more > link" it means the article contains a member "more" which has a member "link"
				'data_field' => 'link',
				// define the target article format
				// when HTML it means that following the link will return a HTML page
				// when JSON it means that following the link will return JSON data
				// in this case the article is contained in the current JSON data,
				// so it can br extracted here, without further requests
				'article_type' => 'json',
				// define as true when the current JSON data contains the full article data required
				// so no further page requests are made, when the full data is not present,
				// then further requests are needed
				'contains_article_data' => true,
				'append_domain' => false
			)
		),
		// this types are separated from parse logic and are fixed and always, at least one, must
		// be defined; this contains the single or arrays of regular expressions which will extract
		// the actual content of the article
		'article' => array(
			// the regular expression for the article title/headline, MANDATORY
			'headline' => '/<h1[^>]*>(.*)<\/h1>/Uis',
			// the regular expression for the article body/text/content, MANDATORY
			'content' => '/<\/h1>\s*<div class="toolbar clearfix clear">\s*<ul class="set-sharing first">.*<\/ul>\s*<\/div>(.*)<div class="toolbar clearfix clear">/Uis',
			// the regular expression for the author, it there is no such information set it to boolean false, system will skip it
			'author' => '/<div class="date-and-provider clearfix">\s*<table[^>]*>.*<\/td>\s*<td[^>]*>.*<img src="[^"]*" alt="(.*)"[^>]*>.*<\/td>/Uis',
			// the regular expression for the article date, it there is no such information set it to boolean
			// false, system will skip it
			// ATTENTION if the article date is missing you will have to set it based on
			// post date in the process_article_date function
			// MANDATORY article_date OR post_date
			'article_date' => false,
			// the regular expression array for the article post date, it there is no such information set
			// it to boolean false, system will skip it
			// ATTENTION if the post date is missing you will have to set it based on article date in the
			// pre_add function or it will fail!
			// MANDATORY article_date OR post_date
			'post_date' => array(
				'/<div class="column articleBox">(.*)<div class="addthis_toolbox/Uis',
				'/<h4>(.*)<\/h4>/Uis'
			)
		),
		// in JSON data articles the path (separated by " > ") to the field must be defined
		'article_json' => array(
			'headline' => 'headline',
			'content' => 'body',
			'author' => 'author',
			'article_date' => 'details > date'
		)
	);

	/**
	* constructor
	* NOTE: it shouldn't be necessary to use or define this in the vast majority of cases
	* @attention if and when this is defined, first and foremost it must call the parent constructor:
	* 				parent::__construct();
	* @access public
	*/
	public function __construct() {

		parent::__construct();

	}

	/**
	* SECTIONS DETECTION METHODS
	*/

	/**
	* pre_detect_sections
	* called before any section detection settings and variables are set
	* NOTE: it shouldn't be necessary to use or define this in the vast majority of cases
	* @return null
	* @access protected
	*/
	protected function pre_detect_sections() {}

	/**
	* detect_section_link
	* prepare the sections detection link for cases with language or other filters
	* NOTE: this will be called before starting crawling in order to set the initial start link
	* which by default is the website homepage, this can be useful when creating multiple plugins
	* inherting one another but with different start pages (f.e. multi-language websites)
	* @param string link The website defined homepage link
	* @return string the start link for sections detection
	* @access protected
	*/
	protected function detect_section_link($link) {

		return $link;

	}

	/**
	* post_detect_sections
	* called after all section detection has finished and the process is about to end
	* NOTE: it shouldn't be necessary to use or define this in the vast majority of cases
	* @return null
	* @access protected
	*/
	protected function post_detect_sections() {}


	/**
	* ARTICLES CRAWLING METHODS
	*/

	/**
	* pre_prepare
	* called before crawling settings for the section are initialized and processing begins
	* and before section start link is set
	* can be used to configure specific settings for a specific section or to set a proxy
	* @param array section The website section details (id, name, etc)
	* @return null
	* @access protected
	*/
	protected function pre_prepare($section) {}

	/**
	* section_link
	* called before crawling for articles is configured and started
	* can be used to process the section link for dates or other link variables
	* @param string link The website section defined link, which to parse and retrieve articles
	* @return string The website section link to parse
	* @access protected
	*/
	protected function section_link($link) {

		return $link;

	}

	/**
	* post_prepare
	* called after crawling settings for the section are initialized and after section
	* start link is set, but before processing begins
	* can be used to configure specific settings for a specific section or to set a proxy
	* @param array section The website section details (id, name, etc)
	* @return null
	* @access protected
	*/
	protected function post_prepare($section) {}

	/**
	* process_headline
	* pre process the article headline
	* called in pre_add before any processing (i.e. cleanup) is done on the resulting headline
	* the headline is in raw state, exactly as matched by the regexp for it
	* can be used to remove uncaught unwanted content or add content, etc
	* @param string headline The article headline as matched by the regexp in the article type
	* @param array article_data The raw article data as matched by the regexp for each defined field
	* @return string The headline to be set for the article
	* @access protected
	*/
	protected function process_headline($headline, $article_data) {

		return $headline;

	}

	/**
	* process_content
	* pre process the article content
	* called in pre_add before any processing (i.e. cleanup) is done on the resulting content
	* the content is in raw state, exactly as matched by the regexp for it
	* can be used to remove uncaught unwanted content or add content, etc
	* @param string content The article content as matched by the regexp in the article type
	* @param array article_data The raw article data as matched by the regexp for each defined field
	* @return string The content to be set for the article
	* @access protected
	*/
	protected function process_content($content, $article_data) {

		return $content;

	}

	/**
	* process_author
	* pre process the article author
	* called in pre_add before any processing (i.e. cleanup) is done on the resulting author
	* the author is in raw state, exactly as matched by the regexp for it
	* can be used to remove uncaught unwanted content or add content, etc
	* @param string author The article author as matched by the regexp in the article type
	* @param array article_data The raw article data as matched by the regexp for each defined field
	* @return string The author to be set for the article
	* @access protected
	*/
	protected function process_author($author, $article_data) {

		return $author;

	}

	/**
	* process_article_date
	* pre process the article date
	* called in pre_add before any processing (i.e. cleanup) is done on the resulting date
	* the date is in raw state, exactly as matched by the regexp for it
	* can be used to normalize various date forms on the website so that process_date is simpler or to
	* remove uncaught unwanted content or add content
	* NOTE: does not require that the returned date is in any specific format, just a string to be later
	* processed into a formatted date in process_date method
	* @param string article_date The article date as matched by the regexp in the article type
	* @param array article_data The raw article data as matched by the regexp for each defined field
	* @return string The article date to be set for the article
	* @access protected
	*/
	protected function process_article_date($article_date, $article_data) {

		return $article_date;

	}

	/**
	* process_post_date
	* pre process the article post date
	* called in pre_add before any processing (i.e. cleanup) is done on the resulting post date
	* the post date is in raw state, exactly as matched by the regexp for it
	* can be used to remove uncaught unwanted content or add content
	* NOTE: does not require that the returned post date is in any specific format
	* @param string post_date The article post date as matched by the regexp in the article type
	* @param array article_data The raw article data as matched by the regexp for each defined field
	* @return string The post date to be set for the article
	* @access protected
	*/
	protected function process_post_date($post_date, $article_data) {

		return $post_date;

	}

	/**
	* process_date
	* process the article date
	* called in pre_add after processing (i.e. cleanup) is done on the resulting date
	* the date is its cleaned up state
	* NOTE: must be defined to convert the article date in the proper format (see below)
	* @param string article_date The cleaned article date
	* @return string The date to be set for the article in YYYY-MM-DD HH:ii:ss format (Y-m-d H:i:s)
	* @access protected
	*/
	protected function process_date($article_date) {

		return $article_date;

	}

	/**
	* pre_add
	* process the article data
	* called before the article is added to the database, to cleanup the article content of HTML tags,
	* and format the article data, to convert the dates to the internal timezone and to validate
	* article content (articles with empty headlines or empty content are skipped)
	* NOTE: it shouldn't be necessary to use or define this in the vast majority of cases, as the content processing
	* functions above should sufice, only define if there are some difficult situations that require different data
	* processing
	* @attention if and when this is defined, it must call the parent pre_add:
	* 				parent::pre_add($article_data);
	* @param array article_data Array containing the article data, reference so modify the parameter directly
	* @return null
	* @access protected
	*/
	protected function pre_add(&$article_data) {

		parent::pre_add($article_data);

	}

	/**
	* post_add
	* called after the article is added to the database
	* NOTE: it shouldn't be necessary to use or define this in the vast majority of cases
	* @param int|null article_id Article ID from database when successfully added, null otherwise
	* @param array article_data Array containing the article data, reference so modify the parameter directly
	* @return null
	* @access protected
	*/
	protected function post_add($article_id, $article_data) {}

	/**
	* pre_article
	* called before an article page is retrieved and the article is added to the database
	* NOTE: it shouldn't be necessary to use or define this in the vast majority of cases
	* @param string article_link The link to the article page, reference so modify the parameter directly
	* @param string referer_link The link where the article link was found, reference so modify the parameter directly
	* @param bool ignore_stop If to ignore errors and continue crawling, reference so modify parameter directly
	* @return null
	* @access protected
	*/
	protected function pre_article(&$article_link, &$referer_link, &$ignore_stop) {}

	/**
	* post_article
	* called after the article page is retrieved and the article is added to the database
	* NOTE: it shouldn't be necessary to use or define this in the vast majority of cases
	* @param array article_data Array containing the article data
	* @return null
	* @access protected
	*/
	protected function post_article($article_data) {}

	/**
	* pre_add_article_keywords
	* called before the article keywords are matched and added to the database
	* can be used to inject content in headline/content if specific keywords must be triggered
	* NOTE: it shouldn't be necessary to use or define this in the vast majority of cases
	* @param int article_id Article ID from database when successfully added
	* @param array article_data Array containing the article data, reference so modify the parameter directly
	* @return null
	* @access protected
	*/
	protected function pre_add_article_keywords($article_id, &$article_data) {}


	/**
	* post_add_article_keywords
	* called after the article keywords are matched and added to the database
	* NOTE: it shouldn't be necessary to use or define this in the vast majority of cases
	* @param array keywords Array containing the keyword ids added to the article
	* @return null
	* @access protected
	*/
	protected function post_add_article_keywords($keywords) {}


	/**
	* GENERIC CRAWLING METHODS
	*/

	/**
	* pre_process
	* called before any crawling settings and variables are set
	* NOTE: it shouldn't be necessary to use or define this in the vast majority of cases
	* @attention if and when this is defined, it must call the parent pre_process:
	* 			parent::pre_process();
	* @return null
	* @access protected
	*/
	protected function pre_process() {

		parent::pre_process();

	}

	/**
	* post_process
	* called after crawling is completed and the process is about to end
	* NOTE: it shouldn't be necessary to use or define this in the vast majority of cases
	* @attention if and when this is defined, it must call the parent post_process:
	* 			parent::post_process();
	* @return null
	* @access protected
	*/
	protected function post_process() {

		parent::post_process();

	}

	/**
	* pre_collect
	* called after crawling settings and variables are set and before crawling starts
	* this sets cookies and attempts to resolve cloudflare protection
	* NOTE: it shouldn't be necessary to use or define this in the vast majority of cases
	* @attention if and when this is defined, it must call the parent pre_collect:
	* 			parent::pre_collect();
	* @return null
	* @access protected
	*/
	protected function pre_collect() {

		parent::pre_collect();

	}

	/**
	* post_collect
	* called after crawling crawling ends and before finishing processing
	* NOTE: it shouldn't be necessary to use or define this in the vast majority of cases
	* @return null
	* @access protected
	*/
	protected function post_collect() {}

	/**
	* pre_get_page
	* called before a page is requested by ant
	* NOTE: it shouldn't be necessary to use or define this in the vast majority of cases
	* @param string link The link to the page to be requested
	* @return null
	* @access protected
	*/
	protected function pre_get_page(&$link) {}

	/**
	* post_get_page
	* called after a page is requested by ant
	* can be used to manipulate the page content before any matching and processing is done
	* NOTE: it shouldn't be necessary to use or define this in the vast majority of cases
	* @param string result The source of the page requested, reference so modify the parameter directly
	* @return null
	* @access protected
	*/
	protected function post_get_page(&$result) {}

}
