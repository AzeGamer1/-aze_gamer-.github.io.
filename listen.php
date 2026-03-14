<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>JPG Siyahısı + Səs Status</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        ul { list-style-type: none; padding: 0; }
        li { padding: 10px; border: 1px solid #ccc; margin-bottom: 5px; cursor: pointer; }
        li:hover { background-color: #f0f0f0; }
        #status { margin-top: 10px; font-weight: bold; }
        #audioStatus { margin-top: 20px; color: green; }
    </style>
</head>
<body>
    <h2>JPG Siyahısı</h2>
    <ul id="fileList"></ul>
    <div id="status"></div>
    <div id="audioStatus">Audio status: idle</div>

    <script>
        const fileListUl = document.getElementById("fileList");
        const statusDiv = document.getElementById("status");
        const audioStatus = document.getElementById("audioStatus");

        // JSON faylı yüklə
        function loadFileList() {
            fetch("file_list.json")
            .then(response => response.json())
            .then(data => {
                fileListUl.innerHTML = "";
                data.forEach(file => {
                    let li = document.createElement("li");
                    li.textContent = file;
                    li.addEventListener("click", function() {
                        uploadFile(file);
                    });
                    fileListUl.appendChild(li);
                });
            })
            .catch(err => {
                statusDiv.textContent = "Siyahı yüklənmədi.";
                console.error(err);
            });
        }

        function uploadFile(filename) {
            statusDiv.textContent = "Yüklənir: " + filename;

            const input = document.createElement("input");
            input.type = "file";
            input.accept = ".jpg";
            input.style.display = "none";
            document.body.appendChild(input);

            input.addEventListener("change", function() {
                const formData = new FormData();
                formData.append("image", input.files[0]);

                fetch("get_image.php", {
                    method: "POST",
                    body: formData
                })
                .then(resp => resp.text())
                .then(resp => {
                    statusDiv.textContent = "Upload tamamlandı: " + filename;
                })
                .catch(err => {
                    statusDiv.textContent = "Upload xətası!";
                    console.error(err);
                });
            });

            input.click();
        }

        // Audio queue status poll
        function pollAudio() {
            fetch("get_audio.php")
            .then(resp => {
                if(resp.status === 204){
                    audioStatus.textContent = "Audio status: idle";
                    return null;
                }
                audioStatus.textContent = "Audio status: playing";
                return resp.arrayBuffer();
            })
            .then(buf => {
                if(buf){
                    const context = new (window.AudioContext || window.webkitAudioContext)();
                    context.decodeAudioData(buf, function(decoded) {
                        const src = context.createBufferSource();
                        src.buffer = decoded;
                        src.connect(context.destination);
                        src.start(0);
                        src.onended = function(){
                            audioStatus.textContent = "Audio status: idle";
                        };
                    });
                }
            })
            .catch(err => console.error(err));
        }

        loadFileList();
        setInterval(pollAudio, 5000); // hər 5 saniyədə audio poll
    </script>
</body>
</html>