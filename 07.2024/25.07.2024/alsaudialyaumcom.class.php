<?php

class alsaudialyaumcom extends plugin_base
{

	// ANT settings
	protected $ant_precision = 2;

	// CRAWL settings
	protected $stop_on_article_found = true;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'article',
				'regexp' => '/<loc>(.*)<\/loc>/Uis',
				'append_domain' => true,
				'process_link' => 'process_article_link'
			)
		),
		'article' => array(
			'headline' => '/^.*<h1[^<]*>(.*)<\/h1>/Uis',
			'content' => '/<div class="article-content".*>(.*)(?:<blockquote class="twitter-tweet">|<div class="row also bg-section">)/Uis',
			'author' => false,
			'article_date' => '/<div class="article-time">.*<span>(.*)<\/span>/Uis'
		)
	);

	protected function process_list1_link($link, $referer_link, $logic)
	{

		return $link;
	}

	private $links = array();
	private $array_index = 0;

	protected function process_article_link($link, $referer_link, $logic)
	{
		$temp_link = '';

		if (empty($this->links)) {
			$result = $this->ant->get($referer_link);

			if (preg_match_all('/<loc>(.*)<\/loc>/Uis', $result, $matches)) {
				$this->links = $matches[0];
				$this->array_index = 0;
			}
		}

		if ($this->array_index < sizeof($this->links) && isset($this->links[$this->array_index])) {
			$temp_link = str_replace('<loc>', '', $this->links[$this->array_index]);
			$temp_link = str_replace('</loc>', '', $temp_link);

			if ($temp_link == 'https://arqam.news/321264/') return false;

			$this->array_index++; // Move to the next element for the next iteration

			return $temp_link;
		}

		return '';
	}


	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/(<script.*<\/script>)/Uis', '', $content);
		$content = preg_replace('/(<div class="ozuftl9m.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<div class="oajrlxb2.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<p style="color:#E74C3C;">.*<\/p>)/Uis', '', $content);
		$content = preg_replace('/(<table.*<\/table>)/Uis', '', $content);
		$content = preg_replace('/(<div class="post-bottom-meta.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(<span class="tagcloud">.*<\/span>)/Uis', '', $content);
		$content = preg_replace('/(<div class="stjgntxs.*<\/div>)/Uis', '', $content);
		$content = preg_replace('/(إضغط هنا)/Uis', '', $content);
		$content = preg_replace('/عبر هذا الرابط/Uis', '', $content);
		$content = preg_replace('/(أخبار ذات صلة)/Uis', '', $content);
		$content = preg_replace('/على الرابط التالي:/Uis', '', $content);
		$content = preg_replace('/هنا/Uis', '', $content);
		$content = preg_replace('/اقرا ايضا: /Uis', '', $content);
		$content = preg_replace('/إحجز تذكرتك من هنا"/Uis', '', $content);
		$content = preg_replace('/<p>(اقرأ ايضا:.*)<\/p>/Uis', '', $content);
		$content = preg_replace('/✅/Uis', '', $content);
		$content = preg_replace('/<p>(اقرا ايضا:.*)<\/p>/Uis', '', $content);
		$content = preg_replace('/<blockquote.*>(.*)<\/blockquote>/Uis', '', $content);
		$content = preg_replace('/<span>(.*)<\/span>/Uis', '', $content);
		$content = preg_replace('/<div class="article-writer".*>(.*)<\/div>/Uis', '', $content);
		$content = preg_replace('/ستحب قراءة:/Uis', '', $content);
		$content = preg_replace('/-/Uis', '', $content);
		$content = preg_replace('/اقرأ أيضا../Uis', '', $content);
		$content = preg_replace('/✍️ وللتقديم في وظائف/Uis', '', $content);
		$content = preg_replace('/يمكنك زيارة هذا الرابط/Uis', '', $content);
		$content = preg_replace('/اقرا ايضا :/Uis', '', $content);
		$content = preg_replace('/واس/Uis', '', $content);
		$content = preg_replace('/الرابط التالي:/Uis', '', $content);
		$content = preg_replace('/>\s*(?:<strong[^>]*>|<span[^>]*>|<em>|<b>|&nbsp;)*+(?:أقرا|إقرا|اقرا|طالع|اقرأ|أقرأ|إقرأ|إقرأ|يهمك|شاهدي|شاهد|أنظر|قـــــــد يهمك|قد يهمك|قد يهمّك)\s*(?:ايضًا|أيضًا|ايضا|أيضا|ايضآ|أيضاً|أيضَا|أيض&#1611;ا|ايضاً|أىضاً|المزيد|أيض|أيضأ)[^<]*<a.*\/a>/Uis', '', $content);




		return $content;
	}

	protected function process_headline($headline, $article_data)
	{

		$headline = preg_replace('/(بادر بالتقديم)/Uis', '', $headline);
		$headline = preg_replace('/بالصور../Uis', '', $headline);


		return $headline;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{
		if (preg_match('/\p{Arabic}+ (\d{1,2}) (\D+) (\d{4}) \| (\d{1,2}):(\d{2}) (م|ص)/u', $article_date, $matches)) {
			// Matches: الخميس 25 يوليو 2024 | 06:16 مساءً
			$day = (int)$matches[1];
			$arabic_month = trim($matches[2]);
			$year = (int)$matches[3];
			$hour = (int)$matches[4];
			$minute = (int)$matches[5];
			$period = $matches[6];

			$arabic_months = [
				'يناير' => 1,
				'فبراير' => 2,
				'مارس' => 3,
				'أبريل' => 4,
				'مايو' => 5,
				'يونيو' => 6,
				'يوليو' => 7,
				'أغسطس' => 8,
				'سبتمبر' => 9,
				'أكتوبر' => 10,
				'نوفمبر' => 11,
				'ديسمبر' => 12
			];

			// Convert month name to number
			if (isset($arabic_months[$arabic_month])) {
				$month = $arabic_months[$arabic_month];
			} else {
				$month = null;
				error_log("Undefined month: $arabic_month");
			}

			// Adjust hour for AM/PM
			if ($period === 'م' && $hour != 12) {
				$hour += 12; // Convert to 24-hour format
			} elseif ($period === 'ص' && $hour === 12) {
				$hour = 0; // Midnight case
			}

			// Format the date to Y-m-d H:i:s
			$article_date = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute, 0);
		}

		return $article_date;
	}
}
