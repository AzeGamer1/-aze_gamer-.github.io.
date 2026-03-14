<?php
session_start();

$KEY_FILE = __DIR__ . "/key";
$DIR_USERS = __DIR__ . "/online_users";
$BLOCK_FILE = __DIR__ . "/blocked_users.txt";
$USERS_JSON = __DIR__ . "/users.json";

// --- Dosyaları yarat yoxdursa ---
if (!file_exists($KEY_FILE)) file_put_contents($KEY_FILE, "");
if (!is_dir($DIR_USERS)) mkdir($DIR_USERS, 0755, true);
if (!file_exists($USERS_JSON)) file_put_contents($USERS_JSON, "{}");
if (!file_exists($BLOCK_FILE)) file_put_contents($BLOCK_FILE, "");

// --- LOGOUT ---
if(isset($_GET['logout'])){
    session_destroy();
    header("Location: panel.php");
    exit;
}

// --- LOGIN ---
if(isset($_POST['password'])){
    if($_POST['password'] === "admin003"){
        $_SESSION['admin'] = true;
    } else {
        $error = "Şifrə yanlışdır!";
    }
}

// Login yoxdursa ekran göstər
if(!isset($_SESSION['admin'])){
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Panel - Giriş</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
body { 
    background:#121223; 
    color:white; 
    font-family:'Poppins', Arial, sans-serif; 
    display:flex; 
    justify-content:center; 
    align-items:center; 
    height:100vh; 
    margin:0; 
}
.box { 
    background:#1a1a30; 
    padding:40px; 
    border-radius:12px; 
    width:320px; 
    text-align:center; 
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
    border: 1px solid #2c2c44;
}
h2 {
    color: #00FFFF;
    font-weight: 600;
    margin-bottom: 25px;
}
input[type="password"] { 
    width:100%; 
    padding:12px; 
    margin-top:10px; 
    border:1px solid #333355; 
    border-radius:8px;
    background: #1e1e35;
    color: white;
    font-size: 16px;
    box-sizing: border-box;
    transition: border-color 0.3s;
}
input[type="password"]:focus {
    border-color: #00FFFF;
    outline: none;
}
button[type="submit"] { 
    margin-top:25px; 
    width:100%; 
    padding:12px; 
    border:none; 
    border-radius:8px; 
    background:#00FFFF; 
    color:#121223; 
    cursor:pointer; 
    font-size:16px;
    font-weight: 600;
    transition: background 0.3s, transform 0.1s;
}
button[type="submit"]:hover {
    background: #00e5e5;
}
.error { 
    color:#ff6b6b; 
    margin-top:15px; 
    font-size: 14px;
}
</style>
</head>
<body>
<div class="box">
<h2>🔑 Admin Girişi</h2>
<form method="post">
<input type="password" name="password" placeholder="Şifrənizi daxil edin">
<button type="submit">Giriş</button>
</form>
<?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
</div>
</body>
</html>
<?php exit; } 

// --- KEY dosyasını oxu ---
$currentKey = trim(file_get_contents($KEY_FILE));

// --- POST: key save ---
if(isset($_POST['saveKey'])){
    file_put_contents($KEY_FILE, $_POST['saveKey']);
    exit;
}

// --- POST: block/unblock ---
if(isset($_POST['action']) && isset($_POST['username'])){
    $username = $_POST['username'];
    $blocked = array_filter(array_map('trim', file($BLOCK_FILE)));

    if($_POST['action']=="block" && !in_array($username,$blocked)){
        $blocked[] = $username;
    } elseif($_POST['action']=="unblock"){
        $blocked = array_diff($blocked, [$username]);
    }
    file_put_contents($BLOCK_FILE, implode("\n",$blocked));
    echo json_encode(["status"=>"ok"]);
    exit;
}

