#!/data/data/com.termux/files/usr/bin/bash

clear
echo "🔥 INSTALLING PISO WIFI SYSTEM (PRO)..."

set -e

# ================= INSTALL =================
pkg update -y && pkg upgrade -y
pkg install php git tmux iproute2 -y

# ================= CLEAN =================
echo "🧹 Cleaning old install..."
rm -rf ~/htdocs

# ================= CLONE =================
echo "📥 Cloning your system..."
git clone https://github.com/YOUR-REPO/YOUR-SYSTEM ~/htdocs || {
    echo "❌ CLONE FAILED!"
    exit 1
}

cd ~/htdocs

# ================= CHECK =================
echo "🔍 Checking files..."

if [ ! -f "index.php" ]; then
    echo "❌ index.php missing!"
    exit 1
fi

if [ ! -f "admin.php" ]; then
    echo "❌ admin.php missing!"
    exit 1
fi

# ================= PERMISSION =================
echo "🔐 Setting permissions..."
chmod -R 777 ~/htdocs

# ================= INIT FILES =================
echo "⚙️ Initializing files..."

touch vouchers.json tokens.json logs.json current.txt
echo "{}" > vouchers.json
echo "{}" > tokens.json
echo "[]" > logs.json
echo "0" > current.txt

# ================= START SERVER =================
echo "🚀 Starting server..."
tmux new -d -s wifi "php -S 0.0.0.0:8080"

echo ""
echo "✅ INSTALL COMPLETE!"
echo "🌐 Open: http://$(ip route get 1 | awk '{print $7;exit}'):8080"
echo "🔑 Admin: /admin.php"