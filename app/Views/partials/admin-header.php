<header class="z-30 flex h-16 shrink-0 items-center border-b border-slate-200 bg-white/95 px-4 shadow-sm backdrop-blur sm:px-6">
  <button data-sidebar-open class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 hover:bg-slate-50 lg:hidden" aria-label="Abrir menú">☰</button>
  <div class="ml-3 min-w-0 sm:ml-0">
    <p class="hidden text-xs font-semibold uppercase tracking-wider text-slate-400 sm:block">Panel de control</p>
    <h1 class="truncate font-bold text-slate-800"><?= htmlspecialchars($title) ?></h1>
  </div>
  <div class="ml-auto flex items-center gap-3">
    <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-2.5 py-2 sm:px-3">
      <span class="grid h-8 w-8 place-items-center rounded-full bg-sky-100 font-bold text-sky-700"><?= strtoupper(substr($user['name'] ?? 'A', 0, 1)) ?></span>
      <div class="hidden sm:block">
        <p class="max-w-40 truncate text-sm font-semibold"><?= htmlspecialchars($user['name'] ?? 'Administrador') ?></p>
        <p class="max-w-40 truncate text-xs text-slate-400"><?= htmlspecialchars($user['email'] ?? '') ?></p>
      </div>
    </div>
  </div>
</header>
