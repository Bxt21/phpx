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

$list = json_decode(file_get_contents($file), true) ?? [];
$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));

function saveUsers($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

# ========== ROUTES ==========

# GET /users → all users
if ($method === 'GET' && $path[0] === 'users' && count($path) === 1) {
    echo json_encode($list);
    exit;
}

# GET /users/{id} → single user
if ($method === 'GET' && $path[0] === 'users' && isset($path[1])) {
    $id = (int)$path[1];
    foreach ($list as $u) {
        if ($u['id'] === $id) {
            echo json_encode($u);
            exit;
        }
    }
    http_response_code(404);
    echo json_encode(["error" => "Not found"]);
    exit;
}

# POST /users → create user
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
    saveUsers($file, $list);
    http_response_code(201);
    echo json_encode($entry);
    exit;
}

# PUT /users/{id} → update user
if ($method === 'PUT' && $path[0] === 'users' && isset($path[1])) {
    $id = (int)$path[1];
    $body = json_decode(file_get_contents("php://input"), true);
    foreach ($list as &$u) {
        if ($u['id'] === $id) {
            if (isset($body['name'])) $u['name'] = $body['name'];
            if (isset($body['email'])) $u['email'] = $body['email'];
            saveUsers($file, $list);
            echo json_encode($u);
            exit;
        }
    }
    http_response_code(404);
    echo json_encode(["error" => "Not found"]);
    exit;
}

# DELETE /users/{id} → remove user
if ($method === 'DELETE' && $path[0] === 'users' && isset($path[1])) {
    $id = (int)$path[1];
    foreach ($list as $i => $u) {
        if ($u['id'] === $id) {
            array_splice($list, $i, 1);
            saveUsers($file, $list);
            http_response_code(204);
            exit;
        }
    }
    http_response_code(404);
    echo json_encode(["error" => "Not found"]);
    exit;
}

# Unknown route
http_response_code(404);
echo json_encode(["error" => "Unknown route"]);
