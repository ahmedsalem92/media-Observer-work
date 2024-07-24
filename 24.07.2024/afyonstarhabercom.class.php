<?php

class afyonstarhabercom extends plugin_base
{
	// ANT settings
	protected $ant_precision = 2;

	// CRAWL settings
	protected $stop_on_article_found = false;
	protected $stop_date_override = true;

	// DEFINITIONS
	protected $site_timezone = 'Asia/Amman';
	protected $logic = array(
		'list1' => array(
			0 => array(
				'type' => 'list1',
				'regexp' => '/<span aria-current="page" class="page-numbers current">.*href="(.*)"/Uis',
				'append_domain' =>	false
			),
			1 => array(
				'type' => 'article',
				'regexp' => '/<div class="kategoriSablon1">.*href="(.*)"/Uis',
				'append_domain' => false
			)
		),
		'article' => array(
			'headline' => '/<title>(.*)<\/title>/Uis',
			'content' => '/<div class="haberinYazisi">(.*)(?:<\/article>|<div class="etiketler">)/Uis',
			'author' => false,
			'article_date' => '/(?:published_time" content="|"datePublished":")(.*)"/Uis'
		)
	);

	protected function process_headline($headline, $article_data)
	{

		$headline = preg_replace('/(\| Afyon Haber \| Afyon Son Dakika Haberleri \| Afyon Star HaberAfyon Haber \| Afyon Son Dakika Haberleri \| Afyon Star Haber)/Uis', '', $headline);
		return $headline;
	}


	protected function process_content($content, $article_data)
	{

		$content = preg_replace('/(Hibya Haber Ajansı)/Uis', '', $content);

		return $content;
	}

	// process the date of the article, return in YYYY-MM-DD HH:mm:ss format
	protected function process_date($article_date)
	{
		// Turkish month mapping
		$turkish_months = [
			'Ocak' => '01', 'Şubat' => '02', 'Mart' => '03', 'Nisan' => '04',
			'Mayıs' => '05', 'Haziran' => '06', 'Temmuz' => '07', 'Ağustos' => '08',
			'Eylül' => '09', 'Ekim' => '10', 'Kasım' => '11', 'Aralık' => '12'
		];

		// Replace Turkish month names with numbers
		foreach ($turkish_months as $tr_month => $num_month) {
			$article_date = str_replace($tr_month, $num_month, $article_date);
		}

		// Convert to DateTime object
		$article_date_obj = DateTime::createFromFormat('d m Y - H:i', $article_date);

		// Format to Y-m-d H:i:s
		if ($article_date_obj) {
			$article_date = $article_date_obj->format('Y-m-d H:i:s');
		}

		return $article_date;
	}
}
