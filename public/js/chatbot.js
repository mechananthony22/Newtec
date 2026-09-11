document.addEventListener('DOMContentLoaded', function () {
    var chatbotContainer = document.getElementById('chatbot-container');
    var ayudaIcono = document.getElementById('boton-Ayuda');
    var closeChatbotBtn = document.getElementById('close-chatbot-btn');
    var userInput = document.getElementById('user-input');
    var sendBtn = document.getElementById('send-btn');
    var chatbotBody = document.getElementById('chatbot-body');

    // Validar que los elementos existan antes de asignar eventos
    if (ayudaIcono && chatbotContainer) {
        ayudaIcono.addEventListener('click', function () {
            chatbotContainer.style.display = 'block';
        });
    }

    if (closeChatbotBtn && chatbotContainer) {
        closeChatbotBtn.addEventListener('click', function () {
            chatbotContainer.style.display = 'none';
        });
    }

    if (sendBtn && userInput && chatbotBody) {
        sendBtn.addEventListener('click', function () {
            var userMessage = userInput.value;
            if (userMessage.trim() === '') return;

            appendUserMessage(userMessage);

            // Simular una respuesta del chatbot
            setTimeout(function () {
                appendChatbotMessage("¡Hola! Soy el chatbot de PeruNet. ¿En qué puedo ayudarte?");
            }, 500);

            userInput.value = '';
        });
    }

    function appendUserMessage(message) {
        if (!chatbotBody) return;

        var messageElement = document.createElement('div');
        messageElement.classList.add('chatbot-message');
        messageElement.innerHTML = '<p>' + message + '</p>';
        chatbotBody.appendChild(messageElement);
        chatbotBody.scrollTop = chatbotBody.scrollHeight; // Desplazamiento automático hacia abajo
    }

    function appendChatbotMessage(message) {
        if (!chatbotBody) return;

        var messageElement = document.createElement('div');
        messageElement.classList.add('chatbot-message', 'chatbot-reply');
        messageElement.innerHTML = '<p>' + message + '</p>';
        chatbotBody.appendChild(messageElement);
        chatbotBody.scrollTop = chatbotBody.scrollHeight; // Desplazamiento automático hacia abajo
    }
});