// --- GET: API for users ---
if(isset($_GET['api'])){
    header("Content-Type: application/json");
    $now = time();
    $allUsers = [];
    $usersMap = json_decode(file_get_contents($USERS_JSON),true);

    foreach(scandir($DIR_USERS) as $file){
        if($file=="."||$file=="..") continue;
        $path = $DIR_USERS."/".$file;
        $last = 0;
        $fp = fopen($path,"r");
        if($fp){
            flock($fp,LOCK_SH);
            $last = (int) fread($fp,filesize($path));
            flock($fp,LOCK_UN);
            fclose($fp);
        }
        $rawName = str_replace(".data","",$file);
        $matchedKey = null;
        foreach($usersMap as $key=>$val){ if($val===$rawName){ $matchedKey=$key; break; } }
        $username = $rawName;
        $status = ($now-$last<=60)?"Online":"Offline";
        $allUsers[]=["key"=>$matchedKey ?? $rawName,"username"=>$username,"last_seen"=>$last,"status"=>$status];
    }

    $blocked = array_filter(array_map('trim', file($BLOCK_FILE)));
    echo json_encode(["users"=>$allUsers,"blocked"=>$blocked]);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Panel - Əsas</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
body{
    margin:0;
    font-family:'Poppins', Arial, sans-serif;
    background:#121223;
    color:white;
}
header{
    background:#1a1a30;
    padding:15px 20px;
    display:flex;
    align-items:center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}
.menuBtn{
    font-size:30px;
    cursor:pointer;
    margin-right:20px;
    color: #00FFFF;
    line-height: 1;
    transition: color 0.3s;
}
.menuBtn:hover {
    color: #fff;
}
h2 {
    color: #00FFFF;
    font-weight: 600;
}
.sidebar{
    position:fixed;
    top:0;
    left:-280px;
    width:260px;
    height:100%;
    background:#1a1a30;
    padding:20px;
    transition:left 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    box-shadow: 4px 0 15px rgba(0, 0, 0, 0.5);
    z-index: 1000;
}
.sidebar.open{left:0;}
.sidebar button{
    width:100%;
    padding:12px;
    margin-top:15px;
    border:none;
    border-radius:8px;
    background:#333355;
    color:white;
    cursor:pointer;
    font-size:15px;
    font-weight: 500;
    transition: background 0.3s;
}
.sidebar button:hover {
    background: #00FFFF;
    color: #1a1a30;
}
.closeBtn{background:#ff4444 !important;}
.closeBtn:hover {
    background: #cc3333 !important;
    color: white !important;
}
.main{padding:30px;}
.inputBox{
    width:100%;
    padding:15px;
    border:1px solid #333355;
    border-radius:8px;
    font-size:16px;
    margin-top:15px;
    background: #1e1e35;
    color: white;
    box-sizing: border-box;
    min-height: 150px;
}
.saveBtn{
    margin-top:20px;
    padding:12px 25px;
    background:#00FFFF;
    border:none;
    border-radius:8px;
    font-size:16px;
    color:#1a1a30;
    cursor:pointer;
    font-weight: 600;
    transition: background 0.3s;
}
.saveBtn:hover { background: #00e5e5; }
#runAnim{
    font-size:18px;
    margin-top:15px;
    opacity:1;
    transition:opacity 0.4s;
    color:#00ff00;
    display:none;
    font-weight: 600;
}
table{
    width:100%;
    border-collapse:separate; /* border-radius üçün */
    border-spacing: 0; /* border-radius üçün */
    margin-top:25px;
    border-radius:10px;
    overflow: hidden; /* border-radius-un işləməsi üçün */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}
th,td{
    padding:15px;
    text-align:left;
}
th{
    background:#2c2c54;
    color:#00FFFF;
    font-weight: 600;
}
tr{
    background:#1e1e35;
    transition:background 0.3s;
}
tr:nth-child(even) { /* Cüt sətirlər üçün fərqli rəng */
    background: #1a1a30;
}
tr:hover{
    background:#2c2c44;
}
td.online{color:#39ff14;font-weight:600;}
td.offline{color:#ff4444;font-weight:600;}
td.icon{width:30px;text-align:center;}
button.edit, button.unblockBtn{
    padding:8px 15px;
    font-size:14px;
    border-radius:6px;
    cursor:pointer;
    border: none;
    font-weight: 500;
    transition: background 0.3s;
}
button.edit{
    background:#00FFFF;
    color:#1a1a30;
}
button.edit:hover { background: #00e5e5; }
button.unblockBtn{
    background:#00ff00;
    color:#1a1a30;
}
button.unblockBtn:hover { background: #00cc00; }

#blockedList{
    margin-top:30px;
    color:#ff8888;
    font-weight:600;
    padding:15px;
    background: #1e1e35;
    border-radius: 8px;
    border-left: 5px solid #ff4444;
}
#stats {
    margin-top: 20px;
    padding: 15px;
    background: #1e1e35;
    border-radius: 8px;
    font-weight: 500;
    color: #ddd;
    border-left: 5px solid #00FFFF;
}
</style>
</head>
<body>
<header>
<div class="menuBtn" onclick="openMenu()">&#9776;</div>
<h2>Panel İdarəetmə</h2>
</header>

<div id="sidebar" class="sidebar">
<h3 style="color:#00FFFF; border-bottom: 2px solid #333355; padding-bottom: 10px; margin-top:0;">Naviqasiya</h3>
<button onclick="showUsers()">👥 ONLINE İSTİFADƏÇİLƏR</button>
<button onclick="showKey()">🔑 KEY YARATMA</button>
<button class="closeBtn" onclick="closeMenu()">❌ Bağla</button>
<button style="background: #ff6b6b;" onclick="location='?logout=1'">🚪 Çıxış</button>
</div>

<div class="main">
<div id="usersSection">
<h2>👥 Aktiv İstifadəçilər</h2>
<div id="stats">Yüklənir...</div>
<table>
<thead>
<tr>
<th></th><th>Kullanıcı Adı</th><th>Durum</th><th>Son Aktivlik</th><th>Əməliyyat</th>
</tr>
</thead>
<tbody id="userList"><tr><td colspan="5" style="text-align:center; padding: 20px;">Məlumatlar yüklənir...</td></tr></tbody>
</table>
<div id="blockedList">Bloklanmış İstifadəçilər:</div>
</div>

<div id="keySection" style="display:none;">
<h2>🔑 Key Tənzimləmə</h2>
<textarea id="keyInput" class="inputBox" rows="6" placeholder="Yeni Key məlumatını bura yazın..."><?php echo htmlspecialchars($currentKey); ?></textarea><br>
<button class="saveBtn" onclick="saveKey()">💾 Key-i Yadda Saxla</button>
<div id="runAnim">Key Saxlanıldı!</div>
</div>
</div>

<script>
function openMenu(){document.getElementById("sidebar").classList.add("open");}
function closeMenu(){document.getElementById("sidebar").classList.remove("open");}

function showKey(){document.getElementById("usersSection").style.display="none"; document.getElementById("keySection").style.display="block"; closeMenu();}
function showUsers(){document.getElementById("keySection").style.display="none"; document.getElementById("usersSection").style.display="block"; closeMenu();}

// --- FETCH DATA ---
function formatTime(ts){ 
    if(ts===0) return "-"; 
    let d=new Date(ts*1000); 
    return d.toLocaleTimeString('az-AZ'); // Yerli saat formatı
}

function fetchData(){
fetch("panel.php?api=1").then(res=>res.json()).then(data=>{
    let html=""; 
    let onlineCount=0; 
    const blockedUsers=data.blocked;

    data.users.sort((a, b) => a.username.localeCompare(b.username)); // İstifadəçi adlarına görə çeşidlə

    data.users.forEach(u=>{
        let icon = u.status==="Online"?"&bull;":"&bull;"; // Daha sadə nöqtə
        let cls = u.status==="Online"?"online":"offline"; 
        if(u.status==="Online") onlineCount++;
        
        let isBlocked = blockedUsers.includes(u.username);
        let rowStyle = isBlocked?"background:#3a1010;":""; // Bloklanmış üçün daha incə fon
        let actionBtn = isBlocked?
            `<button class="unblockBtn" onclick="actionUser('unblock','${u.username}')">🔓 Blokdan Çıxart</button>`:
            `<button class="edit" onclick="actionUser('block','${u.username}')">🔒 Blokla</button>`;
        let blockStatus = isBlocked?"<span style='color:#ff8888; font-weight:600;'>Bloklanıb</span>":"<span style='color:#39ff14; font-weight:600;'>Normal</span>";

        html+=`<tr style="${rowStyle}">
                <td class="icon ${cls}">${icon}</td>
                <td><strong>${u.username}</strong></td>
                <td class="${cls}">${u.status}</td>
                <td>${formatTime(u.last_seen)}</td>
                <td>${actionBtn} <span style="margin-left:10px;">${blockStatus}</span></td>
               </tr>`;
    });
    document.getElementById("userList").innerHTML=html;
    document.getElementById("stats").innerHTML=`**Statistikalar:** Toplam İstifadəçi: ${data.users.length} | Online: <span style="color:#39ff14;">${onlineCount}</span> | Offline: <span style="color:#ff4444;">${data.users.length-onlineCount}</span>`;
    
    let blockedHtml = "Bloklanmış İstifadəçilər:";
    if(blockedUsers.length > 0) {
        blockedHtml += " " + blockedUsers.map(name => `<span>${name}</span>`).join(" | ");
    } else {
        blockedHtml += " Yoxdur.";
    }
    document.getElementById("blockedList").innerHTML=blockedHtml;
});
}

// --- BLOCK/UNBLOCK ---
function actionUser(action, username){
fetch("panel.php",{
    method:"POST",
    headers:{"Content-Type":"application/x-www-form-urlencoded"},
    body:`action=${action}&username=${encodeURIComponent(username)}`
}).then(()=>fetchData());
}

// --- KEY SAVE ---
let animInterval;
function saveKey(){
    let val=document.getElementById("keyInput").value;
    fetch("panel.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"saveKey="+encodeURIComponent(val)
    }).then(()=>{
        let anim=document.getElementById("runAnim"); 
        anim.style.display="block";
        anim.innerHTML="💾 Key uğurla yadda saxlandı!";
        clearInterval(animInterval); // Əvvəlki intervalı təmizlə
        anim.style.opacity = 1;
        setTimeout(() => {
             anim.style.opacity = 0;
        }, 2000);
    });
}


setInterval(fetchData,3000); // Yenilənmə vaxtını 3 saniyə etdim
fetchData();
</script>

</body>
</html>
