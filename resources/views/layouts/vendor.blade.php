<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vendor Dashboard - Hill Staytion</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #49a68c;
            --primary-light: #f0fdfa;
            --primary-hover: #3d8e78;
            --secondary: #f59e0b;
            --danger: #ef4444;
            --bg-body: #f8fafc;
            --bg-sidebar: #064e3b;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --side-text: #ccfbf1;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        aside {
            width: 280px;
            background: var(--bg-sidebar);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 50;
            color: var(--side-text);
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.1);
        }

        .logo {
            padding: 2.5rem 1.5rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .logo span {
            color: var(--side-text);
            font-weight: 300;
        }

        nav {
            flex: 1;
            padding: 0 1.25rem;
        }

        nav ul {
            list-style: none;
        }

        nav a {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.875rem 1.25rem;
            color: var(--side-text);
            text-decoration: none;
            border-radius: 1rem;
            transition: all 0.3s;
            margin-bottom: 0.5rem;
            font-weight: 500;
            position: relative;
        }

        nav a:hover,
        nav a.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        nav a.active {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .unread-badge {
            background: #ef4444;
            color: white;
            border-radius: 9999px;
            padding: 0.125rem 0.5rem;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: auto;
        }

        main {
            flex: 1;
            margin-left: 280px;
            padding: 2.5rem;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        .user-pill {
            background: white;
            padding: 0.625rem 1.25rem;
            border-radius: 1rem;
            border: 1px solid var(--border);
            font-weight: 600;
            font-size: 0.875rem;
            box-shadow: var(--shadow);
        }

        /* Common Components */
        .card {
            background: white;
            border-radius: 1.25rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .card-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: none;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(73, 166, 140, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        /* Table & Badges */
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th { text-align: left; padding: 1rem; font-size: 0.875rem; color: var(--text-muted); font-weight: 600; border-bottom: 2px solid var(--border); }
        td { padding: 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tr:hover td { background: #f8fafc; }
        
        .badge { padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; display: inline-flex; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-pending { background: #fef9c3; color: #854d0e; }
        .badge-info { background: #e0f2fe; color: #075985; }
    </style>
    @yield('styles')
</head>

<body>
    <aside>
        <a href="#" class="logo">HILL STAYTION<span>™</span></a>
        <nav>
            <ul>
                <li><a href="{{ route('vendor.dashboard') }}"
                        class="{{ request()->is('vendor/dashboard') ? 'active' : '' }}">📊 Overview</a></li>
                <li><a href="{{ route('vendor.properties') }}"
                        class="{{ request()->is('vendor/properties*') ? 'active' : '' }}">🏠 My Properties</a></li>
                <li><a href="{{ route('vendor.coupons.index') }}"
                        class="{{ request()->is('vendor/coupons*') ? 'active' : '' }}">🎫 Coupons</a></li>
                <li><a href="{{ route('vendor.withdrawals.index') }}"
                        class="{{ request()->is('vendor/withdrawals*') ? 'active' : '' }}">💰 Withdrawals</a></li>
                <li><a href="{{ route('vendor.bookings.index') }}"
                        class="{{ request()->is('vendor/bookings*') ? 'active' : '' }}">📅 Bookings</a></li>
                <li>
                    <a href="{{ route('vendor.chat') }}" class="{{ request()->is('vendor/chat*') ? 'active' : '' }}">
                        💬 Chat
                        <span id="vendorUnreadBadge" class="unread-badge" style="display: none;"></span>
                    </a>
                </li>
                <li><a href="#">⚙️ Settings</a></li>
            </ul>
        </nav>
        <div style="padding: 1.5rem;">
            <form action="{{ route('vendor.logout') }}" method="POST">
                @csrf
                <button type="submit"
                    style="width:100%; justify-content:center; display:flex; align-items:center; gap:0.5rem;"
                    class="btn btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <main>
        <header>
            <h1>@yield('header', 'Vendor Dashboard')</h1>
            <div class="user-pill">
                {{ Auth::user()->name }}
            </div>
        </header>
        @yield('content')
    </main>

    <!-- Echo and WebSocket Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.3.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

    <script>
        // Initialize Echo for WebSocket
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ env('REVERB_APP_KEY') }}',
            wsHost: window.location.hostname,
            wsPort: {{ env('REVERB_PORT', 8080) }},
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        });

        console.log('✅ Vendor Echo initialized');

        // Monitor connection
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            window.Echo.connector.pusher.connection.bind('connected', () => {
                console.log('✅ Vendor WebSocket connected');
            });

            window.Echo.connector.pusher.connection.bind('disconnected', () => {
                console.log('❌ Vendor WebSocket disconnected');
            });
        }

        // Update unread badge
        function updateUnreadCount() {
            fetch('/vendor/chat/unread-count', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    const badge = document.getElementById('vendorUnreadBadge');
                    if (data.count > 0) {
                        badge.style.display = 'inline-block';
                        badge.textContent = data.count > 9 ? '9+' : data.count;
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(err => console.error('Error fetching unread count:', err));
        }

        // Update every 10 seconds
        setInterval(updateUnreadCount, 10000);
        updateUnreadCount();
    </script>

    @yield('scripts')
</body>

</html>
