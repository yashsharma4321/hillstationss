@extends('layouts.admin')

@section('header', 'Real-time Chat')

@section('styles')
    <style>
        .chat-container {
            display: flex;
            height: calc(100vh - 200px);
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .users-list {
            width: 320px;
            border-right: 1px solid #e5e7eb;
            background: #f9fafb;
            overflow-y: auto;
        }

        .user-item {
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 1px solid #e5e7eb;
        }

        .user-item:hover {
            background: #f3f4f6;
        }

        .user-item.active {
            background: #6366f1;
            color: white;
        }

        .user-item.active .last-message {
            color: rgba(255, 255, 255, 0.8);
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.125rem;
            margin-right: 0.75rem;
        }

        .user-info {
            display: inline-block;
            vertical-align: middle;
        }

        .user-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .last-message {
            font-size: 0.75rem;
            color: #6b7280;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 180px;
        }

        .unread-badge {
            background: #ef4444;
            color: white;
            border-radius: 9999px;
            padding: 0.125rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }

        .chat-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: white;
        }

        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            background: #f9fafb;
        }

        .message {
            margin-bottom: 1rem;
            display: flex;
        }

        .message.sent {
            justify-content: flex-end;
        }

        .message.received {
            justify-content: flex-start;
        }

        .message-bubble {
            max-width: 65%;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            word-wrap: break-word;
        }

        .sent .message-bubble {
            background: #6366f1;
            color: white;
            border-bottom-right-radius: 0.25rem;
        }

        .received .message-bubble {
            background: white;
            border: 1px solid #e5e7eb;
            color: #1f2937;
            border-bottom-left-radius: 0.25rem;
        }

        .message-time {
            font-size: 0.625rem;
            margin-top: 0.25rem;
            opacity: 0.7;
        }

        .chat-input-area {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e5e7eb;
            background: white;
        }

        .input-group {
            display: flex;
            gap: 0.75rem;
        }

        .message-input {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            resize: none;
            font-family: inherit;
            font-size: 0.875rem;
        }

        .message-input:focus {
            outline: none;
            border-color: #6366f1;
        }

        .send-btn {
            padding: 0.75rem 1.5rem;
            background: #6366f1;
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s;
        }

        .send-btn:hover {
            background: #4f46e5;
        }

        .empty-chat {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #6b7280;
            text-align: center;
            flex-direction: column;
        }

        .loading-spinner {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            border: 3px solid #e5e7eb;
            border-top-color: #6366f1;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .debug-panel {
            position: fixed;
            bottom: 10px;
            right: 10px;
            background: #1f2937;
            color: white;
            padding: 10px;
            border-radius: 8px;
            font-size: 12px;
            z-index: 9999;
            font-family: monospace;
        }

        /* Typing Indicator */
        .typing-indicator {
            display: none;
            padding: 0.5rem 1.5rem;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            font-size: 0.8rem;
            font-style: italic;
        }
        .typing-indicator.show {
            display: flex;
        }
        .typing-dots {
            display: flex;
            gap: 4px;
            align-items: center;
        }
        .typing-dots span {
            width: 7px;
            height: 7px;
            background: #9ca3af;
            border-radius: 50%;
            animation: typingBounce 1.2s infinite;
        }
        .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
        .typing-dots span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes typingBounce {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.5; }
            30% { transform: translateY(-6px); opacity: 1; }
        }
    </style>
@endsection

@section('content')
    <div class="chat-container">
        <div class="users-list" id="usersList">
            <div class="text-center p-4">
                <div class="loading-spinner"></div>
                <p class="mt-2 text-sm text-gray-500">Loading users...</p>
            </div>
        </div>
        <div class="chat-area" id="chatArea">
            <div class="empty-chat">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.5">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                </svg>
                <h3 style="font-weight: 600; margin-bottom: 0.5rem;">No conversation selected</h3>
                <p style="font-size: 0.875rem;">Choose a user from the list to start chatting</p>
            </div>
        </div>
    </div>

    <div class="debug-panel" id="debugPanel">
        <strong>🔍 Debug Info</strong><br>
        Echo: <span id="debugEcho">Checking...</span><br>
        Connection: <span id="debugConn">-</span><br>
        <span id="debugMessage"></span>
    </div>
@endsection

