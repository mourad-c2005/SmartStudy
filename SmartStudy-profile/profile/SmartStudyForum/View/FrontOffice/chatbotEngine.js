/**
 * ========================================
 * SMARTSTUDY+ CHATBOT ENGINE
 * Moteur IA Conversationnel Avancé
 * ========================================
 */

// ========================================
// CONFIGURATION GLOBALE
// ========================================

const STATE = {
    conversationHistory: [],
    currentMood: null,
    lastTopic: null,
    userContext: {
        name: CONFIG.currentUser,
        preferences: [],
        concerns: []
    }
};

// ========================================
// BASE DE CONNAISSANCES IA
// ========================================

const KNOWLEDGE_BASE = {
    greetings: {
        patterns: ['bonjour', 'salut', 'hey', 'coucou', 'bonsoir', 'hello', 'hi'],
        responses: [
            "Bonjour {name} ! 😊 Comment vous sentez-vous aujourd'hui ?",
            "Salut {name} ! 👋 Je suis là pour vous écouter. De quoi voulez-vous parler ?",
            "Hello {name} ! 🌟 Ravi de vous revoir. Comment puis-je vous aider ?"
        ],
        suggestions: ["Je me sens bien", "J'ai besoin d'aide", "Je suis stressé(e)"]
    },
    
    stress: {
        patterns: ['stress', 'stressé', 'anxieux', 'angoisse', 'panique', 'inquiet', 'nerveux', 'tendu'],
        responses: [
            "Je comprends que vous vous sentiez {emotion}. 💙\n\n**Techniques immédiates :**\n\n🫁 **Respiration 4-7-8** : Inspirez 4s, retenez 7s, expirez 8s\n🚶 **Micro-pause** : 5 min de marche ou étirements\n📝 **Brain dump** : Notez tout ce qui vous stresse pendant 5 min\n🎵 **Musique apaisante** : Sons binauraux ou nature\n\nVoulez-vous essayer un exercice guidé maintenant ?",
            "Le stress est une réaction normale. Voici comment le gérer :\n\n✅ **Court terme** :\n- Respirez profondément 10 fois\n- Buvez un verre d'eau\n- Changez d'environnement 5 min\n\n✅ **Moyen terme** :\n- Priorisez vos tâches (matrice Eisenhower)\n- Déléguez ce que vous pouvez\n- Planifiez des pauses régulières\n\nQu'est-ce qui vous stresse le plus en ce moment ?"
        ],
        suggestions: ["Exercice de respiration", "Techniques de relaxation", "Gérer mon temps"],
        followUp: "stress_management"
    },
    
    concentration: {
        patterns: ['concentration', 'concentrer', 'distraction', 'focus', 'attention', 'dispersé'],
        responses: [
            "La concentration s'entraîne ! 🧠 Voici mes meilleures techniques :\n\n🍅 **Pomodoro** : 25 min travail + 5 min pause\n📵 **Mode avion** : Zéro distraction pendant les sessions\n🎧 **Alpha waves** : Musique à 10Hz (cherchez sur YouTube)\n💧 **Hydratation** : Un verre d'eau toutes les 45 min\n🌅 **Timing** : Travail difficile le matin (pic cognitif)\n\nQuelle technique voulez-vous essayer en premier ?",
            "Difficulté de concentration ? C'est normal après 45 min ! 🎯\n\n**Méthode SMART** :\n- **S**pécifique : Une tâche claire\n- **M**esurable : Objectif quantifiable\n- **A**tteignable : Réaliste\n- **R**éaliste : À votre portée\n- **T**emporel : Délai défini\n\n**Éliminez :**\n❌ Multitâche (réduit la productivité de 40%)\n❌ Notifications\n❌ Environnement bruyant\n\nSur quoi travaillez-vous actuellement ?"
        ],
        suggestions: ["Technique Pomodoro", "Musique de concentration", "Organiser mon travail"],
        followUp: "focus_techniques"
    },
    
    motivation: {
        patterns: ['motivation', 'démotivé', 'découragé', 'fatigué', 'courage', 'abandonner'],
        responses: [
            "Vous êtes plus fort(e) que vous ne le pensez ! 💪\n\n**Retrouver la motivation :**\n\n🎯 **Micro-objectifs** : Divisez en 15 min max\n🏆 **Célébrez** : Notez 3 victoires par jour\n💭 **Visualisation** : Imaginez-vous réussir\n👥 **Accountability** : Dites vos objectifs à quelqu'un\n⚡ **Règle des 2 min** : Commencez juste 2 min\n\n**Pourquoi faites-vous ça ?** Reconnectez-vous à votre objectif initial.\n\nQuel est votre objectif principal en ce moment ?",
            "La démotivation est temporaire, pas permanente ! ✨\n\n**Stratégies testées :**\n\n📊 **Tracker visuel** : Chaîne de jours réussis\n🎁 **Récompenses** : Après chaque étape\n🔄 **Changez le contexte** : Nouveau lieu d'étude\n📱 **App motivation** : Forest, Habitica, Notion\n\nVous avez déjà surmonté 100% de vos jours difficiles. Continuez ! 🌟"
        ],
        suggestions: ["Fixer des objectifs", "Techniques de motivation", "Récompenses"],
        followUp: "motivation_boost"
    },
    
    study: {
        patterns: ['étude', 'révision', 'examen', 'apprendre', 'mémoriser', 'cours', 'examens'],
        responses: [
            "Optimisons votre apprentissage ! 📚\n\n**Méthodes scientifiquement prouvées :**\n\n🔄 **Répétition espacée** : Révisez après 1j, 3j, 7j, 14j, 30j\n🎤 **Méthode Feynman** : Expliquez à voix haute comme à un enfant\n🗺️ **Mind mapping** : Dessinez des cartes mentales\n❓ **Active recall** : Testez-vous SANS notes\n👥 **Étude en groupe** : Enseignez aux autres\n\n**Évitez :**\n❌ Relecture passive (efficacité 10%)\n❌ Surlignage excessif\n❌ Bachotage la veille\n\nQuelle matière travaillez-vous ?",
            "Apprenez MIEUX, pas plus ! 🎓\n\n**Techniques avancées :**\n\n🧩 **Chunking** : Groupez par thèmes\n🎨 **Méthode des loci** : Associez à des lieux\n📝 **Cornell notes** : Divisez vos notes en 3 colonnes\n⏰ **Ultralearning** : Sessions intenses 90 min\n\n**Combien de temps par jour ?**\n- 2-3h pour difficile\n- 1-2h pour moyen\n- 30min pour facile\n\nVoulez-vous un plan de révision personnalisé ?"
        ],
        suggestions: ["Créer un planning", "Techniques de mémorisation", "Groupe d'étude"],
        followUp: "study_plan"
    },
    
    sleep: {
        patterns: ['sommeil', 'dormir', 'insomnie', 'fatigue', 'nuit', 'réveil', 'fatigué'],
        responses: [
            "Le sommeil est crucial pour réussir ! 😴\n\n**Routine parfaite :**\n\n🌙 **Régularité** : Horaires fixes ±30 min (même week-end)\n📱 **Digital detox** : Arrêt écrans 90 min avant\n🍵 **Tisane** : Camomille + miel 30 min avant\n❄️ **18°C** : Température idéale chambre\n📖 **Lecture** : 15 min livre papier\n🧘 **Relaxation** : Body scan ou méditation\n\n**À éviter :**\n❌ Caféine après 15h\n❌ Sport intense 3h avant\n❌ Repas lourd le soir\n❌ Sieste après 16h\n\nDepuis combien de temps avez-vous des difficultés ?",
            "Améliorer votre sommeil = Améliorer vos performances ! 💤\n\n**Techniques rapides :**\n\n🫁 **4-7-8** : Respirez pour vous endormir\n🎧 **Bruit blanc** : App ou ventilateur\n☕ **Pas de caféine** : Après 14h\n💪 **Exercice** : 30 min le matin\n🌿 **Lavande** : Spray oreiller\n\n**Si ça persiste >2 semaines** : Consultez un médecin.\n\nQuel est votre principal problème de sommeil ?"
        ],
        suggestions: ["Routine de sommeil", "Techniques d'endormissement", "Hygiène du sommeil"],
        followUp: "sleep_improvement"
    },
    
    sadness: {
        patterns: ['triste', 'déprimé', 'mal', 'pleure', 'seul', 'dépression', 'vide'],
        responses: [
            "Je suis là pour vous écouter. 💙\n\n**Actions immédiates :**\n\n☀️ **Lumière** : 15 min de soleil dès le matin\n💬 **Parlez** : SOS Amitié 09 72 39 40 50 (24h/24)\n📓 **Gratitude** : 3 choses positives chaque soir\n🏃 **Bougez** : 10 min de marche = boost moral\n🤗 **Contact social** : Appelez un proche\n\n**Important :** Si ça dure >2 semaines ou pensées sombres, consultez un psychologue.\n\n**C'est courageux de demander de l'aide, pas faible.** 🫂\n\nVoulez-vous des ressources professionnelles ?",
            "Vos émotions sont valides. 💚\n\n**Ressources d'aide :**\n\n📞 **Urgence** : 3114 (numéro national prévention suicide)\n💬 **SOS Amitié** : 09 72 39 40 50\n👨‍⚕️ **Consultations** : Psychologue en ligne (Doctolib)\n🎓 **BAPU** : Service psy gratuit étudiants\n\n**Auto-soin :**\n- Écrivez vos émotions\n- Musique apaisante\n- Routine quotidienne\n- Évitez l'isolement\n\nComment vous sentez-vous en ce moment (sur 10) ?"
        ],
        suggestions: ["Numéros d'urgence", "Techniques d'auto-soin", "Trouver un psy"],
        followUp: "emotional_support"
    },
    
    thanks: {
        patterns: ['merci', 'super', 'génial', 'bien', 'mieux', 'aidé', 'utile'],
        responses: [
            "Avec grand plaisir {name} ! 😊\n\nJe suis heureux de pouvoir vous aider. N'hésitez pas à revenir quand vous en aurez besoin.\n\n**Vous faites un super travail !** 🌟\n\nAvant de partir, avez-vous d'autres questions ?",
            "C'est moi qui vous remercie d'avoir partagé avec moi ! 💚\n\nSe prendre en charge, c'est déjà 50% du chemin.\n\nÀ bientôt {name} ! ✨"
        ],
        suggestions: ["Autre question", "Exporter conversation", "Retour au forum"]
    }
};

