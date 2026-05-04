<?php
session_start();

$vendo_dir = __DIR__ . "/vendo";

// 🔐 SUPER ADMIN PASSWORD
$pass_file = "super_pass.txt";

if(!file_exists($pass_file)){
    file_put_contents($pass_file, password_hash("admin123", PASSWORD_DEFAULT));
}

// ================= LOGIN =================
$saved_pass = file_get_contents($pass_file);

if(!isset($_SESSION['super'])){
    if(isset($_POST['pass'])){
        if(password_verify($_POST['pass'], $saved_pass)){
            $_SESSION['super'] = true;
            header("Location: superadmin.php");
            exit;
        } else {
            $error = "Wrong password!";
        }
    }
?>
<div style="text-align:center;margin-top:80px;">
<h2>🔐 Super Admin Login</h2>
<form method="post">
<input type="password" name="pass" placeholder="Password"><br><br>
<button>Login</button>
</form>
<p style="color:red;"><?= $error ?? '' ?></p>
</div>
<?php exit; }

// LOGOUT
if(isset($_GET['logout'])){
    session_destroy();
    header("Location: superadmin.php");
    exit;
}

// ================= LOAD VENDOS =================
$vendos = [];

if(file_exists($vendo_dir)){
    foreach(scandir($vendo_dir) as $v){
        if($v === '.' || $v === '..') continue;

        $path = "$vendo_dir/$v";

        if(is_dir($path)){

            $config_file = "$path/config.json";
            $config = [];

            if(file_exists($config_file)){
                $config = json_decode(file_get_contents($config_file), true);
            }

            if(!is_array($config)) $config = [];

            // 🔥 AUTO FIX API KEY IF MISSING
            if(!isset($config['api_key'])){
                $new = substr(md5(uniqid()), 0, 12);
                $config['api_key'] = $new;
                file_put_contents($config_file, json_encode($config, JSON_PRETTY_PRINT));
            }

            $key = $config['api_key'];
            $earnings = $config['earnings'] ?? 0;

            $base = "http://".$_SERVER['HTTP_HOST']."/vendo/$v";

            $vendos[] = [
                "name"=>$v,
                "index"=>"$base/index.php",
                "admin"=>"$base/admin.php",
                "api"=>"$base/api.php?key=$key",
                "key"=>$key,
                "earnings"=>$earnings
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Super Admin</title>

<style>
body{
    font-family:Arial;
    background:#0f2027;
    color:white;
    padding:15px;
}
.card{
    background:white;
    color:black;
    padding:15px;
    margin-bottom:15px;
    border-radius:12px;
}
a{color:blue;}
</style>
</head>

<body>

<h2>🔥 SUPER ADMIN PANEL</h2>
<a href="?logout=1" style="color:red;">Logout</a>

<?php foreach($vendos as $v): ?>
<div class="card">

<h3>📡 <?= htmlspecialchars($v['name']) ?></h3>

<p>🌐 Index:<br>
<a href="<?= $v['index'] ?>"><?= $v['index'] ?></a></p>

<p>⚙️ Admin:<br>
<a href="<?= $v['admin'] ?>"><?= $v['admin'] ?></a></p>

<p>🔑 API Key:<br>
<b><?= $v['key'] ?></b></p>

<p>🔗 API:<br>
<a href="<?= $v['api'] ?>&amount=10">
<?= $v['api'] ?>&amount=10
</a></p>

<p>💰 Earnings:<br>
₱<?= $v['earnings'] ?></p>

</div>
<?php endforeach; ?>

</body>
</html>