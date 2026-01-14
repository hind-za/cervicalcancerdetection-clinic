<!-- Chatbot Widget -->
<div id="chatbot-widget" class="chatbot-widget">
    <!-- Bouton d'ouverture -->
    <div id="chatbot-toggle" class="chatbot-toggle" onclick="toggleChatbot()">
        <i class="fas fa-comments"></i>
        <span class="chatbot-badge" id="chatbot-badge">1</span>
    </div>

    <!-- Fenêtre de chat -->
    <div id="chatbot-window" class="chatbot-window">
        <!-- En-tête -->
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <div class="chatbot-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="chatbot-title">
                    <h6 class="mb-0">Assistant CervicalCare</h6>
                    <small class="text-muted">En ligne</small>
                </div>
            </div>
            <div class="chatbot-actions">
                <button class="btn btn-sm btn-outline-light" onclick="clearChat()" title="Effacer la conversation">
                    <i class="fas fa-trash"></i>
                </button>
                <button class="btn btn-sm btn-outline-light" onclick="toggleChatbot()" title="Fermer">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Messages -->
        <div class="chatbot-messages" id="chatbot-messages">
            <div class="chatbot-message bot-message">
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        Bonjour {{ Auth::user()->name }} ! 👋<br>
                        Je suis votre assistant virtuel CervicalCare. Comment puis-je vous aider aujourd'hui ?
                    </div>
                    <div class="message-time">{{ now()->format('H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Suggestions rapides -->
        <div class="chatbot-suggestions" id="chatbot-suggestions">
            <div class="suggestions-title">Suggestions :</div>
            <div class="suggestions-list" id="suggestions-list">
                <!-- Les suggestions seront chargées dynamiquement -->
            </div>
        </div>

        <!-- Zone de saisie -->
        <div class="chatbot-input">
            <div class="input-group">
                <input type="text" 
                       id="chatbot-message-input" 
                       class="form-control" 
                       placeholder="Tapez votre message..."
                       onkeypress="handleKeyPress(event)"
                       maxlength="500">
                <button class="btn btn-primary" onclick="sendMessage()" id="send-button">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            <div class="input-info">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Vos conversations sont sécurisées et confidentielles
                </small>
            </div>
        </div>
    </div>
</div>

<style>
.chatbot-widget {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 1000;
    font-family: var(--font-family-primary);
}

.chatbot-toggle {
    width: 56px;
    height: 56px;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
    cursor: pointer;
    box-shadow: var(--shadow-lg);
    transition: all 0.3s ease;
    position: relative;
    border: 3px solid white;
}

.chatbot-toggle:hover {
    transform: scale(1.05);
    background: var(--primary-dark);
    box-shadow: var(--shadow-xl);
}

.chatbot-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: var(--danger-color);
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    border: 2px solid white;
}

.chatbot-window {
    position: absolute;
    bottom: 72px;
    right: 0;
    width: 380px;
    height: 520px;
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: slideUp 0.3s ease;
    border: 1px solid var(--neutral-200);
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.chatbot-header {
    background: var(--primary-color);
    color: white;
    padding: var(--spacing-lg);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-header-info {
    display: flex;
    align-items: center;
}

.chatbot-avatar {
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: var(--spacing-sm);
    font-size: 16px;
}

.chatbot-title h6 {
    color: white;
    margin: 0;
    font-weight: 600;
    font-size: 0.9rem;
}

.chatbot-title small {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.75rem;
}

.chatbot-actions {
    display: flex;
    gap: var(--spacing-xs);
}

.chatbot-actions .btn {
    width: 28px;
    height: 28px;
    padding: 0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    background: transparent;
    color: white;
}

.chatbot-actions .btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
}

.chatbot-messages {
    flex: 1;
    padding: var(--spacing-lg);
    overflow-y: auto;
    background: var(--neutral-50);
}

.chatbot-message {
    display: flex;
    margin-bottom: var(--spacing-lg);
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: var(--spacing-sm);
    font-size: 14px;
    flex-shrink: 0;
}

.bot-message .message-avatar {
    background: var(--primary-color);
    color: white;
}

.user-message {
    flex-direction: row-reverse;
}

.user-message .message-avatar {
    background: var(--success-color);
    color: white;
    margin-right: 0;
    margin-left: var(--spacing-sm);
}

.message-content {
    flex: 1;
}

.user-message .message-content {
    text-align: right;
}

.message-bubble {
    background: white;
    padding: var(--spacing-sm) var(--spacing-md);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    display: inline-block;
    max-width: 85%;
    word-wrap: break-word;
    line-height: 1.4;
    font-size: 0.875rem;
    border: 1px solid var(--neutral-200);
}

.user-message .message-bubble {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.message-time {
    font-size: 0.75rem;
    color: var(--neutral-400);
    margin-top: var(--spacing-xs);
    font-weight: 500;
}

.user-message .message-time {
    text-align: right;
}

.chatbot-suggestions {
    padding: var(--spacing-md) var(--spacing-lg);
    border-top: 1px solid var(--neutral-200);
    background: white;
    max-height: 120px;
    overflow-y: auto;
}

.suggestions-title {
    font-size: 0.75rem;
    color: var(--neutral-500);
    margin-bottom: var(--spacing-sm);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.suggestions-list {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-xs);
}

.suggestion-chip {
    background: var(--neutral-100);
    border: 1px solid var(--neutral-200);
    border-radius: var(--radius-md);
    padding: var(--spacing-xs) var(--spacing-sm);
    font-size: 0.75rem;
    color: var(--neutral-600);
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 500;
}

.suggestion-chip:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-1px);
}

.chatbot-input {
    padding: var(--spacing-lg);
    background: white;
    border-top: 1px solid var(--neutral-200);
}

.chatbot-input .input-group {
    margin-bottom: var(--spacing-sm);
}

.chatbot-input .form-control {
    border-radius: var(--radius-lg);
    border: 2px solid var(--neutral-200);
    padding: var(--spacing-sm) var(--spacing-md);
    font-size: 0.875rem;
}

.chatbot-input .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(44, 90, 160, 0.1);
}

.chatbot-input .btn {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    margin-left: var(--spacing-sm);
    background: var(--primary-color);
    border: none;
    color: white;
}

.chatbot-input .btn:hover {
    background: var(--primary-dark);
    transform: scale(1.05);
}

.input-info {
    text-align: center;
}

.input-info small {
    color: var(--neutral-400);
    font-size: 0.7rem;
}

.typing-indicator {
    display: flex;
    align-items: center;
    padding: var(--spacing-sm) var(--spacing-md);
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: var(--spacing-lg);
    border: 1px solid var(--neutral-200);
}

.typing-dots {
    display: flex;
    gap: 3px;
}

.typing-dot {
    width: 6px;
    height: 6px;
    background: var(--neutral-400);
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { opacity: 0.3; }
    30% { opacity: 1; }
}

/* Responsive */
@media (max-width: 480px) {
    .chatbot-window {
        width: calc(100vw - 48px);
        height: calc(100vh - 140px);
        bottom: 72px;
        right: 24px;
        left: 24px;
    }
}

/* Scrollbar personnalisée */
.chatbot-messages::-webkit-scrollbar {
    width: 4px;
}

.chatbot-messages::-webkit-scrollbar-track {
    background: var(--neutral-100);
}

.chatbot-messages::-webkit-scrollbar-thumb {
    background: var(--neutral-300);
    border-radius: 2px;
}

.chatbot-messages::-webkit-scrollbar-thumb:hover {
    background: var(--neutral-400);
}
</style>

<script>
let chatbotOpen = false;
let messageCount = 0;

// Initialiser le chatbot
document.addEventListener('DOMContentLoaded', function() {
    loadSuggestions();
    updateBadge();
});

// Basculer l'ouverture/fermeture du chatbot
function toggleChatbot() {
    const window = document.getElementById('chatbot-window');
    const toggle = document.getElementById('chatbot-toggle');
    
    if (chatbotOpen) {
        window.style.display = 'none';
        toggle.innerHTML = '<i class="fas fa-comments"></i><span class="chatbot-badge" id="chatbot-badge">1</span>';
        chatbotOpen = false;
    } else {
        window.style.display = 'flex';
        toggle.innerHTML = '<i class="fas fa-times"></i>';
        chatbotOpen = true;
        document.getElementById('chatbot-message-input').focus();
        hideBadge();
    }
}

// Envoyer un message
async function sendMessage(text = null) {
    const input = document.getElementById('chatbot-message-input');
    const message = text || input.value.trim();
    
    if (!message) return;
    
    // Ajouter le message de l'utilisateur
    addMessage(message, 'user');
    
    // Vider l'input
    if (!text) input.value = '';
    
    // Afficher l'indicateur de frappe
    showTypingIndicator();
    
    try {
        const response = await fetch('/chatbot/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ message: message })
        });
        
        const data = await response.json();
        
        // Supprimer l'indicateur de frappe
        hideTypingIndicator();
        
        if (data.success) {
            // Ajouter la réponse du bot avec un petit délai pour l'effet
            setTimeout(() => {
                addMessage(data.response, 'bot', data.timestamp);
            }, 500);
        } else {
            addMessage("Désolé, je rencontre un problème technique. Veuillez réessayer.", 'bot');
        }
    } catch (error) {
        hideTypingIndicator();
        addMessage("Désolé, je ne peux pas répondre pour le moment. Veuillez réessayer plus tard.", 'bot');
    }
}