// ========================================
// SYSTÈME DE SUGGESTIONS CONTEXTUELLES
// ========================================

const CONTEXTUAL_SUGGESTIONS = {
    stress_management: [
        "Exercice de respiration guidé",
        "Techniques de relaxation",
        "Planifier mes priorités"
    ],
    focus_techniques: [
        "Essayer Pomodoro maintenant",
        "Musique de concentration",
        "Bloquer les distractions"
    ],
    motivation_boost: [
        "Définir un micro-objectif",
        "Système de récompenses",
        "Trouver mon pourquoi"
    ],
    study_plan: [
        "Créer un planning",
        "Techniques de mémorisation",
        "Ressources d'apprentissage"
    ],
    sleep_improvement: [
        "Routine du soir",
        "Exercice de relaxation",
        "Apps de sommeil"
    ],
    emotional_support: [
        "Ressources professionnelles",
        "Numéros d'urgence",
        "Techniques d'auto-soin"
    ]
};

// ========================================
// INITIALISATION
// ========================================

window.onload = () => {
    initializeChatbot();
    loadConversationHistory();
    
    setTimeout(() => {
        addMessage("Bonjour ! 👋 Comment puis-je vous aider aujourd'hui ?", false);
    }, 1000);
};

function initializeChatbot() {
    const input = document.getElementById('userInput');
    input.focus();
}

