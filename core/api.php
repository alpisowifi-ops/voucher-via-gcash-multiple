<?php
date_default_timezone_set("Asia/Manila");
header("Content-Type: application/json");

// ================= FILES =================
$voucher_file = "vouchers.json";
$tokens_file  = "tokens.json";
$logs_file    = "logs.json";
$config_file  = "config.json";

// ================= LOAD JSON =================
function load_json($file){
    if(!file_exists($file)){
        file_put_contents($file, json_encode([], JSON_PRETTY_PRINT));
    }
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

// ================= LOAD CONFIG =================
$config = load_json($config_file);

// 🔐 SECRET KEY (DYNAMIC)
$SECRET = $config['api_key'] ?? null;

// fallback kung wala pa
if(!$SECRET){
    $SECRET = substr(md5(uniqid()), 0, 12);
    $config['api_key'] = $SECRET;
    file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));
}

// ================= AUTH =================
if(!isset($_GET['key']) || $_GET['key'] !== $SECRET){
    echo json_encode([
        "status"=>"error",
        "msg"=>"Unauthorized"
    ]);
    exit;
}

// ================= LOAD DATA =================
$data   = load_json($voucher_file);
$tokens = load_json($tokens_file);
$logs   = load_json($logs_file);

// ================= GET AMOUNT =================
$amount = intval($_GET['amount'] ?? 0);

if(!$amount){
    echo json_encode([
        "status"=>"error",
        "msg"=>"Invalid amount"
    ]);
    exit;
}

// ================= FIND TOKEN =================
$targetToken = null;

foreach(array_reverse($tokens, true) as $t => $info){
    if(
        intval($info['amount']) === $amount &&
        $info['status'] === "pending" &&
        (time() - $info['time']) <= 180
    ){
        $targetToken = $t;
        break;
    }
}

if(!$targetToken){
    echo json_encode([
        "status"=>"error",
        "msg"=>"No pending token"
    ]);
    exit;
}

// ================= CHECK VOUCHER =================
if(!isset($data[$amount]) || count($data[$amount]) == 0){
    echo json_encode([
        "status"=>"error",
        "msg"=>"No voucher available"
    ]);
    exit;
}

// ================= GET VOUCHER =================
$voucher = array_shift($data[$amount]);
file_put_contents($voucher_file, json_encode($data, JSON_PRETTY_PRINT));

// ================= EARNINGS =================
$config['earnings'] = ($config['earnings'] ?? 0) + $amount;
file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));

// ================= SAVE CURRENT =================
file_put_contents("current.txt", $voucher);

// ================= MARK TOKEN =================
$tokens[$targetToken]['status'] = "used";
$tokens[$targetToken]['used_time'] = time();
unset($tokens[$targetToken]);

file_put_contents($tokens_file, json_encode($tokens, JSON_PRETTY_PRINT));

// ================= LOG =================
$logs[] = [
    "voucher"=>$voucher,
    "amount"=>$amount,
    "time"=>date("Y-m-d H:i:s"),
    "ip"=>$_SERVER['REMOTE_ADDR'] ?? 'unknown',
    "token"=>$targetToken
];

file_put_contents($logs_file, json_encode($logs, JSON_PRETTY_PRINT));

// ================= RESPONSE =================
echo json_encode([
    "status"=>"success",
    "voucher"=>$voucher,
    "amount"=>$amount
]);