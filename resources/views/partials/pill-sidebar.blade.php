{{-- =========================================================
     PILL-RAIL SIDEBAR (versi seragam untuk ketua, pembina, siswa)
     Kapsul vertikal ala theme proyek (sky/amber light), bisa dibentangkan.
     Icon = SVG asli sidebar sebelumnya (stroke 2).
     Pakai: @include('partials.pill-sidebar', [
        'psTitle'       => 'Menu Ketua',
        'psLogoBrand'   => 'SOUL',
        'psDashboardUrl'=> route(...),
        'psNotifUrl'    => route(...),
        'psProfileUrl'  => route(...),
        'psItems'       => [ ['icon','label','url','is(route-pattern)'], ... 6 item ],
        'psMore'        => [ ['label','url','is'?] ],  // menu sekunder (dropdown gear)
     ])
     Icon key tersedia: dashboard, calendar, clipboard-check, clipboard-list,
     document, bars, users, user, logout, lock, bell, columns, building,
     star, chat, help.
     ========================================================= --}}
@php
    $psIconSvg = [
        'dashboard' => '<path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/><path d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/><path d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/><path d="M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>',
        'calendar' => '<path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
        'clipboard-check' => '<path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2H9V5zm1 8l2 2 4-4"/>',
        'clipboard-list' => '<path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        'document' => '<path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>',
        'bars' => '<path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
        'users' => '<path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>',
        'user' => '<path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>',
        'logout' => '<path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>',
        'lock' => '<rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/>',
        'columns' => '<path d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>',
        'building' => '<path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
        'star' => '<path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>',
        'chat' => '<path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>',
        'help' => '<path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'bell' => '<path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>',
    ];
    $psGearSvg = '<circle cx="12" cy="12" r="3"/><path d="M12 2v2m0 16v2M2 12h2m16 0h2M4.9 4.9l1.4 1.4m11.4 11.4l1.4 1.4M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>';
    $psChevronSvg = '<path d="M15 19l-7-7 7-7"/>';
    $psLogoutSvg = '<path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>';
@endphp
@php
    $psUser = auth()->user();
    $psRole = $psTitle ? trim(str_ireplace('Menu ', '', (string) $psTitle)) : '';
    if (! $psRole && $psUser && $psUser->role) {
        $psRole = ucfirst($psUser->role);
    }
    $psNama = $psUser?->siswa?->nama ?: ($psUser?->pembina?->nama ?: ($psUser?->username ?: 'Pengguna'));
    $psSubU = $psRole;
@endphp

