<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <link rel="icon" href="{{ asset('images/image2.png') }}" type="image/x-icon"> --}}
    <link rel="icon" href="https://aichat-tlvh.onrender.com/images/image2.png" type="image/x-icon">
    <title>AI Mini Chatbot</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Font Awesome and Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Modern CSS with CSS variables for easy theming */
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --user-color: #4cc9f0;
            --user-hover: #3ab4d9;
            --ai-color: #7209b7;
            --bg-color: #f8f9fa;
            --text-color: #212529;
            --light-text: #6c757d;
            --border-color: #e9ecef;
            --border-radius: 12px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        /* Base Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            background-color: var(--bg-color);
            color: var(--text-color);
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 2.2rem;
            background: linear-gradient(45deg, #4361ee, #7209b7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Chat Container */
        #chat-container {
            display: flex;
            flex-direction: column;
            height: 70vh;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            border: 1px solid var(--border-color);
            flex-grow: 1;
        }

        /* Message Area */
        #chat-box {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            scroll-behavior: smooth;
            background-color: rgba(248, 249, 250, 0.5);
            background-image: 
                radial-gradient(circle at 1px 1px, rgba(0, 0, 0, 0.03) 1px, transparent 0);
            background-size: 20px 20px;
        }

        /* Message Bubbles */
        .message {
            max-width: 85%;
            padding: 0.8rem 1.2rem;
            margin-bottom: 1rem;
            border-radius: var(--border-radius);
            animation: fadeIn 0.3s ease-out;
            position: relative;
            word-wrap: break-word;
            line-height: 1.5;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
        }

        .user-message {
            background-color: var(--user-color);
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 0;
        }

        .user-message:hover {
            background-color: var(--user-hover);
            transform: translateX(-2px);
        }

        .ai-message {
            background-color: white;
            margin-right: auto;
            border-bottom-left-radius: 0;
            border: 1px solid var(--border-color);
        }

        .ai-message:hover {
            transform: translateX(2px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Input Area */
        #input-area {
            display: flex;
            padding: 1rem;
            background: white;
            border-top: 1px solid var(--border-color);
            position: relative;
        }

        #user-input {
            flex: 1;
            padding: 0.8rem 1.2rem;
            padding-right: 3rem;
            border: 2px solid var(--border-color);
            border-radius: 50px;
            font-size: 1rem;
            outline: none;
            transition: var(--transition);
            background-color: var(--bg-color);
        }

        #user-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        #send-btn {
            position: absolute;
            right: 1.5rem;
            padding: 0.5rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 0.5%;
        }

        #send-btn:hover {
            background-color: var(--primary-hover);
            transform: scale(1.05);
        }

        #send-btn:disabled {
            background-color: #adb5bd;
            cursor: not-allowed;
            transform: none;
        }

        #send-btn i {
            font-size: 1rem;
        }

        /* Typing Indicator */
        .typing-indicator {
            display: inline-flex;
            padding: 0.8rem 1.2rem;
            background: white;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background-color: var(--light-text);
            border-radius: 50%;
            margin: 0 2px;
            animation: typingAnimation 1.4s infinite ease-in-out;
        }

        .typing-dot:nth-child(1) {
            animation-delay: 0s;
        }
        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes typingAnimation {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.6; }
            30% { transform: translateY(-4px); opacity: 1; }
        }

        /* Timestamps */
        .timestamp {
            display: block;
            font-size: 0.7rem;
            color: var(--light-text);
            margin-top: 0.3rem;
            text-align: right;
            opacity: 0.8;
        }

        /* Message status indicator */
        .message-status {
            position: absolute;
            right: -20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.7rem;
            color: var(--light-text);
        }

        /* Add these to your existing styles */
        .ai-message h3 {
            color: var(--ai-color);
            margin: 0.8rem 0 0.5rem;
            font-size: 1.1rem;
        }

        .ai-message pre {
            background-color: #f5f5f5;
            padding: 1rem;
            border-radius: 8px;
            overflow-x: auto;
            margin: 0.8rem 0;
            border-left: 3px solid var(--ai-color);
        }

        .ai-message code {
            font-family: 'Courier New', Courier, monospace;
            background-color: rgba(114, 9, 183, 0.1);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .ai-message hr {
            border: none;
            border-top: 1px solid var(--border-color);
            margin: 1rem 0;
        }

        /* Syntax highlighting for code blocks */
        .ai-message .language-php {
            color: #4f5b93;
        }
        .ai-message .language-javascript {
            color: #f8dc3d;
        }
        .ai-message .language-python {
            color: #3572A5;
        }
        .ai-message .language-java {
            color: #b07219;
        }
        .ai-message .language-html {
            color: #e34c26;
        }
        .ai-message .language-css {
            color: #563d7c;
        }

        /* Enhanced Message Styles */
        .ai-message {
            max-width: 90%;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: white;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .ai-message h3 {
            color: var(--ai-color);
            margin: 0.8rem 0 0.5rem;
            font-size: 1.1rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.3rem;
        }

        .ai-message .code-block {
            position: relative;
            background-color: #f5f5f5;
            border-radius: 8px;
            margin: 0.8rem 0;
            border-left: 3px solid var(--ai-color);
        }

        .ai-message pre {
            padding: 1rem;
            overflow-x: auto;
            margin: 0;
            white-space: pre-wrap;
        }

        .ai-message code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9em;
        }

        .ai-message .copy-btn {
            position: absolute;
            right: 0.5rem;
            top: 0.5rem;
            background: rgba(255, 255, 255, 0.8);
            border: none;
            border-radius: 4px;
            padding: 0.3rem 0.5rem;
            cursor: pointer;
            opacity: 0;
            transition: var(--transition);
        }

        .ai-message .code-block:hover .copy-btn {
            opacity: 1;
        }

        .ai-message .copy-btn:hover {
            background: white;
        }

        .ai-message .copy-btn i {
            font-size: 0.8rem;
        }

        .ai-message hr {
            border: none;
            border-top: 1px solid var(--border-color);
            margin: 1rem 0;
        }

        .ai-message ul {
            margin: 0.5rem 0 0.5rem 1.5rem;
        }

        .ai-message li {
            margin-bottom: 0.3rem;
        }

        .ai-message .expand-btn {
            display: block;
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.5rem;
            background-color: var(--bg-color);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            cursor: pointer;
            text-align: center;
            transition: var(--transition);
        }

        .ai-message .expand-btn:hover {
            background-color: #e9ecef;
        }

        .ai-message .expand-btn i {
            margin-left: 0.5rem;
            transition: var(--transition);
        }

        .ai-message .expand-btn.expanded i {
            transform: rotate(180deg);
        }

        /* Syntax highlighting */
        .token.comment,
        .token.prolog,
        .token.doctype,
        .token.cdata {
            color: #6a9955;
        }
        .token.punctuation {
            color: #d4d4d4;
        }
        .token.property,
        .token.tag,
        .token.boolean,
        .token.number,
        .token.constant,
        .token.symbol,
        .token.deleted {
            color: #b5cea8;
        }
        .token.selector,
        .token.attr-name,
        .token.string,
        .token.char,
        .token.builtin,
        .token.inserted {
            color: #ce9178;
        }
        .token.operator,
        .token.entity,
        .token.url,
        .language-css .token.string,
        .style .token.string {
            color: #d4d4d4;
        }
        .token.atrule,
        .token.attr-value,
        .token.keyword {
            color: #569cd6;
        }
        .token.function,
        .token.class-name {
            color: #dcdcaa;
        }
        .token.regex,
        .token.important,
        .token.variable {
            color: #d16969;
        }

        /* Footer section */
        footer {
            text-align: center;
            padding: 1.5rem;
            margin-top: 2rem;
            color: var(--light-text);
            font-size: 0.9rem;
        }
        
        .footer-content {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .footer-heart {
            color: #e25555;
            font-size: 1.2em;
            animation: heartbeat 1.5s infinite;
        }
        
        .footer-name {
            font-weight: 600;
            color: var(--primary-color);
            transition: var(--transition);
        }

        .footer-name:hover {
            color: var(--ai-color);
        }
        
        .footer-github {
            color: var(--text-color);
            transition: var(--transition);
        }

        .footer-github:hover {
            color: var(--primary-color);
            transform: scale(1.2);
        }

        @keyframes heartbeat {
            0% { transform: scale(1); }
            25% { transform: scale(1.1); }
            50% { transform: scale(1); }
            75% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            h1 {
                font-size: 1.8rem;
                margin-bottom: 1rem;
            }
            
            #chat-container {
                height: 80vh;
            }
            
            .message {
                max-width: 90%;
            }
            
            #input-area {
                padding: 0.8rem;
            }
            
            #user-input {
                padding: 0.7rem 1rem;
                padding-right: 2.8rem;
            }
            
            #send-btn {
                right: 1.3rem;
                width: 36px;
                height: 36px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
            }
            
            #chat-box {
                padding: 1rem;
            }
            
            .message {
                padding: 0.7rem 1rem;
            }
            
            footer {
                font-size: 0.8rem;
                padding: 1rem;
            }
        }

        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.15);
        }

        /* Loading animation for initial message */
        .welcome-message {
            position: relative;
            overflow: hidden;
        }

        .welcome-message::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>
