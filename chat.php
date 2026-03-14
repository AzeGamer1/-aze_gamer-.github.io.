<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TEST</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f0f0f0; 
            margin: 0; 
            padding: 0; 
        }
        #input-container {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: space-between;
            padding: 10px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        #message-box { 
            padding: 10px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
            flex: 1;
            resize: none;
        }
        #message-box:focus { 
            border-color: #007BFF; 
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2); 
        }
        button { 
            padding: 10px; 
            background-color: #007BFF; 
            color: #fff; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            transition: background-color 0.3s ease-in-out, transform 0.2s; 
            flex-shrink: 0;
        }
        button:hover { 
            background-color: #0056b3; 
            transform: scale(1.05); 
        }
        #chat-box { 
            width: 100%; 
            max-width: 600px; 
            max-height: calc(100vh - 120px); 
            border: 1px solid #ccc; 
            overflow-y: scroll; 
            padding: 10px; 
            background-color: #fff; 
            margin: 0 auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
            margin-top: 10px;
            display: flex;
            flex-direction: column;
        }
        .message-container {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .message { 
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
            word-wrap: break-word;
            clear: both;
            overflow-wrap: break-word;
            position: relative;
        }
        .message.sent { 
            background-color: #DCF8C6;
            align-self: flex-end;
        }
        .message.received {
            background-color: #EAEAEA;
            align-self: flex-start;
        }
        .user { 
            font-weight: bold; 
        }
        .timestamp { 
            color: #888; 
            font-size: 0.8em; 
        }
        #username-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 2000;
        }
        #username-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        #username-input {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 100%;
        }
    </style>
</head>
<body>
    <div id="username-container" style="display: none;">
        <form id="username-form" onsubmit="setUsername(event)">
            <label for="username-input">Adınızı Girin:</label><br>
            <input type="text" id="username-input" required>
            <button type="submit">Kaydet</button>
        </form>
    </div>

    <div id="input-container">
        <textarea id="message-box" rows="1" placeholder="Mesajınızı yazın..."></textarea>
        <button onclick="sendMessage()">Gönder</button>
    </div>

    <div id="chat-box" class="message-container"></div>

    <script>
        let lastTimestamp = '';

        function setUsername(event) {
            event.preventDefault();
            const username = document.getElementById('username-input').value.trim();
            if (username) {
                localStorage.setItem('username', username);
                document.getElementById('username-container').style.display = 'none';
            }
        }

        function checkUsername() {
            const username = localStorage.getItem('username');
            if (!username) {
                document.getElementById('username-container').style.display = 'flex';
            }
        }

        async function fetchMessages() {
            try {
                const response = await fetch('depo.php');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const messages = await response.json();
                const chatBox = document.getElementById('chat-box');
                const isNewMessage = messages.length > 0 && messages[0].timestamp !== lastTimestamp;

                if (isNewMessage) {
                    lastTimestamp = messages[0].timestamp;

                    chatBox.innerHTML = ''; // Chatbox içeriğini temizle

                    messages.forEach(msg => {
                        const msgDiv = document.createElement('div');
                        msgDiv.classList.add('message');
                        msgDiv.classList.add(msg.user === localStorage.getItem('username') ? 'sent' : 'received');
                        msgDiv.innerHTML = `<span class="user">${msg.user}</span>: ${msg.message}<br><span class="timestamp">${msg.timestamp}</span>`;

                        chatBox.appendChild(msgDiv); 
                    });

                    const firstMessage = chatBox.firstChild;
                    if (firstMessage) {
                        chatBox.scrollTop = firstMessage.offsetTop;
                    }
                }
            } catch (error) {
                console.error('Error fetching messages:', error);
            }
        }

        async function sendMessage() {
            const user = localStorage.getItem('username');
            const message = document.getElementById('message-box').value.trim();

            if (user && message) {
                try {
                    const response = await fetch('depo.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `user=${encodeURIComponent(user)}&message=${encodeURIComponent(message)}`
                    });
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    document.getElementById('message-box').value = '';
                    fetchMessages();
                } catch (error) {
                    console.error('Error sending message:', error);
                }
            } else if (!user) {
                alert("Lütfen kullanıcı adınızı girin.");
            }
        }

        checkUsername();
        const messageInterval = setInterval(fetchMessages, 1000);
        fetchMessages();
    </script>
</body>
</html>