<?php
session_start();

// ✅ Vérifier connexion
if (!isset($_SESSION['user'])) {
    header('Location: ../../../view/login.php');
    exit();
}

$currentUser = $_SESSION['user']['nom'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant IA Bien-être | SmartStudy+</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="chatbotStyles.css">
</head>
<body>
    <!-- Widget utilisateur -->
    

    <div class="chatbot-container">
        <!-- Sidebar -->
        <aside class="chatbot-sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-history"></i> Historique</h3>
                <button class="btn-new-chat" onclick="startNewConversation()">
                    <i class="fas fa-plus"></i> Nouvelle discussion
                </button>
            </div>
            <div class="conversations-list" id="conversationsList">
                <!-- Liste dynamique des conversations -->
            </div>
        </aside>

        <!-- Zone de chat principale -->
        <main class="chatbot-main">
            <!-- Header -->
            <header class="chat-header">
                <div class="bot-info">
                    <div class="bot-avatar-large">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div>
                        <h2>Assistant IA Bien-être</h2>
                        <div class="bot-status">
                            <span class="status-dot"></span>
                            <span id="botStatus">En ligne</span>
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <button class="header-btn" onclick="toggleVoiceMode()" title="Mode vocal">
                        <i class="fas fa-volume-up" id="voiceIcon"></i>
                    </button>
                    <button class="header-btn" onclick="exportConversation()" title="Exporter (PDF)">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="header-btn" onclick="clearCurrentChat()" title="Effacer">
                        <i class="fas fa-trash"></i>
                    </button>
                    <a href="forums.php" class="header-btn" title="Retour au forum">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </header>

            <!-- Messages -->
            <div class="messages-container" id="messagesContainer">
                <div class="welcome-screen" id="welcomeScreen">
                    <div class="welcome-icon">🌟</div>
                    <h3>Bienvenue, <?php echo htmlspecialchars($currentUser); ?> !</h3>
                    <p>Je suis votre assistant bien-être. Parlez-moi librement de vos préoccupations.</p>
                    
                    <!-- Suggestions rapides -->
                    <div class="quick-suggestions">
                        <button class="suggestion-btn" onclick="sendQuickMessage('Je me sens stressé(e)')">
                            😰 Je suis stressé(e)
                        </button>
                        <button class="suggestion-btn" onclick="sendQuickMessage('J\'ai du mal à me concentrer')">
                            😵 Problème de concentration
                        </button>
                        <button class="suggestion-btn" onclick="sendQuickMessage('Je manque de motivation')">
                            😔 Manque de motivation
                        </button>
                        <button class="suggestion-btn" onclick="sendQuickMessage('Comment mieux réviser ?')">
                            📚 Conseils d'étude
                        </button>
                        <button class="suggestion-btn" onclick="sendQuickMessage('Je dors mal')">
                            😴 Problèmes de sommeil
                        </button>
                        <button class="suggestion-btn" onclick="sendQuickMessage('Je me sens triste')">
                            😢 Aide émotionnelle
                        </button>
                    </div>

                    <!-- Indicateur d'humeur -->
                    <div class="mood-tracker">
                        <p class="mood-label">Comment vous sentez-vous aujourd'hui ?</p>
                        <div class="mood-options">
                            <button class="mood-btn" onclick="setMood('very-happy')" title="Très bien">😄</button>
                            <button class="mood-btn" onclick="setMood('happy')" title="Bien">🙂</button>
                            <button class="mood-btn" onclick="setMood('neutral')" title="Neutre">😐</button>
                            <button class="mood-btn" onclick="setMood('sad')" title="Pas bien">😕</button>
                            <button class="mood-btn" onclick="setMood('very-sad')" title="Très mal">😢</button>
                        </div>
                    </div>
                </div>

                <!-- Indicateur de frappe -->
                <div class="typing-indicator" id="typingIndicator">
                    <div class="bot-avatar-small">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>

            <!-- Zone de saisie -->
            <footer class="chat-input-area">
                <!-- Suggestions contextuelles -->
                <div class="contextual-suggestions" id="contextualSuggestions"></div>

                <div class="input-wrapper">
                    <button class="input-btn" onclick="toggleEmojiPicker()" title="Émojis">
                        <i class="fas fa-smile"></i>
                    </button>
                    <textarea 
                        id="userInput" 
                        placeholder="Écrivez votre message... (Maj+Entrée pour nouvelle ligne)"
                        rows="1"
                        onkeydown="handleKeyDown(event)"
                        oninput="autoResize(this); updateSuggestions(this.value)"></textarea>
                    <button class="send-btn" onclick="sendMessage()" id="sendBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>

                <!-- Sélecteur d'émojis simple -->
                <div class="emoji-picker" id="emojiPicker" style="display:none">
                    <button onclick="insertEmoji('😊')">😊</button>
                    <button onclick="insertEmoji('😢')">😢</button>
                    <button onclick="insertEmoji('😰')">😰</button>
                    <button onclick="insertEmoji('💪')">💪</button>
                    <button onclick="insertEmoji('🎯')">🎯</button>
                    <button onclick="insertEmoji('📚')">📚</button>
                    <button onclick="insertEmoji('☕')">☕</button>
                    <button onclick="insertEmoji('🌟')">🌟</button>
                </div>
            </footer>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // Configuration globale
        const CONFIG = {
            currentUser: '<?php echo htmlspecialchars($currentUser); ?>',
            voiceEnabled: false,
            conversationId: null,
            userMood: null
        };
    </script>
    <script src="chatbotEngine.js"></script>
</body>
</html>