// ========================================
// GESTION DES MESSAGES
// ========================================

function sendMessage() {
    const input = document.getElementById('userInput');
    const text = input.value.trim();
    
    if (!text) return;
    
    // Ajouter message utilisateur
    addMessage(text, true);
    input.value = '';
    autoResize(input);
    
    // Cacher écran d'accueil
    hideWelcomeScreen();
    
    // Sauvegarder dans historique
    STATE.conversationHistory.push({
        role: 'user',
        content: text,
        timestamp: new Date()
    });
    
    // Afficher indicateur de frappe
    showTyping();
    
    // Générer réponse IA
    setTimeout(() => {
        const response = generateAIResponse(text);
        hideTyping();
        addMessage(response.message, false);
        
        // Afficher suggestions contextuelles
        if (response.suggestions) {
            displayContextualSuggestions(response.suggestions);
        }
        
        // Mode vocal si activé
        if (CONFIG.voiceEnabled) {
            speakMessage(response.message);
        }
        
        STATE.conversationHistory.push({
            role: 'bot',
            content: response.message,
            timestamp: new Date()
        });
    }, 1500 + Math.random() * 1000);
}

function sendQuickMessage(message) {
    const input = document.getElementById('userInput');
    input.value = message;
    sendMessage();
}

function addMessage(text, isUser) {
    const container = document.getElementById('messagesContainer');
    const welcome = document.getElementById('welcomeScreen');
    
    const group = document.createElement('div');
    group.className = `message-group ${isUser ? 'user' : 'bot'}`;
    
    const time = new Date().toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});
    
    group.innerHTML = `
        <div class="bot-avatar-small">
            ${isUser ? '<i class="fas fa-user"></i>' : '<i class="fas fa-brain"></i>'}
        </div>
        <div class="message-bubble">
            ${formatMessage(text)}
            <div class="message-time">${time}</div>
        </div>
    `;
    
    container.appendChild(group);
    container.scrollTop = container.scrollHeight;
}

