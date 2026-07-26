<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title ?? 'HillStaytion' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #1e293b;
            --accent: #f59e0b;
            --background: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --glass: rgba(255, 255, 255, 0.8);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; color: var(--text-main); line-height: 1.6; background-color: #fcfcfd; overflow-x: hidden; }

        .container { max-width: 1280px; margin: 0 auto; padding: 0 2rem; }

        /* Header */
        header { 
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000; 
            background: var(--glass); backdrop-filter: blur(12px); 
            border-bottom: 1px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
        }
        .header-inner { display: flex; justify-content: space-between; align-items: center; height: 80px; }
        .logo { font-size: 1.5rem; font-weight: 800; color: var(--primary); text-decoration: none; display: flex; align-items: center; gap: 0.5rem; }
        .nav-links { display: flex; gap: 2.5rem; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--text-main); font-weight: 500; font-size: 0.95rem; transition: color 0.2s; }
        .nav-links a:hover { color: var(--primary); }
        .btn-primary { 
            background: var(--primary); color: white; padding: 0.75rem 1.5rem; 
            border-radius: 99px; text-decoration: none; font-weight: 600; 
            font-size: 0.9rem; transition: all 0.3s; 
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-2px); }

        /* Sections General */
        section { padding: 5rem 0; }
        .section-header { text-align: center; margin-bottom: 4rem; }
        .section-title { font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--secondary); margin-bottom: 1rem; }
        .section-subtitle { color: var(--text-muted); font-size: 1.125rem; max-width: 600px; margin: 0 auto; }

        /* Tabs System */
        .tabs-header { display: flex; justify-content: center; gap: 1rem; margin-bottom: 3rem; }
        .tab-btn { 
            padding: 0.6rem 1.5rem; border: 1px solid var(--border); background: white; 
            border-radius: 99px; cursor: pointer; font-weight: 500; font-size: 0.9rem;
            transition: all 0.3s;
        }
        .tab-btn.active { background: var(--primary); color: white; border-color: var(--primary); }

        /* Property Card */
        .property-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 2rem; }
        .property-card { 
            background: white; border-radius: 1.5rem; overflow: hidden; 
            border: 1px solid var(--border); transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .property-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
        .property-img { position: relative; height: 260px; overflow: hidden; }
        .property-img img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s; }
        .property-card:hover .property-img img { transform: scale(1.1); }
        .property-badge { position: absolute; top: 1rem; left: 1rem; background: var(--primary); color: white; padding: 0.4rem 1rem; border-radius: 99px; font-size: 0.75rem; font-weight: 600; }
        .property-content { padding: 1.5rem; }
        .property-meta { display: flex; gap: 1rem; color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem; }
        .property-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--secondary); }
        .property-price { display: flex; flex-direction: column; }
        .price-val { font-size: 1.5rem; font-weight: 800; color: var(--primary); }
        .price-label { font-size: 0.75rem; color: var(--text-muted); }

        /* Stats Grid */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem; }
        .stat-item { text-align: center; padding: 2rem; background: #f8fafc; border-radius: 1.5rem; border: 1px solid var(--border); }
        .stat-val { font-size: 2.5rem; font-weight: 800; color: var(--secondary); display: block; margin-bottom: 0.5rem; }
        .stat-label { color: var(--text-muted); font-weight: 500; }

        /* Footer */
        footer { background: var(--secondary); color: white; padding: 4rem 0; margin-top: 5rem; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 4rem; }
        .footer-brand p { margin-top: 1.5rem; color: #94a3b8; max-width: 300px; }
        .footer-links h4 { font-size: 1.125rem; margin-bottom: 1.5rem; }
        .footer-links ul { list-style: none; }
        .footer-links li { margin-bottom: 0.75rem; }
        .footer-links a { color: #94a3b8; text-decoration: none; transition: color 0.2s; }
        .footer-links a:hover { color: white; }

        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .property-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
    @yield('styles')
</head>
<body>
    <header id="main-header">
        <div class="container header-inner">
            <a href="/" class="logo">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                HillStaytion
            </a>
            <ul class="nav-links">
                <li><a href="/">Home</a></li>
                <li><a href="/destinations">Destinations</a></li>
                <li><a href="/villas">Villas</a></li>
                <li><a href="/about">About Us</a></li>
                <li><a href="/contact">Contact</a></li>
            </ul>
            <div class="header-actions">
                <a href="{{ route('login') }}" class="btn-primary">Become a Host</a>
            </div>
        </div>
    </header>

    <main style="padding-top: 80px;">
        @yield('content')
    </main>

    <footer>
        <div class="container footer-grid">
            <div class="footer-brand">
                <a href="/" class="logo" style="color: white;">HillStaytion</a>
                <p>Curating the finest villa stays for your ultimate comfort and luxury. Your dream staycation starts here.</p>
            </div>
            <div class="footer-links">
                <h4>Explore</h4>
                <ul>
                    <li><a href="#">Featured Villas</a></li>
                    <li><a href="#">Best Rates</a></li>
                    <li><a href="#">Latest Offers</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Company</h4>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Support</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Social</h4>
                <ul>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">LinkedIn</a></li>
                </ul>
            </div>
        </div>
        <div class="container" style="border-top: 1px solid #334155; margin-top: 3rem; padding-top: 2rem; text-align: center; color: #64748b; font-size: 0.875rem;">
            &copy; {{ date('Y') }} HillStaytion. All rights reserved.
        </div>
    </footer>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
        window.addEventListener('scroll', () => {
            const header = document.getElementById('main-header');
            if (window.scrollY > 20) {
                header.style.boxShadow = '0 10px 30px rgba(0,0,0,0.05)';
            } else {
                header.style.boxShadow = 'none';
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
