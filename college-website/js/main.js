// ===== Storage helpers (browser ki localStorage mein data save hota hai) =====
function load(key, fallback) {
  try {
    const v = localStorage.getItem(key);
    return v ? JSON.parse(v) : fallback;
  } catch (e) {
    return fallback;
  }
}
function save(key, value) {
  try {
    localStorage.setItem(key, JSON.stringify(value));
  } catch (e) {
    alert("Data save nahi ho saka.");
  }
}

const getCourses = () => load("courses", DEFAULT_COURSES);
const getNotifications = () =>
  load("notifications", DEFAULT_NOTIFICATIONS).sort((a, b) => b.date.localeCompare(a.date));
const getResults = () => load("results", DEFAULT_RESULTS);
const getApplications = () => load("applications", []);

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, (c) =>
    ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c])
  );
}

function formatDate(d) {
  return new Date(d + "T00:00:00").toLocaleDateString("en-GB", {
    day: "numeric", month: "short", year: "numeric",
  });
}

// Dono phone numbers clickable links ki shakal mein
function phoneLinks(sep = " / ") {
  return [COLLEGE.phone, COLLEGE.phone2].filter(Boolean)
    .map(n => `<a href="tel:${n.replace(/-/g, "")}">${n}</a>`).join(sep);
}

// ===== Poster lightbox (tasveer par click karein to bari dikhe) =====
function openLightbox(src, title) {
  let box = document.getElementById("lightbox");
  if (!box) {
    box = document.createElement("div");
    box.id = "lightbox";
    box.onclick = () => box.classList.remove("open");
    document.body.appendChild(box);
  }
  box.innerHTML = `<img src="${src}" alt="${escapeHtml(title)}"><p>${escapeHtml(title)} · band karne ke liye click karein</p>`;
  box.classList.add("open");
}
document.addEventListener("keydown", e => {
  if (e.key === "Escape") document.getElementById("lightbox")?.classList.remove("open");
});

// ===== Header + Footer har page par =====
const PAGES = [
  ["index.html", "Home"],
  ["courses.html", "Courses"],
  ["apply.html", "Apply"],
  ["notifications.html", "Notifications"],
  ["results.html", "Results"],
  ["contact.html", "Contact"],
];

function renderLayout() {
  const current = location.pathname.split("/").pop() || "index.html";
  const unread = getNotifications().filter((n) => n.important).length;

  const header = document.getElementById("site-header");
  if (header) {
    header.innerHTML = `
      <div class="topbar">
        <span>🎓 ${COLLEGE.admissionBadge}</span>
        <span>📞 ${phoneLinks()}</span>
        ${COLLEGE.email ? `<span>✉️ ${COLLEGE.email}</span>` : ""}
      </div>
      <nav class="navbar">
        <a href="index.html" class="brand">
          <img src="images/logo.png" alt="" class="logo">
          <span class="brand-text">
            <strong>PRIME</strong>
            <small>Vocational Institute · ${COLLEGE.city}</small>
          </span>
        </a>
        <button class="menu-btn" aria-label="Menu" onclick="document.querySelector('.nav-links').classList.toggle('open')">☰</button>
        <ul class="nav-links">
          ${PAGES.map(([href, label]) => `
            <li><a href="${href}" class="${href === current ? "active" : ""}">
              ${label}${label === "Notifications" && unread ? ` <span class="badge">${unread}</span>` : ""}
            </a></li>`).join("")}
        </ul>
      </nav>`;
  }

  const footer = document.getElementById("site-footer");
  if (footer) {
    footer.innerHTML = `
      <div class="footer-grid">
        <div>
          <img src="images/logo.png" alt="" style="width:64px;background:#f1f1f1;border-radius:10px;padding:4px">
          <h3>${COLLEGE.name}, ${COLLEGE.city}</h3>
          <p><em>${COLLEGE.slogan}</em></p>
          <p>${COLLEGE.affiliation}</p>
          ${COLLEGE.established ? `<p>Established ${COLLEGE.established}</p>` : ""}
        </div>
        <div>
          <h3>Quick Links</h3>
          ${PAGES.map(([h, l]) => `<a href="${h}">${l}</a>`).join("")}
        </div>
        <div>
          <h3>Rabta</h3>
          <p>📍 ${COLLEGE.address}</p>
          <p>📞 ${phoneLinks()}</p>
          ${COLLEGE.email ? `<p>✉️ ${COLLEGE.email}</p>` : ""}
          ${COLLEGE.timing ? `<p>🕗 ${COLLEGE.timing}</p>` : ""}
        </div>
      </div>
      <p class="copyright">© ${new Date().getFullYear()} ${COLLEGE.name}. All rights reserved. · <a href="admin.html">Admin</a></p>`;
  }
}

document.addEventListener("DOMContentLoaded", renderLayout);
