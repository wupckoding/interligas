<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="es" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#059669">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Interliga CR - Pádel</title>
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
        // Aplicar dark mode antes de renderizar
        if (localStorage.getItem('darkMode') === 'true') { document.getElementById('html-root').classList.add('dark'); }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', system-ui, sans-serif; -webkit-tap-highlight-color: transparent; }
        body { overscroll-behavior-y: contain; }
        
        /* ===== SPLASH ===== */
        #splash { position:fixed; inset:0; z-index:9999; display:flex; align-items:center; justify-content:center; flex-direction:column;
            background: linear-gradient(135deg, #059669 0%, #0d9488 50%, #0891b2 100%);
            transition: opacity .6s, transform .6s; }
        #splash.hide { opacity:0; transform:scale(1.1); pointer-events:none; }
        .splash-logo { width:80px; height:80px; border-radius:24px; background:rgba(255,255,255,.15); backdrop-filter:blur(20px);
            display:flex; align-items:center; justify-content:center; border:2px solid rgba(255,255,255,.2);
            animation: splashPulse 1.2s ease-in-out infinite alternate; }
        @keyframes splashPulse { 0%{transform:scale(1);box-shadow:0 0 0 0 rgba(255,255,255,.3)} 100%{transform:scale(1.08);box-shadow:0 0 0 20px rgba(255,255,255,0)} }
        .splash-bar { width:120px; height:4px; border-radius:4px; background:rgba(255,255,255,.2); margin-top:32px; overflow:hidden; }
        .splash-bar-fill { height:100%; width:0; border-radius:4px; background:white; animation: splashLoad 1.2s ease forwards; }
        @keyframes splashLoad { to{width:100%} }
        
        /* ===== SCREENS ===== */
        .screen { display:none; position:relative; }
        .screen.active { display:block; animation: screenIn .4s cubic-bezier(.4,0,.2,1); }
        .screen.slide-left { animation: slideLeft .35s cubic-bezier(.4,0,.2,1); }
        .screen.slide-right { animation: slideRight .35s cubic-bezier(.4,0,.2,1); }
        @keyframes screenIn { from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)} }
        @keyframes slideLeft { from{opacity:0;transform:translateX(40px)}to{opacity:1;transform:translateX(0)} }
        @keyframes slideRight { from{opacity:0;transform:translateX(-40px)}to{opacity:1;transform:translateX(0)} }
        
        /* ===== ANIMATIONS ===== */
        .fade-in { animation: fadeIn .4s cubic-bezier(.4,0,.2,1); }
        .slide-up { animation: slideUp .45s cubic-bezier(.4,0,.2,1); }
        .pop-in { animation: popIn .5s cubic-bezier(.175,.885,.32,1.275); }
        @keyframes fadeIn { from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)} }
        @keyframes slideUp { from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)} }
        @keyframes popIn { from{opacity:0;transform:scale(.8)}to{opacity:1;transform:scale(1)} }
        @keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
        @keyframes float1 { 0%,100%{transform:translate(0,0) rotate(0deg)} 33%{transform:translate(15px,-20px) rotate(120deg)} 66%{transform:translate(-10px,10px) rotate(240deg)} }
        @keyframes float2 { 0%,100%{transform:translate(0,0) rotate(0deg)} 33%{transform:translate(-20px,15px) rotate(-120deg)} 66%{transform:translate(15px,-5px) rotate(-240deg)} }
        @keyframes float3 { 0%,100%{transform:translate(0,0)} 50%{transform:translate(10px,-15px)} }
        @keyframes countUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        @keyframes gradientShift { 0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%} }
        @keyframes confettiFall { 0%{transform:translateY(-10px) rotate(0deg);opacity:1} 100%{transform:translateY(100vh) rotate(720deg);opacity:0} }
        @keyframes ripple { to{transform:scale(4);opacity:0} }
        @keyframes pulse-glow { 0%,100%{box-shadow:0 0 0 0 rgba(16,185,129,.4)} 50%{box-shadow:0 0 0 8px rgba(16,185,129,0)} }
        @keyframes toastProgress { from{width:100%} to{width:0%} }
        
        .skeleton { background: linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 50%,#f3f4f6 75%); background-size:200% 100%; animation: shimmer 1.5s infinite; border-radius: 12px; }
        .bottom-sheet { transition: transform .35s cubic-bezier(.4,0,.2,1); }
        .bottom-sheet.closed { transform: translateY(100%); }
        .progress-bar { transition: width .8s cubic-bezier(.4,0,.2,1); }
        
        /* ===== GLASSMORPHISM ===== */
        .glass { background: rgba(255,255,255,.1); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        .glass-card { background: rgba(255,255,255,.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.6); }
        
        /* ===== INTERACTIONS ===== */
        .card-3d { transition: transform .25s cubic-bezier(.4,0,.2,1), box-shadow .25s; perspective: 800px; }
        .card-3d:active { transform: scale(.97) rotateX(2deg); box-shadow: 0 8px 25px rgba(0,0,0,.08); }
        .ripple-btn { position:relative; overflow:hidden; }
        .ripple-btn .ripple-effect { position:absolute; border-radius:50%; background:rgba(255,255,255,.35);
            transform:scale(0); animation: ripple .6s linear; pointer-events:none; }
        .glow-green { animation: pulse-glow 2s infinite; }
        .nav-pill { transition: all .3s cubic-bezier(.4,0,.2,1); }
        
        /* ===== GRADIENT HEADER ===== */
        .header-gradient { background: linear-gradient(135deg, #059669 0%, #0d9488 30%, #0891b2 70%, #059669 100%);
            background-size: 400% 400%; animation: gradientShift 8s ease infinite; position:relative; overflow:hidden; }
        .header-gradient .particle { position:absolute; border-radius:50%; background:rgba(255,255,255,.08); pointer-events:none; }
        .header-gradient .p1 { width:80px;height:80px;top:-20px;right:-10px; animation:float1 12s ease-in-out infinite; }
        .header-gradient .p2 { width:50px;height:50px;bottom:-10px;left:20px; animation:float2 10s ease-in-out infinite; }
        .header-gradient .p3 { width:30px;height:30px;top:50%;left:60%; animation:float3 8s ease-in-out infinite; }
        .header-gradient .p4 { width:120px;height:120px;top:-50px;left:-30px; animation:float1 15s ease-in-out infinite reverse; background:rgba(255,255,255,.04); }
        
        /* ===== GRADIENT TEXT ===== */
        .gradient-text { background: linear-gradient(135deg, #fff 0%, #a7f3d0 50%, #fff 100%);
            background-size: 200% auto; -webkit-background-clip:text; -webkit-text-fill-color:transparent;
            background-clip:text; animation: gradientShift 4s linear infinite; }
        
        /* ===== AVATAR ===== */
        .avatar { width:36px; height:36px; border-radius:12px; display:flex; align-items:center; justify-content:center;
            font-weight:800; font-size:13px; text-transform:uppercase; flex-shrink:0; }
        .avatar-colors { background:linear-gradient(135deg, var(--a1), var(--a2)); color:white; }
        
        /* ===== TOAST PROGRESS ===== */
        .toast-progress { height:3px; border-radius:0 0 12px 12px; background:rgba(255,255,255,.3); animation: toastProgress 3.5s linear forwards; }
        
        /* ===== CONFETTI ===== */
        #confetti-canvas { position:fixed; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:100; }
        
        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width:3px; }
        ::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:4px; }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom, 0px); }
        input, select, textarea { font-size: 16px !important; }
        
        /* ===== DARK MODE ===== */
        .dark body { background: #0f172a; color: #e2e8f0; }
        .dark .bg-gray-50 { background: #0f172a; }
        .dark .bg-white { background: #1e293b; }
        .dark .glass-card { background: rgba(30,41,59,.85); border-color: rgba(255,255,255,.08); }
        .dark .text-gray-800, .dark .text-gray-700, .dark .text-gray-600 { color: #e2e8f0; }
        .dark .text-gray-500, .dark .text-gray-400 { color: #94a3b8; }
        .dark .text-gray-300 { color: #64748b; }
        .dark .border-gray-100, .dark .border-gray-200 { border-color: #334155; }
        .dark .bg-gray-100, .dark .bg-gray-50 { background: #1e293b; }
        .dark .shadow-md { box-shadow: 0 4px 6px -1px rgba(0,0,0,.3); }
        .dark input, .dark select, .dark textarea { background: #1e293b; color: #e2e8f0; border-color: #475569; }
        .dark input:focus, .dark select:focus { background: #1e293b; border-color: #10b981; }
        .dark nav { background: rgba(15,23,42,.95); border-color: #1e293b; }
        .dark .skeleton { background: linear-gradient(90deg,#1e293b 25%,#334155 50%,#1e293b 75%); background-size:200% 100%; }
        
        /* ===== PULL REFRESH ===== */
        #pull-indicator { position:fixed; top:0; left:50%; transform:translateX(-50%) translateY(-50px); z-index:45;
            width:40px; height:40px; border-radius:50%; background:white; box-shadow: 0 4px 15px rgba(0,0,0,.1);
            display:flex; align-items:center; justify-content:center; transition: transform .3s, opacity .3s; opacity:0; }
        #pull-indicator.visible { opacity:1; transform:translateX(-50%) translateY(12px); }
        #pull-indicator.spinning svg { animation: spin .8s linear infinite; }
        @keyframes spin { from{transform:rotate(0deg)}to{transform:rotate(360deg)} }
        
        /* ===== COUNTER ===== */
        .count-anim { animation: countUp .5s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- ===== SPLASH SCREEN ===== -->
<div id="splash">
    <div class="splash-logo">
        <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
    </div>
    <p class="text-white font-extrabold text-xl mt-5 tracking-tight">Interliga</p>
    <p class="text-white/50 text-xs font-medium tracking-widest mt-1">PÁDEL COSTA RICA</p>
    <div class="splash-bar"><div class="splash-bar-fill"></div></div>
</div>

<!-- ===== CONFETTI CANVAS ===== -->
<canvas id="confetti-canvas"></canvas>

<!-- ===== PULL TO REFRESH ===== -->
<div id="pull-indicator">
    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
</div>

<!-- ===== HEADER ===== -->
<header id="mainHeader" class="sticky top-0 z-40 header-gradient shadow-lg shadow-emerald-900/20 transition-all duration-300">
    <div class="particle p1"></div><div class="particle p2"></div><div class="particle p3"></div><div class="particle p4"></div>
    <div class="max-w-lg mx-auto px-4 py-3.5 flex items-center justify-between relative z-10">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 glass rounded-2xl flex items-center justify-center border border-white/20 shadow-lg shadow-emerald-900/10">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <h1 class="gradient-text font-black text-lg leading-tight tracking-tight" id="headerTitle">Interliga</h1>
                <p class="text-white/60 text-[10px] font-semibold tracking-[.2em] uppercase" id="headerSub">Pádel Costa Rica</p>
            </div>
        </div>
        <div class="flex items-center gap-1">
            <button onclick="toggleDarkMode()" id="btnDark" class="text-white/70 hover:text-white p-2.5 rounded-xl hover:bg-white/10 transition-all active:scale-90" title="Modo oscuro">
                <svg class="w-5 h-5" id="darkIcon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>
            <button onclick="toggleRefresh()" id="btnRefresh" class="text-white/70 hover:text-white p-2.5 rounded-xl hover:bg-white/10 transition-all active:scale-90" title="Actualizar">
                <svg class="w-5 h-5" id="refreshIcon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
        </div>
    </div>
</header>

<!-- ===== SCREEN: JORNADAS ===== -->
<div id="screen-jornadas" class="screen active">
    <div class="max-w-lg mx-auto px-4 pt-5 pb-28">
        <!-- Hero Banner -->
        <div class="relative rounded-3xl overflow-hidden mb-6 slide-up" style="min-height:180px">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-700"></div>
            <div class="absolute inset-0 opacity-[.07]" style="background-image:url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
            <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-16 translate-x-12" style="animation:float1 10s ease-in-out infinite"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full translate-y-10 -translate-x-6" style="animation:float2 8s ease-in-out infinite"></div>
            <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-white/5 rounded-full" style="animation:float3 6s ease-in-out infinite"></div>
            <div class="relative z-10 p-6 pb-7">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/15 rounded-full text-white/90 text-[11px] font-bold tracking-wide mb-3 backdrop-blur-sm border border-white/10">
                    <span class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></span>
                    EN VIVO
                </div>
                <h2 class="text-2xl font-black text-white leading-tight tracking-tight">Reserva tu lugar<br>en la interliga</h2>
                <p class="text-white/60 text-sm mt-2.5 max-w-[280px] leading-relaxed">Selecciona una jornada, elige tu partido y asegura tu cupo en minutos.</p>
                <div id="hero-stats" class="flex items-center gap-4 mt-5">
                    <div class="count-anim" style="animation-delay:.2s">
                        <p class="text-2xl font-black text-white" id="stat-jornadas">-</p>
                        <p class="text-white/50 text-[10px] font-semibold tracking-wide">JORNADAS</p>
                    </div>
                    <div class="w-px h-8 bg-white/20"></div>
                    <div class="count-anim" style="animation-delay:.35s">
                        <p class="text-2xl font-black text-white" id="stat-partidos">-</p>
                        <p class="text-white/50 text-[10px] font-semibold tracking-wide">PARTIDOS</p>
                    </div>
                    <div class="w-px h-8 bg-white/20"></div>
                    <div class="count-anim" style="animation-delay:.5s">
                        <p class="text-2xl font-black text-white" id="stat-inscritos">-</p>
                        <p class="text-white/50 text-[10px] font-semibold tracking-wide">INSCRITOS</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mb-4">
            <h3 class="text-gray-800 font-extrabold text-base">Jornadas abiertas</h3>
            <div class="flex items-center gap-1.5">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                <span class="text-xs text-emerald-600 font-semibold">En vivo</span>
            </div>
        </div>

        <div id="jornadas-list" class="space-y-3">
            <div class="skeleton h-32 w-full"></div>
            <div class="skeleton h-32 w-full"></div>
        </div>
        
        <!-- Footer branding -->
        <div class="mt-10 pt-6 border-t border-gray-100 text-center">
            <div class="flex items-center justify-center gap-2 mb-2">
                <div class="w-6 h-6 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-400">Interliga CR</span>
            </div>
            <p class="text-[11px] text-gray-300 font-medium">Pádel Competitivo &bull; Costa Rica 2026</p>
        </div>
    </div>
</div>

<!-- ===== SCREEN: PARTIDOS ===== -->
<div id="screen-partidos" class="screen">
    <div class="max-w-lg mx-auto px-4 pt-4 pb-28">
        <button onclick="showScreen('jornadas','right')" class="ripple-btn flex items-center gap-1.5 text-emerald-600 text-sm font-semibold mb-4 active:opacity-60 transition-opacity px-2 py-1 -ml-2 rounded-lg">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver
        </button>
        <div class="glass-card rounded-2xl p-4 mb-5 shadow-sm">
            <h2 class="text-gray-800 font-black text-lg" id="jornada-nombre"></h2>
            <div class="flex items-center gap-4 mt-1.5 text-sm text-gray-500">
                <span class="flex items-center gap-1.5" id="jornada-fecha-label">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span id="jornada-info"></span>
                </span>
                <span class="flex items-center gap-1.5" id="jornada-loc-label" style="display:none">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    <span id="jornada-ubicacion"></span>
                </span>
            </div>
            <div id="jornada-countdown" class="mt-2"></div>
        </div>
        <div class="flex gap-2 mb-5 overflow-x-auto pb-1 -mx-1 px-1" id="filtros-container">
            <button onclick="filtrarPartidos('todos')" class="filtro-btn ripple-btn shrink-0 px-4 py-2.5 rounded-full text-sm font-bold bg-emerald-600 text-white shadow-md shadow-emerald-200/50 transition-all" data-filtro="todos">Todos</button>
            <button onclick="filtrarPartidos('masculino')" class="filtro-btn ripple-btn shrink-0 px-4 py-2.5 rounded-full text-sm font-bold bg-white text-gray-500 border border-gray-200 transition-all" data-filtro="masculino">&#9794; Masculino</button>
            <button onclick="filtrarPartidos('femenino')" class="filtro-btn ripple-btn shrink-0 px-4 py-2.5 rounded-full text-sm font-bold bg-white text-gray-500 border border-gray-200 transition-all" data-filtro="femenino">&#9792; Femenino</button>
            <button onclick="filtrarPartidos('mixto')" class="filtro-btn ripple-btn shrink-0 px-4 py-2.5 rounded-full text-sm font-bold bg-white text-gray-500 border border-gray-200 transition-all" data-filtro="mixto">&#9894; Mixto</button>
        </div>
        <div id="partidos-count" class="text-xs text-gray-400 font-medium mb-3"></div>
        <div id="partidos-list" class="space-y-3">
            <div class="skeleton h-48 w-full"></div>
            <div class="skeleton h-48 w-full"></div>
        </div>
    </div>
</div>

<!-- ===== SCREEN: DETALLE ===== -->
<div id="screen-detalle" class="screen">
    <div class="max-w-lg mx-auto px-4 pt-4 pb-28">
        <button onclick="showScreen('partidos','right')" class="ripple-btn flex items-center gap-1.5 text-emerald-600 text-sm font-semibold mb-4 active:opacity-60 transition-opacity px-2 py-1 -ml-2 rounded-lg">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Volver a partidos
        </button>
        <div id="detalle-content"></div>
    </div>
</div>

<!-- ===== MODAL INSCRIPCIÓN ===== -->
<div id="modal-overlay" class="fixed inset-0 bg-black/60 z-50 hidden backdrop-blur-sm transition-opacity duration-300" onclick="cerrarModal()"></div>
<div id="modal-inscripcion" class="fixed inset-x-0 bottom-0 z-50 bottom-sheet closed">
    <div class="max-w-lg mx-auto bg-white rounded-t-3xl shadow-2xl px-6 pt-3 pb-8 safe-bottom" onclick="event.stopPropagation()">
        <div class="flex justify-center mb-4"><div class="w-12 h-1.5 bg-gray-200 rounded-full cursor-grab" id="sheet-handle"></div></div>
        <div class="flex items-center gap-3 mb-5">
            <div class="w-11 h-11 rounded-2xl bg-emerald-100 flex items-center justify-center" id="modal-icon">
                <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-black text-gray-800" id="modal-title">Inscripción</h3>
                <p class="text-sm text-gray-500" id="modal-subtitle"></p>
            </div>
        </div>
        
        <!-- Stepper -->
        <div class="flex items-center gap-2 mb-6" id="stepper">
            <div class="flex items-center gap-1.5">
                <span class="w-6 h-6 rounded-full bg-emerald-600 text-white text-[10px] font-bold flex items-center justify-center">1</span>
                <span class="text-[11px] font-semibold text-emerald-700">Tipo</span>
            </div>
            <div class="flex-1 h-0.5 bg-gray-200 rounded"></div>
            <div class="flex items-center gap-1.5">
                <span class="w-6 h-6 rounded-full bg-gray-200 text-gray-500 text-[10px] font-bold flex items-center justify-center" id="step2-dot">2</span>
                <span class="text-[11px] font-semibold text-gray-400" id="step2-label">Datos</span>
            </div>
            <div class="flex-1 h-0.5 bg-gray-200 rounded"></div>
            <div class="flex items-center gap-1.5">
                <span class="w-6 h-6 rounded-full bg-gray-200 text-gray-500 text-[10px] font-bold flex items-center justify-center" id="step3-dot">3</span>
                <span class="text-[11px] font-semibold text-gray-400" id="step3-label">Listo</span>
            </div>
        </div>
        
        <form id="form-inscripcion" onsubmit="enviarInscripcion(event)">
            <input type="hidden" name="partido_id" id="inp-partido-id">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="es_reserva" id="inp-es-reserva" value="0">
            
            <div class="grid grid-cols-3 gap-2.5 mb-5">
                <label class="cursor-pointer">
                    <input type="radio" name="tipo" value="solo" class="peer hidden" checked onchange="togglePareja()">
                    <div class="peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:ring-2 peer-checked:ring-emerald-200 border-2 border-gray-200 rounded-2xl p-3 text-center transition-all hover:border-gray-300">
                        <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-2xl flex items-center justify-center text-xl shadow-sm shadow-emerald-100">&#129489;</div>
                        <div class="font-bold text-xs text-gray-800">Solo</div>
                        <div class="text-[9px] text-gray-400 mt-0.5 leading-tight">Auto-emparejado</div>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="tipo" value="pareja" class="peer hidden" onchange="togglePareja()">
                    <div class="peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:ring-2 peer-checked:ring-emerald-200 border-2 border-gray-200 rounded-2xl p-3 text-center transition-all hover:border-gray-300">
                        <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center text-xl shadow-sm shadow-blue-100">&#128101;</div>
                        <div class="font-bold text-xs text-gray-800">Pareja</div>
                        <div class="text-[9px] text-gray-400 mt-0.5 leading-tight">Con compañero/a</div>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="tipo" value="reserva" class="peer hidden" onchange="togglePareja()">
                    <div class="peer-checked:bg-amber-50 peer-checked:border-amber-500 peer-checked:ring-2 peer-checked:ring-amber-200 border-2 border-gray-200 rounded-2xl p-3 text-center transition-all hover:border-gray-300">
                        <div class="w-12 h-12 mx-auto mb-2 bg-gradient-to-br from-amber-100 to-amber-200 rounded-2xl flex items-center justify-center text-xl shadow-sm shadow-amber-100">&#128165;</div>
                        <div class="font-bold text-xs text-gray-800">Reserva</div>
                        <div class="text-[9px] text-gray-400 mt-0.5 leading-tight">Suplente / backup</div>
                    </div>
                </label>
            </div>

            <div class="space-y-3.5">
                <div class="relative">
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Tu nombre <span class="text-red-400">*</span></label>
                    <input type="text" name="nombre" required maxlength="120" 
                        class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 outline-none transition-all text-sm bg-gray-50 focus:bg-white"
                        placeholder="Nombre completo" oninput="validateField(this)">
                    <div class="absolute right-3 top-[38px] hidden text-emerald-500 validate-icon"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>
                </div>
                <div class="relative">
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Teléfono</label>
                    <input type="tel" name="telefono" maxlength="30"
                        class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 outline-none transition-all text-sm bg-gray-50 focus:bg-white"
                        placeholder="+506 8888-8888">
                </div>
            </div>

            <div id="campos-pareja" class="space-y-3.5 mt-4 hidden">
                <div class="flex items-center gap-2 py-2">
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider px-2">Datos de tu pareja</span>
                    <div class="flex-1 h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
                </div>
                <div class="relative">
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Nombre de pareja <span class="text-red-400">*</span></label>
                    <input type="text" name="pareja_nombre" maxlength="120"
                        class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 outline-none transition-all text-sm bg-gray-50 focus:bg-white"
                        placeholder="Nombre completo de tu pareja" oninput="validateField(this)">
                    <div class="absolute right-3 top-[38px] hidden text-emerald-500 validate-icon"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1.5">Teléfono de pareja</label>
                    <input type="tel" name="pareja_telefono" maxlength="30"
                        class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 outline-none transition-all text-sm bg-gray-50 focus:bg-white"
                        placeholder="+506 8888-8888">
                </div>
            </div>

            <button type="submit" id="btn-inscribir"
                class="ripple-btn w-full mt-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-2xl shadow-lg shadow-emerald-200/50 active:scale-[.97] transition-transform text-sm tracking-wide">
                &#9889; ¡INSCRIBIRME!
            </button>
        </form>
    </div>
</div>

<!-- ===== MODAL BUSCAR ===== -->
<div id="modal-buscar" class="fixed inset-0 bg-black/60 z-50 hidden backdrop-blur-sm flex items-end justify-center" onclick="this.classList.add('hidden')">
    <div class="max-w-lg w-full bg-white rounded-t-3xl shadow-2xl px-6 pt-3 pb-8 safe-bottom slide-up" onclick="event.stopPropagation()">
        <div class="flex justify-center mb-4"><div class="w-12 h-1.5 bg-gray-200 rounded-full"></div></div>
        <div class="flex items-center gap-3 mb-5">
            <div class="w-11 h-11 rounded-2xl bg-blue-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-black text-gray-800">Buscar inscripciones</h3>
                <p class="text-sm text-gray-500">Encuentra tus reservas</p>
            </div>
        </div>
        <div class="flex gap-2">
            <input type="text" id="buscar-nombre" placeholder="Escribe tu nombre..." maxlength="120"
                class="flex-1 px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 outline-none text-sm bg-gray-50 focus:bg-white"
                onkeydown="if(event.key==='Enter')ejecutarBusqueda()">
            <button onclick="ejecutarBusqueda()" class="ripple-btn px-5 py-3.5 bg-emerald-600 text-white font-bold rounded-xl text-sm active:scale-95 transition-transform shadow-md shadow-emerald-200/50">Buscar</button>
        </div>
        <div id="buscar-resultados" class="mt-4 space-y-2 max-h-64 overflow-y-auto"></div>
        <button onclick="document.getElementById('modal-buscar').classList.add('hidden')" class="w-full mt-4 py-2.5 text-gray-400 text-sm font-semibold">Cerrar</button>
    </div>
</div>

<!-- ===== SUCCESS MODAL ===== -->
<div id="modal-success" class="fixed inset-0 bg-black/60 z-[70] hidden backdrop-blur-sm flex items-center justify-center p-4" onclick="cerrarSuccess()">
    <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-sm w-full text-center pop-in" onclick="event.stopPropagation()">
        <div class="w-24 h-24 mx-auto mb-5 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-full flex items-center justify-center relative">
            <svg class="w-12 h-12 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            <div class="absolute inset-0 rounded-full border-4 border-emerald-200 animate-ping opacity-30"></div>
        </div>
        <h3 class="text-xl font-black text-gray-800 mb-2" id="success-title">¡Inscripción exitosa!</h3>
        <p class="text-gray-500 text-sm leading-relaxed mb-6" id="success-msg"></p>
        <div class="flex gap-2.5">
            <button onclick="compartirWhatsApp()" class="ripple-btn flex-1 py-3.5 bg-green-500 text-white font-bold rounded-xl text-sm active:scale-95 transition-transform flex items-center justify-center gap-2 shadow-md shadow-green-200/50">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Compartir
            </button>
            <button onclick="cerrarSuccess()" class="ripple-btn flex-1 py-3.5 bg-gray-100 text-gray-700 font-bold rounded-xl text-sm active:scale-95 transition-transform">Cerrar</button>
        </div>
    </div>
</div>

<!-- ===== TOAST ===== -->
<div id="toast" class="fixed top-20 left-1/2 -translate-x-1/2 z-[60] rounded-2xl shadow-2xl text-sm font-semibold transition-all duration-300 opacity-0 -translate-y-4 pointer-events-none max-w-[85vw] text-center overflow-hidden">
    <div class="px-5 py-3 flex items-center gap-2 justify-center" id="toast-inner"></div>
    <div class="toast-progress" id="toast-progress" style="display:none"></div>
</div>

<!-- ===== FLOATING WHATSAPP ===== -->
<a href="https://wa.me/?text=%F0%9F%8E%BE%20%C2%A1Hola!%20Quiero%20info%20sobre%20la%20Interliga%20de%20P%C3%A1del" target="_blank" 
    class="fixed bottom-24 right-4 z-20 w-14 h-14 bg-green-500 rounded-full flex items-center justify-center shadow-lg shadow-green-500/30 active:scale-90 transition-transform hover:bg-green-600" id="fab-whatsapp">
    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>

<!-- ===== SCREEN: CLASIFICACIÓN ===== -->
<div id="screen-clasificacion" class="screen">
    <div class="max-w-lg mx-auto px-4 pt-5 pb-28">
        <div class="relative rounded-3xl overflow-hidden mb-6 slide-up" style="min-height:140px">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-blue-700 to-slate-800"></div>
            <div class="absolute inset-0 opacity-[.07]" style="background-image:url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-10 translate-x-10" style="animation:float1 10s ease-in-out infinite"></div>
            <div class="relative z-10 p-6">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/15 rounded-full text-white/90 text-[11px] font-bold tracking-wide mb-3 backdrop-blur-sm border border-white/10">
                    <span>&#127942;</span>
                    TABLA DE POSICIONES
                </div>
                <h2 class="text-xl font-black text-white leading-tight tracking-tight">Clasificación General</h2>
                <p class="text-white/50 text-sm mt-1.5">Posiciones actualizadas por clubes</p>
            </div>
        </div>

        <div class="mb-4">
            <select id="clasif-jornada-filter" onchange="cargarClasificacion()" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none transition-all font-medium">
                <option value="">General (todas las jornadas)</option>
            </select>
        </div>

        <div id="clasificacion-table" class="space-y-0">
            <div class="skeleton h-10 w-full mb-1"></div>
            <div class="skeleton h-12 w-full mb-1"></div>
            <div class="skeleton h-12 w-full mb-1"></div>
            <div class="skeleton h-12 w-full mb-1"></div>
        </div>

        <div id="resultados-recientes" class="mt-6"></div>

        <div class="mt-10 pt-6 border-t border-gray-100 text-center">
            <div class="flex items-center justify-center gap-2 mb-2">
                <div class="w-6 h-6 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <span class="text-xs">&#127942;</span>
                </div>
                <span class="text-sm font-bold text-gray-400">Clasificación Interliga</span>
            </div>
            <p class="text-[11px] text-gray-300 font-medium">Pádel Competitivo &bull; Costa Rica 2026</p>
        </div>
    </div>
</div>

<!-- ===== BOTTOM NAV ===== -->
<nav class="fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-xl border-t border-gray-100 z-30 safe-bottom shadow-lg shadow-black/5">
    <div class="max-w-lg mx-auto grid grid-cols-4 py-2 px-2">
        <button onclick="showScreen('jornadas')" class="nav-pill flex flex-col items-center gap-0.5 py-2 rounded-2xl text-emerald-600 bg-emerald-50" data-screen="jornadas">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/></svg>
            <span class="text-[10px] font-bold">Inicio</span>
        </button>
        <button onclick="showScreen('clasificacion')" class="nav-pill flex flex-col items-center gap-0.5 py-2 rounded-2xl text-gray-400" data-screen="clasificacion">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="text-[10px] font-bold">Tabla</span>
        </button>
        <button onclick="buscarMisInscripciones()" class="nav-pill flex flex-col items-center gap-0.5 py-2 rounded-2xl text-gray-400" data-screen="buscar">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span class="text-[10px] font-bold">Buscar</span>
        </button>
        <a href="admin.php" class="nav-pill flex flex-col items-center gap-0.5 py-2 rounded-2xl text-gray-400">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="text-[10px] font-bold">Admin</span>
        </a>
    </div>
</nav>

<script>
const CSRF = '<?= e(csrfToken()) ?>';
let currentJornada = null;
let currentJornadaData = null;
let allPartidos = [];
let currentFiltro = 'todos';
let lastSuccessMsg = '';
let screenHistory = ['jornadas'];

// ===== SPLASH =====
window.addEventListener('load', function() {
    setTimeout(function() {
        var s = document.getElementById('splash');
        s.classList.add('hide');
        setTimeout(function() { s.style.display = 'none'; }, 600);
    }, 1300);
});

// ===== CONFETTI =====
function launchConfetti() {
    var canvas = document.getElementById('confetti-canvas');
    var ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    var particles = [];
    var colors = ['#10b981','#06b6d4','#8b5cf6','#f59e0b','#ef4444','#ec4899','#3b82f6'];
    for (var i = 0; i < 80; i++) {
        particles.push({
            x: canvas.width / 2 + (Math.random() - .5) * 200,
            y: canvas.height / 2,
            vx: (Math.random() - .5) * 15,
            vy: Math.random() * -18 - 5,
            color: colors[Math.floor(Math.random() * colors.length)],
            size: Math.random() * 8 + 4,
            rotation: Math.random() * 360,
            rotSpeed: (Math.random() - .5) * 10,
            gravity: .4 + Math.random() * .2,
            life: 1
        });
    }
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        var alive = false;
        for (var p of particles) {
            if (p.life <= 0) continue;
            alive = true;
            p.x += p.vx;
            p.vy += p.gravity;
            p.y += p.vy;
            p.rotation += p.rotSpeed;
            p.life -= .012;
            p.vx *= .99;
            ctx.save();
            ctx.translate(p.x, p.y);
            ctx.rotate(p.rotation * Math.PI / 180);
            ctx.globalAlpha = p.life;
            ctx.fillStyle = p.color;
            ctx.fillRect(-p.size / 2, -p.size / 2, p.size, p.size * .6);
            ctx.restore();
        }
        if (alive) requestAnimationFrame(animate);
        else ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
    animate();
}

// ===== RIPPLE EFFECT =====
document.addEventListener('click', function(e) {
    var btn = e.target.closest('.ripple-btn');
    if (!btn) return;
    var rect = btn.getBoundingClientRect();
    var ripple = document.createElement('span');
    ripple.className = 'ripple-effect';
    var size = Math.max(rect.width, rect.height);
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
    ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';
    btn.appendChild(ripple);
    setTimeout(function() { ripple.remove(); }, 600);
});

// ===== PULL TO REFRESH =====
(function() {
    var startY = 0, pulling = false, indicator = document.getElementById('pull-indicator');
    document.addEventListener('touchstart', function(e) {
        if (window.scrollY === 0) { startY = e.touches[0].clientY; pulling = true; }
    }, { passive: true });
    document.addEventListener('touchmove', function(e) {
        if (!pulling) return;
        var diff = e.touches[0].clientY - startY;
        if (diff > 10 && diff < 120) {
            indicator.classList.add('visible');
            indicator.style.transform = 'translateX(-50%) translateY(' + Math.min(diff * .6, 60) + 'px)';
        }
    }, { passive: true });
    document.addEventListener('touchend', function() {
        if (!pulling) return;
        pulling = false;
        if (indicator.classList.contains('visible')) {
            indicator.classList.add('spinning');
            toggleRefresh();
            setTimeout(function() { indicator.classList.remove('visible', 'spinning'); indicator.style.transform = ''; }, 1000);
        }
    });
})();

// ===== BOTTOM SHEET SWIPE =====
(function() {
    var handle = document.getElementById('sheet-handle');
    var sheet = document.getElementById('modal-inscripcion');
    var startY, currentY;
    if (!handle) return;
    handle.addEventListener('touchstart', function(e) { startY = e.touches[0].clientY; currentY = startY; }, { passive: true });
    handle.addEventListener('touchmove', function(e) {
        currentY = e.touches[0].clientY;
        var diff = currentY - startY;
        if (diff > 0) { sheet.style.transform = 'translateY(' + diff + 'px)'; }
    }, { passive: true });
    handle.addEventListener('touchend', function() {
        var diff = currentY - startY;
        if (diff > 100) { cerrarModal(); }
        sheet.style.transform = '';
    });
})();

// ===== API =====
async function api(action, params, method) {
    params = params || {};
    method = method || 'GET';
    var url = 'api.php?action=' + action;
    var opts = { method: method, headers: {} };
    if (method === 'POST') {
        var fd = new FormData();
        fd.append('csrf_token', CSRF);
        for (var k in params) { if (params.hasOwnProperty(k)) fd.append(k, params[k]); }
        opts.body = fd;
    } else {
        for (var k in params) { if (params.hasOwnProperty(k)) url += '&' + k + '=' + encodeURIComponent(params[k]); }
    }
    var res = await fetch(url, opts);
    return res.json();
}

// ===== SCREENS =====
function showScreen(name, direction) {
    direction = direction || 'left';
    document.querySelectorAll('.screen').forEach(function(s) { s.classList.remove('active','slide-left','slide-right'); });
    var el = document.getElementById('screen-' + name);
    if (el) { 
        el.classList.add('active');
        el.classList.add(direction === 'right' ? 'slide-right' : 'slide-left');
    }
    document.querySelectorAll('.nav-pill').forEach(function(b) {
        var isActive = b.dataset.screen === name;
        if (isActive) {
            b.classList.add('text-emerald-600', 'bg-emerald-50');
            b.classList.remove('text-gray-400', 'bg-transparent');
        } else {
            b.classList.remove('text-emerald-600', 'bg-emerald-50');
            b.classList.add('text-gray-400');
        }
    });
    if (name === 'jornadas') {
        document.getElementById('headerTitle').textContent = 'Interliga';
        document.getElementById('headerSub').textContent = 'Pádel Costa Rica';
        cargarJornadas();
    }
    if (name === 'clasificacion') {
        document.getElementById('headerTitle').textContent = 'Clasificación';
        document.getElementById('headerSub').textContent = 'TABLA DE POSICIONES';
        cargarClasifJornadas();
        cargarClasificacion();
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function toggleRefresh() {
    var icon = document.getElementById('refreshIcon');
    icon.classList.add('animate-spin');
    setTimeout(function() { icon.classList.remove('animate-spin'); }, 800);
    var active = document.querySelector('.screen.active');
    if (active && active.id === 'screen-jornadas') cargarJornadas();
    else if (active && active.id === 'screen-partidos') cargarPartidos();
}

// ===== ANIMATED COUNTER =====
function animateCounter(el, target) {
    var start = 0;
    var duration = 800;
    var startTime = null;
    function step(timestamp) {
        if (!startTime) startTime = timestamp;
        var progress = Math.min((timestamp - startTime) / duration, 1);
        var eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.floor(eased * target);
        if (progress < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
}

// ===== JORNADAS =====
async function cargarJornadas() {
    var r = await api('jornadas_list', { estado: 'abierta' });
    var c = document.getElementById('jornadas-list');
    
    // Update hero stats
    if (r.ok && r.data.length) {
        var totalP = 0, totalI = 0;
        for (var s of r.data) { totalP += parseInt(s.total_partidos) || 0; totalI += parseInt(s.total_inscritos) || 0; }
        animateCounter(document.getElementById('stat-jornadas'), r.data.length);
        animateCounter(document.getElementById('stat-partidos'), totalP);
        animateCounter(document.getElementById('stat-inscritos'), totalI);
    }
    
    if (!r.ok || !r.data.length) {
        c.innerHTML = '<div class="text-center py-16 fade-in">' +
            '<div class="w-28 h-28 mx-auto mb-5 bg-gradient-to-br from-gray-100 to-gray-50 rounded-full flex items-center justify-center shadow-inner">' +
            '<svg class="w-14 h-14 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>' +
            '</div><p class="text-gray-600 font-bold text-lg">No hay jornadas activas</p>' +
            '<p class="text-gray-400 text-sm mt-2 max-w-xs mx-auto leading-relaxed">Pronto se abrirán nuevas inscripciones. ¡Mantente atento!</p></div>';
        return;
    }
    c.innerHTML = r.data.map(function(j, idx) {
        var fecha = new Date(j.fecha + 'T12:00:00');
        var dia = fecha.toLocaleDateString('es-CR', { weekday: 'long', day: 'numeric', month: 'long' });
        var hoy = new Date(); hoy.setHours(0,0,0,0);
        var diff = Math.ceil((fecha - hoy) / (1000*60*60*24));
        var countdown, countdownColor, countdownIcon;
        if (diff === 0) { countdown = '¡Hoy!'; countdownColor = 'bg-red-500 text-white'; countdownIcon = '&#128308;'; }
        else if (diff === 1) { countdown = 'Mañana'; countdownColor = 'bg-amber-500 text-white'; countdownIcon = '&#128993;'; }
        else if (diff > 0) { countdown = 'En ' + diff + ' días'; countdownColor = 'bg-blue-500 text-white'; countdownIcon = '&#128197;'; }
        else { countdown = 'Pasada'; countdownColor = 'bg-gray-400 text-white'; countdownIcon = '&#9989;'; }
        
        var cardGradient = idx % 3 === 0 ? 'from-emerald-500 to-teal-500' : idx % 3 === 1 ? 'from-blue-500 to-cyan-500' : 'from-purple-500 to-violet-500';
        
        return '<div onclick="verJornada(' + j.id + ')" ' +
             'class="card-3d bg-white rounded-2xl shadow-md hover:shadow-xl border border-gray-100/80 overflow-hidden cursor-pointer fade-in group" style="animation-delay:' + (idx * .1) + 's">' +
            '<div class="p-5 relative">' +
                '<div class="absolute top-4 right-4">' +
                    '<span class="px-2.5 py-1 rounded-lg text-[10px] font-black ' + countdownColor + ' shadow-sm">' + countdownIcon + ' ' + countdown + '</span>' +
                '</div>' +
                '<div class="flex items-start gap-4">' +
                    '<div class="w-14 h-14 shrink-0 bg-gradient-to-br ' + cardGradient + ' rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200/30">' +
                        '<span class="text-white text-xl font-black">' + j.total_partidos + '</span>' +
                    '</div>' +
                    '<div class="flex-1 min-w-0 pt-0.5">' +
                        '<h3 class="font-black text-gray-800 text-base truncate pr-20 group-hover:text-emerald-700 transition-colors">' + esc(j.nombre) + '</h3>' +
                        '<p class="text-gray-500 text-sm mt-1 flex items-center gap-1.5">' +
                            '<svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>' +
                            '<span class="capitalize truncate">' + esc(dia) + '</span>' +
                        '</p>' +
                        (j.ubicacion ? '<p class="text-gray-400 text-xs mt-1 flex items-center gap-1.5"><svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg><span class="truncate">' + esc(j.ubicacion) + '</span></p>' : '') +
                    '</div>' +
                '</div>' +
                '<div class="flex items-center justify-between mt-4 pt-3.5 border-t border-gray-100">' +
                    '<div class="flex items-center gap-3">' +
                        '<span class="flex items-center gap-1 text-xs font-semibold text-gray-500"><svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' + j.total_inscritos + ' inscritos</span>' +
                        '<span class="flex items-center gap-1 text-xs font-semibold text-gray-500"><svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>' + j.total_partidos + ' partidos</span>' +
                    '</div>' +
                    '<svg class="w-5 h-5 text-gray-300 group-hover:text-emerald-500 group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>' +
                '</div>' +
            '</div>' +
            '<div class="h-1 bg-gradient-to-r ' + cardGradient + ' opacity-80"></div>' +
        '</div>';
    }).join('');
}

async function verJornada(id) {
    currentJornada = id;
    var rJ = await api('jornada_get', { id: id });
    if (!rJ.ok) return;
    currentJornadaData = rJ.data;
    var fecha = new Date(rJ.data.fecha + 'T12:00:00');
    var dia = fecha.toLocaleDateString('es-CR', { weekday: 'long', day: 'numeric', month: 'long' });
    document.getElementById('jornada-nombre').textContent = rJ.data.nombre;
    document.getElementById('jornada-info').textContent = dia;
    document.getElementById('headerTitle').textContent = rJ.data.nombre;
    document.getElementById('headerSub').textContent = dia.toUpperCase();
    if (rJ.data.ubicacion) {
        document.getElementById('jornada-loc-label').style.display = 'flex';
        document.getElementById('jornada-ubicacion').textContent = rJ.data.ubicacion;
    } else { document.getElementById('jornada-loc-label').style.display = 'none'; }
    var hoy = new Date(); hoy.setHours(0,0,0,0);
    var diff = Math.ceil((fecha - hoy) / (1000*60*60*24));
    var cdEl = document.getElementById('jornada-countdown');
    if (diff === 0) cdEl.innerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-red-100 text-red-700 mt-1.5">&#128308; ¡Hoy es el día!</span>';
    else if (diff === 1) cdEl.innerHTML = '<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-100 text-amber-700 mt-1.5">&#128993; ¡Es mañana!</span>';
    else if (diff > 1) {
        cdEl.innerHTML = '<div class="flex items-center gap-2 mt-2" id="countdown-timer"></div>';
        startCountdown(fecha);
    }
    else cdEl.innerHTML = '';
    showScreen('partidos', 'left');
    currentFiltro = 'todos';
    document.querySelectorAll('.filtro-btn').forEach(function(b) {
        var isT = b.dataset.filtro === 'todos';
        b.className = 'filtro-btn ripple-btn shrink-0 px-4 py-2.5 rounded-full text-sm font-bold transition-all ' + (isT ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200/50' : 'bg-white text-gray-500 border border-gray-200');
    });
    await cargarPartidos();
}

async function cargarPartidos() {
    var r = await api('partidos_list', { jornada_id: currentJornada });
    allPartidos = r.ok ? r.data : [];
    renderPartidos();
}

function renderPartidos() {
    var filtered = currentFiltro === 'todos' ? allPartidos : allPartidos.filter(function(p) { return p.genero === currentFiltro; });
    var c = document.getElementById('partidos-list');
    var countEl = document.getElementById('partidos-count');
    countEl.textContent = filtered.length + ' partido' + (filtered.length !== 1 ? 's' : '') + ' encontrado' + (filtered.length !== 1 ? 's' : '');
    if (!filtered.length) {
        c.innerHTML = '<div class="text-center py-14 fade-in">' +
            '<div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-gray-100 to-gray-50 rounded-full flex items-center justify-center shadow-inner text-4xl">&#127934;</div>' +
            '<p class="text-gray-500 font-bold">' + (allPartidos.length ? 'No hay partidos con este filtro' : 'No hay partidos creados aún') + '</p></div>';
        return;
    }
    c.innerHTML = filtered.map(function(p, idx) {
        var gInfo = { masculino: { icon:'&#9794;', label:'Masculino', bg:'bg-blue-100', text:'text-blue-700', accent:'from-blue-500 to-blue-600', border:'border-blue-200' },
                      femenino:  { icon:'&#9792;', label:'Femenino',  bg:'bg-pink-100', text:'text-pink-700', accent:'from-pink-500 to-pink-600', border:'border-pink-200' },
                      mixto:     { icon:'&#9894;', label:'Mixto',     bg:'bg-purple-100', text:'text-purple-700', accent:'from-purple-500 to-purple-600', border:'border-purple-200' }
        }[p.genero] || { icon:'?', label:p.genero, bg:'bg-gray-100', text:'text-gray-700', accent:'from-gray-500 to-gray-600', border:'border-gray-200' };
        
        var isLleno = p.estado === 'lleno';
        var isCerrado = p.estado === 'cerrado';
        var pctColor = p.porcentaje >= 100 ? 'bg-red-500' : p.porcentaje >= 75 ? 'bg-amber-500' : 'bg-emerald-500';
        var hora = p.hora.substring(0, 5);
        var espacios = p.cupos_jugadores - p.inscritos;

        return '<div class="card-3d bg-white rounded-2xl shadow-md border border-gray-100/80 overflow-hidden fade-in" style="animation-delay:' + (idx * .07) + 's">' +
            '<div class="flex">' +
                '<div class="w-1.5 bg-gradient-to-b ' + gInfo.accent + '"></div>' +
                '<div class="flex-1 p-4">' +
                    '<div class="flex items-start justify-between mb-3">' +
                        '<div class="min-w-0 flex-1">' +
                            '<div class="flex items-center gap-2 mb-1.5">' +
                                '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-black ' + gInfo.bg + ' ' + gInfo.text + '">' + gInfo.icon + ' ' + gInfo.label + '</span>' +
                                (p.cancha ? '<span class="text-gray-400 text-[11px] font-medium flex items-center gap-0.5"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>' + esc(p.cancha) + '</span>' : '') +
                            '</div>' +
                            '<h3 class="font-black text-gray-800 text-base">' + esc(p.categoria) + '</h3>' +
                            '<div class="flex items-center gap-1.5 mt-1">' +
                                '<svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' +
                                '<span class="text-gray-600 text-sm font-bold">' + hora + '</span>' +
                            '</div>' +
                        '</div>' +
                        '<div class="shrink-0 ml-3">' +
                            (isLleno 
                                ? '<span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[11px] font-black bg-red-100 text-red-700 ring-1 ring-red-200">LLENO</span>' 
                                : '<span class="inline-flex items-center px-3 py-1.5 rounded-xl text-[11px] font-black bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200">' + (espacios > 0 ? espacios + ' libre' + (espacios>1?'s':'') : 'ABIERTO') + '</span>') +
                        '</div>' +
                    '</div>' +
                    '<div class="mb-4">' +
                        '<div class="flex justify-between text-xs mb-1.5">' +
                            '<span class="text-gray-500 font-medium">' + p.inscritos + ' / ' + p.cupos_jugadores + ' jugadores</span>' +
                            '<span class="font-black ' + (p.porcentaje >= 100 ? 'text-red-600' : p.porcentaje >= 75 ? 'text-amber-600' : 'text-emerald-600') + '">' + p.porcentaje + '%</span>' +
                        '</div>' +
                        '<div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">' +
                            '<div class="h-full ' + pctColor + ' rounded-full progress-bar transition-all" style="width:' + p.porcentaje + '%"></div>' +
                        '</div>' +
                    '</div>' +
                    (p.en_espera > 0 ? '<div class="flex items-center gap-1.5 mb-3 px-2.5 py-1.5 bg-amber-50 rounded-lg border border-amber-100"><svg class="w-3.5 h-3.5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="text-[11px] text-amber-700 font-bold">' + p.en_espera + ' en lista de espera</span></div>' : '') +
                    (parseInt(p.reservas) > 0 ? '<div class="flex items-center gap-1.5 mb-3 px-2.5 py-1.5 bg-orange-50 rounded-lg border border-orange-100"><svg class="w-3.5 h-3.5 text-orange-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg><span class="text-[11px] text-orange-700 font-bold">' + p.reservas + ' reserva' + (parseInt(p.reservas)>1?'s':'') + '</span></div>' : '') +
                    '<div class="flex gap-2.5">' +
                        '<button onclick="event.stopPropagation();verDetalle(' + p.id + ')" class="ripple-btn flex-1 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl active:scale-95 transition-all">Ver detalle</button>' +
                        (!isCerrado ? '<button onclick="event.stopPropagation();abrirInscripcion(' + p.id + ', \'' + esc(p.categoria).replace(/'/g, "\\'") + '\', \'' + hora + '\', ' + isLleno + ')" class="ripple-btn flex-1 py-2.5 text-sm font-black text-white ' + (isLleno ? 'bg-amber-500 shadow-amber-200/50' : 'bg-gradient-to-r from-emerald-500 to-teal-500 shadow-emerald-200/50') + ' rounded-xl shadow-md active:scale-95 transition-all">' + (isLleno ? '&#9203; Espera' : '&#9889; Inscribirme') + '</button>' : '<span class="flex-1 py-2.5 text-sm font-bold text-center text-gray-400 bg-gray-50 rounded-xl">Cerrado</span>') +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    }).join('');
}

function filtrarPartidos(filtro) {
    currentFiltro = filtro;
    document.querySelectorAll('.filtro-btn').forEach(function(b) {
        var isActive = b.dataset.filtro === filtro;
        b.className = 'filtro-btn ripple-btn shrink-0 px-4 py-2.5 rounded-full text-sm font-bold transition-all ' + (isActive ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200/50' : 'bg-white text-gray-500 border border-gray-200');
    });
    renderPartidos();
}

// ===== AVATAR HELPERS =====
var avatarPalettes = [
    ['#059669','#10b981'], ['#0284c7','#38bdf8'], ['#7c3aed','#a78bfa'], ['#db2777','#f472b6'],
    ['#ea580c','#fb923c'], ['#ca8a04','#facc15'], ['#0891b2','#22d3ee'], ['#4f46e5','#818cf8']
];
function getAvatar(name) {
    var initials = (name || '?').split(' ').map(function(w){return w[0]}).join('').substring(0,2).toUpperCase();
    var hash = 0;
    for (var c = 0; c < name.length; c++) hash = name.charCodeAt(c) + ((hash << 5) - hash);
    var pal = avatarPalettes[Math.abs(hash) % avatarPalettes.length];
    return '<div class="avatar" style="--a1:'+pal[0]+';--a2:'+pal[1]+';background:linear-gradient(135deg,'+pal[0]+','+pal[1]+');color:white">' + esc(initials) + '</div>';
}

async function verDetalle(partidoId) {
    showScreen('detalle', 'left');
    var results = await Promise.all([
        api('inscripciones_list', { partido_id: partidoId }),
        api('espera_list', { partido_id: partidoId })
    ]);
    var rInsc = results[0], rEspera = results[1];
    var partido = allPartidos.find(function(p) { return p.id == partidoId; });
    if (!partido) return;
    var inscritos = rInsc.ok ? rInsc.data : [];
    var espera = rEspera.ok ? rEspera.data : [];
    var c = document.getElementById('detalle-content');
    var hora = partido.hora.substring(0, 5);
    var gInfo = { masculino: { icon:'&#9794;', label:'Masculino', bg:'bg-blue-100', text:'text-blue-700', accent:'from-blue-500 to-blue-600' },
                  femenino:  { icon:'&#9792;', label:'Femenino',  bg:'bg-pink-100', text:'text-pink-700', accent:'from-pink-500 to-pink-600' },
                  mixto:     { icon:'&#9894;', label:'Mixto',     bg:'bg-purple-100', text:'text-purple-700', accent:'from-purple-500 to-purple-600' }
    }[partido.genero] || { icon:'?', label:partido.genero, bg:'bg-gray-100', text:'text-gray-700', accent:'from-gray-500 to-gray-600' };

    var html = '<div class="bg-white rounded-2xl shadow-md border border-gray-100/80 overflow-hidden mb-5 slide-up">' +
        '<div class="h-2 bg-gradient-to-r ' + gInfo.accent + '"></div>' +
        '<div class="p-5">' +
        '<div class="flex items-start justify-between mb-4">' +
            '<div>' +
                '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-black ' + gInfo.bg + ' ' + gInfo.text + '">' + gInfo.icon + ' ' + gInfo.label + '</span>' +
                '<h3 class="font-black text-xl text-gray-800 mt-2">' + esc(partido.categoria) + '</h3>' +
            '</div>' +
            (partido.estado === 'lleno' 
                ? '<span class="px-3 py-1.5 rounded-xl text-[11px] font-black bg-red-100 text-red-700 ring-1 ring-red-200">LLENO</span>'
                : '<span class="px-3 py-1.5 rounded-xl text-[11px] font-black bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200">ABIERTO</span>') +
        '</div>' +
        '<div class="flex flex-wrap gap-2.5 text-sm text-gray-500">' +
            '<span class="flex items-center gap-1.5 bg-gray-50 px-3 py-2 rounded-xl border border-gray-100"><svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="font-bold">' + hora + '</span></span>' +
            (partido.cancha ? '<span class="flex items-center gap-1.5 bg-gray-50 px-3 py-2 rounded-xl border border-gray-100">&#128205; ' + esc(partido.cancha) + '</span>' : '') +
            '<span class="flex items-center gap-1.5 bg-gray-50 px-3 py-2 rounded-xl border border-gray-100">&#128101; ' + partido.cupos + ' parejas</span>' +
        '</div>' +
        '<div class="mt-5">' +
            '<div class="flex justify-between text-xs mb-2">' +
                '<span class="text-gray-500 font-semibold">Ocupación</span>' +
                '<span class="font-black ' + (partido.porcentaje >= 100 ? 'text-red-600' : 'text-emerald-600') + '">' + partido.inscritos + '/' + partido.cupos_jugadores + '</span>' +
            '</div>' +
            '<div class="h-3 bg-gray-100 rounded-full overflow-hidden">' +
                '<div class="h-full ' + (partido.porcentaje >= 100 ? 'bg-red-500' : 'bg-emerald-500') + ' rounded-full progress-bar" style="width:' + partido.porcentaje + '%"></div>' +
            '</div>' +
        '</div></div></div>';

    var confirmados = inscritos.filter(function(i) { return i.estado === 'confirmado' && !parseInt(i.es_reserva); });
    var reservas = inscritos.filter(function(i) { return i.estado === 'confirmado' && parseInt(i.es_reserva); });
    html += '<div class="flex items-center justify-between mb-3">' +
        '<h4 class="font-bold text-gray-700 flex items-center gap-2">' +
            '<span class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center"><svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></span>' +
            '<span>Inscritos</span></h4>' +
        '<span class="text-xs font-black bg-emerald-100 text-emerald-700 px-3 py-1 rounded-lg">' + confirmados.length + '</span></div>';

    if (!confirmados.length) {
        html += '<div class="bg-gradient-to-br from-gray-50 to-gray-100/50 rounded-2xl p-8 text-center mb-5 border border-gray-100">' +
            '<div class="w-20 h-20 mx-auto mb-4 bg-white rounded-full flex items-center justify-center text-3xl shadow-inner">&#127992;</div>' +
            '<p class="text-gray-600 font-bold">Aún no hay inscritos</p>' +
            '<p class="text-gray-400 text-sm mt-1">¡Sé el primero en anotarte!</p></div>';
    } else {
        var parejas = [], solosSinPareja = [], processed = {};
        for (var k = 0; k < confirmados.length; k++) {
            var i = confirmados[k];
            if (processed[i.id]) continue;
            processed[i.id] = true;
            if (i.tipo === 'pareja') {
                parejas.push({ j1: i.nombre, j2: i.pareja_nombre || '—', auto: false });
            } else if (i.pareja_auto_id) {
                var par = confirmados.find(function(x) { return x.id == i.pareja_auto_id; });
                if (par) { processed[par.id] = true; parejas.push({ j1: i.nombre, j2: par.nombre, auto: true }); }
                else solosSinPareja.push(i);
            } else { solosSinPareja.push(i); }
        }
        html += '<div class="space-y-2.5 mb-5">';
        var num = 1;
        for (var pi = 0; pi < parejas.length; pi++) {
            var pp = parejas[pi];
            html += '<div class="bg-white rounded-xl border border-gray-100 p-3.5 flex items-center gap-3 slide-up shadow-sm" style="animation-delay:' + (num * .06) + 's">' +
                '<div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-500 text-white rounded-xl flex items-center justify-center text-sm font-black shadow-sm">' + num + '</div>' +
                '<div class="flex items-center gap-2 flex-1 min-w-0">' +
                    getAvatar(pp.j1) +
                    '<div class="min-w-0 flex-1">' +
                        '<div class="text-sm font-bold text-gray-800 truncate">' + esc(pp.j1) + ' <span class="text-emerald-500">&amp;</span> ' + esc(pp.j2) + '</div>' +
                        (pp.auto ? '<span class="text-[10px] text-emerald-600 font-semibold">&#10024; Auto-emparejados</span>' : '<span class="text-[10px] text-blue-500 font-semibold">&#128101; Pareja registrada</span>') +
                    '</div>' +
                '</div>' +
                '<span class="text-[10px] bg-emerald-50 text-emerald-600 px-2 py-1 rounded-lg font-black shrink-0">Pareja</span>' +
            '</div>';
            num++;
        }
        for (var si = 0; si < solosSinPareja.length; si++) {
            var s = solosSinPareja[si];
            html += '<div class="bg-amber-50/50 rounded-xl border border-amber-100 p-3.5 flex items-center gap-3">' +
                getAvatar(s.nombre) +
                '<div class="flex-1 min-w-0">' +
                    '<div class="text-sm font-bold text-gray-800 truncate">' + esc(s.nombre) + '</div>' +
                    '<span class="text-[10px] text-amber-600 font-semibold">&#9203; Esperando pareja</span>' +
                '</div>' +
                '<span class="text-[10px] bg-amber-100 text-amber-700 px-2 py-1 rounded-lg font-black shrink-0">Solo</span>' +
            '</div>';
        }
        html += '</div>';
    }

    if (espera.length) {
        html += '<div class="flex items-center justify-between mb-3 mt-6">' +
            '<h4 class="font-bold text-gray-700 flex items-center gap-2">' +
                '<span class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center"><svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>' +
                '<span>Lista de espera</span></h4>' +
            '<span class="text-xs font-black bg-amber-100 text-amber-700 px-3 py-1 rounded-lg">' + espera.length + '</span>' +
        '</div><div class="space-y-2 mb-5">';
        for (var ei = 0; ei < espera.length; ei++) {
            html += '<div class="bg-gray-50 rounded-xl border border-gray-100 p-3 flex items-center gap-3">' +
                getAvatar(espera[ei].nombre) +
                '<span class="text-sm text-gray-700 font-semibold">' + esc(espera[ei].nombre) + '</span>' +
                '<span class="ml-auto text-[10px] bg-gray-200 text-gray-600 px-2 py-0.5 rounded-lg font-bold">#' + (ei+1) + '</span>' +
            '</div>';
        }
        html += '</div>';
    }

    if (reservas.length) {
        html += '<div class="flex items-center justify-between mb-3 mt-6">' +
            '<h4 class="font-bold text-gray-700 flex items-center gap-2">' +
                '<span class="w-8 h-8 bg-orange-100 rounded-xl flex items-center justify-center"><svg class="w-4 h-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg></span>' +
                '<span>Reservas</span></h4>' +
            '<span class="text-xs font-black bg-orange-100 text-orange-700 px-3 py-1 rounded-lg">' + reservas.length + '</span>' +
        '</div><div class="space-y-2 mb-5">';
        for (var ri = 0; ri < reservas.length; ri++) {
            html += '<div class="bg-orange-50/50 rounded-xl border border-orange-100 p-3 flex items-center gap-3">' +
                getAvatar(reservas[ri].nombre) +
                '<span class="text-sm text-gray-700 font-semibold">' + esc(reservas[ri].nombre) + '</span>' +
                '<span class="ml-auto text-[10px] bg-orange-100 text-orange-700 px-2 py-1 rounded-lg font-black">&#128165; Reserva</span>' +
            '</div>';
        }
        html += '</div>';
    }

    if (partido.estado !== 'cerrado') {
        html += '<button onclick="abrirInscripcion(' + partido.id + ', \'' + esc(partido.categoria).replace(/'/g, "\\'") + '\', \'' + hora + '\', ' + (partido.estado === 'lleno') + ')" ' +
            'class="ripple-btn w-full mt-4 py-4 text-sm font-black text-white ' + (partido.estado === 'lleno' ? 'bg-amber-500 shadow-amber-200/50' : 'bg-gradient-to-r from-emerald-500 to-teal-500 shadow-emerald-200/50') + ' rounded-2xl shadow-lg active:scale-[.97] transition-transform tracking-wide">' +
            (partido.estado === 'lleno' ? '&#9203; Unirse a lista de espera' : '&#9889; ¡INSCRIBIRME AHORA!') + '</button>';
    }
    c.innerHTML = html;
}

// ===== INSCRIPCIÓN =====
function abrirInscripcion(partidoId, categoria, hora, lleno) {
    document.getElementById('inp-partido-id').value = partidoId;
    document.getElementById('modal-title').textContent = lleno ? 'Lista de espera' : 'Inscripción';
    document.getElementById('modal-subtitle').textContent = categoria + ' \u2022 ' + hora;
    document.getElementById('form-inscripcion').reset();
    document.getElementById('campos-pareja').classList.add('hidden');
    document.getElementById('inp-es-reserva').value = '0';
    
    // Reset stepper
    document.getElementById('step2-dot').className = 'w-6 h-6 rounded-full bg-gray-200 text-gray-500 text-[10px] font-bold flex items-center justify-center';
    document.getElementById('step2-label').className = 'text-[11px] font-semibold text-gray-400';
    document.getElementById('step3-dot').className = 'w-6 h-6 rounded-full bg-gray-200 text-gray-500 text-[10px] font-bold flex items-center justify-center';
    document.getElementById('step3-label').className = 'text-[11px] font-semibold text-gray-400';
    
    var icon = document.getElementById('modal-icon');
    if (lleno) {
        icon.className = 'w-11 h-11 rounded-2xl bg-amber-100 flex items-center justify-center';
        icon.innerHTML = '<svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
    } else {
        icon.className = 'w-11 h-11 rounded-2xl bg-emerald-100 flex items-center justify-center';
        icon.innerHTML = '<svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>';
    }
    var btn = document.getElementById('btn-inscribir');
    btn.innerHTML = lleno ? '&#9203; Unirme a la lista' : '&#9889; ¡INSCRIBIRME!';
    btn.disabled = false;
    btn.className = 'ripple-btn w-full mt-6 py-4 text-white font-bold rounded-2xl shadow-lg active:scale-[.97] transition-transform text-sm tracking-wide ' + (lleno ? 'bg-amber-500 shadow-amber-200/50' : 'bg-gradient-to-r from-emerald-500 to-teal-500 shadow-emerald-200/50');
    document.getElementById('modal-overlay').classList.remove('hidden');
    requestAnimationFrame(function() { document.getElementById('modal-inscripcion').classList.remove('closed'); });
}

function cerrarModal() {
    document.getElementById('modal-inscripcion').classList.add('closed');
    setTimeout(function() { document.getElementById('modal-overlay').classList.add('hidden'); }, 350);
}

function togglePareja() {
    var tipo = document.querySelector('input[name="tipo"]:checked').value;
    document.getElementById('campos-pareja').classList.toggle('hidden', tipo !== 'pareja');
    // Handle reserva
    var isReserva = tipo === 'reserva';
    document.getElementById('inp-es-reserva').value = isReserva ? '1' : '0';
    // If reserva, internally submit as solo
    var btn = document.getElementById('btn-inscribir');
    if (isReserva) {
        btn.innerHTML = '&#128165; Registrarme como reserva';
        btn.className = 'ripple-btn w-full mt-6 py-4 text-white font-bold rounded-2xl shadow-lg active:scale-[.97] transition-transform text-sm tracking-wide bg-gradient-to-r from-amber-500 to-orange-500 shadow-amber-200/50';
    }
    // Update stepper
    document.getElementById('step2-dot').className = 'w-6 h-6 rounded-full bg-emerald-600 text-white text-[10px] font-bold flex items-center justify-center';
    document.getElementById('step2-label').className = 'text-[11px] font-semibold text-emerald-700';
}

function validateField(inp) {
    var icon = inp.parentElement.querySelector('.validate-icon');
    if (icon) { icon.style.display = inp.value.trim().length >= 2 ? 'block' : 'none'; }
}

async function enviarInscripcion(e) {
    e.preventDefault();
    var btn = document.getElementById('btn-inscribir');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-5 h-5 animate-spin inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Procesando...';
    
    // Update stepper
    document.getElementById('step3-dot').className = 'w-6 h-6 rounded-full bg-emerald-600 text-white text-[10px] font-bold flex items-center justify-center';
    document.getElementById('step3-label').className = 'text-[11px] font-semibold text-emerald-700';
    
    var fd = new FormData(e.target);
    var params = {};
    fd.forEach(function(v, k) { params[k] = v; });
    // If tipo is "reserva", send as "solo" + es_reserva=1
    if (params.tipo === 'reserva') {
        params.tipo = 'solo';
        params.es_reserva = '1';
    }
    try {
        var r = await api('inscribir', params, 'POST');
        cerrarModal();
        if (r.ok) {
            lastSuccessMsg = r.mensaje || '¡Inscripción exitosa!';
            if (r.en_espera) {
                toast(lastSuccessMsg, 'amber');
            } else if (r.es_reserva) {
                toast(lastSuccessMsg, 'amber');
            } else {
                launchConfetti();
                showSuccessModal(lastSuccessMsg);
            }
            await cargarPartidos();
        } else {
            toast(r.error || 'Error al inscribir', 'red');
        }
    } catch (err) {
        toast('Error de conexión. Intenta de nuevo.', 'red');
    }
    btn.disabled = false;
    btn.innerHTML = '&#9889; ¡INSCRIBIRME!';
}

function showSuccessModal(msg) {
    document.getElementById('success-msg').textContent = msg;
    document.getElementById('modal-success').classList.remove('hidden');
}
function cerrarSuccess() { document.getElementById('modal-success').classList.add('hidden'); }
function compartirWhatsApp() {
    var jornadaName = currentJornadaData ? currentJornadaData.nombre : 'Interliga';
    var text = '\uD83C\uDFBE ¡Me inscribí en la ' + jornadaName + '! ' + lastSuccessMsg + '\n\n\uD83D\uDCF2 Inscríbete: ' + location.href;
    window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
}

// ===== BUSCAR =====
function buscarMisInscripciones() {
    document.getElementById('modal-buscar').classList.remove('hidden');
    document.getElementById('buscar-nombre').focus();
    document.getElementById('buscar-resultados').innerHTML = '';
}

async function ejecutarBusqueda() {
    var nombre = document.getElementById('buscar-nombre').value.trim();
    if (!nombre || nombre.length < 2) { toast('Escribe al menos 2 caracteres', 'amber'); return; }
    var c = document.getElementById('buscar-resultados');
    c.innerHTML = '<div class="text-center py-4"><div class="skeleton h-12 w-full mb-2"></div><div class="skeleton h-12 w-full"></div></div>';
    var rJ = await api('jornadas_list');
    if (!rJ.ok) { c.innerHTML = '<p class="text-red-400 text-sm text-center">Error al buscar</p>'; return; }
    var resultados = [];
    for (var ji = 0; ji < rJ.data.length; ji++) {
        var j = rJ.data[ji];
        var rP = await api('partidos_list', { jornada_id: j.id });
        if (!rP.ok) continue;
        for (var pi = 0; pi < rP.data.length; pi++) {
            var p = rP.data[pi];
            var rI = await api('inscripciones_list', { partido_id: p.id });
            if (!rI.ok) continue;
            for (var ii = 0; ii < rI.data.length; ii++) {
                var ins = rI.data[ii];
                if (ins.estado === 'confirmado' && (ins.nombre.toLowerCase().indexOf(nombre.toLowerCase()) >= 0 || (ins.pareja_nombre && ins.pareja_nombre.toLowerCase().indexOf(nombre.toLowerCase()) >= 0))) {
                    resultados.push({ jornada: j.nombre, fecha: j.fecha, categoria: p.categoria, hora: p.hora.substring(0,5), nombre: ins.nombre, tipo: ins.tipo, pareja: ins.pareja_nombre });
                }
            }
        }
    }
    if (!resultados.length) {
        c.innerHTML = '<div class="text-center py-8"><div class="w-16 h-16 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center text-2xl">&#128269;</div><p class="text-gray-500 font-semibold text-sm">Sin resultados para "' + esc(nombre) + '"</p></div>';
        return;
    }
    c.innerHTML = resultados.map(function(r) {
        return '<div class="bg-emerald-50 rounded-xl p-3.5 border border-emerald-100">' +
            '<div class="flex items-center justify-between">' +
                '<div class="min-w-0 flex-1">' +
                    '<p class="text-sm font-bold text-gray-800 truncate">' + esc(r.jornada) + '</p>' +
                    '<p class="text-xs text-gray-500 mt-0.5">' + esc(r.categoria) + ' \u2022 ' + r.hora + ' \u2022 ' + r.fecha + '</p>' +
                    (r.pareja ? '<p class="text-xs text-emerald-600 mt-0.5 font-medium">&#128101; Pareja: ' + esc(r.pareja) + '</p>' : '') +
                '</div>' +
                '<span class="text-[10px] font-black px-2.5 py-1 rounded-lg ' + (r.tipo==='pareja' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700') + '">' + r.tipo + '</span>' +
            '</div>' +
        '</div>';
    }).join('');
}

// ===== TOAST =====
function toast(msg, color) {
    color = color || 'green';
    var t = document.getElementById('toast');
    var inner = document.getElementById('toast-inner');
    var prog = document.getElementById('toast-progress');
    var styles = { green: 'bg-emerald-600 text-white', red: 'bg-red-600 text-white', amber: 'bg-amber-500 text-white' };
    var icons = {
        green: '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>',
        red: '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>',
        amber: '<svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.502-1.32.707-2.026L13.414 4.5a2 2 0 00-2.828 0L3.207 16.974c-.795.706-.347 2.026.707 2.026z"/></svg>'
    };
    t.className = 'fixed top-20 left-1/2 -translate-x-1/2 z-[60] rounded-2xl shadow-2xl text-sm font-semibold transition-all duration-300 max-w-[85vw] text-center overflow-hidden ' + (styles[color] || styles.green);
    inner.innerHTML = (icons[color] || '') + '<span>' + esc(msg) + '</span>';
    prog.style.display = 'block';
    prog.style.animation = 'none';
    requestAnimationFrame(function() { prog.style.animation = 'toastProgress 3.5s linear forwards'; });
    t.style.opacity = '1';
    t.style.transform = 'translate(-50%, 0)';
    t.style.pointerEvents = 'auto';
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(function() { t.style.opacity = '0'; t.style.transform = 'translate(-50%, -1rem)'; t.style.pointerEvents = 'none'; prog.style.display = 'none'; }, 3500);
}

function esc(s) { if (!s) return ''; var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

// ===== PARALLAX HEADER =====
(function() {
    var header = document.getElementById('mainHeader');
    var lastScroll = 0;
    window.addEventListener('scroll', function() {
        var y = window.scrollY;
        if (y > 60) {
            header.style.boxShadow = '0 4px 20px rgba(0,0,0,.15)';
        } else {
            header.style.boxShadow = '';
        }
        lastScroll = y;
    }, { passive: true });
})();

// ===== CLASIFICACIÓN =====
var clasifJornadasLoaded = false;
async function cargarClasifJornadas() {
    if (clasifJornadasLoaded) return;
    var r = await api('jornadas_list');
    if (!r.ok) return;
    var sel = document.getElementById('clasif-jornada-filter');
    sel.innerHTML = '<option value="">General (todas las jornadas)</option>' +
        r.data.map(function(j) { return '<option value="' + j.id + '">' + esc(j.nombre) + ' (' + j.fecha + ')</option>'; }).join('');
    clasifJornadasLoaded = true;
}

async function cargarClasificacion() {
    var jornadaId = document.getElementById('clasif-jornada-filter').value;
    var params = jornadaId ? { jornada_id: jornadaId } : {};
    var results = await Promise.all([
        api('clasificacion', params),
        api('resultados_list', params)
    ]);
    var rC = results[0], rR = results[1];
    renderClasificacion(rC.ok ? rC.data : []);
    renderResultadosRecientes(rR.ok ? rR.data : []);
}

function renderClasificacion(tabla) {
    var c = document.getElementById('clasificacion-table');
    var hasData = tabla.some(function(t) { return t.jj > 0; });
    if (!hasData) {
        c.innerHTML = '<div class="text-center py-14 fade-in">' +
            '<div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-gray-100 to-gray-50 rounded-full flex items-center justify-center shadow-inner text-4xl">&#127942;</div>' +
            '<p class="text-gray-500 font-bold">Sin resultados aún</p>' +
            '<p class="text-gray-400 text-sm mt-1">Los resultados aparecerán aquí</p></div>';
        return;
    }
    var filtrado = tabla.filter(function(t) { return t.jj > 0; });
    var html = '<div class="rounded-2xl overflow-hidden shadow-md border border-gray-100 slide-up">';
    html += '<div class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 px-4 py-3 grid grid-cols-12 gap-1 text-[10px] font-black text-white/80 tracking-wide uppercase">' +
        '<div class="col-span-1 text-center">#</div>' +
        '<div class="col-span-5">Equipo</div>' +
        '<div class="col-span-1 text-center">JG</div>' +
        '<div class="col-span-1 text-center">JP</div>' +
        '<div class="col-span-1 text-center">PG</div>' +
        '<div class="col-span-1 text-center">PP</div>' +
        '<div class="col-span-2 text-center">DIF</div>' +
    '</div>';
    for (var i = 0; i < filtrado.length; i++) {
        var t = filtrado[i];
        var pos = i + 1;
        var bgClass = i % 2 === 0 ? 'bg-white' : 'bg-gray-50';
        var posClass = '';
        if (pos === 1) posClass = 'bg-yellow-400 text-white';
        else if (pos === 2) posClass = 'bg-gray-400 text-white';
        else if (pos === 3) posClass = 'bg-amber-600 text-white';
        else posClass = 'bg-gray-200 text-gray-600';
        var difColor = t.dif > 0 ? 'text-emerald-600' : t.dif < 0 ? 'text-red-500' : 'text-gray-500';
        html += '<div class="' + bgClass + ' px-4 py-3 grid grid-cols-12 gap-1 items-center border-b border-gray-100/80 fade-in" style="animation-delay:' + (i * .04) + 's">' +
            '<div class="col-span-1 flex justify-center"><span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-black ' + posClass + '">' + pos + '</span></div>' +
            '<div class="col-span-5 flex items-center gap-2 min-w-0"><span class="text-base shrink-0">' + (t.emoji || '🏓') + '</span><span class="text-xs font-bold text-gray-800 truncate">' + esc(t.nombre) + '</span></div>' +
            '<div class="col-span-1 text-center text-xs font-black text-gray-800">' + t.jg + '</div>' +
            '<div class="col-span-1 text-center text-xs font-medium text-gray-500">' + t.jp + '</div>' +
            '<div class="col-span-1 text-center text-xs font-bold text-gray-700">' + t.pg + '</div>' +
            '<div class="col-span-1 text-center text-xs font-medium text-gray-500">' + t.pp + '</div>' +
            '<div class="col-span-2 text-center text-xs font-black ' + difColor + '">' + (t.dif > 0 ? '+' : '') + t.dif + '</div>' +
        '</div>';
    }
    html += '</div>';
    html += '<div class="flex flex-wrap gap-3 mt-4 text-[10px] text-gray-400 font-medium px-1">' +
        '<span>JG = Juegos ganados</span><span>JP = Juegos perdidos</span><span>PG = Puntos a favor</span><span>PP = Puntos en contra</span><span>DIF = Diferencia</span>' +
    '</div>';
    c.innerHTML = html;
}

function renderResultadosRecientes(resultados) {
    var c = document.getElementById('resultados-recientes');
    if (!resultados.length) { c.innerHTML = ''; return; }
    var limit = resultados.slice(0, 10);
    var html = '<div class="flex items-center justify-between mb-3">' +
        '<h3 class="text-gray-800 font-extrabold text-base flex items-center gap-2">' +
            '<span class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center"><svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></span>' +
            'Últimos Resultados</h3>' +
        '<span class="text-xs font-black bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-lg">' + resultados.length + '</span></div>';
    html += '<div class="space-y-2.5">';
    for (var i = 0; i < limit.length; i++) {
        var r = limit[i];
        var localWin = parseInt(r.puntos_local) > parseInt(r.puntos_visitante);
        var visitanteWin = parseInt(r.puntos_visitante) > parseInt(r.puntos_local);
        html += '<div class="bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm fade-in" style="animation-delay:' + (i * .05) + 's">' +
            '<div class="text-[10px] text-gray-400 font-semibold mb-2">' + esc(r.jornada_nombre) + '</div>' +
            '<div class="flex items-center gap-2">' +
                '<div class="flex-1 flex items-center gap-2 min-w-0 ' + (localWin ? '' : 'opacity-60') + '">' +
                    '<span class="text-base shrink-0">' + (r.emoji_local || '🏓') + '</span>' +
                    '<span class="text-xs font-bold text-gray-800 truncate">' + esc(r.equipo_local) + '</span>' +
                '</div>' +
                '<div class="flex items-center gap-1.5 shrink-0 px-3 py-1.5 rounded-xl ' + (localWin ? 'bg-emerald-100' : visitanteWin ? 'bg-red-50' : 'bg-gray-100') + '">' +
                    '<span class="text-sm font-black ' + (localWin ? 'text-emerald-700' : 'text-gray-600') + '">' + r.puntos_local + '</span>' +
                    '<span class="text-gray-300 text-xs font-bold">-</span>' +
                    '<span class="text-sm font-black ' + (visitanteWin ? 'text-emerald-700' : 'text-gray-600') + '">' + r.puntos_visitante + '</span>' +
                '</div>' +
                '<div class="flex-1 flex items-center gap-2 justify-end min-w-0 ' + (visitanteWin ? '' : 'opacity-60') + '">' +
                    '<span class="text-xs font-bold text-gray-800 truncate text-right">' + esc(r.equipo_visitante) + '</span>' +
                    '<span class="text-base shrink-0">' + (r.emoji_visitante || '🏓') + '</span>' +
                '</div>' +
            '</div>' +
            (r.observaciones ? '<div class="text-[10px] text-gray-400 mt-2 italic">' + esc(r.observaciones) + '</div>' : '') +
        '</div>';
    }
    html += '</div>';
    c.innerHTML = html;
}

// ===== COUNTDOWN TIMER =====
var countdownInterval = null;
function startCountdown(targetDate) {
    if (countdownInterval) clearInterval(countdownInterval);
    function update() {
        var now = new Date();
        var diff = targetDate - now;
        if (diff <= 0) { clearInterval(countdownInterval); return; }
        var days = Math.floor(diff / (1000*60*60*24));
        var hours = Math.floor((diff % (1000*60*60*24)) / (1000*60*60));
        var mins = Math.floor((diff % (1000*60*60)) / (1000*60));
        var el = document.getElementById('countdown-timer');
        if (!el) { clearInterval(countdownInterval); return; }
        el.innerHTML = '<span class="inline-flex flex-col items-center px-2.5 py-1.5 rounded-xl bg-blue-100 dark:bg-blue-900/40 min-w-[48px]"><span class="text-lg font-black text-blue-700 dark:text-blue-300">' + days + '</span><span class="text-[9px] font-bold text-blue-500 uppercase">Días</span></span>' +
            '<span class="text-blue-300 font-black text-lg">:</span>' +
            '<span class="inline-flex flex-col items-center px-2.5 py-1.5 rounded-xl bg-blue-100 dark:bg-blue-900/40 min-w-[48px]"><span class="text-lg font-black text-blue-700 dark:text-blue-300">' + String(hours).padStart(2,'0') + '</span><span class="text-[9px] font-bold text-blue-500 uppercase">Hrs</span></span>' +
            '<span class="text-blue-300 font-black text-lg">:</span>' +
            '<span class="inline-flex flex-col items-center px-2.5 py-1.5 rounded-xl bg-blue-100 dark:bg-blue-900/40 min-w-[48px]"><span class="text-lg font-black text-blue-700 dark:text-blue-300">' + String(mins).padStart(2,'0') + '</span><span class="text-[9px] font-bold text-blue-500 uppercase">Min</span></span>';
    }
    update();
    countdownInterval = setInterval(update, 60000);
}

// ===== INIT =====
cargarJornadas();

// ===== DARK MODE =====
function toggleDarkMode() {
    var html = document.getElementById('html-root');
    var isDark = html.classList.toggle('dark');
    localStorage.setItem('darkMode', isDark);
    var icon = document.getElementById('darkIcon');
    if (isDark) {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>';
    } else {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>';
    }
}
// Init dark icon state
(function(){
    if (localStorage.getItem('darkMode') === 'true') {
        var icon = document.getElementById('darkIcon');
        if (icon) icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>';
    }
})();

// ===== SERVICE WORKER =====
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js').catch(function(){});
}
</script>
</body>
</html>
