<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canlı Ses Akışı</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
        }
        
        h1 {
            color: #333;
            margin-top: 0;
        }
        
        audio {
            width: 100%;
            max-width: 400px;
            margin: 20px 0;
        }
        
        .status {
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 15px;
            font-weight: bold;
        }
        
        .status.live {
            background: #4CAF50;
            color: white;
        }
        
        .status.loading {
            background: #FFC107;
            color: black;
        }
        
        .status.error {
            background: #f44336;
            color: white;
        }

        .info {
            margin-top: 10px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎙️ Canlı Ses Akışı</h1>
        <audio id="audioPlayer" controls></audio>
        <div id="status" class="status loading">Yükleniyor...</div>
        <div id="info" class="info">Beklemede...</div>
    </div>

    <script>
        var audioPlayer = document.getElementById('audioPlayer');
        var statusDiv = document.getElementById('status');
        var infoDiv = document.getElementById('info');
        
        var playedFiles = [];
        var audioQueue = [];
        var isPlaying = false;
        var checkInterval;

        function checkForNewAudio() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'list.php?t=' + Date.now(), true);
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            var files = JSON.parse(xhr.responseText);
                            processFiles(files);
                            updateStatus('live', null);
                        } catch (e) {
                            console.error('Parse error:', e);
                            updateStatus('error', null);
                        }
                    } else {
                        console.error('Request error:', xhr.status);
                        updateStatus('error', null);
                    }
                }
            };
            
            xhr.onerror = function() {
                console.error('Connection error');
                updateStatus('error', null);
            };
            
            xhr.send();
        }

        function processFiles(files) {
            // Yeni dosyaları bul
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                
                // Bu dosya daha önce oynatılmamışsa kuyruğa ekle
                if (playedFiles.indexOf(file) === -1) {
                    audioQueue.push(file);
                    playedFiles.push(file);
                    console.log('Yeni ses eklendi:', file);
                }
            }

            // Kuyrukta ses varsa ve oynatılmıyorsa, oynat
            if (audioQueue.length > 0 && !isPlaying) {
                playNextAudio();
            }

            // Bilgi güncelle
            infoDiv.textContent = 'Kuyruk: ' + audioQueue.length + ' | Toplam: ' + playedFiles.length;
        }

        function playNextAudio() {
            if (audioQueue.length === 0) {
                isPlaying = false;
                return;
            }

            isPlaying = true;
            var nextFile = audioQueue.shift();
            
            console.log('Oynatılıyor:', nextFile);
            audioPlayer.src = nextFile + '?t=' + Date.now();
            
            var playPromise = audioPlayer.play();
            
            if (playPromise !== undefined) {
                playPromise.then(function() {
                    console.log('Oynatma başladı');
                }).catch(function(error) {
                    console.log('Otomatik oynatma engellendi:', error);
                    updateStatus('error', 'Oynatmak için tıklayın');
                    isPlaying = false;
                });
            }
        }

        function updateStatus(state, message) {
            statusDiv.className = 'status ' + state;
            
            if (state === 'live') {
                statusDiv.textContent = '🔴 Canlı Yayın';
            } else if (state === 'loading') {
                statusDiv.textContent = '⏳ Yükleniyor...';
            } else if (state === 'error') {
                statusDiv.textContent = message || '❌ Bağlantı Hatası';
            }
        }

        // Ses bittiğinde sonrakini oynat
        audioPlayer.addEventListener('ended', function() {
            console.log('Ses bitti, sonraki oynatılıyor');
            isPlaying = false;
            playNextAudio();
        });

        audioPlayer.addEventListener('error', function(e) {
            console.error('Oynatma hatası:', e);
            isPlaying = false;
            // Hata durumunda sonrakine geç
            setTimeout(function() {
                playNextAudio();
            }, 1000);
        });

        // Manuel oynatma başladığında
        audioPlayer.addEventListener('play', function() {
            isPlaying = true;
        });

        // İlk kontrol
        checkForNewAudio();

        // Her 1 saniyede bir yeni ses olup olmadığını kontrol et
        checkInterval = setInterval(function() {
            checkForNewAudio();
        }, 1000);

        window.addEventListener('beforeunload', function() {
            clearInterval(checkInterval);
        });
    </script>
</body>
</html>