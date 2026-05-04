<?php
$config = json_decode(file_get_contents("config.json"), true);
?>

<!DOCTYPE html>
<html>
<head>
<title>Buy WiFi</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
    font-family: Arial;
    text-align: center;
    background: linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    color:white;
    margin:0;
}

.box {
    background:white;
    color:black;
    margin:20px;
    padding:20px;
    border-radius:15px;
}

button {
    padding:15px;
    margin:10px 0;
    width:95%;
    border:none;
    border-radius:10px;
    font-size:16px;
    font-weight:bold;
    background:#2196F3;
    color:white;
}

button:hover {
    background:#1976D2;
}

.instructions {
    text-align:left;
    font-size:14px;
    background:#f5f5f5;
    padding:12px;
    border-radius:10px;
    margin-bottom:15px;
}

.warning {
    color:red;
    font-size:13px;
    margin-top:10px;
}
</style>
</head>

<body>

<h2>📶 Buy WiFi</h2>

<div class="box">

    <!-- 🔥 INSTRUCTIONS -->
    <h3>💡 Instructions</h3>

    <div class="instructions">
        <p>1️⃣ Select your desired promo</p>
        <p>2️⃣ Scan the QR code using GCash</p>
        <p>3️⃣ Pay the exact amount</p>
        <p>4️⃣ Tap <b>I HAVE PAID</b></p>
        <p>5️⃣ Wait for your voucher code</p>
    </div>

    <!-- ⚠️ WARNING -->
    <p class="warning">
        ⚠️ Please send the exact amount only. Incorrect payments will not be processed.
    </p>

    <hr>

    <!-- 💸 PROMO LIST -->
    <h3>💸 Select Promo</h3>

    <?php if(empty($config['rates'])): ?>
        <p>No rates available</p>
    <?php else: ?>
        <?php foreach($config['rates'] as $r): ?>
            <a href="pay.php?amount=<?= $r['amount'] ?>">
                <button>
                    ₱<?= $r['amount'] ?> - <?= htmlspecialchars($r['label']) ?>
                </button>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>