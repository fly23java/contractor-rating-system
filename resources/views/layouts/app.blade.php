<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contractor Rating</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    @auth
    <div class="app-container">
        <aside class="sidebar">
            <div style="display: flex; align-items: center; margin-bottom: 2rem;">
                <div style="width: 40px; height: 40px; background: var(--primary); border-radius: 8px; display: grid; place-items: center; color: white; margin-right: 12px; font-weight: bold;">CR</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: var(--text-main);">Contractor<br><span style="color: var(--primary);">Rating</span></div>
            </div>
            
            <nav style="flex: 1;">
                <a href="{{ route('dashboard') }}" class="btn" style="justify-content: flex-start; width: 100%; background: transparent; color: var(--text-main); margin-bottom: 0.5rem; text-align: left;">Dashboard</a>
                
                @if(auth()->user()->role == 'owner')
                    <a href="{{ route('tenders.create') }}" class="btn" style="justify-content: flex-start; width: 100%; background: transparent; color: var(--text-main); text-align: left;">+ New Tender</a>
                @endif

                @if(auth()->user()->role == 'contractor')
                    <a href="{{ route('contractor.documents') }}" class="btn" style="justify-content: flex-start; width: 100%; background: transparent; color: var(--text-main); text-align: left;">📎 My Documents</a>
                @endif
            </nav>

            <div style="border-top: 1px solid var(--border); padding-top: 1rem; margin-top: 1rem;">
                <div style="font-size: 0.875rem; margin-bottom: 0.5rem; font-weight: 600;">{{ auth()->user()->name }}</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 1rem; text-transform: uppercase;">{{ auth()->user()->role }}</div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn" style="width: 100%; justify-content: flex-start; background: transparent; color: #ef4444; padding-left: 0;">Sign Out</button>
                </form>
            </div>
        </aside>

        <main class="main-content">
            @if(session('success'))
                <div class="card" style="background: #ecfdf5; border: 1px solid #d1fae5; color: #065f46; padding: 1rem; animation: slideIn 0.3s ease-out;">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                 <div class="card" style="background: #fef2f2; border: 1px solid #fee2e2; color: #991b1b; padding: 1rem; animation: slideIn 0.3s ease-out;">
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
    @else
        <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
            @yield('content')
        </div>
    @endauth
</body>
</html>
