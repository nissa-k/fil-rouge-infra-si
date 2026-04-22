async function logout() {
    try {
        const result = await apiFetch("/api/logout", {
            method: "POST"
        });

        if (result.success) {
            window.location.href = "login.html";
        } else {
            alert(result.message || "Erreur lors de la déconnexion.");
        }
    } catch (error) {
        console.error("Erreur logout :", error);
        alert("Erreur réseau lors de la déconnexion.");
    }
}

function escapeQuotes(text) {
    return String(text)
        .replace(/'/g, "\\'")
        .replace(/"/g, "&quot;");
}

function getModeFromUrl() {
    const params = new URLSearchParams(window.location.search);
    const status = params.get("status");

    if (status === "en_cours") return "en_cours";
    if (status === "traitee") return "traitee";
    if (status === "refusee") return "refusee";

    return "all";
}

function renderPageTitle(mode) {
    const h1 = document.querySelector("h1");
    if (!h1) return;

    if (mode === "en_cours") h1.textContent = "Requêtes en cours";
    else if (mode === "traitee") h1.textContent = "Requêtes traitées";
    else if (mode === "refusee") h1.textContent = "Requêtes refusées";
    else h1.textContent = "Modifier / gérer les requêtes";
}

function renderButtons(ticket, mode) {
    if (mode === "en_cours") {
        return `
            <button onclick="treatTicket(${ticket.id})">Traiter</button>
            <button onclick="refuseTicket(${ticket.id})">Refuser</button>
        `;
    }

    if (mode === "traitee" || mode === "refusee") {
        return `
            <button onclick="editTicket(${ticket.id}, '${escapeQuotes(ticket.title)}', '${escapeQuotes(ticket.description)}', '${ticket.priority}', '${ticket.status}')">Modifier</button>
            <button onclick="deleteTicket(${ticket.id})">Supprimer</button>
        `;
    }

    return `
        <button onclick="treatTicket(${ticket.id})">Traiter</button>
        <button onclick="refuseTicket(${ticket.id})">Refuser</button>
        <button onclick="editTicket(${ticket.id}, '${escapeQuotes(ticket.title)}', '${escapeQuotes(ticket.description)}', '${ticket.priority}', '${ticket.status}')">Modifier</button>
        <button onclick="deleteTicket(${ticket.id})">Supprimer</button>
    `;
}

async function loadTickets() {
    const container = document.getElementById("ticketsContainer") || document.getElementById("tickets");
    if (!container) return;

    try {
        const result = await apiFetch("/api/admin/tickets", {
            method: "GET"
        });

        if (!result.success) {
            container.innerHTML = `<p>${result.message}</p>`;
            return;
        }

        const mode = getModeFromUrl();
        renderPageTitle(mode);

        let tickets = result.tickets || [];

        if (mode !== "all") {
            tickets = tickets.filter(ticket => ticket.status === mode);
        }

        if (tickets.length === 0) {
            container.innerHTML = "<p>Aucun ticket trouvé.</p>";
            return;
        }

        container.innerHTML = tickets.map(ticket => `
            <div class="ticket-card">
                <h3>${ticket.title}</h3>
                <p><b>ID :</b> ${ticket.id}</p>
                <p><b>Utilisateur :</b> ${ticket.full_name} (${ticket.email})</p>
                <p><b>Description :</b> ${ticket.description}</p>
                <p><b>Priorité :</b> ${ticket.priority}</p>
                <p><b>Statut :</b> ${ticket.status}</p>
                ${renderButtons(ticket, mode)}
            </div>
        `).join("");
    } catch (error) {
        console.error(error);
        container.innerHTML = "<p>Erreur de chargement des tickets.</p>";
    }
}

async function treatTicket(id) {
    try {
        await apiFetch(`/api/admin/tickets/${id}/status`, {
            method: "PUT",
            body: JSON.stringify({ status: "traitee" })
        });
        location.reload();
    } catch (error) {
        console.error(error);
        alert("Erreur lors du traitement.");
    }
}

async function refuseTicket(id) {
    try {
        await apiFetch(`/api/admin/tickets/${id}/status`, {
            method: "PUT",
            body: JSON.stringify({ status: "refusee" })
        });
        location.reload();
    } catch (error) {
        console.error(error);
        alert("Erreur lors du refus.");
    }
}

async function deleteTicket(id) {
    if (!confirm("Supprimer ce ticket ?")) return;

    try {
        await apiFetch(`/api/admin/tickets/${id}`, {
            method: "DELETE"
        });
        location.reload();
    } catch (error) {
        console.error(error);
        alert("Erreur lors de la suppression.");
    }
}

async function editTicket(id, currentTitle, currentDescription, currentPriority, currentStatus) {
    const title = prompt("Nouveau titre :", currentTitle);
    if (title === null) return;

    const description = prompt("Nouvelle description :", currentDescription);
    if (description === null) return;

    const priority = prompt("Nouvelle priorité (low, medium, high) :", currentPriority);
    if (priority === null) return;

    const status = prompt("Nouveau statut (en_cours, traitee, refusee) :", currentStatus);
    if (status === null) return;

    try {
        const result = await apiFetch(`/api/admin/tickets/${id}`, {
            method: "PUT",
            body: JSON.stringify({
                title,
                description,
                priority,
                status
            })
        });

        alert(result.message || "Ticket modifié.");
        location.reload();
    } catch (error) {
        console.error(error);
        alert("Erreur lors de la modification.");
    }
}

async function loadUsers() {
    const container = document.getElementById("usersContainer") || document.getElementById("users");
    if (!container) return;

    try {
        const result = await apiFetch("/api/admin/users", {
            method: "GET"
        });

        if (!result.success) {
            container.innerHTML = `<p>${result.message}</p>`;
            return;
        }

        container.innerHTML = (result.users || []).map(user => `
            <div class="ticket-card">
                <h3>${user.full_name}</h3>
                <p><b>ID :</b> ${user.id}</p>
                <p><b>Email :</b> ${user.email}</p>
                <p><b>Rôle :</b> ${user.role_name}</p>
                <button onclick="deleteUser(${user.id})">Supprimer</button>
            </div>
        `).join("");
    } catch (error) {
        console.error(error);
        container.innerHTML = "<p>Erreur de chargement des utilisateurs.</p>";
    }
}

async function deleteUser(id) {
    if (!confirm("Supprimer cet utilisateur ?")) return;

    try {
        await apiFetch(`/api/admin/users/${id}`, {
            method: "DELETE"
        });
        location.reload();
    } catch (error) {
        console.error(error);
        alert("Erreur lors de la suppression utilisateur.");
    }
}

document.addEventListener("DOMContentLoaded", () => {
    loadTickets();
    loadUsers();
});