<?php
header('Content-Type: application/json');
$file = __DIR__ . '/products.json';
if (!file_exists($file)) {
$preset = [
["id" => 1, "name" => "Book", "price" => 5.5],
["id" => 2, "name" => "Pen", "price" => 1.2]
];
file_put_contents($file, json_encode($preset, JSON_PRETTY_PRINT));
}
$list = json_decode(file_get_contents($file), true);
$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
if ($method === 'GET' && $path[0] === 'products' && count($path) === 1) {
echo json_encode($list);
exit;
}
if ($method === 'GET' && $path[0] === 'products' && isset($path[1])) {
$id = (int)$path[1];
foreach ($list as $p) if ($p['id'] === $id) {echo json_encode($p); exit;}
http_response_code(404);
echo json_encode(["error" => "Not found"]);
exit;
}
http_response_code(404);
echo json_encode(["error" => "Unknown route"]);