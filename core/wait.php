<!DOCTYPE html>
<html>
<head>
<title>Processing Payment</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
    font-family: Arial;
    text-align: center;
    background: #0f2027;
    color: white;
    margin:0;
}

h2 {
    margin-top:30px;
}

.box {
    background: white;
    color: black;
    margin: 20px;
    padding: 25px;
    border-radius: 15px;
}

.voucher {
    font-size: 30px;
    font-weight: bold;
    color: #00c853;
    margin:15px 0;
}

#timer {
    font-size: 16px;
    margin-top:10px;
}

/* loading spinner */
.loader {
    border: 5px solid #f3f3f3;
    border-top: 5px solid #00c853;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    margin: 30px auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
</head>

<body>

<h2>⏳ Processing Payment...</h2>

<div id="loading">
    <div class="loader"></div>
    <p>Waiting for payment confirmation...</p>
</div>

<div class="box" id="box" style="display:none;">
    <h3>✅ Voucher Ready</h3>

    <div class="voucher" id="code"></div>

    <div id="timer"></div>

    <p>⚡ Connecting to internet...</p>
</div>

<script>

// GET TOKEN
let token = new URLSearchParams(location.search).get("token");

// FLAGS
let activated = false;
let timerStarted = false;

// CHECK EVERY 2s
setInterval(() => {

    fetch("current.txt?" + Date.now())
    .then(res => res.text())
    .then(code => {

        code = code.trim();

        if(code && code !== "0"){

            // SHOW UI
            document.getElementById("loading").style.display = "none";
            document.getElementById("box").style.display = "block";
            document.getElementById("code").innerText = code;

            // START TIMER ONCE
            if(!timerStarted){
                startTimer();
                timerStarted = true;
            }

            // ACTIVATE ONCE
            if(!activated){

                activated = true;

                fetch("http://10.0.0.1/vouchers/activate", {
                    method:"POST",
                    headers:{
                        "Content-Type":"application/x-www-form-urlencoded"
                    },
                    body:"code=" + encodeURIComponent(code)
                })
                .then(res => res.text())
                .then(data => {

                    console.log("Activated:", data);

                    // CLEAR FILE
                    fetch("clear.php");

                    // REDIRECT TO INTERNET
                    setTimeout(()=>{
                        window.location.href = "http://10.0.0.1";
                    },1500);

                });

            }

        }

    });

}, 2000);


// TIMER FUNCTION
function startTimer(){

    let start = Math.floor(Date.now()/1000);
    let duration = 180;

    setInterval(() => {

        let now = Math.floor(Date.now()/1000);
        let left = duration - (now - start);

        if(left <= 0){

            alert("⏰ Voucher expired");

            fetch("clear.php");

            window.location.href = "index.php";
            return;
        }

        let m = Math.floor(left/60);
        let s = left%60;

        document.getElementById("timer").innerText =
            "⏳ Expire in: " +
            String(m).padStart(2,'0') + ":" +
            String(s).padStart(2,'0');

    },1000);
}

</script>

</body>
</html>