<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - Property Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #49a68c;
            --primary-light: #f0fdfa;
            --primary-hover: #3d8e78;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --bg-body: #f8fafc;
            --bg-sidebar: #064e3b;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --side-text: #ccfbf1;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
            padding: 3rem 2rem;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .logo img {
            max-width: 180px;
            height: auto;
            filter: brightness(0) invert(1);
            /* Make logo white for dark sidebar */
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
            letter-spacing: -0.025em;
        }

        nav {
            flex: 1;
            padding: 0 1.5rem;
            overflow-y: auto;
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
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.9375rem;
        }

        nav a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }

        nav a.active {
            background: white;
            color: var(--bg-sidebar);
            font-weight: 600;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        /* Main Content */
        main {
            flex: 1;
            margin-left: 280px;
            padding: 3rem;
            max-width: calc(100vw - 280px);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3.5rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.625rem 1.5rem;
            background: white;
            border-radius: 1.5rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.3s;
        }

        .user-profile:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
        }

        /* Dashboard Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 1.5rem;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            background: var(--primary-light);
            border-radius: 0 0 0 100%;
            opacity: 0.5;
            z-index: 0;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-lg);
        }

        .stat-content {
            position: relative;
            z-index: 1;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.9375rem;
            font-weight: 500;
            margin-bottom: 0.75rem;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.025em;
        }

        /* Tables & Content */
        .content-card {
            background: white;
            border-radius: 1.5rem;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: all 0.3s;
        }

        .card-header {
            padding: 2rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 1.25rem 2rem;
            background: #fbfcfd;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.8125rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        td {
            padding: 1.25rem 2rem;
            border-top: 1px solid var(--border);
            font-size: 0.9375rem;
            color: var(--text-main);
        }

        .badge {
            padding: 0.375rem 1rem;
            border-radius: 2rem;
            font-size: 0.8125rem;
            font-weight: 600;
        }

        .badge-pending {
            background: #fff7ed;
            color: #9a3412;
        }

        .badge-success {
            background: #f0fdf4;
            color: #166534;
        }

        /* Buttons */
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 0.875rem;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 6px rgba(73, 166, 140, 0.2);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 12px rgba(73, 166, 140, 0.3);
        }

        /* Pagination */
        .pagination {
            display: flex;
            list-style: none;
            gap: 0.5rem;
            margin-top: 3rem;
            justify-content: center;
        }

        .page-link {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: white;
            border: 1px solid var(--border);
            color: var(--text-main);
            text-decoration: none;
            transition: all 0.3s;
        }

        .page-item.active .page-link {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
            box-shadow: 0 4px 6px rgba(73, 166, 140, 0.2);
        }

        @media (max-width: 1024px) {
            aside {
                width: 90px;
            }

            .logo-text,
            nav a span {
                display: none;
            }

            .logo {
                padding: 2rem 1rem;
            }

            main {
                margin-left: 90px;
                padding: 2rem;
            }
        }

        .nav-dropdown-menu {
            display: none;
            flex-direction: column;
            gap: 0.25rem;
            padding-left: 2.75rem;
            margin-top: 0.25rem;
            margin-bottom: 0.5rem;
        }

        .nav-dropdown-menu.show {
            display: flex;
        }

        .nav-dropdown-menu a {
            padding: 0.5rem 1rem;
            font-size: 0.8125rem;
            border-radius: 0.75rem;
            margin-bottom: 0;
            color: rgba(204, 251, 241, 0.8);
        }

        .nav-dropdown-menu a:hover,
        .nav-dropdown-menu a.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            box-shadow: none;
            transform: translateX(4px);
        }

        .dropdown-toggle {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .dropdown-toggle .arrow {
            transition: transform 0.3s ease;
        }

        .dropdown-toggle.open .arrow {
            transform: rotate(90deg);
        }
    </style>
    @yield('styles')
</head>


<body>
    <aside>
        <a href="{{ route('admin.dashboard') }}" class="logo">
            <img src="{{ asset('storage/logo-dark.webp') }}" alt="Hill Staytion Logo">

        </a>
        <nav>
            <ul>
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="9" />
                            <rect x="14" y="3" width="7" height="5" />
                            <rect x="14" y="12" width="7" height="9" />
                            <rect x="3" y="16" width="7" height="5" />
                        </svg>
                        <span>Overview</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.vendors.index') }}"
                        class="{{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        <span>Vendors</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.categories.index') }}"
                        class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z" />
                        </svg>
                        <span>Categories</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.destinations.index') }}"
                        class="{{ request()->routeIs('admin.destinations.*') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <span>Destinations</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.states.index') }}"
                        class="{{ request()->routeIs('admin.states.*') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                        </svg>
                        <span>States</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.amenities.index') }}"
                        class="{{ request()->routeIs('admin.amenities.*') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 7l10-5 10 5-10 5z" />
                            <path d="M2 12l10 5 10-5" />
                            <path d="M2 17l10 5 10-5" />
                        </svg>
                        <span>Amenities</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.bookings.index') }}"
                        class="{{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                            <line x1="16" y1="2" x2="16" y2="6" />
                            <line x1="8" y1="2" x2="8" y2="6" />
                            <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <span>Bookings</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.chat') }}"
                        class="{{ request()->routeIs('admin.chat*') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            <path d="M8 10h.01M12 10h.01M16 10h.01" />
                        </svg>
                        <span>Chat</span>
                        <span id="sidebarUnreadBadge"
                            style="margin-left: auto; background: #ef4444; color: white; border-radius: 9999px; padding: 0.125rem 0.5rem; font-size: 0.75rem; display: none;"></span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.properties.index') }}"
                        class="{{ request()->routeIs('admin.properties.*') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                            <polyline points="9 22 9 12 15 12 15 22" />
                        </svg>
                        <span>Properties</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.blogs.index') }}"
                        class="{{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                        </svg>
                        <span>Blogs</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pages.index') }}"
                        class="{{ request()->routeIs('admin.pages.*') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                        <span>Pages</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.collections.index') }}"
                        class="{{ request()->routeIs('admin.collections.*') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                            <path d="M7 3v18M17 3v18M3 7h18M3 17h18" />
                        </svg>
                        <span>Collections</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.coupons.index') }}"
                        class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 5.5l7 7-7 7M9 5.5l-7 7 7 7" />
                            <path d="M12 2v20" />
                        </svg>
                        <span>Coupons</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.withdrawals.index') }}"
                        class="{{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                        </svg>
                        <span>Withdrawals</span>
                    </a>
                </li>
                <li class="nav-dropdown">
                    <a class="dropdown-toggle {{ request()->routeIs('admin.accounting.*') ? 'active open' : '' }}"
                        onclick="this.classList.toggle('open'); this.nextElementSibling.classList.toggle('show');">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2" />
                                <path d="M7 15h0M2 9.5h20" />
                            </svg>
                            <span>Accounting</span>
                        </div>
                        <svg class="arrow" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                    <div class="nav-dropdown-menu {{ request()->routeIs('admin.accounting.*') ? 'show' : '' }}">
                        <a href="{{ route('admin.accounting.index') }}"
                            class="{{ request()->routeIs('admin.accounting.index') ? 'active' : '' }}">Chart of
                            Accounts</a>
                        <a href="{{ route('admin.accounting.trial_balance') }}"
                            class="{{ request()->routeIs('admin.accounting.trial_balance') ? 'active' : '' }}">Trial
                            Balance (CA)</a>
                        <a href="{{ route('admin.accounting.profit_loss') }}"
                            class="{{ request()->routeIs('admin.accounting.profit_loss') ? 'active' : '' }}">Profit &
                            Loss</a>
                    </div>
                </li>
                <li>
                    <a href="{{ route('admin.contacts.index') }}"
                        class="{{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                        <span>Inquiries</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.settings.index') }}"
                        class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3" />
                            <path
                                d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z" />
                        </svg>
                        <span>Settings</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div style="padding: 1.5rem;">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button class="btn"
                    style="width: 100%; justify-content: center; background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <main>
        <header>
            <h1 style="font-size: 1.5rem; font-weight: 700;">@yield('header', 'Dashboard')</h1>
            <div class="user-profile">
                <div class="user-avatar">A</div>
                <div style="font-size: 0.875rem; font-weight: 600;">Admin User</div>
            </div>
        </header>

        @if (session('success'))
            <div
                style="background: #ecfdf5; color: #065f46; padding: 1rem 1.5rem; border-radius: 1rem; border: 1px solid #d1fae5; margin-bottom: 2rem; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                style="background: #fef2f2; color: #991b1b; padding: 1rem 1.5rem; border-radius: 1rem; border: 1px solid #fee2e2; margin-bottom: 2rem; font-weight: 600; display: flex; align-items: center; gap: 0.75rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                {{ session('error') }}
            </div>
        @endif

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

        console.log('✅ Echo initialized with key: {{ env('REVERB_APP_KEY') }}');

        // Monitor WebSocket connection
        if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
            window.Echo.connector.pusher.connection.bind('connected', () => {
                console.log('✅ WebSocket connected to {{ env('REVERB_HOST') }}:{{ env('REVERB_PORT') }}');
            });

            window.Echo.connector.pusher.connection.bind('disconnected', () => {
                console.log('❌ WebSocket disconnected');
            });

            window.Echo.connector.pusher.connection.bind('error', (err) => {
                console.error('WebSocket error:', err);
            });
        }
    </script>

    @yield('scripts')
</body>

</html>
