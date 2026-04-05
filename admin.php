<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="es" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1e293b">
    <title>Admin - Interliga CR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] } } }
        }
        if (localStorage.getItem('darkMode') === 'true') { document.getElementById('html-root').classList.add('dark'); }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', system-ui, sans-serif; -webkit-tap-highlight-color: transparent; }
        body { overscroll-behavior-y: contain; }
        
        /* ===== ANIMATIONS ===== */
        .fade-in { animation: fadeIn .3s ease; }
        .slide-up { animation: slideUp .4s cubic-bezier(.4,0,.2,1); }
        .pop-in { animation: popIn .5s cubic-bezier(.175,.885,.32,1.275); }
        @keyframes fadeIn { from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)} }
        @keyframes slideUp { from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)} }
        @keyframes popIn { from{opacity:0;transform:scale(.8)}to{opacity:1;transform:scale(1)} }
        @keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
        @keyframes ripple { to{transform:scale(4);opacity:0} }
        @keyframes countUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        @keyframes gradientShift { 0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%} }
        @keyframes float1 { 0%,100%{transform:translate(0,0) rotate(0deg)} 33%{transform:translate(12px,-15px) rotate(120deg)} 66%{transform:translate(-8px,8px) rotate(240deg)} }
        @keyframes pulse-ring { 0%{transform:scale(.8);opacity:1} 100%{transform:scale(2.5);opacity:0} }
        @keyframes toastProgress { from{width:100%} to{width:0%} }
        
        .skeleton { background: linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 50%,#f3f4f6 75%); background-size:200% 100%; animation: shimmer 1.5s infinite; border-radius: 12px; }
        .bottom-sheet { transition: transform .35s cubic-bezier(.4,0,.2,1); }
        .bottom-sheet.closed { transform: translateY(100%); }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn .3s ease; }
        .card-press { transition: transform .15s, box-shadow .15s; }
        .card-press:active { transform: scale(.97); }
        
        /* ===== RIPPLE ===== */
        .ripple-btn { position:relative; overflow:hidden; }
        .ripple-btn .ripple-effect { position:absolute; border-radius:50%; background:rgba(255,255,255,.35);
            transform:scale(0); animation: ripple .6s linear; pointer-events:none; }
        
        /* ===== GLASSMORPHISM ===== */
        .glass { background: rgba(255,255,255,.08); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        .glass-card { background: rgba(255,255,255,.88); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,.6); }
        
        /* ===== HEADER ===== */
        .admin-header { background: linear-gradient(135deg, #0f172a 0%, #1e293b 40%, #334155 100%);
            position:relative; overflow:hidden; }
        .admin-header .particle { position:absolute; border-radius:50%; background:rgba(255,255,255,.03); pointer-events:none; }
        .admin-header .p1 { width:100px;height:100px;top:-30px;right:-20px; animation:float1 12s ease-in-out infinite; }
        .admin-header .p2 { width:60px;height:60px;bottom:-20px;left:30px; animation:float1 10s ease-in-out infinite reverse; }
        
        /* ===== GRADIENT TEXT ===== */
        .gradient-text-admin { background: linear-gradient(135deg, #fff 0%, #94a3b8 50%, #fff 100%);
            background-size: 200% auto; -webkit-background-clip:text; -webkit-text-fill-color:transparent;
            background-clip:text; animation: gradientShift 4s linear infinite; }
        
        /* ===== AVATAR ===== */
        .avatar-sm { width:32px; height:32px; border-radius:10px; display:flex; align-items:center; justify-content:center;
            font-weight:800; font-size:11px; text-transform:uppercase; flex-shrink:0; }
        
        /* ===== STAT CARD ===== */
        .stat-card { position:relative; overflow:hidden; }
        .stat-card .stat-bg { position:absolute; right:-10px; bottom:-10px; width:60px; height:60px; border-radius:16px;
            opacity:.08; transform:rotate(12deg); }
        
        /* ===== TOAST ===== */
        .toast-progress { height:3px; border-radius:0 0 12px 12px; background:rgba(255,255,255,.3); animation: toastProgress 3.5s linear forwards; }
        
        ::-webkit-scrollbar { width:3px; }
        ::-webkit-scrollbar-thumb { background:#d1d5db; border-radius:4px; }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom, 0px); }
        input, select, textarea { font-size: 16px !important; }
        
        /* ===== DARK MODE ===== */
        .dark body { background: #0f172a; color: #e2e8f0; }
        .dark .bg-gray-50 { background: #0f172a; }
        .dark .bg-white { background: #1e293b; }
        .dark .text-gray-800, .dark .text-gray-700, .dark .text-gray-600 { color: #e2e8f0; }
        .dark .text-gray-500, .dark .text-gray-400 { color: #94a3b8; }
        .dark .border-gray-100, .dark .border-gray-200 { border-color: #334155; }
        .dark .bg-gray-100 { background: #1e293b; }
        .dark input, .dark select, .dark textarea { background: #1e293b; color: #e2e8f0; border-color: #475569; }
        .dark input:focus, .dark select:focus { background: #0f172a; border-color: #10b981; }
        .dark .stat-card { border-color: #334155; }
        .dark .glass-card { background: rgba(30,41,59,.85); border-color: rgba(255,255,255,.08); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<!-- ======= LOGIN ======= -->
<div id="login-screen" class="min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"></div>
    <div class="absolute inset-0 opacity-[.03]" style="background-image:url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="w-full max-w-sm relative z-10">
        <div class="text-center mb-8 slide-up">
            <div class="w-20 h-20 mx-auto mb-5 bg-gradient-to-br from-slate-600 to-slate-800 rounded-3xl flex items-center justify-center shadow-2xl shadow-slate-900/50 border border-slate-600/50 relative">
                <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <div class="absolute inset-0 rounded-3xl border-2 border-white/10 animate-pulse"></div>
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight">Panel Admin</h1>
            <p class="text-slate-400 text-sm mt-1.5 font-medium">Interliga Pádel Costa Rica</p>
        </div>
        <form onsubmit="doLogin(event)" class="bg-white/10 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/10 p-6 space-y-4 slide-up" style="animation-delay:.1s">
            <div>
                <label class="block text-sm font-bold text-white/80 mb-1.5">Usuario</label>
                <input type="text" id="login-user" required class="w-full px-4 py-3.5 rounded-xl border-2 border-white/10 focus:border-emerald-400 focus:ring-4 focus:ring-emerald-400/20 outline-none transition-all text-sm bg-white/5 text-white placeholder-white/30" placeholder="admin">
            </div>
            <div>
                <label class="block text-sm font-bold text-white/80 mb-1.5">Contraseña</label>
                <input type="password" id="login-pass" required class="w-full px-4 py-3.5 rounded-xl border-2 border-white/10 focus:border-emerald-400 focus:ring-4 focus:ring-emerald-400/20 outline-none transition-all text-sm bg-white/5 text-white placeholder-white/30" placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;">
            </div>
            <button type="submit" id="login-btn" class="ripple-btn w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-black rounded-2xl shadow-lg shadow-emerald-500/20 active:scale-[.97] transition-transform text-sm tracking-wide">
                Iniciar sesión
            </button>
            <p id="login-error" class="text-red-400 text-sm text-center hidden font-medium"></p>
        </form>
        <a href="index.php" class="block text-center text-sm text-slate-500 mt-5 font-medium hover:text-slate-300 transition-colors">&larr; Volver al sitio público</a>
    </div>
</div>

<!-- ======= ADMIN PANEL ======= -->
<div id="admin-panel" class="hidden">
    <!-- Header -->
    <header class="sticky top-0 z-40 admin-header shadow-xl shadow-slate-900/30">
        <div class="particle p1"></div><div class="particle p2"></div>
        <div class="max-w-2xl mx-auto px-4 py-3.5 flex items-center justify-between relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <h1 class="gradient-text-admin font-black text-base leading-tight">Interliga Admin</h1>
                    <p class="text-white/40 text-[10px] font-semibold tracking-[.15em]">PANEL DE CONTROL</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button onclick="toggleDarkMode()" class="text-white/50 hover:text-white p-2.5 rounded-xl hover:bg-white/10 transition-all active:scale-90" title="Modo oscuro">
                    <svg class="w-5 h-5" id="darkIcon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>
                <a href="index.php" class="text-white/50 hover:text-white p-2.5 rounded-xl hover:bg-white/10 transition-all active:scale-90" title="Ver sitio">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
                <button onclick="doLogout()" class="text-white/50 hover:text-white p-2.5 rounded-xl hover:bg-white/10 transition-all active:scale-90" title="Cerrar sesión">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </div>
        </div>
        <!-- Tabs -->
        <div class="max-w-2xl mx-auto px-4 flex gap-1 overflow-x-auto pb-3 -mb-0.5">
            <button onclick="switchTab('dashboard')" class="admin-tab ripple-btn shrink-0 px-4 py-2.5 rounded-xl text-xs font-black text-white bg-white/15 border border-white/10 shadow-sm" data-tab="dashboard">Dashboard</button>
            <button onclick="switchTab('jornadas')" class="admin-tab ripple-btn shrink-0 px-4 py-2.5 rounded-xl text-xs font-bold text-white/40 hover:text-white hover:bg-white/5 transition-all" data-tab="jornadas">Jornadas</button>
            <button onclick="switchTab('partidos')" class="admin-tab ripple-btn shrink-0 px-4 py-2.5 rounded-xl text-xs font-bold text-white/40 hover:text-white hover:bg-white/5 transition-all" data-tab="partidos">Partidos</button>
            <button onclick="switchTab('inscripciones')" class="admin-tab ripple-btn shrink-0 px-4 py-2.5 rounded-xl text-xs font-bold text-white/40 hover:text-white hover:bg-white/5 transition-all" data-tab="inscripciones">Inscripciones</button>
            <button onclick="switchTab('clasificacion')" class="admin-tab ripple-btn shrink-0 px-4 py-2.5 rounded-xl text-xs font-bold text-white/40 hover:text-white hover:bg-white/5 transition-all" data-tab="clasificacion">Clasificación</button>
            <button onclick="switchTab('actividad')" class="admin-tab ripple-btn shrink-0 px-4 py-2.5 rounded-xl text-xs font-bold text-white/40 hover:text-white hover:bg-white/5 transition-all" data-tab="actividad">Actividad</button>
        </div>
    </header>

    <div class="max-w-2xl mx-auto px-4 pt-5 pb-8">

        <!-- TAB: DASHBOARD -->
        <div id="tab-dashboard" class="tab-content active">
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6" id="stats-grid">
                <div class="skeleton h-32 w-full"></div>
                <div class="skeleton h-32 w-full"></div>
                <div class="skeleton h-32 w-full"></div>
                <div class="skeleton h-32 w-full"></div>
                <div class="skeleton h-32 w-full"></div>
            </div>
            <!-- Quick actions -->
            <h3 class="text-sm font-black text-gray-800 mb-3">Acciones rápidas</h3>
            <div class="grid grid-cols-2 gap-3">
                <button onclick="switchTab('jornadas');setTimeout(function(){openJornadaForm()},200)" class="ripple-btn bg-white rounded-2xl border border-gray-100 p-4 shadow-sm text-left active:scale-95 transition-transform">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <p class="text-sm font-bold text-gray-800">Nueva jornada</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Crear evento</p>
                </button>
                <button onclick="switchTab('partidos');setTimeout(function(){openPartidoForm()},200)" class="ripple-btn bg-white rounded-2xl border border-gray-100 p-4 shadow-sm text-left active:scale-95 transition-transform">
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <p class="text-sm font-bold text-gray-800">Nuevo partido</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Agregar cancha</p>
                </button>
                <button onclick="window.location='api.php?action=backup_db'" class="ripple-btn bg-white rounded-2xl border border-gray-100 p-4 shadow-sm text-left active:scale-95 transition-transform">
                    <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                    </div>
                    <p class="text-sm font-bold text-gray-800">Backup DB</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Descargar SQL</p>
                </button>
                <button onclick="exportarCSV()" class="ripple-btn bg-white rounded-2xl border border-gray-100 p-4 shadow-sm text-left active:scale-95 transition-transform">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="text-sm font-bold text-gray-800">Exportar CSV</p>
                    <p class="text-[11px] text-gray-400 mt-0.5">Inscripciones</p>
                </button>
            </div>
        </div>

        <!-- TAB: JORNADAS -->
        <div id="tab-jornadas" class="tab-content">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-black text-gray-800">Jornadas</h2>
                <button onclick="openJornadaForm()" class="ripple-btn px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-sm font-bold rounded-xl shadow-md shadow-emerald-200/50 active:scale-95 transition-transform">+ Nueva</button>
            </div>
            <div id="admin-jornadas-list" class="space-y-3">
                <div class="skeleton h-20 w-full"></div>
            </div>
        </div>

        <!-- TAB: PARTIDOS -->
        <div id="tab-partidos" class="tab-content">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-black text-gray-800">Partidos</h2>
                <button onclick="openPartidoForm()" class="ripple-btn px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-sm font-bold rounded-xl shadow-md shadow-emerald-200/50 active:scale-95 transition-transform">+ Nuevo</button>
            </div>
            <div class="mb-4">
                <select id="admin-jornada-filter" onchange="cargarAdminPartidos()" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all font-medium">
                    <option value="">Seleccionar jornada...</option>
                </select>
            </div>
            <div id="admin-partidos-list" class="space-y-3">
                <p class="text-gray-400 text-sm text-center py-8 font-medium">Selecciona una jornada</p>
            </div>
        </div>

        <!-- TAB: INSCRIPCIONES -->
        <div id="tab-inscripciones" class="tab-content">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-black text-gray-800">Inscripciones</h2>
                <button onclick="exportarCSV()" class="ripple-btn px-3 py-2 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl hover:bg-emerald-200 transition-all active:scale-95">📥 CSV</button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <select id="insc-jornada" onchange="cargarPartidosParaInsc()" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all font-medium">
                    <option value="">Jornada...</option>
                </select>
                <select id="insc-partido" onchange="cargarAdminInscripciones()" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all font-medium">
                    <option value="">Partido...</option>
                </select>
            </div>
            <div class="mb-3">
                <input type="text" id="insc-search" placeholder="🔍 Buscar por nombre o teléfono..." oninput="filtrarInscripcionesLocal()" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 text-sm bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all font-medium">
            </div>
            <div id="bulk-bar" class="hidden mb-3 bg-slate-800 text-white rounded-xl p-3 flex items-center justify-between gap-2">
                <span class="text-xs font-bold"><span id="bulk-count">0</span> seleccionados</span>
                <div class="flex gap-2">
                    <button onclick="bulkCancelar()" class="ripple-btn px-3 py-1.5 bg-red-500 text-white text-xs font-bold rounded-lg active:scale-95">Cancelar</button>
                    <button onclick="bulkReserva()" class="ripple-btn px-3 py-1.5 bg-amber-500 text-white text-xs font-bold rounded-lg active:scale-95">A reserva</button>
                </div>
            </div>
            <div id="admin-insc-list" class="space-y-3">
                <p class="text-gray-400 text-sm text-center py-8 font-medium">Selecciona jornada y partido</p>
            </div>
        </div>

        <!-- TAB: CLASIFICACIÓN -->
        <div id="tab-clasificacion" class="tab-content">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-black text-gray-800">Clasificación</h2>
            </div>

            <!-- Sub-tabs -->
            <div class="flex gap-2 mb-4 overflow-x-auto pb-1">
                <button onclick="switchClasifSub('equipos')" class="clasif-sub-tab ripple-btn shrink-0 px-4 py-2.5 rounded-xl text-xs font-black bg-slate-800 text-white shadow-sm" data-sub="equipos">Equipos</button>
                <button onclick="switchClasifSub('resultados')" class="clasif-sub-tab ripple-btn shrink-0 px-4 py-2.5 rounded-xl text-xs font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all" data-sub="resultados">Resultados</button>
                <button onclick="switchClasifSub('tabla')" class="clasif-sub-tab ripple-btn shrink-0 px-4 py-2.5 rounded-xl text-xs font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all" data-sub="tabla">Tabla</button>
            </div>

            <!-- Sub: Equipos -->
            <div id="clasif-sub-equipos" class="clasif-sub-content">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-gray-700">Equipos registrados</h3>
                    <button onclick="openEquipoForm()" class="ripple-btn px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-sm font-bold rounded-xl shadow-md shadow-emerald-200/50 active:scale-95 transition-transform">+ Nuevo</button>
                </div>
                <div id="admin-equipos-list" class="space-y-2">
                    <div class="skeleton h-14 w-full"></div>
                </div>
            </div>

            <!-- Sub: Resultados -->
            <div id="clasif-sub-resultados" class="clasif-sub-content" style="display:none">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-bold text-gray-700">Resultados por jornada</h3>
                    <button onclick="openResultadoForm()" class="ripple-btn px-4 py-2.5 bg-gradient-to-r from-indigo-500 to-blue-500 text-white text-sm font-bold rounded-xl shadow-md shadow-indigo-200/50 active:scale-95 transition-transform">+ Nuevo</button>
                </div>
                <div class="mb-3">
                    <select id="admin-res-jornada" onchange="cargarAdminResultados()" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all font-medium">
                        <option value="">Todas las jornadas...</option>
                    </select>
                </div>
                <div id="admin-resultados-list" class="space-y-2">
                    <div class="skeleton h-14 w-full"></div>
                </div>
            </div>

            <!-- Sub: Tabla -->
            <div id="clasif-sub-tabla" class="clasif-sub-content" style="display:none">
                <div class="mb-3">
                    <select id="admin-tabla-jornada" onchange="cargarAdminTabla()" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all font-medium">
                        <option value="">General (todas las jornadas)</option>
                    </select>
                </div>
                <div id="admin-tabla-content" class="space-y-0">
                    <div class="skeleton h-10 w-full mb-1"></div>
                    <div class="skeleton h-12 w-full mb-1"></div>
                </div>
            </div>
        </div>

        <!-- TAB: ACTIVIDAD -->
        <div id="tab-actividad" class="tab-content">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-black text-gray-800">Registro de Actividad</h2>
                <div class="flex gap-2">
                    <button onclick="window.location='api.php?action=backup_db'" class="ripple-btn px-3 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-200 transition-all active:scale-95">💾 Backup DB</button>
                </div>
            </div>
            <div id="admin-audit-list" class="space-y-2">
                <div class="skeleton h-14 w-full"></div>
            </div>
            <div id="audit-pagination" class="flex items-center justify-center gap-3 mt-4" style="display:none">
                <button onclick="cargarAuditLog(auditPage-1)" id="audit-prev" class="ripple-btn px-4 py-2.5 bg-gray-100 text-gray-600 text-xs font-bold rounded-xl hover:bg-gray-200 transition-all active:scale-95">&larr; Anterior</button>
                <span id="audit-page-info" class="text-xs font-bold text-gray-500"></span>
                <button onclick="cargarAuditLog(auditPage+1)" id="audit-next" class="ripple-btn px-4 py-2.5 bg-gray-100 text-gray-600 text-xs font-bold rounded-xl hover:bg-gray-200 transition-all active:scale-95">Siguiente &rarr;</button>
            </div>
        </div>
    </div>
</div>
<div id="admin-modal-overlay" class="fixed inset-0 bg-black/60 z-50 hidden backdrop-blur-sm" onclick="closeAdminModal()"></div>
<div id="admin-modal" class="fixed inset-x-0 bottom-0 z-50 bottom-sheet closed">
    <div class="max-w-lg mx-auto bg-white rounded-t-3xl shadow-2xl px-6 pt-3 pb-8 safe-bottom max-h-[85vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex justify-center mb-3"><div class="w-12 h-1.5 bg-gray-200 rounded-full"></div></div>
        <div id="admin-modal-content"></div>
    </div>
</div>

<!-- ======= TOAST ======= -->
<div id="toast" class="fixed top-20 left-1/2 -translate-x-1/2 z-[60] rounded-2xl shadow-2xl text-sm font-semibold transition-all duration-300 opacity-0 -translate-y-4 pointer-events-none max-w-[85vw] text-center overflow-hidden">
    <div class="px-5 py-3 flex items-center gap-2 justify-center" id="toast-inner"></div>
    <div class="toast-progress" id="toast-progress" style="display:none"></div>
</div>

<script>
const CSRF = '<?= e(csrfToken()) ?>';
let adminJornadas = [];
let adminPartidos = [];
let currentTab = 'dashboard';

// ===== RIPPLE =====
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

function esc(s) { if (!s) return ''; var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

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
    t.style.opacity = '1'; t.style.transform = 'translate(-50%, 0)'; t.style.pointerEvents = 'auto';
    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(function() { t.style.opacity = '0'; t.style.transform = 'translate(-50%, -1rem)'; t.style.pointerEvents = 'none'; prog.style.display = 'none'; }, 3500);
}

// ===== ANIMATED COUNTER =====
function animateCounter(el, target) {
    var duration = 700;
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

// ===== AVATAR =====
var avatarPalettes = [
    ['#059669','#10b981'], ['#0284c7','#38bdf8'], ['#7c3aed','#a78bfa'], ['#db2777','#f472b6'],
    ['#ea580c','#fb923c'], ['#ca8a04','#facc15'], ['#0891b2','#22d3ee'], ['#4f46e5','#818cf8']
];
function getAvatar(name) {
    var initials = (name || '?').split(' ').map(function(w){return w[0]}).join('').substring(0,2).toUpperCase();
    var hash = 0;
    for (var c = 0; c < name.length; c++) hash = name.charCodeAt(c) + ((hash << 5) - hash);
    var pal = avatarPalettes[Math.abs(hash) % avatarPalettes.length];
    return '<div class="avatar-sm" style="background:linear-gradient(135deg,'+pal[0]+','+pal[1]+');color:white">' + esc(initials) + '</div>';
}

// ======= AUTH =======
async function checkLogin() {
    var r = await api('admin_check');
    if (r.ok && r.logueado) {
        document.getElementById('login-screen').classList.add('hidden');
        document.getElementById('admin-panel').classList.remove('hidden');
        initAdmin();
    }
}

async function doLogin(e) {
    e.preventDefault();
    var btn = document.getElementById('login-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-5 h-5 animate-spin inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Verificando...';
    var r = await api('admin_login', { usuario: document.getElementById('login-user').value, clave: document.getElementById('login-pass').value }, 'POST');
    if (r.ok) {
        document.getElementById('login-screen').classList.add('hidden');
        document.getElementById('admin-panel').classList.remove('hidden');
        initAdmin();
    } else {
        var errEl = document.getElementById('login-error');
        errEl.textContent = r.error || 'Error de autenticación';
        errEl.classList.remove('hidden');
        errEl.style.animation = 'none';
        requestAnimationFrame(function() { errEl.style.animation = 'fadeIn .3s ease'; });
    }
    btn.disabled = false; btn.textContent = 'Iniciar sesión';
}

async function doLogout() {
    await api('admin_logout', {}, 'POST');
    location.reload();
}

// ======= TABS =======
function switchTab(tab) {
    currentTab = tab;
    document.querySelectorAll('.tab-content').forEach(function(el) { el.classList.remove('active'); });
    document.getElementById('tab-' + tab).classList.add('active');
    document.querySelectorAll('.admin-tab').forEach(function(b) {
        var isActive = b.dataset.tab === tab;
        if (isActive) {
            b.className = 'admin-tab ripple-btn shrink-0 px-4 py-2.5 rounded-xl text-xs font-black text-white bg-white/15 border border-white/10 shadow-sm';
        } else {
            b.className = 'admin-tab ripple-btn shrink-0 px-4 py-2.5 rounded-xl text-xs font-bold text-white/40 hover:text-white hover:bg-white/5 transition-all';
        }
    });
    if (tab === 'dashboard') cargarStats();
    else if (tab === 'jornadas') cargarAdminJornadas();
    else if (tab === 'partidos') cargarJornadasSelect();
    else if (tab === 'inscripciones') cargarJornadasSelectInsc();
    else if (tab === 'clasificacion') initClasifTab();
}

// ======= MODAL =======
function openAdminModal(html) {
    document.getElementById('admin-modal-content').innerHTML = html;
    document.getElementById('admin-modal-overlay').classList.remove('hidden');
    requestAnimationFrame(function() { document.getElementById('admin-modal').classList.remove('closed'); });
}
function closeAdminModal() {
    document.getElementById('admin-modal').classList.add('closed');
    setTimeout(function() { document.getElementById('admin-modal-overlay').classList.add('hidden'); }, 350);
}

// ======= DASHBOARD =======
async function cargarStats() {
    var r = await api('stats');
    if (!r.ok) return;
    var d = r.data;
    var cards = [
        { key:'jornadas', label:'Jornadas', val:d.jornadas, icon:'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', iconBg:'bg-blue-100', iconColor:'text-blue-600', statBg:'bg-blue-500', gradient:'from-blue-50 to-blue-100/30' },
        { key:'partidos', label:'Partidos', val:d.partidos, icon:'M13 10V3L4 14h7v7l9-11h-7z', iconBg:'bg-emerald-100', iconColor:'text-emerald-600', statBg:'bg-emerald-500', gradient:'from-emerald-50 to-emerald-100/30' },
        { key:'inscripciones', label:'Titulares', val:d.inscripciones, icon:'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', iconBg:'bg-purple-100', iconColor:'text-purple-600', statBg:'bg-purple-500', gradient:'from-purple-50 to-purple-100/30' },
        { key:'reservas', label:'Reservas', val:d.reservas||0, icon:'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', iconBg:'bg-orange-100', iconColor:'text-orange-600', statBg:'bg-orange-500', gradient:'from-orange-50 to-orange-100/30' },
        { key:'en_espera', label:'En espera', val:d.en_espera, icon:'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', iconBg:'bg-amber-100', iconColor:'text-amber-600', statBg:'bg-amber-500', gradient:'from-amber-50 to-amber-100/30' },
        { key:'equipos', label:'Equipos', val:d.equipos||0, icon:'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', iconBg:'bg-indigo-100', iconColor:'text-indigo-600', statBg:'bg-indigo-500', gradient:'from-indigo-50 to-indigo-100/30' },
        { key:'resultados', label:'Resultados', val:d.resultados||0, icon:'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', iconBg:'bg-cyan-100', iconColor:'text-cyan-600', statBg:'bg-cyan-500', gradient:'from-cyan-50 to-cyan-100/30' }
    ];
    document.getElementById('stats-grid').className = 'grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6';
    document.getElementById('stats-grid').innerHTML = cards.map(function(c, idx) {
        return '<div class="stat-card bg-gradient-to-br ' + c.gradient + ' rounded-2xl border border-gray-100/80 p-5 shadow-sm fade-in" style="animation-delay:' + (idx*.06) + 's">' +
            '<div class="stat-bg ' + c.statBg + '"></div>' +
            '<div class="relative z-10">' +
                '<div class="flex items-center gap-2.5 mb-3">' +
                    '<div class="w-10 h-10 ' + c.iconBg + ' rounded-xl flex items-center justify-center shadow-sm">' +
                        '<svg class="w-5 h-5 ' + c.iconColor + '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="' + c.icon + '"/></svg>' +
                    '</div>' +
                '</div>' +
                '<p class="text-3xl font-black text-gray-800" id="stat-' + c.key + '">' + c.val + '</p>' +
                '<p class="text-[11px] text-gray-500 font-semibold mt-1 tracking-wide">' + c.label + '</p>' +
            '</div>' +
        '</div>';
    }).join('');
    // Animate counters
    cards.forEach(function(c) { animateCounter(document.getElementById('stat-' + c.key), parseInt(c.val) || 0); });
}

// ======= JORNADAS ADMIN =======
async function cargarAdminJornadas() {
    var r = await api('jornadas_list');
    adminJornadas = r.ok ? r.data : [];
    var c = document.getElementById('admin-jornadas-list');
    if (!adminJornadas.length) {
        c.innerHTML = '<div class="text-center py-14"><div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-gray-100 to-gray-50 rounded-full flex items-center justify-center shadow-inner text-3xl">&#128197;</div><p class="text-gray-500 font-bold">No hay jornadas creadas</p><p class="text-gray-400 text-sm mt-1">Crea la primera jornada arriba</p></div>';
        return;
    }
    c.innerHTML = adminJornadas.map(function(j, idx) {
        var estadoClass = j.estado === 'abierta' ? 'bg-emerald-100 text-emerald-700' : j.estado === 'cerrada' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600';
        var borderColor = j.estado === 'abierta' ? 'border-l-emerald-500' : j.estado === 'cerrada' ? 'border-l-red-400' : 'border-l-gray-400';
        return '<div class="bg-white rounded-xl border border-gray-100 border-l-4 ' + borderColor + ' p-4 shadow-sm card-press fade-in" style="animation-delay:' + (idx*.05) + 's">' +
            '<div class="flex items-center justify-between">' +
                '<div class="flex-1 min-w-0">' +
                    '<div class="flex items-center gap-2 mb-1.5">' +
                        '<h3 class="font-bold text-gray-800 text-sm truncate">' + esc(j.nombre) + '</h3>' +
                        '<span class="px-2 py-0.5 rounded-md text-[10px] font-black ' + estadoClass + ' shrink-0">' + j.estado + '</span>' +
                    '</div>' +
                    '<p class="text-xs text-gray-400 font-medium">' + j.fecha + (j.ubicacion ? ' &bull; ' + esc(j.ubicacion) : '') + '</p>' +
                    '<div class="flex items-center gap-3 mt-2">' +
                        '<span class="text-[11px] text-gray-500 font-semibold flex items-center gap-1"><svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>' + j.total_partidos + ' partidos</span>' +
                        '<span class="text-[11px] text-gray-500 font-semibold flex items-center gap-1"><svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7"/></svg>' + j.total_inscritos + ' inscritos</span>' +
                    '</div>' +
                '</div>' +
                '<div class="flex gap-1.5 ml-3 shrink-0">' +
                    '<button onclick="editJornada(' + j.id + ')" class="p-2.5 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all active:scale-90" title="Editar"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>' +
                    '<button onclick="deleteJornada(' + j.id + ')" class="p-2.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-all active:scale-90" title="Eliminar"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>' +
                    '<button onclick="duplicateJornada(' + j.id + ')" class="p-2.5 rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100 transition-all active:scale-90" title="Duplicar"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></button>' +
                '</div>' +
            '</div>' +
        '</div>';
    }).join('');
}

function openJornadaForm(data) {
    var isEdit = !!data;
    var html = '<div class="flex items-center gap-3 mb-5"><div class="w-11 h-11 rounded-2xl ' + (isEdit ? 'bg-blue-100' : 'bg-emerald-100') + ' flex items-center justify-center"><svg class="w-5 h-5 ' + (isEdit ? 'text-blue-600' : 'text-emerald-600') + '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div><div><h3 class="text-lg font-black text-gray-800">' + (isEdit ? 'Editar' : 'Nueva') + ' Jornada</h3><p class="text-sm text-gray-400">' + (isEdit ? 'Modificar datos' : 'Crear nuevo evento') + '</p></div></div>' +
        '<form onsubmit="saveJornada(event, ' + (isEdit ? data.id : 'null') + ')" class="space-y-3.5">' +
            '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Nombre *</label>' +
                '<input type="text" id="j-nombre" required maxlength="200" value="' + (isEdit ? esc(data.nombre) : '') + '" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none text-sm bg-gray-50 focus:bg-white transition-all" placeholder="Jornada 1 - Enero"></div>' +
            '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Fecha *</label>' +
                '<input type="date" id="j-fecha" required value="' + (isEdit ? data.fecha : '') + '" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none text-sm bg-gray-50 focus:bg-white transition-all"></div>' +
            '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Ubicación</label>' +
                '<input type="text" id="j-ubicacion" maxlength="200" value="' + (isEdit && data.ubicacion ? esc(data.ubicacion) : '') + '" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none text-sm bg-gray-50 focus:bg-white transition-all" placeholder="Club Pádel San José"></div>' +
            '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Estado</label>' +
                '<select id="j-estado" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-gray-50 focus:bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all">' +
                    '<option value="abierta"' + (isEdit && data.estado==='abierta' ? ' selected' : '') + '>Abierta</option>' +
                    '<option value="cerrada"' + (isEdit && data.estado==='cerrada' ? ' selected' : '') + '>Cerrada</option>' +
                    '<option value="finalizada"' + (isEdit && data.estado==='finalizada' ? ' selected' : '') + '>Finalizada</option>' +
                '</select></div>' +
            '<button type="submit" class="ripple-btn w-full py-4 bg-gradient-to-r from-slate-700 to-slate-900 text-white font-black rounded-2xl shadow-lg active:scale-[.97] transition-transform text-sm">' + (isEdit ? 'Guardar cambios' : 'Crear jornada') + '</button>' +
        '</form>';
    openAdminModal(html);
}

async function editJornada(id) { var j = adminJornadas.find(function(x) { return x.id == id; }); if (j) openJornadaForm(j); }

async function saveJornada(e, id) {
    e.preventDefault();
    var params = { nombre: document.getElementById('j-nombre').value, fecha: document.getElementById('j-fecha').value, ubicacion: document.getElementById('j-ubicacion').value, estado: document.getElementById('j-estado').value };
    var action = id ? 'jornada_update' : 'jornada_create';
    if (id) params.id = id;
    var r = await api(action, params, 'POST');
    closeAdminModal();
    if (r.ok) { toast(id ? 'Jornada actualizada' : 'Jornada creada'); cargarAdminJornadas(); }
    else toast(r.error || 'Error', 'red');
}

async function deleteJornada(id) {
    if (!confirm('¿Eliminar esta jornada y todos sus partidos?')) return;
    var r = await api('jornada_delete', { id: id }, 'POST');
    if (r.ok) { toast('Jornada eliminada'); cargarAdminJornadas(); }
    else toast(r.error || 'Error', 'red');
}

async function duplicateJornada(id) {
    var j = adminJornadas.find(function(x) { return x.id == id; });
    if (!j) return;
    var r = await api('jornada_create', { nombre: j.nombre + ' (copia)', fecha: j.fecha, ubicacion: j.ubicacion || '', estado: 'abierta' }, 'POST');
    if (r.ok) { toast('Jornada duplicada'); cargarAdminJornadas(); }
    else toast(r.error || 'Error', 'red');
}

// ======= PARTIDOS ADMIN =======
async function cargarJornadasSelect() {
    var r = await api('jornadas_list');
    adminJornadas = r.ok ? r.data : [];
    var sel = document.getElementById('admin-jornada-filter');
    sel.innerHTML = '<option value="">Seleccionar jornada...</option>' +
        adminJornadas.map(function(j) { return '<option value="' + j.id + '">' + esc(j.nombre) + ' (' + j.fecha + ')</option>'; }).join('');
}

async function cargarAdminPartidos() {
    var jornadaId = document.getElementById('admin-jornada-filter').value;
    if (!jornadaId) { document.getElementById('admin-partidos-list').innerHTML = '<p class="text-gray-400 text-sm text-center py-8 font-medium">Selecciona una jornada</p>'; return; }
    var r = await api('partidos_list', { jornada_id: jornadaId });
    adminPartidos = r.ok ? r.data : [];
    var c = document.getElementById('admin-partidos-list');
    if (!adminPartidos.length) {
        c.innerHTML = '<div class="text-center py-14"><div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-gray-100 to-gray-50 rounded-full flex items-center justify-center shadow-inner text-3xl">&#127934;</div><p class="text-gray-500 font-bold">Sin partidos</p><p class="text-gray-400 text-sm mt-1">Agrega partidos a esta jornada</p></div>';
        return;
    }
    c.innerHTML = adminPartidos.map(function(p, idx) {
        var gInfo = { masculino: { bg:'bg-blue-100', text:'text-blue-700', accent:'border-l-blue-500' }, femenino: { bg:'bg-pink-100', text:'text-pink-700', accent:'border-l-pink-500' }, mixto: { bg:'bg-purple-100', text:'text-purple-700', accent:'border-l-purple-500' } }[p.genero] || { bg:'bg-gray-100', text:'text-gray-700', accent:'border-l-gray-400' };
        var estadoClass = p.estado === 'abierto' ? 'bg-emerald-100 text-emerald-700' : p.estado === 'lleno' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600';
        return '<div class="bg-white rounded-xl border border-gray-100 border-l-4 ' + gInfo.accent + ' p-4 shadow-sm card-press fade-in" style="animation-delay:' + (idx*.05) + 's">' +
            '<div class="flex items-start justify-between">' +
                '<div class="flex-1 min-w-0">' +
                    '<div class="flex items-center gap-2 mb-1.5 flex-wrap">' +
                        '<h3 class="font-bold text-gray-800 text-sm">' + esc(p.categoria) + '</h3>' +
                        '<span class="px-2 py-0.5 rounded-md text-[10px] font-black ' + gInfo.bg + ' ' + gInfo.text + '">' + p.genero + '</span>' +
                        '<span class="px-2 py-0.5 rounded-md text-[10px] font-black ' + estadoClass + '">' + p.estado + '</span>' +
                    '</div>' +
                    '<p class="text-xs text-gray-400 font-medium">' + p.hora.substring(0,5) + (p.cancha ? ' &bull; ' + esc(p.cancha) : '') + ' &bull; ' + p.cupos + ' parejas</p>' +
                    '<div class="flex items-center gap-2.5 mt-2">' +
                        '<div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden"><div class="h-full ' + (p.porcentaje >= 100 ? 'bg-red-500' : 'bg-emerald-500') + ' rounded-full transition-all" style="width:' + p.porcentaje + '%"></div></div>' +
                        '<span class="text-xs font-black ' + (p.porcentaje >= 100 ? 'text-red-600' : 'text-emerald-600') + '">' + p.inscritos + '/' + p.cupos_jugadores + '</span>' +
                    '</div>' +
                '</div>' +
                '<div class="flex gap-1.5 ml-3 shrink-0">' +
                    '<button onclick="editPartido(' + p.id + ')" class="p-2.5 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all active:scale-90"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>' +
                    '<button onclick="duplicarPartido(' + p.id + ')" class="p-2.5 rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-100 transition-all active:scale-90" title="Duplicar"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></button>' +
                    '<button onclick="deletePartido(' + p.id + ')" class="p-2.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-all active:scale-90"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>' +
                '</div>' +
            '</div>' +
        '</div>';
    }).join('');
}

function openPartidoForm(data) {
    var isEdit = !!data;
    var jornadaId = document.getElementById('admin-jornada-filter') ? document.getElementById('admin-jornada-filter').value : '';
    var html = '<div class="flex items-center gap-3 mb-5"><div class="w-11 h-11 rounded-2xl ' + (isEdit ? 'bg-blue-100' : 'bg-emerald-100') + ' flex items-center justify-center"><svg class="w-5 h-5 ' + (isEdit ? 'text-blue-600' : 'text-emerald-600') + '" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div><div><h3 class="text-lg font-black text-gray-800">' + (isEdit ? 'Editar' : 'Nuevo') + ' Partido</h3><p class="text-sm text-gray-400">' + (isEdit ? 'Modificar datos' : 'Agregar partido') + '</p></div></div>' +
        '<form onsubmit="savePartido(event, ' + (isEdit ? data.id : 'null') + ')" class="space-y-3.5">' +
            '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Jornada *</label>' +
                '<select id="p-jornada" required class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-gray-50 focus:bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all">' +
                    adminJornadas.map(function(j) { return '<option value="' + j.id + '"' + ((isEdit ? data.jornada_id == j.id : j.id == jornadaId) ? ' selected' : '') + '>' + esc(j.nombre) + '</option>'; }).join('') +
                '</select></div>' +
            '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Categoría *</label>' +
                '<input type="text" id="p-categoria" required maxlength="100" list="categorias-list" value="' + (isEdit ? esc(data.categoria) : '') + '" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-gray-50 focus:bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all" placeholder="4ta fuerza">' +
                '<datalist id="categorias-list"><option value="1ra fuerza"><option value="2da fuerza"><option value="3ra fuerza"><option value="4ta fuerza"><option value="5ta fuerza"><option value="6ta fuerza"><option value="Principiantes"><option value="Open"></datalist></div>' +
            '<div class="grid grid-cols-2 gap-3">' +
                '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Género *</label>' +
                    '<select id="p-genero" required class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-gray-50 focus:bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all">' +
                        '<option value="masculino"' + (isEdit && data.genero==='masculino' ? ' selected' : '') + '>Masculino</option>' +
                        '<option value="femenino"' + (isEdit && data.genero==='femenino' ? ' selected' : '') + '>Femenino</option>' +
                        '<option value="mixto"' + (isEdit && data.genero==='mixto' ? ' selected' : '') + '>Mixto</option>' +
                    '</select></div>' +
                '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Hora *</label>' +
                    '<input type="time" id="p-hora" required value="' + (isEdit ? data.hora.substring(0,5) : '18:00') + '" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-gray-50 focus:bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all"></div>' +
            '</div>' +
            '<div class="grid grid-cols-2 gap-3">' +
                '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Cancha</label>' +
                    '<input type="text" id="p-cancha" maxlength="50" value="' + (isEdit && data.cancha ? esc(data.cancha) : '') + '" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-gray-50 focus:bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all" placeholder="Cancha 1"></div>' +
                '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Cupos (parejas)</label>' +
                    '<input type="number" id="p-cupos" required min="1" max="50" value="' + (isEdit ? data.cupos : '8') + '" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-gray-50 focus:bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all"></div>' +
            '</div>' +
            (isEdit ? '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Estado</label><select id="p-estado" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-gray-50 focus:bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all"><option value="abierto"' + (data.estado==='abierto' ? ' selected' : '') + '>Abierto</option><option value="lleno"' + (data.estado==='lleno' ? ' selected' : '') + '>Lleno</option><option value="cerrado"' + (data.estado==='cerrado' ? ' selected' : '') + '>Cerrado</option></select></div>' : '') +
            '<button type="submit" class="ripple-btn w-full py-4 bg-gradient-to-r from-slate-700 to-slate-900 text-white font-black rounded-2xl shadow-lg active:scale-[.97] transition-transform text-sm">' + (isEdit ? 'Guardar' : 'Crear partido') + '</button>' +
        '</form>';
    openAdminModal(html);
}

async function editPartido(id) { var p = adminPartidos.find(function(x) { return x.id == id; }); if (p) openPartidoForm(p); }

async function savePartido(e, id) {
    e.preventDefault();
    var params = { jornada_id: document.getElementById('p-jornada').value, categoria: document.getElementById('p-categoria').value, genero: document.getElementById('p-genero').value, hora: document.getElementById('p-hora').value, cancha: document.getElementById('p-cancha').value, cupos: document.getElementById('p-cupos').value };
    if (id) { params.id = id; params.estado = document.getElementById('p-estado').value; }
    var action = id ? 'partido_update' : 'partido_create';
    var r = await api(action, params, 'POST');
    closeAdminModal();
    if (r.ok) { toast(id ? 'Partido actualizado' : 'Partido creado'); cargarAdminPartidos(); }
    else toast(r.error || 'Error', 'red');
}

async function deletePartido(id) {
    if (!confirm('¿Eliminar este partido y todas sus inscripciones?')) return;
    var r = await api('partido_delete', { id: id }, 'POST');
    if (r.ok) { toast('Partido eliminado'); cargarAdminPartidos(); }
    else toast(r.error || 'Error', 'red');
}

// ======= INSCRIPCIONES ADMIN =======
async function cargarJornadasSelectInsc() {
    var r = await api('jornadas_list');
    adminJornadas = r.ok ? r.data : [];
    document.getElementById('insc-jornada').innerHTML = '<option value="">Jornada...</option>' +
        adminJornadas.map(function(j) { return '<option value="' + j.id + '">' + esc(j.nombre) + '</option>'; }).join('');
}

async function cargarPartidosParaInsc() {
    var jornadaId = document.getElementById('insc-jornada').value;
    var sel = document.getElementById('insc-partido');
    if (!jornadaId) { sel.innerHTML = '<option value="">Partido...</option>'; return; }
    var r = await api('partidos_list', { jornada_id: jornadaId });
    var partidos = r.ok ? r.data : [];
    sel.innerHTML = '<option value="">Partido...</option>' +
        partidos.map(function(p) { return '<option value="' + p.id + '">' + esc(p.categoria) + ' - ' + p.genero + ' (' + p.hora.substring(0,5) + ')</option>'; }).join('');
}

async function cargarAdminInscripciones() {
    var partidoId = document.getElementById('insc-partido').value;
    if (!partidoId) { document.getElementById('admin-insc-list').innerHTML = '<p class="text-gray-400 text-sm text-center py-8 font-medium">Selecciona un partido</p>'; return; }
    var results = await Promise.all([
        api('inscripciones_list', { partido_id: partidoId }),
        api('espera_list', { partido_id: partidoId })
    ]);
    var rI = results[0], rE = results[1];
    var inscritos = rI.ok ? rI.data : [];
    var espera = rE.ok ? rE.data : [];
    var c = document.getElementById('admin-insc-list');
    var html = '';
    selectedInscripciones.clear();
    document.getElementById('bulk-count').textContent = '0';
    document.getElementById('bulk-bar').className = 'hidden mb-3 bg-slate-800 text-white rounded-xl p-3 flex items-center justify-between gap-2';
    
    // Titulares (confirmados, no reserva)
    var titulares = inscritos.filter(function(i){return i.estado==='confirmado' && !parseInt(i.es_reserva)});
    var reservas = inscritos.filter(function(i){return i.estado==='confirmado' && parseInt(i.es_reserva)});

    html += '<div class="flex items-center justify-between mb-3"><h4 class="font-bold text-gray-700 text-sm flex items-center gap-2"><span class="w-7 h-7 bg-emerald-100 rounded-xl flex items-center justify-center"><svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>Titulares</h4><span class="text-xs font-black bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-lg">' + titulares.length + '</span></div>';
    
    if (!titulares.length) {
        html += '<div class="bg-gray-50 rounded-xl p-6 text-center mb-4 border border-gray-100"><p class="text-gray-400 text-sm font-medium">No hay titulares inscritos aún</p></div>';
    } else {
        html += '<div class="space-y-2 mb-4">';
        for (var k = 0; k < titulares.length; k++) {
            var ins = titulares[k];
            html += '<div class="bg-white rounded-xl border border-gray-100 p-3.5 flex items-center gap-3 fade-in" style="animation-delay:' + (k*.04) + 's">' +
                '<input type="checkbox" onchange="toggleBulkSelect(' + ins.id + ',this)" class="w-4 h-4 rounded accent-emerald-500 shrink-0">' +
                getAvatar(ins.nombre) +
                '<div class="min-w-0 flex-1">' +
                    '<p class="text-sm font-bold text-gray-800 truncate">' + esc(ins.nombre) + '</p>' +
                    '<p class="text-[11px] text-gray-400 font-medium">' + ins.tipo + (ins.pareja_nombre ? ' con ' + esc(ins.pareja_nombre) : '') + (ins.telefono ? ' &bull; ' + esc(ins.telefono) : '') + '</p>' +
                '</div>' +
                '<div class="flex gap-1 ml-2 shrink-0">' +
                    '<button onclick="openEditInscripcion({id:' + ins.id + ',nombre:\'' + esc(ins.nombre).replace(/'/g,"\\'") + '\',telefono:\'' + esc(ins.telefono||'').replace(/'/g,"\\'") + '\'})" class="p-2 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all active:scale-90" title="Editar"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>' +
                    (ins.telefono ? '<button onclick="openWhatsApp(\'' + esc(ins.telefono).replace(/'/g,"\\'") + '\',\'' + esc(ins.nombre).replace(/'/g,"\\'") + '\',\'\',\'\')" class="p-2 rounded-xl bg-green-50 text-green-600 hover:bg-green-100 transition-all active:scale-90" title="WhatsApp"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492l4.624-1.467A11.96 11.96 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818c-2.168 0-4.19-.574-5.944-1.573l-.427-.25-2.74.87.89-2.672-.28-.443A9.79 9.79 0 012.182 12c0-5.423 4.395-9.818 9.818-9.818S21.818 6.577 21.818 12s-4.395 9.818-9.818 9.818z"/></svg></button>' : '') +
                    (reservas.length > 0 ? '<button onclick="openSwapModal(' + ins.id + ', \'' + esc(ins.nombre).replace(/'/g,"\\'") + '\')" class="p-2 rounded-xl bg-orange-50 text-orange-600 hover:bg-orange-100 transition-all active:scale-90" title="Intercambiar con reserva"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg></button>' : '') +
                    '<button onclick="hacerReserva(' + ins.id + ')" class="p-2 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-100 transition-all active:scale-90" title="Mover a reserva"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg></button>' +
                    '<button onclick="cancelarInscripcion(' + ins.id + ')" class="p-2 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition-all active:scale-90" title="Cancelar"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>' +
                '</div>' +
            '</div>';
        }
        html += '</div>';
    }

    // Reservas
    if (reservas.length) {
        html += '<div class="flex items-center justify-between mb-3 mt-4"><h4 class="font-bold text-gray-700 text-sm flex items-center gap-2"><span class="w-7 h-7 bg-orange-100 rounded-xl flex items-center justify-center"><svg class="w-3.5 h-3.5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg></span>Reservas</h4><span class="text-xs font-black bg-orange-100 text-orange-700 px-2.5 py-1 rounded-lg">' + reservas.length + '</span></div>';
        html += '<div class="space-y-2 mb-4">';
        for (var ri = 0; ri < reservas.length; ri++) {
            var res = reservas[ri];
            html += '<div class="bg-orange-50/50 rounded-xl border border-orange-100 p-3.5 flex items-center gap-3 fade-in">' +
                getAvatar(res.nombre) +
                '<div class="min-w-0 flex-1">' +
                    '<p class="text-sm font-bold text-gray-800 truncate">' + esc(res.nombre) + '</p>' +
                    '<p class="text-[11px] text-orange-500 font-semibold">&#128165; Reserva' + (res.telefono ? ' &bull; ' + esc(res.telefono) : '') + '</p>' +
                '</div>' +
                '<div class="flex gap-1 ml-2 shrink-0">' +
                    '<button onclick="hacerReserva(' + res.id + ')" class="p-2 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-all active:scale-90" title="Promover a titular"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg></button>' +
                    '<button onclick="cancelarInscripcion(' + res.id + ')" class="p-2 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition-all active:scale-90" title="Cancelar"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>' +
                '</div>' +
            '</div>';
        }
        html += '</div>';
    }

    // Lista de espera
    if (espera.length) {
        html += '<div class="flex items-center justify-between mb-3 mt-4"><h4 class="font-bold text-gray-700 text-sm flex items-center gap-2"><span class="w-7 h-7 bg-amber-100 rounded-xl flex items-center justify-center"><svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>En espera</h4><span class="text-xs font-black bg-amber-100 text-amber-700 px-2.5 py-1 rounded-lg">' + espera.length + '</span></div>';
        html += '<div class="space-y-2">';
        for (var ei = 0; ei < espera.length; ei++) {
            var e = espera[ei];
            html += '<div class="bg-amber-50/50 rounded-xl border border-amber-100 p-3.5 flex items-center gap-3 fade-in">' +
                getAvatar(e.nombre) +
                '<div class="min-w-0 flex-1">' +
                    '<p class="text-sm font-bold text-gray-800">#' + (ei+1) + ' ' + esc(e.nombre) + '</p>' +
                    '<p class="text-[11px] text-gray-400 font-medium">' + (e.telefono ? esc(e.telefono) : 'Sin teléfono') + '</p>' +
                '</div>' +
                '<div class="flex gap-1.5 ml-2 shrink-0">' +
                    '<button onclick="promoverEspera(' + e.id + ')" class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-all active:scale-90" title="Promover"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg></button>' +
                    '<button onclick="eliminarEspera(' + e.id + ')" class="p-2.5 rounded-xl bg-red-50 text-red-500 hover:bg-red-100 transition-all active:scale-90" title="Eliminar"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>' +
                '</div>' +
            '</div>';
        }
        html += '</div>';
    }

    c.innerHTML = html || '<p class="text-gray-400 text-sm text-center py-8 font-medium">Sin datos</p>';
    // Store for swap modal
    window._currentReservas = reservas;
}

async function cancelarInscripcion(id) {
    if (!confirm('¿Cancelar esta inscripción?')) return;
    var r = await api('cancelar_inscripcion', { id: id }, 'POST');
    if (r.ok) { toast('Inscripción cancelada'); cargarAdminInscripciones(); }
    else toast(r.error || 'Error', 'red');
}

async function promoverEspera(id) {
    var r = await api('promover_espera', { id: id }, 'POST');
    if (r.ok) { toast('Promovido exitosamente'); cargarAdminInscripciones(); }
    else toast(r.error || 'Error', 'red');
}

async function eliminarEspera(id) {
    if (!confirm('¿Eliminar de la lista de espera?')) return;
    var r = await api('eliminar_espera', { id: id }, 'POST');
    if (r.ok) { toast('Eliminado de espera'); cargarAdminInscripciones(); }
    else toast(r.error || 'Error', 'red');
}

// ======= RESERVAS / SWAP =======
function openSwapModal(titularId, titularNombre) {
    var reservas = window._currentReservas || [];
    if (!reservas.length) { toast('No hay reservas disponibles', 'amber'); return; }
    var html = '<div class="flex items-center gap-3 mb-5">' +
        '<div class="w-11 h-11 rounded-2xl bg-orange-100 flex items-center justify-center">' +
            '<svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>' +
        '</div>' +
        '<div><h3 class="text-lg font-black text-gray-800">Intercambiar jugador</h3>' +
            '<p class="text-sm text-gray-400">Reemplazar a <strong class="text-gray-600">' + esc(titularNombre) + '</strong></p></div></div>' +
        '<p class="text-sm text-gray-500 mb-3 font-medium">Selecciona el reserva que entrará como titular:</p>' +
        '<div class="space-y-2">';
    for (var i = 0; i < reservas.length; i++) {
        var res = reservas[i];
        html += '<button onclick="ejecutarSwap(' + titularId + ',' + res.id + ')" class="ripple-btn w-full bg-orange-50 hover:bg-orange-100 border border-orange-200 rounded-xl p-3.5 flex items-center gap-3 transition-all active:scale-[.97]">' +
            getAvatar(res.nombre) +
            '<div class="text-left flex-1 min-w-0">' +
                '<p class="text-sm font-bold text-gray-800 truncate">' + esc(res.nombre) + '</p>' +
                '<p class="text-[11px] text-orange-500 font-medium">' + (res.telefono ? esc(res.telefono) : 'Sin teléfono') + '</p>' +
            '</div>' +
            '<svg class="w-5 h-5 text-orange-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>' +
        '</button>';
    }
    html += '</div>';
    openAdminModal(html);
}

async function ejecutarSwap(titularId, reservaId) {
    var r = await api('swap_reserva', { titular_id: titularId, reserva_id: reservaId }, 'POST');
    closeAdminModal();
    if (r.ok) { toast(r.mensaje || 'Intercambio realizado'); cargarAdminInscripciones(); }
    else toast(r.error || 'Error al intercambiar', 'red');
}

async function hacerReserva(id) {
    var r = await api('hacer_reserva', { id: id }, 'POST');
    if (r.ok) { toast(r.mensaje || 'Estado actualizado'); cargarAdminInscripciones(); }
    else toast(r.error || 'Error', 'red');
}

// ======= CLASIFICACIÓN =======
let adminEquipos = [];
let adminResultados = [];

function switchClasifSub(sub) {
    document.querySelectorAll('.clasif-sub-content').forEach(function(el) { el.style.display = 'none'; });
    document.getElementById('clasif-sub-' + sub).style.display = 'block';
    document.querySelectorAll('.clasif-sub-tab').forEach(function(b) {
        var isActive = b.dataset.sub === sub;
        b.className = 'clasif-sub-tab ripple-btn shrink-0 px-4 py-2.5 rounded-xl text-xs ' + (isActive ? 'font-black bg-slate-800 text-white shadow-sm' : 'font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-all');
    });
    if (sub === 'equipos') cargarAdminEquipos();
    else if (sub === 'resultados') { cargarResJornadasSelect(); cargarAdminResultados(); }
    else if (sub === 'tabla') { cargarTablaJornadasSelect(); cargarAdminTabla(); }
}

async function initClasifTab() {
    switchClasifSub('equipos');
}

// --- Equipos ---
async function cargarAdminEquipos() {
    var r = await api('equipos_list');
    adminEquipos = r.ok ? r.data : [];
    var c = document.getElementById('admin-equipos-list');
    if (!adminEquipos.length) {
        c.innerHTML = '<div class="text-center py-10"><div class="w-16 h-16 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center text-2xl">🏓</div><p class="text-gray-500 font-bold text-sm">Sin equipos</p></div>';
        return;
    }
    c.innerHTML = adminEquipos.map(function(eq, idx) {
        return '<div class="bg-white rounded-xl border border-gray-100 p-3.5 flex items-center gap-3 card-press fade-in" style="animation-delay:' + (idx*.03) + 's">' +
            '<span class="text-2xl shrink-0">' + (eq.logo_emoji || '🏓') + '</span>' +
            '<div class="flex-1 min-w-0"><p class="text-sm font-bold text-gray-800 truncate">' + esc(eq.nombre) + '</p></div>' +
            '<div class="flex gap-1.5 shrink-0">' +
                '<button onclick="editEquipo(' + eq.id + ')" class="p-2 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all active:scale-90"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>' +
                '<button onclick="deleteEquipo(' + eq.id + ')" class="p-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-all active:scale-90"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>' +
            '</div>' +
        '</div>';
    }).join('');
}

function openEquipoForm(data) {
    var isEdit = !!data;
    var html = '<div class="flex items-center gap-3 mb-5"><div class="w-11 h-11 rounded-2xl ' + (isEdit ? 'bg-blue-100' : 'bg-emerald-100') + ' flex items-center justify-center text-xl">' + (isEdit && data.logo_emoji ? data.logo_emoji : '🏓') + '</div><div><h3 class="text-lg font-black text-gray-800">' + (isEdit ? 'Editar' : 'Nuevo') + ' Equipo</h3><p class="text-sm text-gray-400">' + (isEdit ? 'Modificar datos' : 'Agregar equipo') + '</p></div></div>' +
        '<form onsubmit="saveEquipo(event, ' + (isEdit ? data.id : 'null') + ')" class="space-y-3.5">' +
            '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Nombre *</label>' +
                '<input type="text" id="eq-nombre" required maxlength="120" value="' + (isEdit ? esc(data.nombre) : '') + '" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none text-sm bg-gray-50 focus:bg-white transition-all" placeholder="Club Pádel"></div>' +
            '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Emoji</label>' +
                '<input type="text" id="eq-emoji" maxlength="10" value="' + (isEdit ? esc(data.logo_emoji) : '🏓') + '" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none text-sm bg-gray-50 focus:bg-white transition-all text-2xl text-center" placeholder="🏓"></div>' +
            '<button type="submit" class="ripple-btn w-full py-4 bg-gradient-to-r from-slate-700 to-slate-900 text-white font-black rounded-2xl shadow-lg active:scale-[.97] transition-transform text-sm">' + (isEdit ? 'Guardar' : 'Crear equipo') + '</button>' +
        '</form>';
    openAdminModal(html);
}

function editEquipo(id) { var eq = adminEquipos.find(function(x) { return x.id == id; }); if (eq) openEquipoForm(eq); }

async function saveEquipo(e, id) {
    e.preventDefault();
    var params = { nombre: document.getElementById('eq-nombre').value, logo_emoji: document.getElementById('eq-emoji').value };
    if (id) params.id = id;
    var r = await api(id ? 'equipo_update' : 'equipo_create', params, 'POST');
    closeAdminModal();
    if (r.ok) { toast(r.mensaje || 'Equipo guardado'); cargarAdminEquipos(); cargarStats(); }
    else toast(r.error || 'Error', 'red');
}

async function deleteEquipo(id) {
    if (!confirm('¿Eliminar este equipo?')) return;
    var r = await api('equipo_delete', { id: id }, 'POST');
    if (r.ok) { toast(r.mensaje || 'Equipo eliminado'); cargarAdminEquipos(); cargarStats(); }
    else toast(r.error || 'Error', 'red');
}

// --- Resultados ---
async function cargarResJornadasSelect() {
    var r = await api('jornadas_list');
    adminJornadas = r.ok ? r.data : [];
    var sel = document.getElementById('admin-res-jornada');
    sel.innerHTML = '<option value="">Todas las jornadas...</option>' +
        adminJornadas.map(function(j) { return '<option value="' + j.id + '">' + esc(j.nombre) + ' (' + j.fecha + ')</option>'; }).join('');
}

async function cargarAdminResultados() {
    var jornadaId = document.getElementById('admin-res-jornada').value;
    var params = jornadaId ? { jornada_id: jornadaId } : {};
    var r = await api('resultados_list', params);
    adminResultados = r.ok ? r.data : [];
    var c = document.getElementById('admin-resultados-list');
    if (!adminResultados.length) {
        c.innerHTML = '<div class="text-center py-10"><div class="w-16 h-16 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center text-2xl">📊</div><p class="text-gray-500 font-bold text-sm">Sin resultados</p><p class="text-gray-400 text-xs mt-1">Agrega resultados de partidos</p></div>';
        return;
    }
    c.innerHTML = adminResultados.map(function(r, idx) {
        var localWin = parseInt(r.puntos_local) > parseInt(r.puntos_visitante);
        var visitanteWin = parseInt(r.puntos_visitante) > parseInt(r.puntos_local);
        return '<div class="bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm card-press fade-in" style="animation-delay:' + (idx*.03) + 's">' +
            '<div class="text-[10px] text-gray-400 font-semibold mb-2">' + esc(r.jornada_nombre) + '</div>' +
            '<div class="flex items-center gap-2">' +
                '<div class="flex-1 flex items-center gap-1.5 min-w-0">' +
                    '<span class="text-base shrink-0">' + (r.emoji_local || '🏓') + '</span>' +
                    '<span class="text-xs font-bold text-gray-800 truncate ' + (localWin ? '' : 'opacity-60') + '">' + esc(r.equipo_local) + '</span>' +
                '</div>' +
                '<div class="flex items-center gap-1 shrink-0 px-2.5 py-1 rounded-lg bg-gray-100">' +
                    '<span class="text-sm font-black ' + (localWin ? 'text-emerald-700' : 'text-gray-600') + '">' + r.puntos_local + '</span>' +
                    '<span class="text-gray-300 text-xs">-</span>' +
                    '<span class="text-sm font-black ' + (visitanteWin ? 'text-emerald-700' : 'text-gray-600') + '">' + r.puntos_visitante + '</span>' +
                '</div>' +
                '<div class="flex-1 flex items-center gap-1.5 justify-end min-w-0">' +
                    '<span class="text-xs font-bold text-gray-800 truncate text-right ' + (visitanteWin ? '' : 'opacity-60') + '">' + esc(r.equipo_visitante) + '</span>' +
                    '<span class="text-base shrink-0">' + (r.emoji_visitante || '🏓') + '</span>' +
                '</div>' +
                '<div class="flex gap-1 shrink-0 ml-1">' +
                    '<button onclick="editResultado(' + r.id + ')" class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all active:scale-90"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>' +
                    '<button onclick="deleteResultado(' + r.id + ')" class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-all active:scale-90"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>' +
                '</div>' +
            '</div>' +
            (r.observaciones ? '<div class="text-[10px] text-gray-400 mt-1.5 italic">' + esc(r.observaciones) + '</div>' : '') +
        '</div>';
    }).join('');
}

async function openResultadoForm(data) {
    var isEdit = !!data;
    var equiposR = await api('equipos_list');
    var equipos = equiposR.ok ? equiposR.data : [];
    var jornadasR = await api('jornadas_list');
    var jornadas = jornadasR.ok ? jornadasR.data : [];
    var selJornada = document.getElementById('admin-res-jornada') ? document.getElementById('admin-res-jornada').value : '';
    var html = '<div class="flex items-center gap-3 mb-5"><div class="w-11 h-11 rounded-2xl bg-indigo-100 flex items-center justify-center"><svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div><div><h3 class="text-lg font-black text-gray-800">' + (isEdit ? 'Editar' : 'Nuevo') + ' Resultado</h3><p class="text-sm text-gray-400">Registrar marcador del partido</p></div></div>' +
        '<form onsubmit="saveResultado(event, ' + (isEdit ? data.id : 'null') + ')" class="space-y-3.5">' +
            (isEdit ? '' : '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Jornada *</label><select id="r-jornada" required class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-gray-50 focus:bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all">' +
                jornadas.map(function(j) { return '<option value="' + j.id + '"' + (j.id == selJornada ? ' selected' : '') + '>' + esc(j.nombre) + '</option>'; }).join('') +
            '</select></div>') +
            '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Equipo Local *</label><select id="r-local" required class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-gray-50 focus:bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all">' +
                '<option value="">Seleccionar...</option>' +
                equipos.map(function(eq) { return '<option value="' + eq.id + '"' + (isEdit && data.equipo_local_id == eq.id ? ' selected' : '') + '>' + (eq.logo_emoji||'🏓') + ' ' + esc(eq.nombre) + '</option>'; }).join('') +
            '</select></div>' +
            '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Equipo Visitante *</label><select id="r-visitante" required class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-gray-50 focus:bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all">' +
                '<option value="">Seleccionar...</option>' +
                equipos.map(function(eq) { return '<option value="' + eq.id + '"' + (isEdit && data.equipo_visitante_id == eq.id ? ' selected' : '') + '>' + (eq.logo_emoji||'🏓') + ' ' + esc(eq.nombre) + '</option>'; }).join('') +
            '</select></div>' +
            '<div class="grid grid-cols-2 gap-3">' +
                '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Puntos Local</label>' +
                    '<input type="number" id="r-puntos-local" required min="0" max="999" value="' + (isEdit ? data.puntos_local : '0') + '" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-gray-50 focus:bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all text-center text-lg font-black"></div>' +
                '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Puntos Visitante</label>' +
                    '<input type="number" id="r-puntos-visitante" required min="0" max="999" value="' + (isEdit ? data.puntos_visitante : '0') + '" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-gray-50 focus:bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all text-center text-lg font-black"></div>' +
            '</div>' +
            '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Observaciones</label>' +
                '<input type="text" id="r-obs" maxlength="255" value="' + (isEdit && data.observaciones ? esc(data.observaciones) : '') + '" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 text-sm bg-gray-50 focus:bg-white focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none transition-all" placeholder="Nota opcional..."></div>' +
            '<button type="submit" class="ripple-btn w-full py-4 bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-black rounded-2xl shadow-lg active:scale-[.97] transition-transform text-sm">' + (isEdit ? 'Guardar' : 'Registrar resultado') + '</button>' +
        '</form>';
    openAdminModal(html);
}

function editResultado(id) { var r = adminResultados.find(function(x) { return x.id == id; }); if (r) openResultadoForm(r); }

async function saveResultado(e, id) {
    e.preventDefault();
    var params = {
        equipo_local_id: document.getElementById('r-local').value,
        equipo_visitante_id: document.getElementById('r-visitante').value,
        puntos_local: document.getElementById('r-puntos-local').value,
        puntos_visitante: document.getElementById('r-puntos-visitante').value,
        observaciones: document.getElementById('r-obs').value
    };
    if (id) { params.id = id; }
    else { params.jornada_id = document.getElementById('r-jornada').value; }
    var r = await api(id ? 'resultado_update' : 'resultado_create', params, 'POST');
    closeAdminModal();
    if (r.ok) { toast(r.mensaje || 'Resultado guardado'); cargarAdminResultados(); cargarStats(); }
    else toast(r.error || 'Error', 'red');
}

async function deleteResultado(id) {
    if (!confirm('¿Eliminar este resultado?')) return;
    var r = await api('resultado_delete', { id: id }, 'POST');
    if (r.ok) { toast(r.mensaje || 'Resultado eliminado'); cargarAdminResultados(); cargarStats(); }
    else toast(r.error || 'Error', 'red');
}

// --- Tabla preview ---
async function cargarTablaJornadasSelect() {
    var r = await api('jornadas_list');
    var jornadas = r.ok ? r.data : [];
    var sel = document.getElementById('admin-tabla-jornada');
    sel.innerHTML = '<option value="">General (todas las jornadas)</option>' +
        jornadas.map(function(j) { return '<option value="' + j.id + '">' + esc(j.nombre) + ' (' + j.fecha + ')</option>'; }).join('');
}

async function cargarAdminTabla() {
    var jornadaId = document.getElementById('admin-tabla-jornada').value;
    var params = jornadaId ? { jornada_id: jornadaId } : {};
    var r = await api('clasificacion', params);
    var tabla = r.ok ? r.data : [];
    var c = document.getElementById('admin-tabla-content');
    var hasData = tabla.some(function(t) { return t.jj > 0; });
    if (!hasData) {
        c.innerHTML = '<div class="text-center py-10"><div class="w-16 h-16 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center text-2xl">🏆</div><p class="text-gray-500 font-bold text-sm">Sin datos de clasificación</p><p class="text-gray-400 text-xs mt-1">Agrega resultados para ver la tabla</p></div>';
        return;
    }
    var filtrado = tabla.filter(function(t) { return t.jj > 0; });
    var html = '<div class="rounded-2xl overflow-hidden shadow-md border border-gray-100">';
    html += '<div class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 px-3 py-2.5 grid grid-cols-12 gap-1 text-[10px] font-black text-white/80 tracking-wide uppercase">' +
        '<div class="col-span-1 text-center">#</div>' +
        '<div class="col-span-4">Equipo</div>' +
        '<div class="col-span-1 text-center">JJ</div>' +
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
        var posClass = pos <= 3 ? (pos === 1 ? 'bg-yellow-400 text-white' : pos === 2 ? 'bg-gray-400 text-white' : 'bg-amber-600 text-white') : 'bg-gray-200 text-gray-600';
        var difColor = t.dif > 0 ? 'text-emerald-600' : t.dif < 0 ? 'text-red-500' : 'text-gray-500';
        html += '<div class="' + bgClass + ' px-3 py-2.5 grid grid-cols-12 gap-1 items-center border-b border-gray-100/80 text-xs">' +
            '<div class="col-span-1 flex justify-center"><span class="w-5 h-5 rounded flex items-center justify-center text-[9px] font-black ' + posClass + '">' + pos + '</span></div>' +
            '<div class="col-span-4 flex items-center gap-1.5 min-w-0"><span class="text-sm shrink-0">' + (t.emoji || '🏓') + '</span><span class="font-bold text-gray-800 truncate text-[11px]">' + esc(t.nombre) + '</span></div>' +
            '<div class="col-span-1 text-center font-medium text-gray-500">' + t.jj + '</div>' +
            '<div class="col-span-1 text-center font-black text-gray-800">' + t.jg + '</div>' +
            '<div class="col-span-1 text-center font-medium text-gray-500">' + t.jp + '</div>' +
            '<div class="col-span-1 text-center font-bold text-gray-700">' + t.pg + '</div>' +
            '<div class="col-span-1 text-center font-medium text-gray-500">' + t.pp + '</div>' +
            '<div class="col-span-2 text-center font-black ' + difColor + '">' + (t.dif > 0 ? '+' : '') + t.dif + '</div>' +
        '</div>';
    }
    html += '</div>';
    c.innerHTML = html;
}

// ======= INIT =======
function initAdmin() { switchTab('dashboard'); }

// ======= DARK MODE =======
function toggleDarkMode() {
    var html = document.getElementById('html-root');
    html.classList.toggle('dark');
    var isDark = html.classList.contains('dark');
    localStorage.setItem('darkMode', isDark);
    var icon = document.getElementById('darkIcon');
    if (icon) {
        icon.innerHTML = isDark
            ? '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>'
            : '<path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>';
    }
}

// ======= EXPORT CSV =======
function exportarCSV() {
    var partidoId = document.getElementById('insc-partido') ? document.getElementById('insc-partido').value : '';
    var jornadaId = document.getElementById('insc-jornada') ? document.getElementById('insc-jornada').value : '';
    var url = 'api.php?action=exportar_inscripciones';
    if (partidoId) url += '&partido_id=' + partidoId;
    else if (jornadaId) url += '&jornada_id=' + jornadaId;
    window.location = url;
    toast('Exportando CSV...', 'green');
}

// ======= DUPLICATE PARTIDO =======
async function duplicarPartido(id) {
    var r = await api('duplicar_partido', { id: id }, 'POST');
    if (r.ok) { toast('Partido duplicado'); cargarAdminPartidos(); }
    else toast(r.error || 'Error', 'red');
}

// ======= EDIT INSCRIPCION =======
function openEditInscripcion(ins) {
    var html = '<div class="flex items-center gap-3 mb-5"><div class="w-11 h-11 rounded-2xl bg-blue-100 flex items-center justify-center"><svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></div><div><h3 class="text-lg font-black text-gray-800">Editar Inscripción</h3><p class="text-sm text-gray-400">Modificar datos del jugador</p></div></div>' +
        '<form onsubmit="saveEditInscripcion(event,' + ins.id + ')" class="space-y-3.5">' +
            '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Nombre *</label>' +
                '<input type="text" id="ei-nombre" required maxlength="100" value="' + esc(ins.nombre) + '" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none text-sm bg-gray-50 focus:bg-white transition-all"></div>' +
            '<div><label class="block text-sm font-bold text-gray-700 mb-1.5">Teléfono</label>' +
                '<input type="text" id="ei-telefono" maxlength="20" value="' + esc(ins.telefono || '') + '" class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-slate-500 focus:ring-4 focus:ring-slate-100 outline-none text-sm bg-gray-50 focus:bg-white transition-all"></div>' +
            '<button type="submit" class="ripple-btn w-full py-4 bg-gradient-to-r from-slate-700 to-slate-900 text-white font-black rounded-2xl shadow-lg active:scale-[.97] transition-transform text-sm">Guardar cambios</button>' +
        '</form>';
    openAdminModal(html);
}

async function saveEditInscripcion(e, id) {
    e.preventDefault();
    var r = await api('inscripcion_update', { id: id, nombre: document.getElementById('ei-nombre').value, telefono: document.getElementById('ei-telefono').value }, 'POST');
    closeAdminModal();
    if (r.ok) { toast('Inscripción actualizada'); cargarAdminInscripciones(); }
    else toast(r.error || 'Error', 'red');
}

// ======= BULK ACTIONS =======
var selectedInscripciones = new Set();

function toggleBulkSelect(id, cb) {
    if (cb.checked) selectedInscripciones.add(id);
    else selectedInscripciones.delete(id);
    var count = selectedInscripciones.size;
    document.getElementById('bulk-count').textContent = count;
    document.getElementById('bulk-bar').className = count > 0 ? 'mb-3 bg-slate-800 text-white rounded-xl p-3 flex items-center justify-between gap-2 fade-in' : 'hidden mb-3 bg-slate-800 text-white rounded-xl p-3 flex items-center justify-between gap-2';
}

async function bulkCancelar() {
    if (!selectedInscripciones.size) return;
    if (!confirm('¿Cancelar ' + selectedInscripciones.size + ' inscripción(es)?')) return;
    var ids = Array.from(selectedInscripciones).join(',');
    var r = await api('bulk_cancelar', { ids: ids }, 'POST');
    if (r.ok) { toast(r.mensaje || 'Canceladas'); selectedInscripciones.clear(); cargarAdminInscripciones(); }
    else toast(r.error || 'Error', 'red');
}

async function bulkReserva() {
    if (!selectedInscripciones.size) return;
    if (!confirm('¿Mover ' + selectedInscripciones.size + ' inscripción(es) a reserva?')) return;
    var ids = Array.from(selectedInscripciones).join(',');
    var r = await api('bulk_hacer_reserva', { ids: ids }, 'POST');
    if (r.ok) { toast(r.mensaje || 'Movidas a reserva'); selectedInscripciones.clear(); cargarAdminInscripciones(); }
    else toast(r.error || 'Error', 'red');
}

// ======= SEARCH FILTER =======
function filtrarInscripcionesLocal() {
    var q = (document.getElementById('insc-search').value || '').toLowerCase();
    var rows = document.querySelectorAll('#admin-insc-list > div > div, #admin-insc-list > div');
    rows.forEach(function(row) {
        var text = (row.textContent || '').toLowerCase();
        if (!q || text.indexOf(q) !== -1) row.style.display = '';
        else row.style.display = 'none';
    });
}

// ======= AUDIT LOG =======
var auditPage = 1;
async function cargarAuditLog(page) {
    page = page || 1;
    if (page < 1) page = 1;
    auditPage = page;
    var r = await api('audit_log_list', { page: page });
    if (!r.ok) { document.getElementById('admin-audit-list').innerHTML = '<p class="text-gray-400 text-sm text-center py-8">Error al cargar log</p>'; return; }
    var logs = r.data || [];
    var c = document.getElementById('admin-audit-list');
    if (!logs.length) {
        c.innerHTML = '<div class="text-center py-14"><div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-gray-100 to-gray-50 rounded-full flex items-center justify-center shadow-inner text-3xl">📋</div><p class="text-gray-500 font-bold">Sin actividad registrada</p></div>';
        document.getElementById('audit-pagination').style.display = 'none';
        return;
    }
    c.innerHTML = logs.map(function(l, idx) {
        var actionIcons = {
            'login': '🔐', 'jornada_create': '📅', 'jornada_update': '✏️', 'jornada_delete': '🗑️',
            'partido_create': '🎾', 'partido_update': '✏️', 'partido_delete': '🗑️',
            'equipo_create': '🏓', 'equipo_update': '✏️', 'equipo_delete': '🗑️',
            'resultado_create': '📊', 'resultado_update': '✏️', 'resultado_delete': '🗑️',
            'inscripcion_update': '✏️', 'cancelar_inscripcion': '❌', 'duplicar_partido': '📋'
        };
        var icon = actionIcons[l.accion] || '📌';
        return '<div class="bg-white rounded-xl border border-gray-100 p-3.5 flex items-start gap-3 fade-in" style="animation-delay:' + (idx*.03) + 's">' +
            '<span class="text-lg shrink-0 mt-0.5">' + icon + '</span>' +
            '<div class="flex-1 min-w-0">' +
                '<p class="text-sm font-bold text-gray-800">' + esc(l.accion) + '</p>' +
                '<p class="text-[11px] text-gray-400 mt-0.5 truncate">' + esc(l.detalle || '') + '</p>' +
                '<p class="text-[10px] text-gray-300 mt-1">' + esc(l.usuario || 'sistema') + ' &bull; ' + esc(l.created_at) + '</p>' +
            '</div>' +
        '</div>';
    }).join('');
    // Pagination
    var pag = document.getElementById('audit-pagination');
    pag.style.display = 'flex';
    document.getElementById('audit-page-info').textContent = 'Página ' + r.page + ' / ' + r.pages;
    document.getElementById('audit-prev').disabled = r.page <= 1;
    document.getElementById('audit-next').disabled = r.page >= r.pages;
    document.getElementById('audit-prev').style.opacity = r.page <= 1 ? '.4' : '1';
    document.getElementById('audit-next').style.opacity = r.page >= r.pages ? '.4' : '1';
}

// ======= WHATSAPP NOTIFICATION =======
function openWhatsApp(telefono, nombre, categoria, fecha) {
    if (!telefono) { toast('Sin número de teléfono', 'amber'); return; }
    var tel = telefono.replace(/[^0-9+]/g, '');
    if (!tel.startsWith('+')) tel = '+506' + tel;
    var msg = '¡Hola ' + nombre + '! 🏓\nTu inscripción en *Interliga Pádel* para *' + categoria + '* el ' + fecha + ' ha sido confirmada. ¡Nos vemos en la cancha! 🎾';
    window.open('https://wa.me/' + tel.replace('+', '') + '?text=' + encodeURIComponent(msg), '_blank');
}

// Override switchTab to support actividad
var _origSwitchTab = switchTab;
switchTab = function(tab) {
    currentTab = tab;
    document.querySelectorAll('.tab-content').forEach(function(el) { el.classList.remove('active'); });
    document.getElementById('tab-' + tab).classList.add('active');
    document.querySelectorAll('.admin-tab').forEach(function(b) {
        var isActive = b.dataset.tab === tab;
        if (isActive) {
            b.className = 'admin-tab ripple-btn shrink-0 px-4 py-2.5 rounded-xl text-xs font-black text-white bg-white/15 border border-white/10 shadow-sm';
        } else {
            b.className = 'admin-tab ripple-btn shrink-0 px-4 py-2.5 rounded-xl text-xs font-bold text-white/40 hover:text-white hover:bg-white/5 transition-all';
        }
    });
    if (tab === 'dashboard') cargarStats();
    else if (tab === 'jornadas') cargarAdminJornadas();
    else if (tab === 'partidos') cargarJornadasSelect();
    else if (tab === 'inscripciones') cargarJornadasSelectInsc();
    else if (tab === 'clasificacion') initClasifTab();
    else if (tab === 'actividad') cargarAuditLog(1);
};

checkLogin();
</script>
</body>
</html>
