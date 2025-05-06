<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Royal Events Chatbot</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: white;
    }
    h4 {
      margin: 0;
      padding: 10px;
      background: #f4f4f4;
      border-bottom: 1px solid #ccc;
      text-align: center;
    }
    #messages {
      height: 340px;
      overflow-y: auto;
      padding: 10px;
    }
    .user {
      text-align: right;
      color: blue;
      margin: 5px 0;
    }
    .bot {
      text-align: left;
      color: green;
      margin: 5px 0;
    }
    .chat-input {
      display: flex;
      padding: 10px;
      border-top: 1px solid #ccc;
    }
    input[type="text"] {
      flex: 1;
      padding: 8px;
      font-size: 14px;
    }
    button {
      padding: 8px 12px;
      background-color: #007bff;
      color: white;
      border: none;
      margin-left: 5px;
      cursor: pointer;
    }
    button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

<h4>Royal Events Chatbot</h4>
<div id="messages"></div>
<div class="chat-input">
  <input type="text" id="userInput" placeholder="Ask me anything..." />
  <button onclick="sendMessage()">Send</button>
</div>

<script>
function sendMessage() {
  const userInput = document.getElementById("userInput").value.trim();
  if (!userInput) return;

  const messages = document.getElementById("messages");
  messages.innerHTML += `<div class="user"><strong>You:</strong> ${userInput}</div>`;

  fetch("chatbot_response.php", {
    method: "POST",
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: "message=" + encodeURIComponent(userInput)
  })
  .then(response => response.text())
  .then(data => {
    messages.innerHTML += `<div class="bot"><strong>Bot:</strong> ${data}</div>`;
    document.getElementById("userInput").value = "";
    messages.scrollTop = messages.scrollHeight;
  });
}
</script>

</body>
</html>
