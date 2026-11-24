<aside id="sidebar"
  class="fixed inset-y-0 left-0 z-50 flex h-screen w-72 shrink-0 -translate-x-full flex-col bg-slate-950 text-white shadow-2xl transition-transform duration-200 lg:static lg:translate-x-0 lg:shadow-none">
  <div class="flex h-16 shrink-0 items-center justify-between border-b border-white/10 px-5">
    <a href="<?= BASE_URL ?>/admin" class="flex min-w-0 items-center gap-3 font-bold">
      <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-sky-500 shadow-lg shadow-sky-500/20">A</span>
      <span class="truncate">AdminPanel</span>
    </a>
    <button data-sidebar-close class="grid h-9 w-9 place-items-center rounded-lg hover:bg-white/10 lg:hidden" aria-label="Cerrar menú">✕</button>
  </div>

  <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto p-4" aria-label="Navegación administrativa">
    <a data-admin-link href="<?= BASE_URL ?>/admin" class="admin-link"><span>▦</span><span>Dashboard</span></a>
    <a data-admin-link href="<?= BASE_URL ?>/admin/productos" class="admin-link"><span>□</span><span>Productos</span></a>
  </nav>

  <div class="shrink-0 border-t border-white/10 p-4">
    <form action="<?= BASE_URL ?>/logout" method="post">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
      <button class="w-full rounded-xl border border-white/10 px-4 py-3 text-left text-sm text-slate-300 hover:bg-white/10 hover:text-white">Cerrar sesión</button>
    </form>
  </div>
</aside>
<div id="sidebarBackdrop" class="fixed inset-0 z-40 hidden bg-slate-950/60 backdrop-blur-sm lg:hidden"></div>
