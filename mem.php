<?php

function format_size($size) {
    $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    if ($size == 0) {
        return 'n/a';
    } else {
        return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i];
    }
}

function updateChart ($mems) {
    $lowerBound = 0;
    $upperBound = 2000;
    $width = 350;
    $height = 200;
    $datapoints = isset($_SESSION['argv']) && isset($_SESSION['argv'][1]) ? $_SESSION['argv'][1] : 400;
    $title = isset($_SESSION['argv']) && isset($_SESSION['argv'][2]) ? $_SESSION['argv'][2] : 'Memory Usage';
    $title = isset($_SESSION['argv']) && isset($_SESSION['argv'][2]) ? $_SESSION['argv'][2] : 'Memory Usage';
    $last = count($mems) - 1;
    $lastValue = format_size($mems[$last]);
    $filename = "http://chart.apis.google.com/chart?" . implode('&', array(
        "chxt=y",
        "chm=t" . rawurlencode($lastValue) . ",0000FF,0,{$last},10",
        "chxr=0,{$lowerBound},{$upperBound}",
        "chtt=" . rawurlencode($title),
        "cht=lc",
        "chs={$width}x{$height}",
        "chds=" . $lowerBound * 1024 * 1024 . "," . $upperBound * 1024 * 1024,
        "chd=t:" . implode(',', array_slice($mems, $datapoints * -1)),
    ));
    print("$filename\n");
    file_put_contents('mem.jpg', file_get_contents($filename));
}

$render = false;
$mems = array();
$fh = fopen('php://stdin', 'r');
$i = 0;
while ($line = fgets($fh)) {
    if (trim($line) === 'START') {
        $render = true;
    } else {
        list($date, $ts, $mem) = explode("\t", $line);
        $mems[] = trim($mem)*1024;
        if ($render) {
            updateChart($mems);
        }
        $i++;
    }
}
