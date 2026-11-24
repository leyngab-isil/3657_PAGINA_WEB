"use strict";

document.addEventListener("DOMContentLoaded", () => {
  const API = window.App.api("productos");
  const state = { page: 1, limit: 10, totalPages: 1, products: [] };
  const $ = selector => document.querySelector(selector);
  const table = $("#productsTable");
  if (!table) return;

  const escapeHtml = value => String(value ?? "").replace(/[&<>"']/g, ch => ({
    "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;"
  }[ch]));

  const getArray = payload => {
    if (Array.isArray(payload)) return payload;
    return payload.data || payload.datos || payload.products || payload.productos || [];
  };

  function buildParams() {
    const params = new URLSearchParams();
    const search = $("#productSearch")?.value.trim() || "";
    const minPrice = $("#minPrice")?.value.trim() || "";
    state.limit = Number($("#productLimit")?.value || 10);

    // IMPORTANTE: cambia "search" si tu backend usa otro nombre, por ejemplo q, nombre o buscar.
    if (search) params.set("search", search);
    if (minPrice) params.set("precio_min", minPrice);
    params.set("page", state.page);
    params.set("limit", state.limit);

    return params;
  }

  function showRequest(url) {
    const preview = $("#requestPreview");
    if (preview) preview.textContent = `GET ${url}`;
  }

  async function loadProducts() {
    const params = buildParams();
    const url = `${API}?${params.toString()}`;
    showRequest(url);

    table.innerHTML = '<tr><td colspan="4" class="px-5 py-14 text-center text-slate-400">Consultando API...</td></tr>';

    try {
      const payload = await window.App.request(url);
      
      state.products = getArray(payload).productos;

      state.totalPages = Number(
        Math.floor(payload?.data.total/state.limit) ??
        1
      );

      const total = Number(
        payload?.total ??
        payload?.meta?.total ??
        payload?.data.total
      );

      renderRows();
      renderPagination(total);
    } catch (error) {
      table.innerHTML = `<tr><td colspan="4" class="px-5 py-14 text-center text-rose-600">${escapeHtml(error.message)}</td></tr>`;
      window.App.showAlert?.($("#productAlert"), error.message, "error");
    }
  }

  function renderRows() {
    if (!state.products.length) {
      table.innerHTML = '<tr><td colspan="4" class="px-5 py-14 text-center text-slate-400">No se encontraron productos.</td></tr>';
      return;
    }

    table.innerHTML = state.products.map(product => {
      const id = product.id ?? product.id_producto ?? "";
      const nombre = product.nombre ?? product.name ?? "Sin nombre";
      const precio = Number(product.precio ?? product.price ?? 0);
      const descripcion = product.descripcion ?? product.description ?? "";

      return `<tr class="hover:bg-slate-50/80">
        <td class="whitespace-nowrap px-5 py-4 text-slate-500">${escapeHtml(id)}</td>
        <td class="min-w-56 px-5 py-4">
          <p class="font-semibold text-slate-800">${escapeHtml(nombre)}</p>
          <p class="mt-1 text-xs text-slate-400">${escapeHtml(descripcion)}</p>
        </td>
        <td class="whitespace-nowrap px-5 py-4 font-medium">S/ ${precio.toFixed(2)}</td>
        <td class="whitespace-nowrap px-5 py-4 text-right">
          <button data-edit="${escapeHtml(id)}" class="mr-2 rounded-lg bg-amber-50 px-3 py-2 font-medium text-amber-700 hover:bg-amber-100">Editar</button>
          <button data-delete="${escapeHtml(id)}" class="rounded-lg bg-rose-50 px-3 py-2 font-medium text-rose-700 hover:bg-rose-100">Eliminar</button>
        </td>
      </tr>`;
    }).join("");
  }

  function renderPagination(total) {
    const info = $("#productPaginationInfo");
    const pagination = $("#productPagination");
    if (info) info.textContent = `Página ${state.page} de ${Math.max(1, state.totalPages)} · ${total} registros`;
    if (pagination) {
      pagination.innerHTML = `
        <button data-page="${state.page - 1}" ${state.page <= 1 ? "disabled" : ""} class="rounded-lg border border-slate-200 px-3 py-2 text-sm disabled:opacity-40">Anterior</button>
        <button data-page="${state.page + 1}" ${state.page >= state.totalPages ? "disabled" : ""} class="rounded-lg border border-slate-200 px-3 py-2 text-sm disabled:opacity-40">Siguiente</button>`;
    }
  }

  function openModal(product = null) {
    const modal = $("#productModal");
    const form = $("#productForm");
    if (!modal || !form) return;

    form.reset();
    form.dataset.mode = product ? "edit" : "create";
    form.elements.id.value = product?.id ?? product?.id_producto ?? "";
    form.elements.nombre.value = product?.nombre ?? product?.name ?? "";
    form.elements.precio.value = product?.precio ?? product?.price ?? "";
    if (form.elements.descripcion) form.elements.descripcion.value = product?.descripcion ?? product?.description ?? "";

    const title = $("#productModalTitle");
    if (title) title.textContent = product ? "Editar producto" : "Nuevo producto";

    modal.classList.remove("hidden");
    modal.classList.add("flex");
    document.body.classList.add("modal-open");
    form.elements.nombre.focus();
  }

  function closeModal() {
    const modal = $("#productModal");
    modal?.classList.add("hidden");
    modal?.classList.remove("flex");
    document.body.classList.remove("modal-open");
  }

  async function saveProduct(event) {
    event.preventDefault();
    const form = event.currentTarget;
    if (!form.checkValidity()) return form.reportValidity();

    const raw = Object.fromEntries(new FormData(form));
    const editing = form.dataset.mode === "edit";
    const body = {
      nombre: raw.nombre.trim(),
      precio: Number(raw.precio)
    };
    if (raw.descripcion !== undefined) body.descripcion = raw.descripcion.trim();

    const button = $("#saveProductButton");
    if (button) button.disabled = true;

    try {
      await window.App.request(editing ? `${API}/${raw.id}` : API, {
        method: editing ? "PUT" : "POST",
        body: JSON.stringify(body)
      });
      closeModal();
      window.App.showAlert?.($("#productAlert"), editing ? "Producto actualizado correctamente." : "Producto creado correctamente.", "success");
      await loadProducts();
    } catch (error) {
      window.App.showAlert?.($("#productAlert"), error.message, "error");
    } finally {
      if (button) button.disabled = false;
    }
  }

  async function deleteProduct(id) {
    if (!confirm("¿Seguro que quieres eliminar este producto?")) return;
    try {
      await window.App.request(`${API}/${id}`, { method: "DELETE" });
      window.App.showAlert?.($("#productAlert"), "Producto eliminado correctamente.", "success");
      await loadProducts();
    } catch (error) {
      window.App.showAlert?.($("#productAlert"), error.message, "error");
    }
  }

  $("#productFilters")?.addEventListener("submit", event => {
    event.preventDefault();
    state.page = 1;
    loadProducts();
  });

  $("#clearFilters")?.addEventListener("click", () => {
    $("#productFilters")?.reset();
    state.page = 1;
    loadProducts();
  });

  $("#reloadProducts")?.addEventListener("click", loadProducts);
  $("#newProductButton")?.addEventListener("click", () => openModal());
  $("#productForm")?.addEventListener("submit", saveProduct);
  document.querySelectorAll("[data-modal-close]").forEach(button => button.addEventListener("click", closeModal));

  $("#productPagination")?.addEventListener("click", event => {
    const button = event.target.closest("[data-page]");
    if (button && !button.disabled) {
      state.page = Number(button.dataset.page);
      loadProducts();
    }
  });

  table.addEventListener("click", event => {
    const edit = event.target.closest("[data-edit]");
    const remove = event.target.closest("[data-delete]");
    if (edit) openModal(state.products.find(item => String(item.id ?? item.id_producto) === edit.dataset.edit));
    if (remove) deleteProduct(remove.dataset.delete);
  });

  // Búsqueda automática opcional con debounce.
  // El botón Buscar también funciona por el submit del formulario.
  // let debounceTimer;
  // $("#productSearch")?.addEventListener("input", () => {
  //   clearTimeout(debounceTimer);
  //   debounceTimer = setTimeout(() => {
  //     state.page = 1;
  //     loadProducts();
  //   }, 500);
  // });

  document.addEventListener("keydown", event => {
    if (event.key === "Escape") closeModal();
  });

  loadProducts();
});