function formatMessage(text) {
    // Remplacer Markdown simple
    text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    text = text.replace(/\n/g, '<br>');
    return text;
}

function hideWelcomeScreen() {
    const welcome = document.getElementById('welcomeScreen');
    if (welcome) {
        welcome.style.display = 'none';
    }
}

// ========================================
// MOTEUR IA CONVERSATIONNEL
// ========================================

function generateAIResponse(userMessage) {
    const normalizedMessage = userMessage.toLowerCase();
    
    // Remplacer {name} et {emotion}
    const replacements = {
        '{name}': STATE.userContext.name,
        '{emotion}': detectEmotion(normalizedMessage)
    };
    
    // Parcourir la base de connaissances
    for (const [category, data] of Object.entries(KNOWLEDGE_BASE)) {
        for (const pattern of data.patterns) {
            if (normalizedMessage.includes(pattern)) {
                const response = data.responses[Math.floor(Math.random() * data.responses.length)];
                let finalMessage = response;
                
                // Remplacer variables
                for (const [key, value] of Object.entries(replacements)) {
                    finalMessage = finalMessage.replace(new RegExp(key, 'g'), value);
                }
                
                STATE.lastTopic = category;
                
                return {
                    message: finalMessage,
                    suggestions: data.suggestions,
                    followUp: data.followUp
                };
            }
        }
    }
    
    // Réponse par défaut si aucun pattern trouvé
    return {
        message: "Je suis là pour vous aider avec :\n\n🧘 Gestion du stress\n🎯 Amélioration de la concentration\n💪 Boost de motivation\n📚 Techniques d'étude\n😴 Qualité du sommeil\n💚 Soutien émotionnel\n\nDe quoi souhaitez-vous parler ?",
        suggestions: ["Je suis stressé(e)", "Problème de concentration", "Aide pour réviser"]
    };
}

function detectEmotion(text) {
    const emotions = {
        'stressé': 'stressé(e)',
        'anxieux': 'anxieux(se)',
        'triste': 'triste',
        'fatigué': 'fatigué(e)',
        'démotivé': 'démotivé(e)'
    };
    
    for (const [key, value] of Object.entries(emotions)) {
        if (text.includes(key)) return value;
    }
    
    return 'préoccupé(e)';
}

// ========================================
// SUGGESTIONS CONTEXTUELLES
// ========================================

function displayContextualSuggestions(suggestions) {
    const container = document.getElementById('contextualSuggestions');
    container.innerHTML = '';
    
    if (!suggestions || suggestions.length === 0) return;
    
    suggestions.forEach(suggestion => {
        const btn = document.createElement('button');
        btn.className = 'contextual-suggestion';
        btn.textContent = suggestion;
        btn.onclick = () => sendQuickMessage(suggestion);
        container.appendChild(btn);
    });
}

function updateSuggestions(text) {
    if (text.length < 3) {
        document.getElementById('contextualSuggestions').innerHTML = '';
        return;
    }
    
    // Suggestions basées sur mots-clés
    const keywords = {
        'stress': ["Techniques de relaxation", "Respiration guidée"],
        'étud': ["Méthodes d'étude", "Planning de révision"],
        'dormi': ["Routine sommeil", "Techniques d'endormissement"],
        'motiv': ["Objectifs SMART", "Système de récompenses"]
    };
    
    for (const [keyword, suggestions] of Object.entries(keywords)) {
        if (text.toLowerCase().includes(keyword)) {
            displayContextualSuggestions(suggestions);
            return;
        }
    }
}

// ========================================
// INDICATEUR DE FRAPPE
// ========================================

function showTyping() {
    document.getElementById('typingIndicator').classList.add('active');
    document.getElementById('botStatus').textContent = 'En train d\'écrire...';
}

function hideTyping() {
    document.getElementById('typingIndicator').classList.remove('active');
    document.getElementById('botStatus').textContent = 'En ligne';
}

// ========================================
// MOOD TRACKER
// ========================================

