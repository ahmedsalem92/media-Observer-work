<?php

class yemennownewscom extends plugin_base
{

	// ANT settings
	protected $ant_precision = 2;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0';
	// CRAWL settings
	protected $stop_on_article_found = true;
	protected $stop_date_override = true;
	protected $stop_on_date = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_next_link'

			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<div class="main_news".*<h3[^>]*>\s*<a href="([^"]*)"/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			),
			2 => array(
				'type' => 'article',
				'regexp' => '/<div class="more_news_item">\s*<a href="([^"]*)"/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			),
			3 => array(
				'type' => 'article',
				'regexp' => '/<li class="even_block">\s*<h3[^>]*>\s*<a href="([^"]*)"/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/headline":"(.*)"/Uis',
			'content' => '/<div id="details">(.*)<div class="sourcelink">/Uis',
			'author' => false,
			'article_date' => '/"datePublished":"([^"]*)"/Uis'
		)
	);

	protected $next_page = 1;

	protected function process_next_link($link, $referer_link, $logic)
	{
		$this->next_page = $this->next_page + 1;
		if ($this->page_count < 41) {
			return 'https://yemennownews.com/?page=' . $this->next_page;
		} else {
			return false;
		}
	}


	protected function process_content($content, $article_data)
	{


		$content = preg_replace('/<p>\s*(?:<strong[^>]*>|<span[^>]*>|<em>|<b>|&nbsp;)*+(?:أقرا|إقرا|اقرا|طالع|اقرأ|أقرأ|إقرأ|إقرأ|اقراء|يهمك|شاهدي|شاهد|أنظر|قـــــــد يهمك|قد يهمك|قد يهمّك)\s*(?:ايضًا|أيضًا|ايضا|أيضا|ايضآ|أيضاً|أيضَا|أيض&#1611;ا|ايضاً|أىضاً|المزيد|أيض|أيضأ)\s*:.*<\/p>\s*<p>\s*<\/p>\s*<p>\s*<\/p><p>/Uis', '', $content);
		$content = preg_replace('/(<p>\s*(?:<strong[^>]*>|<span[^>]*>|<em>|<b>|&nbsp;)*+(?:أقرا|إقرا|اقرا|طالع|اقرأ|أقرأ|إقرأ|إقرأ|اقراء|يهمك|شاهدي|شاهد|أنظر|قـــــــد يهمك|قد يهمك|قد يهمّك)\s*(?:ايضًا|أيضًا|ايضا|أيضا|ايضآ|أيضاً|أيضَا|أيض&#1611;ا|ايضاً|أىضاً|المزيد|أيض|أيضأ)\s*.*<\/p>\s*<p>\s*<\/p>\s*<p>.*<\/p><p>)/Uis', '', $content);
		$content = preg_replace('/(<p>اخبار ذات صلة<\/p>\s*<p>.*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>اقرأ أيضاً\.\.<\/p>\s*<p>.*\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>قد يهمك[^<]*<\/p>\s*<p>.*\/p>)/Uis', '', $content);
		$content = preg_replace('/(الأكثر قراءة.*<p>(?:=)*+<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>\s*تابع جديد.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>(?:»)*+<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>(?:«)*+<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>\s*\|\s*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>\d{1,5}<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>\s*من:\s*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>\s*اليمن الآن\s*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>\s*المحتويات\s*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>\s*المصدر\s*<\/p>)/Uis', '', $content);
		$content = preg_replace('/تعليقات الفيس بوك/Uis', '', $content);
		$content = preg_replace('/السابق/Uis', '', $content);
		$content = preg_replace('/التالى/Uis', '', $content);
		$content = preg_replace('/-/Uis', '', $content);
		$content = preg_replace('/اخبار وتقارير/Uis', '', $content);
		$content = preg_replace('/نافذة اليمن _ عدن/Uis', '', $content);
		$content = preg_replace('/<p>(.*)<p>-<\/p>/Uis', '', $content);
		$content = preg_replace('/مشاركة/Uis', '', $content);
		$content = preg_replace('/المصدر: RT/Uis', '', $content);
		$content = preg_replace('/مرتبط/Uis', '', $content);
		$content = preg_replace('/نسخ الرابط/Uis', '', $content);
		$content = preg_replace('/تم نسخ الرابط/Uis', '', $content);
		$content = preg_replace('/يمن إيكو|أخبار:/Uis', '', $content);
		$content = preg_replace('/â€œ/Uis', '', $content);
		$content = preg_replace('/â€‌/Uis', '', $content);
		$content = preg_replace('/معجب بهذه:/Uis', '', $content);
		$content = preg_replace('/شارك هذا الموضوع:/Uis', '', $content);
		$content = preg_replace('/Tweet/Uis', '', $content);
		$content = preg_replace('/المزيد/Uis', '', $content);
		$content = preg_replace('/Telegram/Uis', '', $content);
		$content = preg_replace('/إعجاب/Uis', '', $content);
		$content = preg_replace('/تحميل.../Uis', '', $content);
		

		$content = preg_replace('/<p>(.*الحديدة، نيوزيمن:)<\/p>/Uis', '', $content);
		$content = preg_replace('/<div id="response" data-content-found="true">(.*)_ عدن<\/p>/Uis', '', $content);
		$content = preg_replace('/(<ins.*<\/ins>)/Uis', '', $content);
		$content = preg_replace('/(<script.*<\/script>)/Uis', '', $content);
		$content = preg_replace('/(<style.*<\/style>)/Uis', '', $content);
		if (preg_match('/(<div class="content">)/Uis', $content, $matches)) {
			if (strlen(trim(strip_tags($content))) == 0)
				return 'no content';
		}

		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{
		//2018-05-21T08:20:26+00:00
		if (preg_match('/(\d{4})\/(\d{1,2})\/(\d{1,2}) /Uis', $article_date, $matches)) {

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$matches[1] . '-' . $matches[2] . '-' . $matches[3] . ' 16:00:00',
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		} else {
			$date_obj = DateTime::createFromFormat('Y-m-d H:i:s O', $article_date);

			// Check if the date was parsed successfully
			if ($date_obj) {
				$date_obj->setTimezone(new DateTimeZone($this->site_timezone));
				$article_date = $date_obj->format('Y-m-d H:i:s');
			}
		}
		return $article_date;
	}
}