@section('scripts')
    <script>
        let currentUserId = null;
        let currentUserName = null;
        let echoChannel = null;
        const authUserId = {{ auth()->id() ?? 0 }};

        // Get CSRF token safely
        function getCsrfToken() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (!token) {
                console.error('CSRF token meta tag not found!');
                return null;
            }
            return token.content;
        }

        function debugLog(message, type = 'info') {
            console.log(`[${type.toUpperCase()}] ${message}`);
            const debugSpan = document.getElementById('debugMessage');
            if (debugSpan) {
                debugSpan.innerHTML = message;
                setTimeout(() => {
                    if (debugSpan.innerHTML === message) debugSpan.innerHTML = '';
                }, 3000);
            }
        }

        function updateDebugPanel() {
            const echoSpan = document.getElementById('debugEcho');
            const connSpan = document.getElementById('debugConn');

            if (typeof window.Echo !== 'undefined' && window.Echo) {
                echoSpan.innerHTML = '✅ Loaded';
                echoSpan.style.color = '#10b981';

                if (window.Echo.connector && window.Echo.connector.pusher) {
                    const state = window.Echo.connector.pusher.connection.state;
                    connSpan.innerHTML = state;
                    if (state === 'connected') {
                        connSpan.style.color = '#10b981';
                    } else {
                        connSpan.style.color = '#f59e0b';
                    }
                }
            } else {
                echoSpan.innerHTML = '❌ Not Loaded';
                echoSpan.style.color = '#ef4444';
            }
        }

        function loadUsers() {
            debugLog('Loading users...');
            const csrfToken = getCsrfToken();
            if (!csrfToken) {
                debugLog('CSRF token missing!', 'error');
                return;
            }

            fetch('/admin/chat/users', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    return response.json();
                })
                .then(users => {
                    const usersList = document.getElementById('usersList');
                    if (users.length === 0) {
                        usersList.innerHTML = '<div class="text-center p-4 text-gray-500">No other users found</div>';
                        return;
                    }

                    usersList.innerHTML = users.map(user => `
                <div class="user-item ${currentUserId === user.id ? 'active' : ''}" 
                     onclick="selectUser(${user.id}, '${escapeHtml(user.name)}')">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center;">
                            <div class="user-avatar">${user.name.charAt(0).toUpperCase()}</div>
                            <div class="user-info">
                                <div class="user-name">${escapeHtml(user.name)}</div>
                                <div class="last-message">${escapeHtml(user.last_message) || 'No messages yet'}</div>
                            </div>
                        </div>
                        ${user.unread_count > 0 ? `<span class="unread-badge">${user.unread_count}</span>` : ''}
                    </div>
                    <div class="last-message" style="margin-left: 60px; font-size: 0.625rem;">
                        ${user.last_message_time || ''}
                    </div>
                </div>
            `).join('');

                    debugLog(`Loaded ${users.length} users`);
                    updateDebugPanel();
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                    debugLog(`Error: ${error.message}`, 'error');
                });
        }

        function selectUser(userId, userName) {
            debugLog(`Selected user: ${userName} (ID: ${userId})`);
            currentUserId = userId;
            currentUserName = userName;
            loadUsers();
            loadMessages(userId);
            setupWebSocketListener();
        }

        function setupWebSocketListener() {
            if (typeof window.Echo !== 'undefined' && window.Echo) {
                debugLog(`Setting up WebSocket listener on chat.${authUserId}`);
                try {
                    if (!echoChannel) {
                        echoChannel = window.Echo.private(`chat.${authUserId}`);
                        echoChannel.listen('.message.sent', (e) => {
                            debugLog(`Received message from ${e.sender_name}`);
                            // If the message is from the currently selected user, append it
                            if (currentUserId == e.sender_id) {
                                appendMessage(e, 'received');
                            }
                            // Always reload users list to update unread counts and last message
                            loadUsers();
                        });
                        // Also listen for typing events
                        echoChannel.listen('.user.typing', (e) => {
                            if (currentUserId == e.sender_id) {
                                showTypingIndicator();
                            }
                        });
                    }
                } catch (e) {
                    console.error('WebSocket setup error:', e);
                    debugLog(`WebSocket error: ${e.message}`, 'error');
                }
            }
        }

        function loadMessages(userId) {
            debugLog(`Loading messages for user ${userId}`);
            const csrfToken = getCsrfToken();

            fetch(`/admin/chat/messages/${userId}`, {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(messages => {
                    const chatArea = document.getElementById('chatArea');
                    chatArea.innerHTML = `
                <div class="chat-header">
                    <h3 style="font-weight: 600;">Chat with ${escapeHtml(currentUserName)}</h3>
                </div>
                <div class="messages-container" id="messagesContainer">
                    ${messages.map(msg => `
                            <div class="message ${msg.sender_id == authUserId ? 'sent' : 'received'}">
                                <div class="message-bubble">
                                    ${escapeHtml(msg.message)}
                                    <div class="message-time">${msg.created_at}</div>
                                </div>
                            </div>
                        `).join('')}
                </div>
                <div class="typing-indicator" id="typingIndicator">
                    <div class="typing-dots"><span></span><span></span><span></span></div>
                    <span id="typingName"></span> is typing...
                </div>
                <div class="chat-input-area">
                    <div class="input-group">
                        <textarea id="messageInput" class="message-input" placeholder="Type your message..." rows="2" oninput="handleTypingInput()"></textarea>
                        <button class="send-btn" onclick="sendMessage()">Send</button>
                    </div>
                </div>
            `;

                    const container = document.getElementById('messagesContainer');
                    if (container) container.scrollTop = container.scrollHeight;

                    const messageInput = document.getElementById('messageInput');
                    if (messageInput) {
                        messageInput.addEventListener('keypress', function(e) {
                            if (e.key === 'Enter' && !e.shiftKey) {
                                e.preventDefault();
                                sendMessage();
                            }
                        });
                        messageInput.focus();
                    }

                    debugLog(`Loaded ${messages.length} messages`);
                })
                .catch(error => {
                    console.error('Error loading messages:', error);
                    debugLog(`Error loading messages: ${error.message}`, 'error');
                });
        }

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            if (!messageInput) {
                debugLog('Message input not found!', 'error');
                return;
            }

            const message = messageInput.value.trim();
            debugLog(`Sending message: "${message.substring(0, 30)}"`);

            if (!message) {
                debugLog('No message to send', 'error');
                return;
            }

            if (!currentUserId) {
                debugLog('No user selected', 'error');
                alert('Please select a user first');
                return;
            }

            const csrfToken = getCsrfToken();
            if (!csrfToken) {
                debugLog('CSRF token missing! Refresh the page.', 'error');
                alert('CSRF token missing. Please refresh the page.');
                return;
            }

            fetch('/admin/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        receiver_id: currentUserId,
                        message: message
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        debugLog('Message sent successfully!');
                        messageInput.value = '';
                        appendMessage(data.message, 'sent');
                        loadUsers();
                    } else {
                        debugLog(`Send failed: ${data.error || 'Unknown error'}`, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    debugLog(`Error: ${error.message}`, 'error');
                    alert(`Error: ${error.message}`);
                });
        }

        function appendMessage(message, direction) {
            const container = document.getElementById('messagesContainer');
            if (!container) return;

            const isSent = direction === 'sent' || message.sender_id === authUserId;
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
            messageDiv.innerHTML = `
            <div class="message-bubble">
                ${escapeHtml(message.message)}
                <div class="message-time">Just now</div>
            </div>
        `;
            container.appendChild(messageDiv);
            container.scrollTop = container.scrollHeight;
        }

        // Typing indicator logic
        let typingTimer = null;
        let typingTimeout = null;

        function sendTypingEvent() {
            if (!currentUserId) return;
            fetch('/admin/chat/typing', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ receiver_id: currentUserId })
            }).catch(() => {});
        }

        function handleTypingInput() {
            clearTimeout(typingTimer);
            sendTypingEvent();
            typingTimer = setTimeout(() => {}, 2000);
        }

        // Listen for typing events via WebSocket
        function setupTypingListener() {
            if (echoChannel) {
                echoChannel.listen('.user.typing', (e) => {
                    if (currentUserId == e.sender_id) {
                        showTypingIndicator();
                    }
                });
            }
        }

        function showTypingIndicator() {
            const indicator = document.getElementById('typingIndicator');
            const nameSpan = document.getElementById('typingName');
            if (indicator) {
                if (nameSpan) nameSpan.textContent = currentUserName || 'User';
                indicator.classList.add('show');
                clearTimeout(typingTimeout);
                typingTimeout = setTimeout(() => {
                    indicator.classList.remove('show');
                }, 2500);
            }
        }

        // Initialize
        setTimeout(updateDebugPanel, 1000);
        loadUsers();
        setupWebSocketListener();
        // Setup typing listener after channel is created
        setTimeout(setupTypingListener, 1500);
        setInterval(loadUsers, 10000);
        setInterval(updateDebugPanel, 5000);
    </script>
@endsection
