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

$list = json_decode(file_get_contents($file), true) ?? [];
$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));

function saveData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

# ========== ROUTES ==========

# GET /products → all
if ($method === 'GET' && $path[0] === 'products' && count($path) === 1) {
    echo json_encode($list);
    exit;
}

# GET /products/{id} → one
if ($method === 'GET' && $path[0] === 'products' && isset($path[1])) {
    $id = (int)$path[1];
    foreach ($list as $p) {
        if ($p['id'] === $id) {
            echo json_encode($p);
            exit;
        }
    }
    http_response_code(404);
    echo json_encode(["error" => "Not found"]);
    exit;
}

# POST /products → create
if ($method === 'POST' && $path[0] === 'products') {
    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input || !isset($input['name']) || !isset($input['price'])) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid input"]);
        exit;
    }
    $newId = empty($list) ? 1 : max(array_column($list, 'id')) + 1;
    $newItem = ["id" => $newId, "name" => $input['name'], "price" => (float)$input['price']];
    $list[] = $newItem;
    saveData($file, $list);
    http_response_code(201);
    echo json_encode($newItem);
    exit;
}

# PUT /products/{id} → update
if ($method === 'PUT' && $path[0] === 'products' && isset($path[1])) {
    $id = (int)$path[1];
    $input = json_decode(file_get_contents("php://input"), true);
    foreach ($list as &$p) {
        if ($p['id'] === $id) {
            if (isset($input['name'])) $p['name'] = $input['name'];
            if (isset($input['price'])) $p['price'] = (float)$input['price'];
            saveData($file, $list);
            echo json_encode($p);
            exit;
        }
    }
    http_response_code(404);
    echo json_encode(["error" => "Not found"]);
    exit;
}

# DELETE /products/{id} → remove
if ($method === 'DELETE' && $path[0] === 'products' && isset($path[1])) {
    $id = (int)$path[1];
    foreach ($list as $i => $p) {
        if ($p['id'] === $id) {
            array_splice($list, $i, 1);
            saveData($file, $list);
            http_response_code(204); # No content
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
