<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - PropPortal</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #49a68c;
            --primary-hover: #3d8e78;
            --bg-gradient: linear-gradient(135deg, #064e3b 0%, #022c22 100%);
            --glass-bg: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
            --text-main: #f0fdfa;
            --text-muted: #94a3b8;
        }

        body {
            background: var(--bg-gradient);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            margin: 0;
            overflow: hidden;
            position: relative;
        }

        /* Decorative blobs */
        body::before,
        body::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            filter: blur(100px);
            z-index: -1;
        }

        body::before {
            background: rgba(73, 166, 140, 0.2);
            top: 20%;
            left: 20%;
        }

        body::after {
            background: rgba(45, 212, 191, 0.2);
            bottom: 20%;
            right: 20%;
        }

        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 3rem;
            border-radius: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 420px;
            border: 1px solid var(--glass-border);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-container {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo-img {
            height: 48px;
            width: auto;
            margin-bottom: 1rem;
            filter: drop-shadow(0 0 8px rgba(99, 102, 241, 0.4));
        }

        .logo-text {
            font-size: 1.75rem;
            font-weight: 700;
            background: linear-gradient(to right, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: block;
            letter-spacing: -0.025em;
        }

        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        p {
            color: var(--text-muted);
            font-size: 0.9375rem;
            text-align: center;
            margin-bottom: 2.5rem;
            font-weight: 300;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-main);
            margin-bottom: 0.6rem;
            margin-left: 0.25rem;
        }

        .input-wrapper {
            position: relative;
        }

        input {
            width: 100%;
            padding: 0.875rem 1.25rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 1rem;
            outline: none;
            color: white;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-sizing: border-box;
        }

        input:focus {
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        button {
            width: 100%;
            padding: 1rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 1rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }

        button:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        .error {
            color: #f87171;
            font-size: 0.8125rem;
            margin-top: 0.5rem;
            padding-left: 0.25rem;
        }

        /* Subtle micro-animations */
        .form-group:focus-within label {
            color: var(--primary);
            transition: color 0.3s ease;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="logo-container">
            <img src="{{ asset('storage/logo-dark.webp') }}" alt="PropPortal Logo" class="logo-img">

        </div>

        <h1>Welcome Back</h1>
        <p>Enter your credentials to access the secure portal</p>

        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        placeholder="admin@propportal.com" autocomplete="email">
                </div>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" required placeholder="••••••••"
                        autocomplete="current-password">
                </div>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <button type="submit">Unlock Dashboard</button>
        </form>
    </div>
</body>

</html>