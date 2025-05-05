<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Chatbot</title>
  <style>
    body { font-family: Arial; margin: 0; padding: 10px; }
    #messages { height: 350px; overflow-y: auto; border: 1px solid #ccc; padding: 8px; margin-bottom: 8px; }
    .user { text-align: right; color: blue; margin: 5px 0; }
    .bot { text-align: left; color: green; margin: 5px 0; }
    input[type="text"] { width: 70%; padding: 5px; }
    button { padding: 5px 10px; }
  </style>
</head>
<body>

<h4>Royal Events Chatbot</h4>
<div id="messages"></div>
<input type="text" id="userInput" placeholder="Ask me anything..." />
<button onclick="sendMessage()">Send</button>

<script>
function sendMessage() {
  const userInput = document.getElementById("userInput").value.trim();
  if (!userInput) return;

  const messages = document.getElementById("messages");
  messages.innerHTML += `<div class="user"><strong>You:</strong> ${userInput}</div>`;

  fetch("/frontend/chatbot_response.php", {
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
