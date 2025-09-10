<?php
header('Content-Type: application/json');
$file = __DIR__ . '/users.json';
if (!file_exists($file)) {
$preset = [
["id" => 1, "name" => "Eve", "email" => "eve@mail.com"],
["id" => 2, "name" => "Dan", "email" => "dan@mail.com"]
];
file_put_contents($file, json_encode($preset, JSON_PRETTY_PRINT));
}
$list = json_decode(file_get_contents($file), true);
$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
if ($method === 'POST' && $path[0] === 'users') {
$body = json_decode(file_get_contents('php://input'), true);
if (!$body || !isset($body['name']) || !isset($body['email'])) {
http_response_code(400);
echo json_encode(["error" => "Invalid data"]);
exit;
}
$ids = array_column($list, 'id');
$newId = $ids ? max($ids) + 1 : 1;
$entry = ["id" => $newId, "name" => $body['name'], "email" => $body['email']];
$list[] = $entry;
file_put_contents($file, json_encode($list, JSON_PRETTY_PRINT));
http_response_code(201);
echo json_encode($entry);
exit;
}
if ($method === 'GET' && $path[0] === 'users' && isset($path[1])) {
$id = (int)$path[1];
foreach ($list as $u) if ($u['id'] === $id) {echo json_encode($u); exit;}
http_response_code(404);
echo json_encode(["error" => "Not found"]);
exit;
}
http_response_code(404);
echo json_encode(["error" => "Unknown route"]);