<style>
    .ps-rail {
        position: fixed;
        inset: 0 auto 0 0;
        z-index: 40;
        display: none;
        width: 292px;
        padding: 14px;
        align-items: stretch;
        transition: width .25s ease;
    }
    @media (min-width: 1024px) { .ps-rail { display: flex; } }
    body.ps-collapsed .ps-rail { width: 100px; }

    .ps-pill {
        position: relative;
        width: 100%;
        align-self: stretch;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 6px;
        padding: 16px 12px 14px;
        border-radius: 30px;
        background: rgba(255, 255, 255, .9);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1px solid rgba(186, 230, 253, .9);
        box-shadow:
            0 10px 40px -16px rgba(2, 132, 199, .30),
            0 2px 18px -8px rgba(14, 165, 233, .25),
            inset 0 1px 0 rgba(255, 255, 255, .8);
    }

    .ps-deco {
        position: absolute;
        inset: 0;
        border-radius: inherit;
        overflow: hidden;
        pointer-events: none;
    }

    .ps-blob { position: absolute; border-radius: 50%; filter: blur(38px); pointer-events: none; }
    .ps-blob-1 { top: -40px; right: -40px; width: 170px; height: 170px; background: rgba(186, 230, 253, .55); }
    .ps-blob-2 { bottom: 20px; left: -50px; width: 150px; height: 150px; background: rgba(254, 240, 199, .6); }

    .ps-toggle {
        position: absolute;
        top: 22px;
        right: -16px;
        z-index: 60;
        width: 26px;
        height: 26px;
        border-radius: 9999px;
        background: #fff;
        border: 1px solid #bae6fd;
        box-shadow: 0 4px 12px -2px rgba(14, 165, 233, .28);
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: color .2s ease, border-color .2s ease, box-shadow .2s ease;
    }
    .ps-toggle:hover { color: #0284c7; border-color: #7dd3fc; box-shadow: 0 4px 14px -2px rgba(14, 165, 233, .4); }
    .ps-toggle:focus-visible { outline: 2px solid rgba(56, 189, 248, .6); outline-offset: 2px; }
    .ps-toggle svg { width: 14px; height: 14px; transition: transform .25s ease; }
    body.ps-collapsed .ps-toggle svg { transform: rotate(180deg); }

    .ps-logo {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 2px 4px 8px;
        text-decoration: none;
        border-radius: 14px;
        transition: background-color .15s ease;
    }
    .ps-logo:hover { background: rgba(240, 249, 255, .7); }
    .ps-logo:focus-visible { outline: 2px solid rgba(56, 189, 248, .6); outline-offset: 2px; }
    .ps-logo-box {
        width: 42px;
        height: 42px;
        flex-shrink: 0;
        border-radius: 16px;
        background: linear-gradient(135deg, #38bdf8, #3b82f6);
        color: #fff;
        font-weight: 800;
        font-size: 13px;
        letter-spacing: .02em;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 16px -5px rgba(56, 189, 248, .65);
    }
    .ps-logo-txt { text-align: left; min-width: 0; }
    .ps-logo-txt strong { font-size: 13px; font-weight: 800; color: #0f172a; display: block; line-height: 1.2; letter-spacing: .01em; }
    .ps-logo-txt small { font-size: 10px; color: #94a3b8; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }
    body.ps-collapsed .ps-logo-txt { display: none; }

    .ps-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 0 6px 6px;
    }
    .ps-section-lbl { font-size: 10px; font-weight: 700; color: #94a3b8; letter-spacing: .09em; text-transform: uppercase; white-space: nowrap; }
    .ps-section-sub { font-size: 9px; font-weight: 600; color: #cbd5e1; letter-spacing: .07em; text-transform: uppercase; white-space: nowrap; }
    body.ps-collapsed .ps-section { display: none; }

    .ps-nav {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
    }
    .ps-nav-main { flex: 1 1 auto; min-height: 0; overflow-y: auto; overflow-x: hidden; padding: 2px 0; }
    .ps-nav-main::-webkit-scrollbar { width: 5px; }
    .ps-nav-main::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 999px; }
    .ps-nav-main::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }

    .ps-item {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        padding: 9px 14px;
        border-radius: 12px;
        font-size: 12px;
        color: #64748b;
        background: transparent;
        border: 0;
        cursor: pointer;
        text-align: left;
        text-decoration: none;
        transition: background-color .15s ease, color .15s ease, transform .15s ease, box-shadow .15s ease;
        min-width: 0;
    }
    .ps-item svg { width: 16px; height: 16px; flex-shrink: 0; }
    .ps-item .ps-lb { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ps-item:hover { background: #f0f9ff; color: #0369a1; transform: translateX(2px); }
    .ps-item:focus-visible { outline: 2px solid rgba(56, 189, 248, .6); outline-offset: 2px; }
    .ps-item.ps-active,
    .ps-item[aria-current="page"] {
        background: linear-gradient(to right, #e0f2fe, #dbeafe);
        color: #075985;
        font-weight: 600;
        box-shadow: 0 1px 2px rgba(186, 230, 253, .65);
        transform: none;
    }
    .ps-bar {
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 24px;
        border-radius: 0 9999px 9999px 0;
        background: linear-gradient(to bottom, #38bdf8, #3b82f6);
    }
    body.ps-collapsed .ps-item { justify-content: center; padding: 10px 0; }
    body.ps-collapsed .ps-item:hover { transform: none; }
    body.ps-collapsed .ps-item .ps-bar { display: none; }
    body.ps-collapsed .ps-item .ps-lb { display: none; }

    .ps-tip {
        position: absolute;
        left: calc(100% + 14px);
        top: 50%;
        transform: translate(-6px, -50%);
        background: #0f172a;
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        padding: 6px 10px;
        border-radius: 8px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity .15s ease, transform .15s ease;
        z-index: 70;
        box-shadow: 0 8px 20px rgba(15, 23, 42, .25);
    }
    .ps-tip::before {
        content: '';
        position: absolute;
        right: 100%;
        top: 50%;
        transform: translateY(-50%);
        border: 5px solid transparent;
        border-right-color: #0f172a;
    }
    .ps-rail:not(.ps-collapsed) .ps-tip { display: none; }
    body.ps-collapsed .ps-item:hover .ps-tip,
    body.ps-collapsed .ps-logo:hover .ps-tip,
    body.ps-collapsed .ps-gear summary:hover .ps-tip { opacity: 1; transform: translate(0, -50%); }

    .ps-spacer { flex: 1 1 auto; min-height: 12px; }

    .ps-dot {
        position: absolute;
        top: 7px;
        right: 8px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        box-shadow: 0 0 0 2px #fff, 0 2px 6px rgba(245, 158, 11, .5);
    }

    .ps-gear { position: relative; }
    .ps-gear summary { list-style: none; }
    .ps-gear summary::-webkit-details-marker { display: none; }

    .ps-menu {
        position: absolute;
        left: calc(100% + 16px);
        bottom: 0;
        min-width: 224px;
        background: #fff;
        border: 1px solid #e0f2fe;
        border-radius: 16px;
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 2px;
        box-shadow: 0 18px 40px -12px rgba(2, 132, 199, .35);
        opacity: 0;
        transform: translateX(-8px);
        pointer-events: none;
        transition: opacity .18s ease, transform .18s ease;
        z-index: 80;
    }
    .ps-menu-head {
        font-size: 10px;
        letter-spacing: .09em;
        text-transform: uppercase;
        color: #94a3b8;
        font-weight: 700;
        padding: 2px 12px 7px;
    }
    .ps-menu a,
    .ps-menu form,
    .ps-menu button {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        text-align: left;
        padding: 9px 12px;
        border-radius: 12px;
        color: #475569;
        font-size: 12px;
        font-weight: 600;
        background: transparent;
        border: 0;
        cursor: pointer;
        text-decoration: none;
        transition: background-color .15s ease, color .15s ease;
    }
    .ps-menu a:hover,
    .ps-menu button:hover { background: #f0f9ff; color: #0369a1; }
    .ps-menu .ps-active { color: #0369a1; background: #f0f9ff; }
    .ps-menu hr { border: none; border-top: 1px solid #e2e8f0; margin: 6px 4px; }
    .ps-menu .ps-danger { color: #f87171; }
    .ps-menu .ps-danger:hover { background: rgba(239, 68, 68, .1); color: #ef4444; }
    .ps-menu a svg,
    .ps-menu button svg { width: 16px; height: 16px; flex-shrink: 0; }
    .ps-gear[open] .ps-menu { opacity: 1; transform: translateX(0); pointer-events: auto; }

    .ps-user {
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 6px;
        border-radius: 16px;
        background: linear-gradient(to right, #f0f9ff, #fffbeb);
        border: 1px solid #e0f2fe;
        box-shadow: 0 1px 3px rgba(186, 230, 253, .6);
        position: relative;
    }
    .ps-user-link {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1 1 auto;
        min-width: 0;
        padding: 4px;
        border-radius: 12px;
        text-decoration: none;
        transition: background-color .15s ease;
    }
    .ps-user-link:hover { background: rgba(224, 242, 254, .8); }
    .ps-user-link:focus-visible { outline: 2px solid rgba(56, 189, 248, .6); outline-offset: 2px; }
    .ps-avatar {
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        border-radius: 9999px;
        background: linear-gradient(135deg, #fcd34d, #facc15);
        color: #78350f;
        font-weight: 800;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(253, 230, 138, .7);
    }
    .ps-user-txt { text-align: left; min-width: 0; flex: 1 1 auto; }
    .ps-user-txt strong { font-size: 12px; font-weight: 700; color: #1e293b; display: block; line-height: 1.25; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ps-user-txt small { font-size: 10px; color: #94a3b8; font-weight: 500; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ps-user-logout {
        flex-shrink: 0;
        color: #94a3b8;
        background: transparent;
        border: 0;
        cursor: pointer;
        padding: 6px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color .15s ease, background-color .15s ease;
    }
    .ps-user-logout:hover { color: #ef4444; background: rgba(239, 68, 68, .08); }
    .ps-user-logout svg { width: 16px; height: 16px; }
    body.ps-collapsed .ps-user { justify-content: center; padding: 6px; background: linear-gradient(to right, #f0f9ff, #fffbeb); }
    body.ps-collapsed .ps-user-txt,
    body.ps-collapsed .ps-user-logout { display: none; }

    @media (min-width: 1024px) {
        .ps-page { padding-left: 300px; transition: padding-left .25s ease; }
        body.ps-collapsed .ps-page { padding-left: 108px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .ps-rail, .ps-toggle, .ps-toggle svg, .ps-page, .ps-item, .ps-tip, .ps-menu { transition: none !important; }
        .ps-item:hover { transform: none; }
    }
</style>

<aside class="ps-rail" id="ps-rail" aria-label="{{ $psTitle ?? 'Navigasi' }}">
    <div class="ps-pill">
        <div class="ps-deco" aria-hidden="true">
            <div class="ps-blob ps-blob-1"></div>
            <div class="ps-blob ps-blob-2"></div>
        </div>

        <button type="button" class="ps-toggle" data-ps-toggle aria-label="Lipat atau bentangkan {{ $psTitle ?? 'navigasi' }}" aria-expanded="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                {!! $psChevronSvg !!}
            </svg>
        </button>

        <a class="ps-logo" href="{{ $psDashboardUrl }}" aria-label="Ke dashboard {{ $psTitle ?? '' }}">
            <span class="ps-logo-box">SOUL</span>
            <span class="ps-logo-txt">
                <strong>{{ $psLogoBrand ?? 'SOUL' }}</strong>
                <small>Panel {{ $psRole }}</small>
            </span>
            <span class="ps-tip">Dashboard</span>
        </a>

        <div class="ps-section" aria-hidden="true">
            <span class="ps-section-lbl">Menu utama</span>
            <span class="ps-section-sub">{{ strtoupper($psTitle ?? '') }}</span>
        </div>

        <nav class="ps-nav ps-nav-main" aria-label="Menu utama">
            @foreach ($psItems as $psItem)
                @php
                    $psActive = request()->routeIs((array) $psItem['is']);
                @endphp
                <a href="{{ $psItem['url'] }}" class="ps-item {{ $psActive ? 'ps-active' : '' }}"
                   aria-current="{{ $psActive ? 'page' : 'false' }}">
                    @if ($psActive)
                        <span class="ps-bar" aria-hidden="true"></span>
                    @endif
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        {!! $psIconSvg[$psItem['icon']] ?? '' !!}
                    </svg>
                    <span class="ps-lb">{{ $psItem['label'] }}</span>
                    <span class="ps-tip">{{ $psItem['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="ps-spacer" aria-hidden="true"></div>

        <nav class="ps-nav" aria-label="Lainnya">
            <a href="{{ $psNotifUrl }}" class="ps-item" aria-label="Notifikasi">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    {!! $psIconSvg['bell'] !!}
                </svg>
                <span class="ps-lb">Notifikasi</span>
                <span class="ps-dot" aria-hidden="true"></span>
                <span class="ps-tip">Notifikasi</span>
            </a>

            <details class="ps-gear">
                <summary class="ps-item" aria-label="Menu lainnya">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        {!! $psGearSvg !!}
                    </svg>
                    <span class="ps-lb">Pengaturan</span>
                    <span class="ps-tip">Lainnya</span>
                </summary>
                <div class="ps-menu">
                    @if (! empty($psMore))
                        <p class="ps-menu-head">Menu lebih banyak</p>
                        @foreach ($psMore as $psMoreItem)
                            @php
                                $psMoreActive = isset($psMoreItem['is']) ? request()->routeIs((array) $psMoreItem['is']) : false;
                            @endphp
                            <a href="{{ $psMoreItem['url'] }}" class="{{ $psMoreActive ? 'ps-active' : '' }}">{{ $psMoreItem['label'] }}</a>
                        @endforeach
                        <hr>
                    @endif
                    <a href="{{ $psProfileUrl }}">Profil</a>
                    <hr>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="ps-danger">Keluar</button>
                    </form>
                </div>
            </details>
        </nav>

        <div class="ps-user">
            <a class="ps-user-link" href="{{ $psProfileUrl }}" aria-label="Profil {{ $psNama }}">
                <span class="ps-avatar">{{ strtoupper(mb_substr($psNama, 0, 1)) }}</span>
                <span class="ps-user-txt">
                    <strong>{{ $psNama }}</strong>
                    <small>{{ $psSubU }}</small>
                </span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="ps-user-logout" aria-label="Keluar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        {!! $psLogoutSvg !!}
                    </svg>
                </button>
            </form>
            <span class="ps-tip">{{ $psNama }}</span>
        </div>
    </div>
</aside>

<script>
    (function () {
        var rail = document.getElementById('ps-rail');
        var gear = rail && rail.querySelector('.ps-gear');
        if (!rail || typeof window.__psSidebarInit !== 'undefined') return;
        window.__psSidebarInit = true;

        var btn = rail.querySelector('[data-ps-toggle]');
        var btnIcon = btn && btn.querySelector('svg');

        function apply(collapsed) {
            document.body.classList.toggle('ps-collapsed', collapsed);
            if (btnIcon) btnIcon.style.transform = collapsed ? 'rotate(180deg)' : 'rotate(0deg)';
            if (btn) btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        }

        apply(localStorage.getItem('soul_sidebar_collapsed') === '1');

        if (btn) {
            btn.addEventListener('click', function () {
                var collapsed = document.body.classList.contains('ps-collapsed');
                collapsed = !collapsed;
                localStorage.setItem('soul_sidebar_collapsed', collapsed ? '1' : '0');
                apply(collapsed);
            });
        }

        if (gear) {
            document.addEventListener('click', function (event) {
                if (gear.open && !gear.contains(event.target)) {
                    gear.removeAttribute('open');
                }
            });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && gear.open) {
                    gear.removeAttribute('open');
                    gear.querySelector('summary').focus();
                }
            });
        }
    })();
</script>