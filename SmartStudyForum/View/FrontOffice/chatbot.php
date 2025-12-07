<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant IA Bien-être | SmartStudy+</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        :root {
            --primary: #4CAF50;
            --primary-dark: #2e7d32;
            --secondary: #2196F3;
            --bg-light: #F5F7FA;
            --bg-white: #FFFFFF;
            --text-primary: #2C3E50;
            --text-secondary: #7F8C8D;
            --border: #E0E6ED;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .chatbot-wrapper {
            width: 100%;
            max-width: 900px;
            height: 90vh;
            background: var(--bg-white);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .chat-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 24px;
            color: white;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .bot-avatar {
            width: 56px;
            height: 56px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        
        .bot-info h2 { font-size: 20px; font-weight: 600; margin-bottom: 4px; }
        
        .bot-status {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            opacity: 0.95;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            background: #4CAF50;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .header-actions {
            margin-left: auto;
            display: flex;
            gap: 8px;
        }
        
        .header-btn {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border: none;
            border-radius: 12px;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .header-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.05);
        }
        
        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            background: var(--bg-light);
        }
        
        .messages-container::-webkit-scrollbar { width: 6px; }
        .messages-container::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        
        .message-group {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            animation: fadeIn 0.4s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .message-group.user { flex-direction: row-reverse; }
        
        .message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        
        .message-group.bot .message-avatar {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }
        
        .message-group.user .message-avatar {
            background: linear-gradient(135deg, var(--secondary), #1976D2);
            color: white;
        }
        
        .message-bubble {
            max-width: 70%;
            padding: 16px 20px;
            border-radius: 20px;
            line-height: 1.6;
        }
        
        .message-group.bot .message-bubble {
            background: white;
            color: var(--text-primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-bottom-left-radius: 4px;
        }
        
        .message-group.user .message-bubble {
            background: linear-gradient(135deg, var(--secondary), #1976D2);
            color: white;
            border-bottom-right-radius: 4px;
        }
        
        .message-time {
            font-size: 11px;
            margin-top: 6px;
            opacity: 0.6;
        }
        
        .typing-indicator {
            display: none;
            padding: 16px 20px;
            background: white;
            border-radius: 20px;
            width: fit-content;
        }
        
        .typing-indicator.active { display: flex; gap: 6px; }
        
        .typing-dot {
            width: 8px;
            height: 8px;
            background: var(--primary);
            border-radius: 50%;
            animation: bounce 1.4s infinite;
        }
        
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        
        @keyframes bounce {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-8px); }
        }
        
        .welcome-screen {
            text-align: center;
            padding: 60px 40px;
        }
        
        .welcome-icon {
            font-size: 64px;
            margin-bottom: 24px;
            animation: float 3s ease-in-out infinite;
        }
        
        .welcome-screen h3 {
            font-size: 28px;
            color: var(--text-primary);
            margin-bottom: 12px;
        }
        
        .welcome-screen p {
            color: var(--text-secondary);
            font-size: 16px;
            line-height: 1.6;
            max-width: 500px;
            margin: 0 auto 32px;
        }
        
        .feature-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            margin-top: 32px;
        }
        
        .feature-pill {
            padding: 8px 16px;
            background: rgba(76,175,80,0.1);
            border: 2px solid rgba(76,175,80,0.2);
            border-radius: 20px;
            font-size: 14px;
            color: var(--primary);
            font-weight: 500;
        }
        
        .input-area {
            padding: 24px;
            background: white;
            border-top: 1px solid var(--border);
        }
        
        .input-wrapper {
            display: flex;
            gap: 12px;
            align-items: center;
            background: var(--bg-light);
            border: 2px solid var(--border);
            border-radius: 24px;
            padding: 8px 8px 8px 20px;
            transition: all 0.3s;
        }
        
        .input-wrapper:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(76,175,80,0.1);
        }
        
        .input-wrapper input {
            flex: 1;
            border: none;
            background: transparent;
            font-size: 15px;
            padding: 8px 0;
            outline: none;
        }
        
        .send-btn {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .send-btn:hover {
            transform: scale(1.05) rotate(15deg);
        }
        
        .send-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        @media (max-width: 768px) {
            .chatbot-wrapper { height: 100vh; border-radius: 0; }
            .message-bubble { max-width: 85%; }
        }
    </style>
</head>
<body>
    <div class="chatbot-wrapper">
        <div class="chat-header">
            <div class="bot-avatar">🤖</div>
            <div class="bot-info">
                <h2>Assistant IA Bien-être</h2>
                <div class="bot-status">
                    <span class="status-dot"></span>
                    <span>En ligne</span>
                </div>
            </div>
            <div class="header-actions">
                <button class="header-btn" onclick="clearChat()" title="Nouvelle conversation">
                    <i class="fas fa-redo"></i>
                </button>
                <button class="header-btn" onclick="window.location.href='forums.php'" title="Retour">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="messages-container" id="messagesContainer">
            <div class="welcome-screen" id="welcomeScreen">
                <div class="welcome-icon">🌟</div>
                <h3>Espace de bien-être étudiant</h3>
                <p>Je suis ici pour vous accompagner. Parlez-moi librement de vos préoccupations.</p>
                <div class="feature-pills">
                    <span class="feature-pill">🧘 Stress</span>
                    <span class="feature-pill">🎯 Concentration</span>
                    <span class="feature-pill">💪 Motivation</span>
                    <span class="feature-pill">📚 Études</span>
                    <span class="feature-pill">😴 Sommeil</span>
                </div>
            </div>
        </div>

        <div class="input-area">
            <div class="input-wrapper">
                <input type="text" id="userInput" placeholder="Écrivez votre message..." onkeypress="handleEnter(event)">
                <button class="send-btn" onclick="sendMessage()">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <script src="chatbotAI.js"></script>
    <script>
        // Initialisation
        window.onload = () => {
            setTimeout(() => {
                addMessage("Bonjour ! 👋 Comment puis-je vous aider aujourd'hui ?", false);
            }, 1000);
        };

        function handleEnter(e) {
            if (e.key === 'Enter') sendMessage();
        }

        function addMessage(text, isUser) {
            const container = document.getElementById('messagesContainer');
            const welcome = document.getElementById('welcomeScreen');
            if (welcome) welcome.remove();
            
            const group = document.createElement('div');
            group.className = `message-group ${isUser ? 'user' : 'bot'}`;
            
            const time = new Date().toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});
            
            group.innerHTML = `
                <div class="message-avatar">${isUser ? '👤' : '🤖'}</div>
                <div class="message-bubble">
                    ${text.replace(/\n/g, '<br>')}
                    <div class="message-time">${time}</div>
                </div>
            `;
            
            container.appendChild(group);
            container.scrollTop = container.scrollHeight;
        }

        function showTyping() {
            const container = document.getElementById('messagesContainer');
            const typing = document.createElement('div');
            typing.className = 'message-group bot';
            typing.id = 'typingIndicator';
            typing.innerHTML = `
                <div class="message-avatar">🤖</div>
                <div class="typing-indicator active">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            `;
            container.appendChild(typing);
            container.scrollTop = container.scrollHeight;
        }

        function hideTyping() {
            const typing = document.getElementById('typingIndicator');
            if (typing) typing.remove();
        }

        function sendMessage() {
            const input = document.getElementById('userInput');
            const text = input.value.trim();
            if (!text) return;
            
            addMessage(text, true);
            input.value = '';
            
            showTyping();
            
            setTimeout(() => {
                hideTyping();
                const response = getBotResponse(text);
                addMessage(response);
            }, 1500);
        }

        function getBotResponse(msg) {
            const m = msg.toLowerCase();
            
            // Salutations
            if (/bonjour|salut|hey|coucou/i.test(m)) {
                return "Bonjour ! 😊 Comment vous sentez-vous aujourd'hui ? N'hésitez pas à me parler de ce qui vous préoccupe.";
            }
            
            // Stress
            if (/stress|anxieux|angoisse|panique|inquiet/i.test(m)) {
                return "Je comprends que vous vous sentiez stressé(e). 💙\n\n🫁 **Respirez** : 4 secondes inspire, 7 secondes retient, 8 secondes expire.\n\n🚶 **Bougez** : 10 min de marche changent tout.\n\n📝 **Écrivez** : Notez tout ce qui vous stresse pendant 5 min.\n\nVous n'êtes pas seul(e). Voulez-vous en parler davantage ?";
            }
            
            // Concentration
            if (/concentration|concentrer|distraction|focus/i.test(m)) {
                return "La concentration s'entraîne ! 🧠\n\n🍅 **Pomodoro** : 25 min travail + 5 min pause.\n\n📵 **Mode avion** : Zéro distraction.\n\n🎧 **Alpha waves** : Musique à 10Hz sur YouTube.\n\n💧 **Hydratez-vous** : Un verre d'eau toutes les 45 min.\n\nQuelle technique voulez-vous essayer en premier ?";
            }
            
            // Motivation
            if (/motivation|démotivé|découragé|fatigué|courage/i.test(m)) {
                return "Vous êtes plus fort(e) que vous ne le pensez ! 💪\n\n🎯 **Progrès** : Chaque petit pas compte.\n\n🏆 **Célébrez** : Notez vos victoires quotidiennes.\n\n💭 **Pourquoi** : Rappelez-vous votre objectif initial.\n\nVous avez déjà surmonté 100% de vos jours difficiles. Continuez ! ✨";
            }
            
            // Études
            if (/étude|révision|examen|apprendre|mémoriser/i.test(m)) {
                return "Optimisons votre apprentissage ! 📚\n\n🔄 **Répétition espacée** : Révisez après 1, 3, 7, 14 jours.\n\n🎤 **Méthode Feynman** : Expliquez à voix haute.\n\n🗺️ **Mind map** : Dessinez des cartes mentales.\n\n❓ **Active recall** : Testez-vous sans notes.\n\nQuelle matière travaillez-vous ?";
            }
            
            // Sommeil
            if (/sommeil|dormir|insomnie|fatigue|nuit/i.test(m)) {
                return "Le sommeil est crucial ! 😴\n\n🌙 **Régularité** : Horaires fixes même le week-end.\n\n📱 **Digital detox** : Arrêt écrans 90 min avant.\n\n🍵 **Tisane** : Camomille + miel 30 min avant.\n\n❄️ **18°C** : Température idéale de la chambre.\n\nDepuis combien de temps avez-vous des difficultés ?";
            }
            
            // Tristesse
            if (/triste|déprimé|mal|pleure|seul/i.test(m)) {
                return "Je suis là pour vous écouter. 💙\n\n☀️ **Lumière** : 15 min de soleil dès le matin.\n\n💬 **Parlez** : SOS Amitié 09 72 39 40 50.\n\n📝 **Gratitude** : 3 choses positives chaque soir.\n\n🏃 **Bougez** : 10 min de marche.\n\nSi ça dure >2 semaines, consultez un psy. C'est courageux, pas faible. 🫂";
            }
            
            // Merci / Positif
            if (/merci|super|génial|bien|mieux/i.test(m)) {
                return "Avec plaisir ! 😊 Je suis heureux de pouvoir vous aider. N'hésitez pas à revenir quand vous en aurez besoin. Vous faites du super travail ! 🌟";
            }
            
            // Par défaut
            return "Je suis là pour vous aider avec :\n\n🧘 Gestion du stress\n🎯 Amélioration de la concentration\n💪 Boost de motivation\n📚 Techniques d'étude\n😴 Qualité du sommeil\n\nDe quoi souhaitez-vous parler ?";
        }

        function clearChat() {
            const container = document.getElementById('messagesContainer');
            container.innerHTML = `
                <div class="welcome-screen" id="welcomeScreen">
                    <div class="welcome-icon">🌟</div>
                    <h3>Nouvelle conversation</h3>
                    <p>Comment puis-je vous aider ?</p>
                </div>
            `;
            setTimeout(() => addMessage("Bonjour ! Comment allez-vous ? 👋", false), 500);
        }
    </script>
</body>
</html>