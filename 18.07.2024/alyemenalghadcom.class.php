<?php

class alyemenalghadcom extends plugin_base {
	// ANT settings
	protected $ant_precision = 4;
	protected $use_proxies = true;
	protected $agent = 'Mozilla/5.0 (X11; Linux x86_64; rv:46.0) Gecko/20100101 Firefox/46.0';
	// CRAWL settings
	protected $stop_on_date = false;
	protected $stop_on_article_found = true;
	protected $stop_date_override = true;
	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list2',
				'regexp' => '/^(.*)$/Uis',
				'append_domain' => false,
				'process_link' => 'process_list1_link'
			)
		),
			'list2'=>array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => false,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="entry-content[^>]*>(.*)<div class="yarpp yarpp-related yarpp-related-website yarpp-template-list">/Uis',
			'author' => '/<a href=[^>]* title="جميع المقالات بواسطة: ">(.*)<\/a>/Uis',
			'article_date' => '/"datePublished":\s*"(.*)"/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic) {
		$temp_link ='';//https://news.alyemenalghad.com/sitemap-posts.xml //https:\/\/news\.alyemenalghad\.com\/sitemap-posttype-post\.\d*?\.xml
		if(preg_match('/<loc>(https:\/\/news\.alyemenalghad\.com\/sitemap-posts\.xml)<\/loc>/Uis', $link, $matches)){
			$temp_link = $matches[0];
			$temp_link = str_replace('<loc>' , '' , $temp_link);
			$temp_link = str_replace('</loc>' , '' , $temp_link);
		}

		return $temp_link;
	}

	protected function process_content($content, $article_data){
		$content = preg_replace('/(رسائل رمضان للأخ الغالي)/Uis', '', $content);
		$content = preg_replace('/(أفضل الرسائل لشهر رمضان)/Uis', '', $content);
		$content = preg_replace('/(استنتاجات رئيسية:)/Uis', '', $content);
		$content = preg_replace('/(الخلاصة)/Uis', '', $content);
		$content = preg_replace('/<div class="bd_toc_header_title">(.*)<\/ul>/Uis', '', $content);
		$content = preg_replace('/(<span class="h-text heading-typo">.*<div class="post-meta">.*<div class="post-meta">)/Uis', '', $content);
		$content = preg_replace('/<p[^>]*>يمكنكم قراءة الخبر الاخر من خلال.*<\/p>/Uis', '', $content);
		$content = preg_replace('/<div id=\'jp-relatedposts\' class=\'jp-relatedposts\'>.*<\/div>/Uis', '', $content);
		$content = preg_replace('/(<p>&#1588;&#1575;&#1607;&#1583; &#1571;&#1610;&#1590;&#1611;&#1575;:.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>&#1610;&#1605;&#1603;&#1606;&#1603; &#1571;&#1610;&#1590;&#1611;&#1575; &#1605;&#1588;&#1575;&#1607;&#1583;&#1577;.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<p>&#1602;&#1583; &#1610;&#1593;&#1580;&#1576;&#1603;.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(>\s*(?:<strong[^>]*>|<span[^>]*>|<em>|<b>|&nbsp;)*+(?:أقرا|إقرا|اقرا|طالع|اقرأ|أقرأ|إقرأ|إقرأ|يهمك|شاهدي|شاهد|أنظر|قـــــــد يهمك|قد يهمك|قد يهمّك)\s*(?:ايضًا|أيضًا|ايضا|أيضا|ايضآ|أيضاً|أيضَا|أيض&#1611;ا|ايضاً|أىضاً|المزيد|أيض|أيضأ)[^<]*<)/Uis', '><', $content);
		$content = preg_replace('/(>\s*مقالات قد تكون[^<]*<)/Uis', '><', $content);
		$content = preg_replace('/(>\s*مقالات اخرى\s*<)/Uis', '><', $content);
		$content = preg_replace('/(>\s*مشاهدة المباريات ايضاً\s*<)/Uis', '><', $content);
		$content = preg_replace('/(<ul class="list row categories.*\/ul>)/Uis', '', $content);
		$content = preg_replace('/(div class="lwptoc_header">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="fit_content">.*<\/div>\s*<\/div>\s*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<h3 id="مقالات-اخرى".*<\/h3>)/Uis', '', $content);
		$content = preg_replace('/(<ul class="related-inside.*<\/ul>)/Uis', '', $content);
		$content = preg_replace('/(<div class="lwptoc_item".*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="LinkSuggestion.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="views.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="crp_related.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div id="interactivity".*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="article__google-news.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<nav>\s*<ul class=\'ez-toc-list.*<\/nav>)/Uis', '', $content);
		$content = preg_replace('/(<p>لمزيد من التفاصيل.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/<p>يمكن متابعة:<\/p>\s*<ul>\s*<li class="yoast-link-suggestion__container.*<\/ul>/Uis', '', $content);
		$content = preg_replace('/<p>يمكن مشاهدة:<\/p>/Uis', '', $content);
		$content = preg_replace('/<p><a class="LinkSuggestion__Link-sc-1gewdgc-4.*a>/Uis', '', $content);
		$content = preg_replace('/<p>شاهد ايضاً:\s*<a.*a>/Uis', '', $content);
		$content = preg_replace('/<p>يمكن متابعة:\s*<a.*a>/Uis', '', $content);
		$content = preg_replace('/<p>\W+:\s*<a.*a>/Uis', '', $content);
		$content = preg_replace('/<ul>\s*<li class="yoast-link-suggestion__container.*<\/ul>/Uis', '', $content);
		$content = preg_replace('/(<div id="ez-toc-container".*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(&#1575;&#1602;&#1585;&#1571; &#1571;&#1610;&#1590;&#1611;&#1575;:.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<div class="ez-toc-title-container">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<ul class="ez-toc-list.*<\/ul>)/Uis', '', $content);
		$content = preg_replace('/(<p class="ez-toc-title">.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<div class="bs-irp right bs-irp-text-2-full">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/((?:<div class="textwidget"|<div class="tie-col-md-3 normal-side">).*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<h3><strong>.*<\/strong>)/Uis', '', $content);
		$content = preg_replace('/(<div class="widget-title the-global-title has-block-head-4">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="mag-box-container clearfix">.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<p class="ez-toc-title">محتوى الخبر<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<ul class="ez-toc.*<\/ul>)/Uis', '', $content);
		$content = preg_replace('/(&#1571;&#1606;&#1592;&#1585; &#1571;&#1610;&#1590;&#1575;.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(&#1588;&#1575;&#1607;&#1583; &#1575;&#1610;&#1590;&#1575;&#1611;.*<\/span>)/Uis', '', $content);
		$content = preg_replace('/(<ul class="posts-items.*<\/ul>)/Uis', '', $content);
		$content = preg_replace('/(<p class="toc_title">.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(اقرأ أيضًا:.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(أنظر أيضا:.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/<div id="inline-related-post"[^<]*>(.*)<\/ul>/Uis', '', $content);
		$content = preg_replace('/(روابط اخرى:.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/محتوى الخبر/Uis', '', $content);
		$content = preg_replace('/Advertisement/Uis', '', $content);
		$content = preg_replace('/(تصاميم صور رمضان تهنئة خلفيات)/Uis', '', $content);
		$content = preg_replace('/(تسجيل منصة مدرستي)/Uis', '', $content);
		$content = preg_replace('/(تسجيل دخول جيميل)/Uis', '', $content);
		$content = preg_replace('/(تسجيل دخول هوتميل)/Uis', '', $content);
		$content = preg_replace('/(مباشر الراجحي)/Uis', '', $content);
		$content = preg_replace('/(دعاء لليمت)/Uis', '', $content);
		$content = preg_replace('/(تردد قناة الجزيرة)/Uis', '', $content);
		$content = preg_replace('/(تسجيل مكتب العمل)/Uis', '', $content);
		$content = preg_replace('/(تسجيل دخول سناب)/Uis', '', $content);
		$content = preg_replace('/(تسجيل دخول واتس)/Uis', '', $content);
		$content = preg_replace('/(تسجيل دخول جداره)/Uis', '', $content);
		$content = preg_replace('/(تسجيل الروضة)/Uis', '', $content);
		$content = preg_replace('/(منصة مدرستي)/Uis', '', $content);
		$content = preg_replace('/(حلول)/Uis', '', $content);
		$content = preg_replace('/(واتس ويب)/Uis', '', $content);
		$content = preg_replace('/(الفاقد التعليمي)/Uis', '', $content);
		$content = preg_replace('/(دعاء السفر)/Uis', '', $content);
		$content = preg_replace('/(بدون حقوق تصاميم رمضان>)/Uis', '', $content);
		$content = preg_replace('/(شاهد :)/Uis', '', $content);
		$content = preg_replace('/(مرتبط)/Uis', '', $content);
		$content = preg_replace('/(صور رمضان 2022 مع تهنئة شهر رمضان المبارك)/Uis', '', $content);
		$content = preg_replace('/(تهنئة و رسائل رمضان 2022 اسلامية للأزواج والام والاب والاخ)/Uis', '', $content);
		$content = preg_replace('/(صور رمضان 2022)/Uis', '', $content);
		$content = preg_replace('/(1)/Uis', '', $content);
		$content = preg_replace('/(صور رمضان)/Uis', '', $content);
		$content = preg_replace('/(روابط مهمة تهمك.*<\/table>)/Uis', '', $content);
		$content = preg_replace('/(مقال للأهمية.*<\/a>)/Uis', '', $content);
		$content = preg_replace('/<div class="info"><p> <span>(.*)<\/a>/Uis', '', $content);
		$content = preg_replace('/<time class="published  d-none" datetime=(.*)<\/time>/Uis', '', $content);
		$content = preg_replace('/<span class="MetaTitle">(.*)<\/time>/Uis', '', $content);
		$content = preg_replace('/(ذات صلة.*<\/ul>)/Uis', '', $content);
		return $content;
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
