<?php

// crawler.class.php  // 

$r1 = '<div class="card">\s*<!--.*?-->\s*<a href="([^"]*)"';

// <div class="card">
// <!-- <a href="https://www.timeturk.com/dunya" class="card-cat" target="_blank" title="DÜNYA Haberleri">DÜNYA</a> -->
// <a href="
// https://www.timeturk.com/dunya/soykirimci-israil-gazze-seridi-ne-bomba-yagdirdi/haber-1789108" title="Soykırımcı İsrail Gazze Şeridi'ne bomba yağdırdı!
// "


$h1 = '<h1[^<]*>(.*)<\/h1>';
// select headline

// 'regexp'=> '/<\/div>\s*<div>\s*<div class="pagination-card">\s*<a href="([^"]*)" title=/Uis',
// 'regexp'=> '/<div class="col-12 col-md-6 col-lg-4 col-xl-3">\s*<div class="card">\s*<!-- <a .*<a href="(.*)"/Uis',



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

