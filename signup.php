<?php
$core = __DIR__;
$vendo_dir = __DIR__ . "/vendo";

if(!file_exists($vendo_dir)){
    mkdir($vendo_dir, 0777, true);
}

$msg = "";
$links = [];

if(isset($_POST['vendo_name'])){

    // sanitize name
    $vendo_name = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $_POST['vendo_name']));

    if(!$vendo_name){
        $msg = "❌ Invalid vendo name";
    } else {

        $path = "$vendo_dir/$vendo_name";

        if(file_exists($path)){
            $msg = "❌ Vendo already exists";
        } else {

            mkdir($path, 0777, true);

            // ================= COPY CORE FILES =================
            $files = [
                "index.php",
                "pay.php",
                "wait.php",
                "admin.php",
                "api.php",
                "clear.php",
                "config.json",
                "vouchers.json",
                "tokens.json",
                "logs.json",
                "qr.jpg"
            ];

            foreach($files as $f){
                if(file_exists("$core/$f")){
                    copy("$core/$f", "$path/$f");
                }
            }

            // ================= GENERATE UNIQUE API KEY =================
            $new_key = substr(md5(uniqid().rand()), 0, 12);

            // ================= UPDATE CONFIG.JSON =================
            $config_file = "$path/config.json";

            $config = [];
            if(file_exists($config_file)){
                $config = json_decode(file_get_contents($config_file), true);
            }

            if(!is_array($config)) $config = [];

            $config['api_key'] = $new_key;
            $config['earnings'] = $config['earnings'] ?? 0;

            file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));

            // ================= BUILD LINKS =================
            $base = "http://".$_SERVER['HTTP_HOST']."/vendo/$vendo_name";

            $index = "$base/index.php";
            $admin = "$base/admin.php";
            $api   = "$base/api.php?key=$new_key";
            $wait  = "$base/wait.php";

            // ================= SAVE SETUP =================
            $setup = [
                "vendo_name"=>$vendo_name,
                "index"=>$index,
                "admin"=>$admin,
                "api"=>$api,
                "wait"=>$wait,
                "usage"=>"Use API like this: api.php?key=$new_key&amount=10"
            ];

            file_put_contents("$path/setup.json", json_encode($setup, JSON_PRETTY_PRINT));

            $links = $setup;
            $msg = "✅ Vendo Created Successfully!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Create Vendo</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
    font-family: Arial;
    background: linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    color:white;
    text-align:center;
    margin:0;
}

.box {
    background:white;
    color:black;
    margin:20px;
    padding:20px;
    border-radius:15px;
}

input {
    width:90%;
    padding:12px;
    margin:10px;
    border-radius:10px;
    border:1px solid #ccc;
}

button {
    padding:15px;
    width:90%;
    border:none;
    border-radius:10px;
    background:#2196F3;
    color:white;
    font-size:16px;
}

.link {
    background:#eee;
    padding:10px;
    margin:5px;
    border-radius:8px;
    word-break:break-all;
}
</style>
</head>

<body>

<h2>🛠 Create Vendo</h2>

<div class="box">
<form method="post">
<input name="vendo_name" placeholder="Enter vendo name (e.g. juanwifi)" required>
<button>Create</button>
</form>

<h3><?= $msg ?></h3>
</div>

<?php if($links): ?>
<div class="box">
<h3>🔗 Your Vendo Links</h3>

<p>🌐 Index</p>
<div class="link"><?= $links['index'] ?></div>

<p>⚙️ Admin</p>
<div class="link"><?= $links['admin'] ?></div>

<p>🔑 API</p>
<div class="link"><?= $links['api'] ?>&amount=10</div>

<p>⏳ Wait Page</p>
<div class="link"><?= $links['wait'] ?></div>

<br>

<a href="vendo/<?= $links['vendo_name'] ?>/setup.json" download>
<button>⬇ Download Setup File</button>
</a>

</div>
<?php endif; ?>

</body>
</html>