<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chatbot</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Font Awesome for GitHub icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Modern CSS with CSS variables for easy theming */
        :root {
            --primary-color: #4361ee;
            --user-color: #4cc9f0;
            --ai-color: #7209b7;
            --bg-color: #f8f9fa;
            --text-color: #212529;
            --border-radius: 12px;
        }

        /* Base Styles */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            background-color: var(--bg-color);
            color: var(--text-color);
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }

        /* Chat Container */
        #chat-container {
            display: flex;
            flex-direction: column;
            height: 70vh;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        /* Message Area */
        #chat-box {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            scroll-behavior: smooth;
        }

        /* Message Bubbles */
        .message {
            max-width: 80%;
            padding: 0.8rem 1.2rem;
            margin-bottom: 1rem;
            border-radius: var(--border-radius);
            animation: fadeIn 0.3s ease-out;
            position: relative;
            word-wrap: break-word;
        }

        .user-message {
            background-color: var(--user-color);
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 0;
        }

        .ai-message {
            background-color: #f1f1f1;
            margin-right: auto;
            border-bottom-left-radius: 0;
        }

        /* Input Area */
        #input-area {
            display: flex;
            padding: 1rem;
            background: white;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        #user-input {
            flex: 1;
            padding: 0.8rem 1.2rem;
            border: 2px solid #e9ecef;
            border-radius: 50px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s;
        }

        #user-input:focus {
            border-color: var(--primary-color);
        }

        #send-btn {
            margin-left: 0.8rem;
            padding: 0.8rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }

        #send-btn:hover {
            background-color: #3a56d4;
            transform: translateY(-1px);
        }

        #send-btn:disabled {
            background-color: #adb5bd;
            cursor: not-allowed;
            transform: none;
        }

        /* Typing Indicator */
        .typing-indicator {
            display: inline-flex;
            padding: 0.8rem 1.2rem;
            background: #f1f1f1;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background-color: #6c757d;
            border-radius: 50%;
            margin: 0 2px;
            animation: typingAnimation 1.4s infinite ease-in-out;
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

        /* Timestamps (optional) */
        .timestamp {
            display: block;
            font-size: 0.7rem;
            color: #6c757d;
            margin-top: 0.3rem;
            text-align: right;
        }

                footer {
            text-align: center;
            padding: 1.5rem;
            margin-top: 2rem;
            color: #6c757d;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .footer-content {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }
        
        .footer-heart {
            color: #e25555;
            font-size: 1.2em;
        }
        
        .footer-name {
            font-weight: 600;
            color: #4361ee;
        }
        
        @media (max-width: 600px) {
            footer {
                font-size: 0.9rem;
                padding: 1rem;
            }
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            body {
                padding: 1rem;
            }
            #chat-container {
                height: 85vh;
            }
            .message {
                max-width: 90%;
            }
        }
    </style>
</head>
<body>
    <h1>AI Mini Chatbot</h1>
    <div id="chat-container">
        <!-- Chat messages will appear here -->
        <div id="chat-box">
            <div class="message ai-message">
                Hello! I'm your AI assistant. How can I help you today?
                <span class="timestamp">Just now</span>
            </div>
        </div>

        <!-- Input area -->
        <div id="input-area">
            <input type="text" id="user-input" placeholder="Type your message..." autocomplete="off">
            <button id="send-btn">Send</button>
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
                
                messageDiv.innerHTML = `
                    ${content}
                    <span class="timestamp">${getCurrentTime()}</span>
                `;
                
                chatBox.appendChild(messageDiv);
                scrollToBottom();
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
                if (indicator) indicator.remove();
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
                    // Simulate API delay for demo (remove in production)
                    await new Promise(resolve => setTimeout(resolve, 1000));
                    
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
                if (e.key === 'Enter') sendMessage();
            });

            // Initial focus
            userInput.focus();
        });
    </script>
</body>
</html>