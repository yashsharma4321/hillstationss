@extends('layouts.admin')

@section('header', 'Platform Settings')

@section('styles')
<style>
    /* Premium Modern Form Design */
    :root {
        --color-input-bg: #f8fafc;
        --color-input-border: #e2e8f0;
        --color-input-focus: #49a68c;
        --color-input-focus-rgba: rgba(73, 166, 140, 0.2);
    }
    
    .settings-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    @media (min-width: 1024px) {
        .settings-layout {
            grid-template-columns: 3fr 1fr;
        }
    }
    
    .settings-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    @media(min-width: 768px) {
        .settings-grid {
            grid-template-columns: 1fr 1fr;
        }
    }
    
    .settings-grid-full {
        grid-column: 1 / -1;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.5rem;
        letter-spacing: 0.01em;
    }

    .form-control {
        width: 100%;
        padding: 0.875rem 1rem;
        background-color: var(--color-input-bg);
        border: 1px solid var(--color-input-border);
        border-radius: 0.75rem;
        font-size: 0.9375rem;
        color: var(--text-main);
        transition: all 0.25s ease;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) inset;
    }

    .form-control:hover {
        border-color: #cbd5e1;
    }

    .form-control:focus {
        border-color: var(--color-input-focus);
        background-color: #ffffff;
        outline: none;
        box-shadow: 0 0 0 4px var(--color-input-focus-rgba);
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
        line-height: 1.5;
    }
    
    .textarea-code {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
        font-size: 0.8125rem;
        background: #0f172a;
        color: #e2e8f0;
        border: 1px solid #1e293b;
    }

    .textarea-code:focus {
        background: #0f172a;
        border-color: var(--color-input-focus);
        color: #f8fafc;
    }

    .card-body {
        padding: 2.5rem;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f5f9;
        grid-column: 1 / -1;
    }

    .section-icon {
        width: 32px;
        height: 32px;
        background: #f0fdfa;
        color: var(--primary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    
    .section-desc {
        font-size: 0.8125rem;
        color: #64748b;
        margin-top: 0.25rem;
        font-weight: 400;
    }

    .media-card {
        background: #ffffff;
        border: 1px dashed #cbd5e1;
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.2s;
        margin-bottom: 1rem;
    }
    
    .media-card:hover {
        border-color: var(--primary);
        background: #f8fafc;
    }

    .action-bar {
        position: sticky;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        padding: 1.5rem 2rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        z-index: 10;
        margin: 2rem -2rem -2rem -2rem;
    }

    .btn-save {
        padding: 0.875rem 2rem;
        border-radius: 0.75rem;
        font-size: 0.9375rem;
        letter-spacing: 0.025em;
        text-transform: uppercase;
        box-shadow: 0 4px 12px rgba(73, 166, 140, 0.25);
    }
</style>
@endsection

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="settings-layout">
        <div class="main-column">
            <!-- Branding Section -->
            <div class="content-card" style="margin-bottom: 2rem;">
                <div class="card-body settings-grid">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                        </div>
                        <div>
                            <h3 class="section-title">Branding Identity</h3>
                            <div class="section-desc">Manage logo and branding elements across the portal</div>
                        </div>
                    </div>

                    <!-- Platform Logo -->
                    <div class="form-group settings-grid-full">
                        <label for="logo" class="form-label">Platform Logo</label>
                        <div class="media-card">
                            @if(!empty($settings['logo']))
                                <img src="{{ Storage::url($settings['logo']) }}" alt="Logo" style="height: 60px; object-fit: contain; margin-bottom: 1rem;">
                            @else
                                <div style="display:inline-block; padding: 1rem; background:#f1f5f9; border-radius:8px; margin-bottom: 1rem;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                </div>
                            @endif
                            <input type="file" name="logo" id="logo" class="form-control" accept="image/*" style="background:white;">
                        </div>
                    </div>

                    <!-- White Logo - NEW FIELD ADDED -->
                    <div class="form-group settings-grid-full">
                        <label for="white_logo" class="form-label">White Logo (For Dark Backgrounds)</label>
                        <div class="media-card">
                            @if(!empty($settings['white_logo']))
                                <img src="{{ Storage::url($settings['white_logo']) }}" alt="White Logo" style="height: 60px; object-fit: contain; margin-bottom: 1rem; background: #1e293b; padding: 8px; border-radius: 4px;">
                            @else
                                <div style="display:inline-block; padding: 1rem; background:#1e293b; border-radius:8px; margin-bottom: 1rem;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                </div>
                            @endif
                            <input type="file" name="white_logo" id="white_logo" class="form-control" accept="image/*" style="background:white;">
                        </div>
                    </div>

                    <!-- Favicon -->
                    <div class="form-group settings-grid-full">
                        <label for="favicon" class="form-label">Favicon (Browser Tab Icon)</label>
                        <div class="media-card" style="padding: 1rem;">
                            @if(!empty($settings['favicon']))
                                <img src="{{ Storage::url($settings['favicon']) }}" alt="Favicon" style="height: 32px; width: 32px; margin-bottom: 0.5rem; border-radius: 4px;">
                            @endif
                            <input type="file" name="favicon" id="favicon" class="form-control" accept="image/*" style="background:white; padding: 0.5rem;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="content-card" style="margin-bottom: 2rem;">
                <div class="card-body settings-grid">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <div>
                            <h3 class="section-title">Contact & Addresses</h3>
                            <div class="section-desc">Public contact details displayed on the frontend</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ $settings['email'] ?? '' }}" placeholder="info@example.com">
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ $settings['phone'] ?? '' }}" placeholder="+1 234 567 8900">
                    </div>
                    
                    <div class="form-group settings-grid-full">
                        <label for="address" class="form-label">Physical Address</label>
                        <textarea name="address" id="address" class="form-control" placeholder="123 Example St, City, Country">{{ $settings['address'] ?? '' }}</textarea>
                    </div>

                    <div class="form-group settings-grid-full">
                        <label for="footer_short_content" class="form-label">Footer Short Content</label>
                        <textarea name="footer_short_content" id="footer_short_content" class="form-control" placeholder="A brief description of your company to display in the website footer.">{{ $settings['footer_short_content'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Developer Scripts -->
            <div class="content-card" style="margin-bottom: 2rem;">
                <div class="card-body settings-grid">
                    <div class="section-header">
                        <div class="section-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                        </div>
                        <div>
                            <h3 class="section-title">Developer Scripts</h3>
                            <div class="section-desc">Add Analytics, Facebook Pixel, or custom scripts globally.</div>
                        </div>
                    </div>
                    
                    <div class="form-group settings-grid-full">
                        <label for="header_script" class="form-label">&lt;head&gt; Scripts</label>
                        <textarea name="header_script" id="header_script" class="form-control textarea-code" placeholder="<!-- Google Analytics -->...">{{ $settings['header_script'] ?? '' }}</textarea>
                    </div>

                    <div class="form-group settings-grid-full">
                        <label for="footer_script" class="form-label">Before &lt;/body&gt; Scripts</label>
                        <textarea name="footer_script" id="footer_script" class="form-control textarea-code" placeholder="<script src='...'></script>">{{ $settings['footer_script'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="sidebar-column">
            <!-- Platform Setting Sidebar Component -->
            <div class="content-card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <div class="section-header" style="margin-bottom: 1.5rem; padding-bottom: 0.5rem;">
                        <div>
                            <h3 class="section-title">Core Setup</h3>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="commission_percentage" class="form-label">Platform Commission (%)</label>
                        <div style="position: relative;">
                            <input type="number" step="0.01" name="commission_percentage" id="commission_percentage" class="form-control" value="{{ $settings['commission_percentage'] ?? '' }}" placeholder="10.00" style="padding-right: 2.5rem;">
                            <span style="position: absolute; right: 1rem; top: 0.8rem; color: #94a3b8; font-weight:600;">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Links Panel -->
            <div class="content-card" style="margin-bottom: 2rem;">
                <div class="card-body">
                    <div class="section-header" style="margin-bottom: 1.5rem; padding-bottom: 0.5rem;">
                        <div>
                            <h3 class="section-title">Social Media</h3>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="facebook" class="form-label">Facebook</label>
                        <input type="url" name="facebook" id="facebook" class="form-control" value="{{ $settings['facebook'] ?? '' }}" placeholder="https://facebook.com/yourpage">
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="instagram" class="form-label">Instagram</label>
                        <input type="url" name="instagram" id="instagram" class="form-control" value="{{ $settings['instagram'] ?? '' }}" placeholder="https://instagram.com/yourpage">
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="youtube" class="form-label">YouTube</label>
                        <input type="url" name="youtube" id="youtube" class="form-control" value="{{ $settings['youtube'] ?? '' }}" placeholder="https://youtube.com/yourchannel">
                    </div>

                    <div class="form-group">
                        <label for="twitter" class="form-label">Twitter / X</label>
                        <input type="url" name="twitter" id="twitter" class="form-control" value="{{ $settings['twitter'] ?? '' }}" placeholder="https://twitter.com/yourpage">
                    </div>
                </div>
            </div>

            <!-- Gateway Setup -->
            <div class="content-card">
                <div class="card-body">
                    <div class="section-header" style="margin-bottom: 1.5rem; padding-bottom: 0.5rem;">
                        <div>
                            <h3 class="section-title">Gateways</h3>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label for="razorpay_key" class="form-label">Razorpay Key</label>
                        <input type="text" name="razorpay_key" id="razorpay_key" class="form-control" value="{{ $settings['razorpay_key'] ?? '' }}" placeholder="rzp_test_xxxxxx">
                    </div>

                    <div class="form-group">
                        <label for="razorpay_secret" class="form-label">Razorpay Secret</label>
                        <input type="password" name="razorpay_secret" id="razorpay_secret" class="form-control" value="{{ $settings['razorpay_secret'] ?? '' }}" placeholder="••••••••••••••">
                    </div>
                </div>
            </div>
            
            <div class="action-bar" style="background: transparent; border: none; padding: 0; margin: 2rem 0 0 0; position: static;">
                <button type="submit" class="btn btn-primary btn-save" style="width: 100%; justify-content: center;">Save All Changes</button>
            </div>
        </div>
    </div>
</form>
@endsection