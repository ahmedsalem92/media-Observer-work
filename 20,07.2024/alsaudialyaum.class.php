<?php

class arqamtmcom extends plugin_base
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
			'content' => '/<div class="article-content".*>(.*)<div class="row also bg-section">/Uis',
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
		$content = preg_replace('/<p>(من \(<a.*<\/a>)/Uis', '', $content);
		$content = preg_replace('/(<a.*<\/a>)/Uis', '', $content);
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
		
		
		

		
		
		
		
		$content = preg_replace('/واس/Uis', '', $content);
		
		
		return $content;
	}

	protected function process_headline($headline, $article_data){
		
		$headline = preg_replace('/(بادر بالتقديم)/Uis', '', $headline);
		$headline = preg_replace('/بالصور../Uis', '', $headline);

		
		return $headline;
	}

	// process the date of the article, return in YYYY-MM-DD HH:ii:ss format
	protected function process_date($article_date)
	{
		$months = [
			'يناير' => '01', 'فبراير' => '02', 'مارس' => '03',
			'أبريل' => '04', 'مايو' => '05', 'يونيو' => '06',
			'يوليو' => '07', 'أغسطس' => '08', 'سبتمبر' => '09',
			'أكتوبر' => '10', 'نوفمبر' => '11', 'ديسمبر' => '12'
		];

		if (preg_match('/^(\S+) (\d+) (\S+) (\d{4}) | (\d{2}):(\d{2}) (\S+)/u', $article_date, $matches)) {
			$month = $months[$matches[3]];
			$day = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
			$year = $matches[4];
			$hour = str_pad($matches[5], 2, '0', STR_PAD_LEFT);
			$minute = str_pad($matches[6], 2, '0', STR_PAD_LEFT);

			$date_formatted = $year . '-' . $month . '-' . $day . ' ' . $hour . ':' . $minute . ':00';

			$article_date_obj = DateTime::createFromFormat(
				'Y-m-d H:i:s',
				$date_formatted,
				new DateTimeZone($this->site_timezone)
			);
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;
	}
}
