"use strict";
window.App = {
  baseUrl: window.APP_URL || "",
  url(path = "") { const p = String(path).replace(/^\/+/, ""); return p ? `${this.baseUrl}/${p}` : `${this.baseUrl}/`; },
  api(path = "") { const p = String(path).replace(/^\/+/, ""); return p ? `${this.baseUrl}/api/${p}` : `${this.baseUrl}/api`; },
  async request(url, options = {}) {
    const response = await fetch(url, { ...options, headers: { Accept: "application/json", ...(options.body ? { "Content-Type": "application/json" } : {}), ...(options.headers || {}) } });
    if (response.status === 204) return null;
    const type = response.headers.get("content-type") || "";
    if (!type.includes("application/json")) { const text = await response.text(); console.error("Respuesta no JSON:", text); throw new Error("La API devolvió HTML o una respuesta no JSON. Revisa la ruta del endpoint."); }
    const data = await response.json();
    if (!response.ok) throw new Error(data.message || data.mensaje || `Error HTTP ${response.status}`);
    return data;
  },
  showAlert(element, message, type = "info") {
    if (!element) return;
    const styles = { success: "border-emerald-200 bg-emerald-50 text-emerald-700", error: "border-rose-200 bg-rose-50 text-rose-700", info: "border-sky-200 bg-sky-50 text-sky-700" };
    element.className = `rounded-xl border p-4 text-sm ${styles[type] || styles.info}`;
    element.textContent = message;
    element.classList.remove("hidden");
  }
};
