<?php
ini_set('memory_limit','1G');
require __DIR__ . '/vendor/autoload.php';
use mpyw\Co\Co;
use mpyw\Co\CURLException;

echo 'Please enter your tracking code (like:UA-00000000-0)'.PHP_EOL;
$trackingCode = trim(fgets(STDIN));

do {
    echo 'How much send a request for test?' . PHP_EOL;
    $totalRequest = trim(fgets(STDIN));
    if (!filter_var($totalRequest, FILTER_VALIDATE_INT)) {
        echo 'Invalid number, try again'.PHP_EOL;
    }else {
        break;
    }
}while(true);

$url = "http://www.google-analytics.com/collect";

$request = [];
for($i = 0; $i<$totalRequest; $i++) {

    $params  = array(
        'v'=>'1',
        '_v'=>'j40',
        'a'=>$i,
        't'=>'pageview',
        '_s'=>'1',
        'dr'=>'http://example.com/', //referer
        'dl'=>'http://example.com/testTraffic', //path
        'ul'=>'ja',
        'de'=>'UTF-8',
        'dt'=>'GoogleAnalyticsRequestTest',
        'sd'=>'24-bit',
        'sr'=>'1280x800',
        'vp'=>'1279x175',
        'je'=>'0',
        'fl'=>'20.0 r0',
        'cid'=>"1".$i,
        'tid'=>$trackingCode//Google Analytics Tracking Code Here
    );
    $built = $url."?".http_build_query($params);

    $request[] = function () use ($built) {
        $content = yield curl_init_with($built);
        return $content;
    };
}

$data = Co::wait($request,['throw' => false]);

echo 'Done.'.PHP_EOL;

function curl_init_with(string $url, array $options = [])
{
    $ch = curl_init();
    $options = array_replace([
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
    ], $options);
    curl_setopt_array($ch, $options);
    return $ch;
}