function setMood(mood) {
    STATE.currentMood = mood;
    const moodMessages = {
        'very-happy': "Super ! 😄 Content de voir que vous allez bien ! Comment puis-je rendre votre journée encore meilleure ?",
        'happy': "C'est bien ! 🙂 De quoi voulez-vous parler aujourd'hui ?",
        'neutral': "D'accord. 😐 Y a-t-il quelque chose dont vous aimeriez discuter ?",
        'sad': "Je suis là pour vous. 😕 Voulez-vous en parler ?",
        'very-sad': "Je suis vraiment désolé que vous vous sentiez ainsi. 😢 Parlez-moi, je suis là pour vous écouter."
    };
    
    hideWelcomeScreen();
    addMessage(moodMessages[mood], false);
}

// ========================================
// GESTION CLAVIER
// ========================================

function handleKeyDown(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage();
    }
}

function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

// ========================================
// MODE VOCAL
// ========================================

function toggleVoiceMode() {
    CONFIG.voiceEnabled = !CONFIG.voiceEnabled;
    const icon = document.getElementById('voiceIcon');
    icon.className = CONFIG.voiceEnabled ? 'fas fa-volume-up' : 'fas fa-volume-mute';
    
    addMessage(
        CONFIG.voiceEnabled 
            ? "Mode vocal activé 🔊" 
            : "Mode vocal désactivé 🔇", 
        false
    );
}

function speakMessage(text) {
    if (!('speechSynthesis' in window)) return;
    
    const cleanText = text.replace(/\*\*/g, '').replace(/[🎯💪📚😊]/g, '');
    const utterance = new SpeechSynthesisUtterance(cleanText);
    utterance.lang = 'fr-FR';
    utterance.rate = 0.9;
    speechSynthesis.speak(utterance);
}

// ========================================
// GESTION CONVERSATIONS
// ========================================

function startNewConversation() {
    if (confirm('Démarrer une nouvelle conversation ? (L\'actuelle sera sauvegardée)')) {
        saveCurrentConversation();
        STATE.conversationHistory = [];
        document.getElementById('messagesContainer').innerHTML = '';
        location.reload();
    }
}

function clearCurrentChat() {
    if (confirm('Effacer cette conversation ?')) {
        STATE.conversationHistory = [];
        document.getElementById('messagesContainer').innerHTML = `
            <div class="welcome-screen" id="welcomeScreen">
                <div class="welcome-icon">🌟</div>
                <h3>Nouvelle conversation</h3>
                <p>Comment puis-je vous aider ?</p>
            </div>
        `;
    }
}

function saveCurrentConversation() {
    // Sauvegarder en localStorage (temporaire)
    const conversations = JSON.parse(localStorage.getItem('chatbot_conversations') || '[]');
    conversations.push({
        id: Date.now(),
        date: new Date().toISOString(),
        messages: STATE.conversationHistory,
        mood: STATE.currentMood
    });
    localStorage.setItem('chatbot_conversations', JSON.stringify(conversations));
}

function loadConversationHistory() {
    const conversations = JSON.parse(localStorage.getItem('chatbot_conversations') || '[]');
    const listContainer = document.getElementById('conversationsList');
    
    conversations.slice(-10).reverse().forEach(conv => {
        const item = document.createElement('div');
        item.className = 'conversation-item';
        const date = new Date(conv.date);
        const preview = conv.messages[0]?.content.substring(0, 50) + '...';
        
        item.innerHTML = `
            <div class="conversation-date">${date.toLocaleDateString('fr-FR')}</div>
            <div class="conversation-preview">${preview}</div>
        `;
        
        listContainer.appendChild(item);
    });
}

// ========================================
// EXPORT CONVERSATION
// ========================================

function exportConversation() {
    if (STATE.conversationHistory.length === 0) {
        alert('Aucune conversation à exporter.');
        return;
    }
    
    let text = `CONVERSATION SMARTSTUDY+ ASSISTANT IA\n`;
    text += `Date: ${new Date().toLocaleDateString('fr-FR')}\n`;
    text += `Utilisateur: ${STATE.userContext.name}\n`;
    text += `\n${'='.repeat(50)}\n\n`;
    
    STATE.conversationHistory.forEach(msg => {
        const role = msg.role === 'user' ? 'VOUS' : 'ASSISTANT';
        text += `[${role}] ${msg.content}\n\n`;
    });
    
    const blob = new Blob([text], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `conversation_${Date.now()}.txt`;
    a.click();
}

// ========================================
// EMOJIS
// ========================================

function toggleEmojiPicker() {
    const picker = document.getElementById('emojiPicker');
    picker.style.display = picker.style.display === 'none' ? 'flex' : 'none';
}

function insertEmoji(emoji) {
    const input = document.getElementById('userInput');
    input.value += emoji;
    input.focus();
    toggleEmojiPicker();
}