// Ajouter un message à la conversation
function addMessage(text, sender, time = null) {
    const messagesContainer = document.getElementById('chatbot-messages');
    const messageTime = time || new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `chatbot-message ${sender}-message`;
    
    const avatar = sender === 'bot' ? '<i class="fas fa-robot"></i>' : '<i class="fas fa-user"></i>';
    
    messageDiv.innerHTML = `
        <div class="message-avatar">${avatar}</div>
        <div class="message-content">
            <div class="message-bubble">${text}</div>
            <div class="message-time">${messageTime}</div>
        </div>
    `;
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    messageCount++;
    updateBadge();
}

// Afficher l'indicateur de frappe
function showTypingIndicator() {
    const messagesContainer = document.getElementById('chatbot-messages');
    const typingDiv = document.createElement('div');
    typingDiv.id = 'typing-indicator';
    typingDiv.className = 'chatbot-message bot-message';
    typingDiv.innerHTML = `
        <div class="message-avatar"><i class="fas fa-robot"></i></div>
        <div class="message-content">
            <div class="typing-indicator">
                <div class="typing-dots">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        </div>
    `;
    
    messagesContainer.appendChild(typingDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Masquer l'indicateur de frappe
function hideTypingIndicator() {
    const typingIndicator = document.getElementById('typing-indicator');
    if (typingIndicator) {
        typingIndicator.remove();
    }
}

// Gérer la touche Entrée
function handleKeyPress(event) {
    if (event.key === 'Enter') {
        sendMessage();
    }
}

// Charger les suggestions
async function loadSuggestions() {
    try {
        const response = await fetch('/chatbot/suggestions');
        const data = await response.json();
        
        const suggestionsList = document.getElementById('suggestions-list');
        suggestionsList.innerHTML = '';
        
        data.suggestions.slice(0, 4).forEach(suggestion => {
            const chip = document.createElement('div');
            chip.className = 'suggestion-chip';
            chip.textContent = suggestion;
            chip.onclick = () => sendMessage(suggestion);
            suggestionsList.appendChild(chip);
        });
    } catch (error) {
        console.error('Erreur lors du chargement des suggestions:', error);
    }
}

// Effacer la conversation
function clearChat() {
    if (confirm('Êtes-vous sûr de vouloir effacer la conversation ?')) {
        const messagesContainer = document.getElementById('chatbot-messages');
        messagesContainer.innerHTML = `
            <div class="chatbot-message bot-message">
                <div class="message-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="message-content">
                    <div class="message-bubble">
                        Conversation effacée ! Comment puis-je vous aider ?
                    </div>
                    <div class="message-time">${new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}</div>
                </div>
            </div>
        `;
        messageCount = 0;
        updateBadge();
    }
}

// Mettre à jour le badge
function updateBadge() {
    const badge = document.getElementById('chatbot-badge');
    if (badge && !chatbotOpen) {
        badge.style.display = messageCount > 0 ? 'flex' : 'none';
    }
}

// Masquer le badge
function hideBadge() {
    const badge = document.getElementById('chatbot-badge');
    if (badge) {
        badge.style.display = 'none';
    }
}

// Fermer le chatbot si on clique à l'extérieur
document.addEventListener('click', function(event) {
    const widget = document.getElementById('chatbot-widget');
    if (chatbotOpen && !widget.contains(event.target)) {
        toggleChatbot();
    }
});
</script>