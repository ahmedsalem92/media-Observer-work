<?php

class wam_en extends plugin_base {

	protected $ant_precision = 4;
	protected $use_proxies = true;
	protected $agent = 'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)';

	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = true;

	protected $site_timezone = 'Asia/Amman';
	protected $exclude_sections = array( '/en/category/photos-and-videos', '/view/subscriber/myView', '/view/subscriber/bookmarks', 'view/subscriber/selectSection');
	protected $sections = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/("runtime[^"]*\.js")/Uis',
				'regexp' => '/^(.*)$/Uis',
				'process_link' => 'api_sections_link'
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'section',
				'regexp' => '/(<a[^>]*>([^<]*?)<\/a>)/Uis',
				'process_page_transformations' => 'section_page_transform'
			)
		),
		'section' => array(
			'link' => '/<a[^>]*href="([^"]+?)"[^>]*>/Uis',
			'name' => '/<a[^>]*>(.*?)<\/a>/Uis',
			'process_link' => 'filter_sections'
		)
	);
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/("paging":\{[^\}]+\})(?!.*"paging":\{[^\}]+\})/Uis',
				'process_link' => 'process_lists_link'
			),
			1 => array(
				'type' => 'article_json',
				'data_type' => 'json',
				'data_iterate' => 'sections > articlesResult > items',
				'data_field' => 'urlSlug',
				'article_type' => 'json',
				'process_link' => 'process_article_link',
				'process_page_transformations' => 'merge_article_sections',
				'contains_article_data' => false
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/("paging":\{[^\}]+\})(?!.*"paging":\{[^\}]+\})/Uis',
				'process_link' => 'process_lists_link'
			),
			1 => array(
				'type' => 'article_json',
				'data_type' => 'json',
				'data_iterate' => 'items',
				'data_field' => 'urlSlug',
				'article_type' => 'json',
				'process_link' => 'process_article_link',
				'contains_article_data' => false
			)
		),
		'article_json' => array(
			'headline' => 'title',
			'content' => 'body',
			'author' => 'articleAuthors',
			'article_date' => 'articleDate'
		)
	);
	protected $logic_search = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/("runtime[^"]*\.js")/Uis',
				'process_link' => 'api_search_link',
				'append_domain' => false
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/"totalCount":(\d+),/Uis',
				'process_link' => 'process_search_link'
			),
			1 => array(
				'type' => 'article_json',
				'data_type' => 'json',
				'data_iterate' => 'items',
				'data_field' => 'urlSlug',
				'article_type' => 'json',
				'contains_article_data' => false,
				'process_link' => 'process_article_link',
				'append_domain' => false
			)
		),
		'article_json' => array(
			'headline' => 'title',
			'content' => 'body',
			'author' => 'articleAuthors',
			'article_date' => 'articleDate'
		)
	);
	protected $using_search = false;
	protected $postbacks = array();

	// append the english language to the start link
	protected function detect_section_link($link) {

		return rtrim($link, '/') . '/en/';

	}

	protected function api_sections_link($result_link, $referer_link, $logic_type) {

		return rtrim($this->settings['site_link'], '/') . '/api/app/menu/GetMenuItems';

	}

	protected function section_page_transform($page, $link) {

		$result = '';

		$menu_data = json_decode($page, true);
		if (is_array($menu_data)) {
			$sections = $this->extract_section_links($menu_data);
			if (!empty($sections)) {
				$sections = array_reverse($sections);
				foreach($sections as $link => $title) {
					$result .= '<a href="' . $link . '">' . htmlentities($title) . '</a>';
				}
			}
		}

		return $result;

	}

	private function extract_section_links($data) {

		$sections = array();
		foreach($data as $branch) {
			if (isset($branch['navigationLink']) && isset($branch['navigationLink']['viewUrl']) &&
					!is_null($branch['navigationLink']['viewUrl']) && $branch['navigationLink']['viewUrl'] != '') {
				$link = $this->combine_link($branch['navigationLink']['viewUrl']);
				$sections[$link] = isset($branch['navigationLink']['title']) ? $branch['navigationLink']['title'] : '????';
			}
			if (isset($branch['subItems']) && !is_null($branch['subItems']) && !empty($branch['subItems'])) {
				$subsections = $this->extract_section_links($branch['subItems']);
				$sections = array_merge($subsections, $sections);
			}
		}
		return $sections;

	}

	protected function filter_sections($section_link, $section_name, $referer_link, $logic) {

		if (in_array(strtolower(str_ireplace(rtrim($this->settings['site_link'], '/'), '', $section_link)), $this->exclude_sections) ){
			return '';
		}

		if( $this->isArrayPartOfString($this->exclude_sections,$section_link) ) {
			return '';
		}
		return $section_link;

	}

	protected function isArrayPartOfString($haystack, $needle) {

		if (trim($needle) == '') {
			return false;
		}
		foreach ($haystack as $string) {
			if (trim($string) != '' && strpos($needle, $string) !== false) {
				return true;
			}
		}
		return false;

	}

	protected function section_link($link) {

		$url_parts = parse_url($link);
		$domain = 'https://' . $url_parts['host'];

		if ($this->using_search) {
			return $domain . '/en/';
		}

		return $domain . '/api/app/views/GetViewByUrl?url=' .
			rawurlencode(ltrim(str_ireplace($domain, '', $link), '/'));

	}

	public function prepare_search($section_id) {

		$this->logic = $this->logic_search;
		$this->using_search = true;

	}

	protected function api_search_link($result_link, $referer_link, $logic_type) {

		$search_link = rtrim($this->settings['site_link'], '/') . '/api/app/articles/search';
		$this->postbacks[$search_link] = array(
			'skipCount' => 0,
			'maxResultCount' => 100,
			'requiredMediaTypeq' => null
		);
		return $search_link;

	}

	protected function process_lists_link($result_link, $referer_link, $logic_type) {

		$list_link = '';
		$data = json_decode('{' . $result_link . '}', true);
		if (is_array($data) && isset($data['paging']) && !empty($data['paging']) &&
				isset($data['paging']['pageNumber']) && isset($data['paging']['sectionInfo']) &&
				isset($data['paging']['hasNext']) && $data['paging']['hasNext']) {
			$list_link = rtrim($this->settings['site_link'], '/') . '/api/app/views/GetSectionArticlesFDto';
			$data['paging']['pageNumber']++;
			$data['paging']['pageSize'] = 20;
			$list_link .= '?get' . (int)$data['paging']['pageNumber'] . 'page' . $data['paging']['sectionInfo'] . 'sectioncode';
			$this->postbacks[$list_link] = $data['paging'];
		}
		return $list_link;

	}

	protected function process_search_link($link, $referer_link, $logic) {

		$total = (int)$link;
		if (isset($this->postbacks[$referer_link]) &&
				((int)$this->postbacks[$referer_link]['skipCount'] + (int)$this->postbacks[$referer_link]['maxResultCount']) < $total) {
			$new_link = preg_replace('/\?get(\d+)page/Uis', '', $referer_link);
			$next_page = ((int)$this->postbacks[$referer_link]['skipCount'] + (int)$this->postbacks[$referer_link]['maxResultCount']);
			$new_link .= '?get' . $next_page . 'page';
			$this->postbacks[$new_link] = array(
				'skipCount' => $next_page,
				'maxResultCount' => (int)$this->postbacks[$referer_link]['maxResultCount'],
				'requiredMediaTypeq' => null
			);
		}

		return $new_link;

	}

	protected function merge_article_sections($page, $link) {

		$data = json_decode($page, true);
		if (is_array($data) && isset($data['sections']) && !empty($data['sections'])) {
			$main_section = array_shift($data['sections']);
			if (!empty($data['sections'])) {
				foreach($data['sections'] as $section) {
					$main_section['articlesResult']['items'] = array_merge(
						$main_section['articlesResult']['items'],
						$section['articlesResult']['items']
					);
				}
			}
			$data['sections'] = $main_section;
			$page = json_encode($data);
		}

		return $page;

	}

	protected function process_article_link($link, $referer_link, $logic) {

		return rtrim($this->settings['site_link'], '/') . '/article/' . $link;

	}

	protected function pre_get_page(&$page) {

		if (array_key_exists($page, $this->postbacks)) {
			$post_json = json_encode($this->postbacks[$page]);
			$this->ant->set_post($post_json);
			$this->ant->set_custom_headers(
				array(
					'Content-Type: application/json',
				)
			);
			//$page = preg_replace('/\?get\d+page(?:.*?sectioncode)?/is', '', $page);
		}
		if (preg_match('/\/article\//Uis', $page)) {
			$slug = str_ireplace(rtrim($this->settings['site_link'], '/') . '/article/', '', $page);
			$page = rtrim($this->settings['site_link'], '/') . '/api/app/articles/GetArticleBySlug?slug=' . rawurlencode($slug);
		}

	}

	protected function post_get_page(&$result) {

		$this->ant->unset_post();
		$this->ant->unset_custom_headers();

	}

	protected function process_author($author, $article) {

		if (is_array($author)) {
			return implode(', ', $author);
		}
		elseif (is_string($author)) {
			return str_replace(' <br />', ', ', $author);
		}
		else {
			return '';
		}

	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		$date = new DateTime($article_date);
		if ($date instanceof DateTime) {
			$article_date = $date->format('Y-m-d H:i:s');
		}

		return $article_date;

	}

}
