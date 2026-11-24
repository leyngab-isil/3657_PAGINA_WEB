"use strict";
document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById("sidebar");
  const backdrop = document.getElementById("sidebarBackdrop");
  const open = () => { sidebar?.classList.remove("-translate-x-full"); backdrop?.classList.remove("hidden"); document.body.classList.add("menu-open"); };
  const close = () => { if (innerWidth < 1024) sidebar?.classList.add("-translate-x-full"); backdrop?.classList.add("hidden"); document.body.classList.remove("menu-open"); };
  document.querySelector("[data-sidebar-open]")?.addEventListener("click", open);
  document.querySelector("[data-sidebar-close]")?.addEventListener("click", close);
  backdrop?.addEventListener("click", close);
  document.addEventListener("keydown", event => { if (event.key === "Escape") close(); });
  window.addEventListener("resize", () => { if (innerWidth >= 1024) { backdrop?.classList.add("hidden"); document.body.classList.remove("menu-open"); } });
  const current = location.pathname.replace(/\/$/, "");
  document.querySelectorAll("[data-admin-link]").forEach(link => { const path = new URL(link.href).pathname.replace(/\/$/, ""); if (path === current) link.classList.add("is-active"); });
});
