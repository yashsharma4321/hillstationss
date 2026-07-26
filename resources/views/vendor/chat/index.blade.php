@extends('layouts.vendor')

@section('header', 'Customer Chat')

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

        .customers-list {
            width: 320px;
            border-right: 1px solid #e5e7eb;
            background: #f9fafb;
            overflow-y: auto;
        }

        .customer-item {
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 1px solid #e5e7eb;
        }

        .customer-item:hover {
            background: #f3f4f6;
        }

        .customer-item.active {
            background: #10b981;
            color: white;
        }

        .customer-item.active .last-message {
            color: rgba(255, 255, 255, 0.8);
        }

        .customer-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.125rem;
            margin-right: 0.75rem;
        }

        .customer-info {
            display: inline-block;
            vertical-align: middle;
        }

        .customer-name {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .customer-role {
            font-size: 0.625rem;
            background: #e5e7eb;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            margin-left: 0.5rem;
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
            background: #10b981;
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
            border-color: #10b981;
        }

        .send-btn {
            padding: 0.75rem 1.5rem;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s;
        }

        .send-btn:hover {
            background: #059669;
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
            border-top-color: #10b981;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .no-customers {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
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
        <!-- Customers List (Only those who booked vendor's properties) -->
        <div class="customers-list" id="customersList">
            <div class="text-center p-4">
                <div class="loading-spinner"></div>
                <p class="mt-2 text-sm text-gray-500">Loading customers...</p>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area" id="chatArea">
            <div class="empty-chat">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.5">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    <path d="M8 10h.01M12 10h.01M16 10h.01" />
                </svg>
                <h3 class="text-lg font-semibold mb-2">No conversation selected</h3>
                <p class="text-sm">Select a customer to start chatting</p>
            </div>
        </div>
    </div>

    <div class="debug-panel" id="debugPanel">
        <strong>🔍 Vendor Debug</strong><br>
        Echo: <span id="debugEcho">Checking...</span><br>
        Connection: <span id="debugConn">-</span><br>
        <span id="debugMessage"></span>
    </div>
@endsection

@section('scripts')
    <script>
        let currentCustomerId = null;
        let currentCustomerName = null;
        let echoChannel = null;
        const vendorId = {{ auth()->id() ?? 0 }};

        function getCsrfToken() {
            const token = document.querySelector('meta[name="csrf-token"]');
            return token ? token.content : null;
        }

        function loadCustomers() {
            fetch('/vendor/chat/customers', {
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(customers => {
                    const customersList = document.getElementById('customersList');

                    if (!customers || customers.length === 0) {
                        customersList.innerHTML = `
                    <div class="no-customers">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <h4 style="margin-top: 1rem;">No customers yet</h4>
                        <p style="font-size: 0.875rem;">When customers book your properties, they'll appear here.</p>
                    </div>
                `;
                        return;
                    }

                    customersList.innerHTML = customers.map(customer => `
                <div class="customer-item ${currentCustomerId === customer.id ? 'active' : ''}" 
                     onclick="selectCustomer(${customer.id}, '${escapeHtml(customer.name)}')">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center;">
                            <div class="customer-avatar">
                                ${customer.name.charAt(0).toUpperCase()}
                            </div>
                            <div class="customer-info">
                                <div class="customer-name">
                                    ${escapeHtml(customer.name)}
                                    <span class="customer-role">${customer.role === 'admin' ? 'Admin' : 'Customer'}</span>
                                </div>
                                <div class="last-message">${escapeHtml(customer.last_message) || 'No messages yet'}</div>
                            </div>
                        </div>
                        ${customer.unread_count > 0 ? `<span class="unread-badge">${customer.unread_count}</span>` : ''}
                    </div>
                    <div class="last-message" style="margin-left: 60px; font-size: 0.625rem;">
                        ${customer.last_message_time || ''}
                    </div>
                </div>
            `).join('');
                })
                .catch(error => {
                    console.error('Error loading customers:', error);
                    document.getElementById('customersList').innerHTML =
                        '<div class="text-center p-4 text-red-500">Error loading customers</div>';
                });
        }

        function selectCustomer(customerId, customerName) {
            currentCustomerId = customerId;
            currentCustomerName = customerName;
            loadCustomers();
            loadMessages(customerId);
            setupWebSocketListener();
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

        function setupWebSocketListener() {
            if (typeof window.Echo !== 'undefined' && window.Echo) {
                if (!echoChannel) {
                    console.log(`Setting up WebSocket listener on chat.${vendorId}`);
                    echoChannel = window.Echo.private(`chat.${vendorId}`);
                    echoChannel.listen('.message.sent', (e) => {
                        console.log("WebSocket event received: ", e);
                        // If the message is from the currently selected user, append it
                        if (currentCustomerId == e.sender_id) {
                            appendMessage(e, 'received');
                        }
                        // Always reload customers list to update unread counts and last message
                        loadCustomers();
                    });
                    // Also listen for typing events
                    echoChannel.listen('.user.typing', (e) => {
                        if (currentCustomerId == e.sender_id) {
                            showTypingIndicator();
                        }
                    });
                }
            }
        }

        function loadMessages(customerId) {
            fetch(`/vendor/chat/messages/${customerId}`, {
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(messages => {
                    const chatArea = document.getElementById('chatArea');
                    chatArea.innerHTML = `
                <div class="chat-header">
                    <h3 style="font-weight: 600;">Chat with ${escapeHtml(currentCustomerName)}</h3>
                </div>
                <div class="messages-container" id="messagesContainer">
                    ${messages.map(msg => `
                            <div class="message ${msg.sender_id == vendorId ? 'sent' : 'received'}">
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
                });
        }

        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput?.value.trim();

            if (!message || !currentCustomerId) return;

            fetch('/vendor/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        receiver_id: currentCustomerId,
                        message: message
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageInput.value = '';
                        appendMessage(data.message, 'sent');
                        loadCustomers();
                    } else {
                        alert(data.error || 'Failed to send message');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error sending message');
                });
        }

        function appendMessage(message, direction) {
            const container = document.getElementById('messagesContainer');
            if (!container) return;

            const isSent = direction === 'sent' || message.sender_id === vendorId;
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

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Typing indicator logic
        let typingTimer = null;
        let typingTimeout = null;

        function sendTypingEvent() {
            if (!currentCustomerId) return;
            fetch('/vendor/chat/typing', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ receiver_id: currentCustomerId })
            }).catch(() => {});
        }

        function handleTypingInput() {
            clearTimeout(typingTimer);
            sendTypingEvent();
            typingTimer = setTimeout(() => {}, 2000);
        }

        function setupTypingListener() {
            if (echoChannel) {
                echoChannel.listen('.user.typing', (e) => {
                    if (currentCustomerId == e.sender_id) {
                        showTypingIndicator();
                    }
                });
            }
        }

        function showTypingIndicator() {
            const indicator = document.getElementById('typingIndicator');
            const nameSpan = document.getElementById('typingName');
            if (indicator) {
                if (nameSpan) nameSpan.textContent = currentCustomerName || 'User';
                indicator.classList.add('show');
                clearTimeout(typingTimeout);
                typingTimeout = setTimeout(() => {
                    indicator.classList.remove('show');
                }, 2500);
            }
        }

        // Initialize
        setTimeout(updateDebugPanel, 1000);
        loadCustomers();
        setupWebSocketListener();
        setTimeout(setupTypingListener, 1500);
        setInterval(loadCustomers, 10000);
        setInterval(updateDebugPanel, 5000);
    </script>
@endsection
