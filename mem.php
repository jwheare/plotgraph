<?php

function format_size($size) {
    $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    if ($size == 0) {
        return 'n/a';
    } else {
        return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i];
    }
}

$CONF = array(
    'lowerBound' => 0,
    'upperBound' => 300,
    'width' => 350,
    'height' => 200,
    'datapoints' => isset($_SERVER['argv']) && isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 400,
    'title' => isset($_SERVER['argv']) && isset($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : 'Memory Usage',
);


function updateChart ($mems) {
    global $CONF;
    $last = count($mems) - 1;
    $lastValue = format_size($mems[$last]);
    $filename = "http://chart.apis.google.com/chart?" . implode('&', array(
        "chxt=y",
        "chxr=0,{$CONF['lowerBound']},{$CONF['upperBound']}",
        "chtt=" . rawurlencode($CONF['title']),
        "cht=lc",
        "chs={$CONF['width']}x{$CONF['height']}",
        "chds=" . $CONF['lowerBound'] * 1024 * 1024 . "," . $CONF['upperBound'] * 1024 * 1024,
        "chm=t" . rawurlencode($lastValue) . ",0000FF,0,{$last},10",
        "chd=t:" . implode(',', array_slice($mems, $CONF['datapoints'] * -1)),
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