</head>
<body>
    <h1>AI Mini Chatbot</h1>
    <div id="chat-container">
        <!-- Chat messages will appear here -->
        <div id="chat-box">
            <div class="message ai-message welcome-message">
                Hello! I'm your AI Mini Chatbot. How can I help you today?
                <span class="timestamp">Just now</span>
            </div>
        </div>

        <!-- Input area -->
        <div id="input-area">
            <input type="text" id="user-input" placeholder="Type your message..." autocomplete="off" autofocus>
            <button id="send-btn" aria-label="Send message">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <!-- Footer section -->
    <footer>
        <div class="footer-content">
            <span>Created with</span>
            <span class="footer-heart">♥</span>
            <span>by</span>
            <span class="footer-name">mn_annas</span>
            <a href="https://github.com/mnannas" target="_blank" aria-label="GitHub Profile">
                <i class="fab fa-github footer-github"></i>
            </a>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const chatBox = document.getElementById('chat-box');
            const userInput = document.getElementById('user-input');
            const sendBtn = document.getElementById('send-btn');
            
            // Remove shimmer effect after initial load
            setTimeout(() => {
                const welcomeMsg = document.querySelector('.welcome-message');
                if (welcomeMsg) {
                    welcomeMsg.classList.remove('welcome-message');
                }
            }, 1500);

            // Auto-scroll to bottom
            function scrollToBottom() {
                chatBox.scrollTop = chatBox.scrollHeight;
            }

            // Get current time for timestamps
            function getCurrentTime() {
                const now = new Date();
                return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }

            // Add message to chat
            function addMessage(content, isUser = false) {
                const messageDiv = document.createElement('div');
                messageDiv.className = isUser ? 'message user-message' : 'message ai-message';
                
                // For AI messages, allow HTML content (sanitized by backend)
                if (isUser) {
                    messageDiv.textContent = content;
                } else {
                    messageDiv.innerHTML = content;
                }
                
                messageDiv.innerHTML += `<span class="timestamp">${getCurrentTime()}</span>`;
                
                chatBox.appendChild(messageDiv);
                scrollToBottom();
                
                // Initialize interactive elements for AI messages
                if (!isUser) {
                    initMessageFeatures(messageDiv);
                }
                
                // Add slight delay for animation
                setTimeout(() => {
                    messageDiv.style.opacity = '1';
                }, 10);
            }

            // Initialize interactive features for AI messages
            function initMessageFeatures(messageDiv) {
                // Copy buttons for code blocks
                messageDiv.querySelectorAll('.copy-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const codeBlock = this.parentElement.querySelector('code');
                        if (codeBlock) {
                            navigator.clipboard.writeText(codeBlock.textContent)
                                .then(() => {
                                    const originalIcon = this.innerHTML;
                                    this.innerHTML = '<i class="fas fa-check"></i>';
                                    setTimeout(() => {
                                        this.innerHTML = originalIcon;
                                    }, 2000);
                                });
                        }
                    });
                });

                // Expand/collapse buttons for long messages
                messageDiv.querySelectorAll('.expand-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const content = this.previousElementSibling;
                        if (content.style.display === 'none') {
                            content.style.display = 'block';
                            this.innerHTML = 'Show Less <i class="fas fa-chevron-up"></i>';
                            this.classList.add('expanded');
                        } else {
                            content.style.display = 'none';
                            this.innerHTML = 'Show More <i class="fas fa-chevron-down"></i>';
                            this.classList.remove('expanded');
                        }
                        scrollToBottom();
                    });
                });

                // Apply syntax highlighting
                messageDiv.querySelectorAll('pre code').forEach(block => {
                    hljs.highlightElement(block);
                });
            }

            // Show typing indicator
            function showTypingIndicator() {
                const typingDiv = document.createElement('div');
                typingDiv.className = 'message ai-message typing-indicator';
                typingDiv.id = 'typing-indicator';
                typingDiv.innerHTML = `
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                `;
                chatBox.appendChild(typingDiv);
                scrollToBottom();
            }

            // Hide typing indicator
            function hideTypingIndicator() {
                const indicator = document.getElementById('typing-indicator');
                if (indicator) {
                    indicator.style.opacity = '0';
                    setTimeout(() => indicator.remove(), 300);
                }
            }

            // Handle sending message
            async function sendMessage() {
                const message = userInput.value.trim();
                if (!message) return;

                // Add user message
                addMessage(message, true);
                userInput.value = '';
                sendBtn.disabled = true;

                // Show typing indicator
                showTypingIndicator();

                try {
                    // Send to server
                    const response = await fetch('/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ message })
                    });

                    if (!response.ok) throw new Error('Network error');
                    
                    const data = await response.json();
                    addMessage(data.reply);
                } catch (error) {
                    addMessage(`Sorry, I encountered an error: ${error.message}`);
                    console.error('Chat error:', error);
                } finally {
                    hideTypingIndicator();
                    sendBtn.disabled = false;
                    userInput.focus();
                }
            }

            // Event Listeners
            sendBtn.addEventListener('click', sendMessage);
            
            userInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            // Input validation
            userInput.addEventListener('input', () => {
                sendBtn.disabled = userInput.value.trim() === '';
            });

            // Initial focus
            userInput.focus();

            // Load syntax highlighting library
            const hljsScript = document.createElement('script');
            hljsScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js';
            hljsScript.onload = () => {
                hljs.highlightAll();
            };
            document.head.appendChild(hljsScript);

            // Load additional language support
            const hljsLanguages = document.createElement('script');
            hljsLanguages.src = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/languages/javascript.min.js';
            document.head.appendChild(hljsLanguages);
        });
    </script>
</body>
<!-- Add these before your closing </head> tag -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/github.min.css">
<style>
.hljs {
    background: transparent !important;
    padding: 0 !important;
}
</style>
</html>