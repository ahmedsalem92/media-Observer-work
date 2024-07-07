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

