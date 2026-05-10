<?php

$core = __DIR__ . "/core";
$vendo_dir = __DIR__ . "/vendo";

if(!file_exists($vendo_dir)){
    mkdir($vendo_dir,0777,true);
}

$msg = "";
$links = [];

if(isset($_POST['vendo_name'])){

    $vendo_name = strtolower(
        preg_replace('/[^a-zA-Z0-9]/','',$_POST['vendo_name'])
    );

    if(!$vendo_name){

        $msg = "❌ Invalid Name";

    }else{

        $path = "$vendo_dir/$vendo_name";

        if(file_exists($path)){

            $msg = "❌ Vendo Already Exists";

        }else{

            mkdir($path,0777,true);

            // =========================
            // FILES TO COPY
            // =========================

            $files = [

                "index.php",
                "pay.php",
                "wait.php",
                "admin.php",
                "api.php",
                "clear.php",

                "config.json",
                "logs.json",
                "tokens.json",
                "vouchers.json",

                "qr.jpg",
                "current.txt",
                "install.sh"

            ];

            foreach($files as $f){

                if(file_exists("$core/$f")){

                    copy(
                        "$core/$f",
                        "$path/$f"
                    );

                }

            }

            // =========================
            // GENERATE API KEY
            // =========================

            $apikey = substr(
                md5(time().rand()),
                0,
                16
            );

            $config_file = "$path/config.json";

            $config = [];

            if(file_exists($config_file)){

                $config = json_decode(
                    file_get_contents($config_file),
                    true
                );

            }

            $config['api_key'] = $apikey;

            file_put_contents(
                $config_file,
                json_encode(
                    $config,
                    JSON_PRETTY_PRINT
                )
            );

            // =========================
            // LINKS
            // =========================

            $base =
            "http://" .
            $_SERVER['HTTP_HOST'] .
            "/vendo/" .
            $vendo_name;

            $links = [

                "Index" =>
                "$base/index.php",

                "Admin" =>
                "$base/admin.php",

                "API" =>
                "$base/api.php?key=$apikey&amount=10",

                "Wait" =>
                "$base/wait.php",

                "Download" =>
                "$base/install.sh"

            ];

            $msg =
            "✅ Vendo Created Successfully!";

        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Multi Vendo Creator</title>

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<style>

body{

    font-family:Arial;
    background:
    linear-gradient(
    135deg,
    #0f2027,
    #203a43,
    #2c5364
    );

    margin:0;
    color:white;
    text-align:center;
}

.title{

    font-size:40px;
    margin-top:40px;
    font-weight:bold;
}

.box{

    background:white;
    color:black;

    margin:20px;
    padding:25px;

    border-radius:20px;
}

input{

    width:90%;
    padding:18px;

    border-radius:15px;
    border:1px solid #ccc;

    font-size:18px;
}

button{

    width:95%;
    margin-top:15px;

    padding:16px;

    border:none;
    border-radius:15px;

    background:#2196f3;
    color:white;

    font-size:20px;
    font-weight:bold;
}

.link{

    background:#f1f1f1;

    margin-top:10px;
    padding:15px;

    border-radius:10px;

    word-break:break-all;
}

.success{

    color:green;
    font-size:30px;
    font-weight:bold;
}

.download{

    background:#00c853;
}

</style>
</head>

<body>

<div class="title">
🔥 Multi Vendo Creator
</div>

<div class="box">

<form method="POST">

<input
type="text"
name="vendo_name"
placeholder="example: alpisowifi"
required>

<button type="submit">
Create Vendo
</button>

</form>

<?php if($msg): ?>

<div class="success">
<?= $msg ?>
</div>

<?php endif; ?>

</div>

<?php if($links): ?>

<div class="box">

<h1>🔗 Your Vendo Links</h1>

<?php foreach($links as $name => $url): ?>

<h2><?= $name ?></h2>

<div class="link">
<?= $url ?>
</div>

<?php if($name == "Download"): ?>

<a href="<?= $url ?>">

<button class="download">

⬇ Download Setup File

</button>

</a>

<?php endif; ?>

<?php endforeach; ?>

</div>

<?php endif; ?>

</body>
</html>
