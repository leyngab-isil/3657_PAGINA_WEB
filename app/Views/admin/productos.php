<?php
$title = 'Productos';
$pageScript = 'productos.js';
ob_start();
?>
<section class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
  <div>
    <p class="text-sm font-semibold text-sky-600">Consumo de API REST</p>
    <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Gestión de productos</h2>
    <p class="mt-2 max-w-2xl text-slate-500">Prueba el listado, la búsqueda mediante query parameters y las operaciones CRUD sin recargar la página.</p>
  </div>
  <button id="newProductButton" class="inline-flex items-center justify-center rounded-xl bg-sky-600 px-5 py-3 font-semibold text-white shadow-lg shadow-sky-600/15 hover:bg-sky-700">+ Nuevo producto</button>
</section>

<div id="productAlert" class="mt-5 hidden" role="alert"></div>

<section class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
  <form id="productFilters" class="grid gap-4 border-b border-slate-200 p-4 md:grid-cols-2 xl:grid-cols-[minmax(260px,1fr)_180px_180px_auto]">
    <div>
      <label for="productSearch" class="mb-1.5 block text-sm font-medium text-slate-700">Buscar</label>
      <input id="productSearch" name="q" type="search" class="form-light" placeholder="Nombre, descripción o código...">
    </div>
    <div>
      <label for="minPrice" class="mb-1.5 block text-sm font-medium text-slate-700">Precio mínimo</label>
      <input id="minPrice" name="precio_min" type="number" min="0" step="0.01" class="form-light" placeholder="0.00">
    </div>
    <div>
      <label for="productLimit" class="mb-1.5 block text-sm font-medium text-slate-700">Registros</label>
      <select id="productLimit" name="limit" class="form-light">
        <option value="10">10 por página</option>
        <option value="25">25 por página</option>
        <option value="50">50 por página</option>
      </select>
    </div>
    <div class="flex items-end gap-2">
      <button class="flex-1 rounded-xl bg-slate-900 px-4 py-3 font-semibold text-white hover:bg-slate-800">Buscar</button>
      <button id="clearFilters" type="button" class="rounded-xl border border-slate-200 px-4 py-3 font-semibold text-slate-600 hover:bg-slate-50">Limpiar</button>
    </div>
  </form>

  <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
    <p id="requestPreview" class="min-w-0 truncate font-mono text-xs text-slate-500"></p>
    <button id="reloadProducts" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">Actualizar</button>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
        <tr>
          <th class="px-5 py-4">ID</th>
          <th class="px-5 py-4">Producto</th>
          <th class="px-5 py-4">Precio</th>
          <th class="px-5 py-4 text-center">Acciones</th>
        </tr>
      </thead>
      <tbody id="productsTable" class="divide-y divide-slate-100">
        <tr>
          <td colspan="4" class="px-5 py-14 text-center text-slate-400">Preparando consulta...</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="flex flex-col gap-3 border-t border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
    <span id="productPaginationInfo" class="text-sm text-slate-500"></span>
    <div id="productPagination" class="flex gap-2"></div>
  </div>
</section>

<div id="productModal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-slate-950/65 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="productModalTitle">
  <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h3 id="productModalTitle" class="text-xl font-bold">Nuevo producto</h3>
        <p class="mt-1 text-sm text-slate-500">Completa los datos y envíalos a la API.</p>
      </div>
      <button data-modal-close class="grid h-9 w-9 place-items-center rounded-lg hover:bg-slate-100" aria-label="Cerrar">✕</button>
    </div>
    <form id="productForm" class="mt-6 space-y-4" data-mode="create">
      <input type="hidden" name="id">
      <div><label class="mb-1.5 block text-sm font-medium">Nombre</label><input name="nombre" required minlength="2" class="form-light"></div>
      <div class="grid gap-4 sm:grid-cols-2">
        <div><label class="mb-1.5 block text-sm font-medium">Precio</label><input name="precio" type="number" min="0" step="0.01" required class="form-light"></div>
      </div>
      <div><label class="mb-1.5 block text-sm font-medium">Descripción</label><textarea name="descripcion" rows="3" class="form-light resize-y"></textarea></div>
      <div class="flex justify-end gap-3 pt-3"><button type="button" data-modal-close class="rounded-xl border border-slate-200 px-4 py-2.5 font-medium">Cancelar</button><button id="saveProductButton" class="rounded-xl bg-sky-600 px-5 py-2.5 font-semibold text-white hover:bg-sky-700">Guardar</button></div>
    </form>
  </div>
</div>
<?php $content = ob_get_clean();
require __DIR__ . '/../layouts/admin.php'; ?>