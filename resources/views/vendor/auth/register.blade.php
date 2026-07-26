<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Registration - PropPortal</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0d9488;
            --primary-hover: #0f766e;
            --bg-body: #f8fafc;
            --border: #e2e8f0;
            --text-main: #0f172a;
        }

        body {
            margin: 0;
            padding: 2rem 0;
            font-family: 'Outfit', sans-serif;
            background: var(--bg-body);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .register-card {
            background: white;
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 100%;
            max-width: 800px;
            border: 1px solid var(--border);
        }

        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .register-header p {
            color: #64748b;
            font-size: 0.875rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .full-width {
            grid-column: span 2;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.2s;
            outline: none;
            box-sizing: border-box;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1);
        }

        .btn-register {
            width: 100%;
            padding: 1rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 1rem;
        }

        .btn-register:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .error-message {
            background: #fef2f2;
            color: #dc2626;
            padding: 0.75rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            border: 1px solid #fee2e2;
        }

        .success-message {
            background: #f0fdf4;
            color: #166534;
            padding: 0.75rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            border: 1px solid #dcfce7;
        }

        .required {
            color: #ef4444;
        }

        @media (max-width: 640px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            .full-width {
                grid-column: span 1;
            }
            body {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-header">
            <h1>Join as a Vendor</h1>
            <p>Start managing your properties and bookings today.</p>
        </div>

        @if($errors->any())
            <div class="error-message">
                <ul style="margin:0; padding-left:1.5rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('vendor.register.post') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div style="font-size: 1.125rem; font-weight: 700; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; margin-bottom: 1.5rem; color: var(--primary);">Personal Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Full Name <span class="required">*</span></label>
                    <input type="text" name="name" class="form-input" placeholder="e.g. John Doe" required value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address <span class="required">*</span></label>
                    <input type="email" name="email" class="form-input" placeholder="e.g. john@example.com" required value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number <span class="required">*</span></label>
                    <input type="text" name="phone" class="form-input" placeholder="e.g. +91 9876543210" required value="{{ old('phone') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Password <span class="required">*</span></label>
                    <input type="password" name="password" class="form-input" placeholder="Min. 8 characters" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" class="form-input" placeholder="Repeat password" required>
                </div>
            </div>

            <div style="font-size: 1.125rem; font-weight: 700; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; margin: 2rem 0 1.5rem; color: var(--primary);">Business Details</div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Business/Brand Name <span class="required">*</span></label>
                    <input type="text" name="business_name" class="form-input" placeholder="e.g. Sun-n-Sand Villas" required value="{{ old('business_name') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Brand Logo</label>
                    <input type="file" name="brand_logo" class="form-input" accept="image/*">
                </div>
                <div class="form-group">
                    <label class="form-label">City <span class="required">*</span></label>
                    <input type="text" name="city" class="form-input" placeholder="e.g. Mumbai" required value="{{ old('city') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">State <span class="required">*</span></label>
                    <input type="text" name="state" class="form-input" placeholder="e.g. Maharashtra" required value="{{ old('state') }}">
                </div>
                <div class="form-group full-width">
                    <label class="form-label">Full Office Address <span class="required">*</span></label>
                    <textarea name="address" class="form-input" rows="3" placeholder="Street, Area, Pincode" required>{{ old('address') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn-register">Register as Vendor</button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center; color: #64748b; font-size: 0.875rem;">
            Already have an account? <a href="{{ route('vendor.login') }}" style="color: var(--primary); text-decoration: none; font-weight: 600;">Sign In</a>
        </div>

        <div style="margin-top: 1.5rem; text-align: center; border-top: 1px solid var(--border); padding-top: 1.5rem;">
            <a href="/" style="color: #64748b; font-size: 0.875rem; text-decoration: none; font-weight: 500;">← Back to Website</a>
        </div>
    </div>
</body>
</html>
