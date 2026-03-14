<?php
// PHP ilə mesaj çıxarmaq
$message = "Gülay, mən səni sevirəm ❤️❤️❤️";
?>
<!DOCTYPE html>
<html lang="az">
<head>
<meta charset="UTF-8">
<title>Sevgi Mesajı</title>
<style>
body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background: #111;
    margin: 0;
    font-family: sans-serif;
}

.love-box {
    background: linear-gradient(135deg, #ff4081, #ff80ab);
    padding: 40px 60px;
    border-radius: 30px;
    color: white;
    font-size: 24px;
    text-align: center;
    box-shadow: 0 0 30px #ff4081;
    position: relative;
    overflow: hidden;
    animation: pulse 1s infinite alternate;
}

@keyframes pulse {
    0% { transform: scale(1); box-shadow: 0 0 20px #ff4081; }
    100% { transform: scale(1.05); box-shadow: 0 0 40px #ff80ab; }
}

/* Ürəklər effekti */
.heart {
    position: absolute;
    font-size: 20px;
    animation: float 3s linear infinite;
    opacity: 0.8;
}

@keyframes float {
    0% { transform: translateY(0) scale(1); opacity: 1; }
    100% { transform: translateY(-300px) scale(1.5); opacity: 0; }
}
</style>
</head>
<body>

<div class="love-box">
    <?php echo $message; ?>
</div>

<script>
// Dinamik ürekler yaratmaq
function createHeart() {
    const heart = document.createElement('div');
    heart.classList.add('heart');
    heart.innerText = '❤️';
    heart.style.left = Math.random() * window.innerWidth + 'px';
    heart.style.animationDuration = (2 + Math.random() * 2) + 's';
    document.body.appendChild(heart);

    // 3s sonra sil
    setTimeout(() => {
        heart.remove();
    }, 3000);
}

// Hər 300ms bir yeni ürek
setInterval(createHeart, 300);
</script>

</body>
</html>