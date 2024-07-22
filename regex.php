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

