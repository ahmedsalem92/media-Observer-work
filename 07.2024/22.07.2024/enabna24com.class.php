<?php

class enabna24com extends plugin_base {

	// ANT settings
	protected $ant_precision = 2;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;
	protected $stop_on_date = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<li class="bg-brand-primary text-white font-bold relative block mr[^<]*>.*<li class="relative page-list block mr-2\.5 py-2 px-3 rounded-xl leading-tight hover:bg-gray-200">.*<li class="relative page-list block mr-2\.5 py-2 px-3 rounded-xl leading-tight hover:bg-gray-200">.*href="(.*)"/Uis',
				'append_domain' => true
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<p text-lg[^<]*>.*href="(.*)"/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'

			),

		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/(?:<\/h1>|<div class="ma-0 pa-3 elevation-10[^<]*>)(.*)(?:<\/article>|<\/article>)/Uis',
			'author' => false,
			'article_date' => '/publish_date:"(.*)"/Uis'
		)
	);

	protected function process_article_link($link, $referer_link, $logic) {
		if ($link == 'https://en.abna24.com/all/titr/1') return false;
		return $link;
	}

	protected function process_content($content, $article_data){
		$content = preg_replace('/(<video itemprop="video" border-10 border-[^<]*>)/Uis', 'video', $content);
		$content = preg_replace('/(<img rounded-lg object-cover w-full src="https:\/\/media\.abna24\.ir\/image\/jpeg\/2023\/November\/9\/1c324250[^<]*>)/Uis', 'photo', $content);
		$content = preg_replace('/<a[^<]*class="border-orange-400 text-[^<]*>(.*)<\/a>/Uis', '', $content);
		$content = preg_replace('/<a[^<]*class="text-slate-400 border-slate-[^<]*>(.*)<\/a>/Uis', '', $content);
		$content = preg_replace('/(\/129)/Uis', '', $content);
		$content = preg_replace('/<div order-4[^<]*>.*div class="i-mdi[^<]*>.*<div>(.*)<\/div>/Uis', '', $content);
		$content = preg_replace('/<div text-md[^<]*><div class="i-carbon-[^<]*><\/div>(.*)<\/div>/Uis', '', $content);
		$content = preg_replace('/<p text-12px[^<]*>(.*)<\/p>/Uis', '', $content);
		$content = preg_replace('/<div pl-2[^<]*>(.*)<\/div>/Uis', '', $content);
		$content = preg_replace('/<h1[^<]*>(.*)<\/h1>/Uis', '', $content);
		$content = preg_replace('/(Pictures)/Uis', '', $content);
		$content = preg_replace('/<h3 text-md[^<]*>(.*)<\/h3>/Uis', '', $content);
		$content = preg_replace('/(<p style="text-align: justify;">End\/ 257<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<img itemprop="image" w-full src="https:\/\/media\.abna24\.ir\/image\/jpeg\/2023\/November\/9\/dc314250[^<]*>)/Uis', 'photo', $content);
		return $content ;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date) {

		//2018-05-21T08:20:26+00:00
		if (preg_match('/(.*)T/Uis', $article_date, $matches)) {
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
