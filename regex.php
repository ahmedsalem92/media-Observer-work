<?php

// crawler.class.php  // 

$r1 = '<div class="card">\s*<!--.*?-->\s*<a href="([^"]*)"';

// <div class="card">
// <!-- <a href="https://www.timeturk.com/dunya" class="card-cat" target="_blank" title="DÜNYA Haberleri">DÜNYA</a> -->
// <a href="
// https://www.timeturk.com/dunya/soykirimci-israil-gazze-seridi-ne-bomba-yagdirdi/haber-1789108" title="Soykırımcı İsrail Gazze Şeridi'ne bomba yağdırdı!
// "

// <br \/>\s*<a\s*id="ctl00_ContentPlaceHolder.*href="(.*)"

$h1 = '<h1[^<]*>(.*)<\/h1>';

// regex لازالة
/(<button[^>]*>Subscribe<\/button>)/Uis




// select headline

// 'regexp'=> '/<\/div>\s*<div>\s*<div class="pagination-card">\s*<a href="([^"]*)" title=/Uis',
// 'regexp'=> '/<div class="col-12 col-md-6 col-lg-4 col-xl-3">\s*<div class="card">\s*<!-- <a .*<a href="(.*)"/Uis',

return date('Y-m-d H:i:s');

// توقف عند ال 40 في ال next page

// protected $next_page = 1;

// protected function process_next_link($link, $referer_link, $logic)
// {
//     $this->next_page = $this->next_page + 1;
//     if ($this->page_count < 41) {
//         return 'https://yemennownews.com/?page=' . $this->next_page;
//     } else {
//         return false;
//     }
// }

// (#390)
// --------------------------
// found already processed article ()... skipping it...
// found already processed article ()... skipping it...
// found already processed article ()... skipping it...
// found already processed article ()... skipping it...
// found already processed article ()... skipping it...
// found new article...
// getting article page... (https://swiftnewz.com/archives/463757)
// using proxy = 77.247.115.157:8800



 // واللوجيك دي بنجيب لينك المقال لوحده والتاريخ لوحده

protected function process_article_link($link, $referer_link, $logic) {

    if(
        preg_match('/<a class="image" href="([^"]*)"/Uis',$link,$article_link) &&
        preg_match('/<span class="fa fa-clock-o">(.*)<a href="#">/Uis',$link,$matche) 
        
        
    ){
        $link = $article_link[1];
        $this->date_article = $matche[1];
    }

    return $link;  
    

}

/// empty headline
protected function process_headline($headline, $article_data)
{

    if (empty($headline)) {
        return 'no headline';
    } else {
        return $headline;
    }
}

// empty content
if (empty($content)) {
    return 'no content';
} else {
    return $content;
}

// empty article_date
if (empty($article_date)) {
    return date('Y-m-d H:i:s');
} else {

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


// select all the data first in the page then loop in links
1 => array(
    'type' => 'article',
    'regexp' => [
        '/<div class="col-md-12">\s*<\/div>\s*<\/div>(.*)<\/section>/Uis',
        '/<a href="(.*)"/Uis',
    ],
    'append_domain' => true,
)

// loop on the next page

'list1' => array(
    0 => array(
        'type' => 'list1',
        'regexp' => '/^(.*)$/Uis',
        'append_domain' => true,
        'process_link' => 'process_list1_link',
    ),

protected $next_page = 2; 
protected function process_list1_link($link, $referer_link, $logic)
{
    return 'https://tech2030.net/?s=&paged=' . $this->next_page++;
}



