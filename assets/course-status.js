const JSON_URL = "/json/courses.json";
const POLL_INTERVAL = 20000; // 20s

function formatDate(d) {
  return new Date(d).toLocaleString();
}

async function fetchStatus() {
  try {
    const res = await fetch(JSON_URL, { cache: "no-store" });
    if (!res.ok) throw new Error(res.status + " " + res.statusText);
    const data = await res.json();
    updateDOM(data);
  } catch (e) {
    console.error("Failed to fetch course status", e);
    const fetchedAt = document.getElementById("fetched_at");
    if (fetchedAt)
      fetchedAt.textContent = new Date().toLocaleTimeString() + " (error)";
  }
}

function updateDOM(data) {
  const lastUpdate = document.getElementById("last_update");
  const count = document.getElementById("count");
  const status = document.getElementById("status");
  const message = document.getElementById("message");
  const fetchedAt = document.getElementById("fetched_at");

  if (lastUpdate) lastUpdate.textContent = data.last_update ?? "-";
  if (count) count.textContent = data.count ?? "-";
  if (status) {
    status.textContent = data.status ?? "-";
    status.className = status.className.replace(/text-\w+-\d{3}/g, "");
    if (data.status === "success") status.classList.add("text-green-600");
    else if (data.status === "warning") status.classList.add("text-yellow-600");
    else if (data.status === "error") status.classList.add("text-red-600");
    else status.classList.add("text-gray-700");
  }
  if (message) message.textContent = data.message ?? "-";
  if (fetchedAt) fetchedAt.textContent = new Date().toLocaleTimeString();
}

// start polling immediately
fetchStatus();
setInterval(fetchStatus, POLL_INTERVAL);
