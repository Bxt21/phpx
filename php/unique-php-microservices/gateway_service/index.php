<?php
header('Content-Type: application/json');

$usr = 'http://127.0.0.1:8101';
$prd = 'http://127.0.0.1:8102';

$uid = isset($_GET['user']) ? (int)$_GET['user'] : null;
$pid = isset($_GET['product']) ? (int)$_GET['product'] : null;

function hit($url) {
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    $r = curl_exec($c);
    $s = curl_getinfo($c, CURLINFO_HTTP_CODE);
    curl_close($c);
    return [$s, $r];
}

$out = ["user" => null, "product" => null, "issues" => []];

if ($uid) {
    [$s, $r] = hit("$usr/users/$uid");
    $out['user'] = $s === 200 ? json_decode($r, true) : null;
    if ($s !== 200) $out['issues'][] = "user service problem";
}

if ($pid) {
    [$s, $r] = hit("$prd/products/$pid");
    $out['product'] = $s === 200 ? json_decode($r, true) : null;
    if ($s !== 200) $out['issues'][] = "product service problem";
}

if (!$uid && !$pid) {
    http_response_code(400);
    echo json_encode(["error" => "provide user and/or product params"]);
    exit;
}

echo json_encode($out, JSON_PRETTY_PRINT);
