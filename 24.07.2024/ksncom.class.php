<?php

class ksncom extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;
	protected $stop_on_date = true;
	protected $use_proxies = true;

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
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link',
			)
		),
		'list2' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/(?:<h1[^<]*>|<span class="video-title__text">|<h2 class="title">|<h1 class="article-title">)(.*)(?:<\/h1>|<\/h2>|<\/span>)/Uis',
			'content' => '/(?:<div class="article-content article-body[^<]*>|<div class="content">|<div class="video-playlist__player" data-playlist="article-list1"[^<]*>)(.*)(?:<\/footer>|<section class="content-syndication-search">)/Uis',
			'author' => '/<h1 class="article-title">.*<p class="article-authors">\s*by: <span>(.*),/Uis',
			'article_date' => '/(?:<div class="video-lead-share">.*<time datetime="|<p class="byline--date">|<meta name="sailthru\.date" content=")(.*)(?:"|<\/p>)/Uis'
		)
	);


	protected function process_content($content, $article_data){

		$content = preg_replace('/(<div class="container signup.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="video-playlist__player-intrinsic-sizer">.*<\/div>\s*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<span class="article-list__article.*<\/span>)/Uis', '', $content);
		$content = preg_replace('/(<div class="form-button.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="newsletter.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="newsletter-subscribe">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<button class="toggle">.*<\/button>)/Uis', '', $content);
		$content = preg_replace('/(<div class="video-meta">.*\/a>)/Uis', '', $content);
		return $content;
	}




/*	//Function Affect on section
	protected function section_link($link) {
		if ($link == "https://www.ksn.com/sitemap.xml"){return 'https://www.ksn.com/sitemap.xml?yyyy=' . date("Y") . '&mm=' . date("m") . '&dd=' .date("d") ;}

elseif ($link == "https://www.ksn.com/video-sitemap.xml"){return 'https://www.ksn.com/video-sitemap.xml?yyyy=' . date("Y") . '&mm=' . date("m") . '&dd=' .date("d")  ;}

elseif ($link == "https://www.ksn.com/virtual-sitemaps.xml?ingestor=cision"){return 'https://www.ksn.com/virtual-sitemaps.xml?ingestor=cision&yyyy=' . date("Y") . '&mm=' . date("m") . '&dd=' .date("d",  strtotime('-1 Day'))  ;}

elseif ($link == "https://www.ksn.com/virtual-sitemaps.xml?ingestor=globenewswire"){return 'https://www.ksn.com/virtual-sitemaps.xml?ingestor=globenewswire&yyyy=' . date("Y") . '&mm=' . date("m") . '&dd=' .date("d")  ;}

elseif ($link == "https://www.ksn.com/virtual-sitemaps.xml?ingestor=ein-presswire"){return 'https://www.ksn.com/virtual-sitemaps.xml?ingestor=ein-presswire&yyyy=' . date("Y") . '&mm=' . date("m") . '&dd=' .date("d")  ;}

elseif ($link == "https://www.ksn.com/virtual-sitemaps.xml?ingestor=accesswire"){return 'https://www.ksn.com/virtual-sitemaps.xml?ingestor=accesswire&yyyy=' . date("Y") . '&mm=' . date("m") . '&dd=' .date("d")  ;}

elseif ($link == "https://www.ksn.com/news-sitemap.xml"){ return 'https://www.ksn.com/news-sitemap.xml' ;}
	}
*/


	//Function Affect on section
	protected function section_link($link) {
		if ($link == "https://www.ksn.com/sitemap.xml"){return 'https://www.ksn.com/sitemap.xml?yyyy=' . date("Y") . '&mm=' . date("m") . '&dd=' .date("d") ;}

elseif ($link == "https://www.ksn.com/video-sitemap.xml"){return 'https://www.ksn.com/video-sitemap.xml?yyyy=' . date("Y") . '&mm=' . date("m") . '&dd=' .date("d")  ;}

elseif ($link == "https://www.ksn.com/virtual-sitemaps.xml?ingestor=cision"){return 'https://www.ksn.com/virtual-sitemaps.xml?ingestor=cision&yyyy=' . date("Y") . '&mm=' . date("m") . '&dd=' .date("d")  ;}

elseif ($link == "https://www.ksn.com/virtual-sitemaps.xml?ingestor=globenewswire"){return 'https://www.ksn.com/virtual-sitemaps.xml?ingestor=globenewswire&yyyy=' . date("Y") . '&mm=' . date("m") . '&dd=' .date("d")  ;}

elseif ($link == "https://www.ksn.com/virtual-sitemaps.xml?ingestor=ein-presswire"){return 'https://www.ksn.com/virtual-sitemaps.xml?ingestor=ein-presswire&yyyy=' . date("Y") . '&mm=' . date("m") . '&dd=' .date("d")  ;}

elseif ($link == "https://www.ksn.com/virtual-sitemaps.xml?ingestor=accesswire"){return 'https://www.ksn.com/virtual-sitemaps.xml?ingestor=accesswire&yyyy=' . date("Y") . '&mm=' . date("m") . '&dd=' .date("d")  ;}

elseif ($link == "https://www.ksn.com/news-sitemap.xml"){ return 'https://www.ksn.com/news-sitemap.xml' ;}
	}



	// next page

	protected function process_next_link($link, $referer_link, $logic) {


		$fake_link = $this->settings['site_section_link'] . date("d",  strtotime('-1 Day')) ;
		$fake_link = preg_replace('/dd=\d{2}/Uis', 'dd=', $fake_link);

		return $fake_link ;

	}




	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//Sep 23, 2022,
		if (preg_match('/(\w+?) (\d+?), (\w+?),/Uis', $article_date, $matches)) {
			$month = date("m", strtotime($matches[1]));
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[3] . '-' . $month . '-' . $matches[2] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}
		//2018-05-21T08:20:26+00:00
		elseif (preg_match('/(.*)T/Uis', $article_date, $matches)) {
			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;

	}

}
