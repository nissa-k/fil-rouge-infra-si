async function logout() {

    try {

        const result = await apiFetch("/api/logout", {
            method: "POST"
        });

        if (result.success) {

            window.location.href = "login.html";

        } else {

            alert(result.message || "Erreur logout");
        }

    } catch (error) {

        console.error(error);

        alert("Erreur réseau");
    }
}

function escapeQuotes(text) {

    return String(text)
        .replace(/'/g, "\\'")
        .replace(/"/g, "&quot;");
}

function getStatusBadge(status) {

    if (status === "traitee") {

        return `
            <div class="ticket-status-right status-traitee">
                Traité
            </div>
        `;
    }

    if (status === "refusee") {

        return `
            <div class="ticket-status-right status-refusee">
                Refusé
            </div>
        `;
    }

    return `
        <div class="ticket-status-right status-en-cours">
            En cours
        </div>
    `;
}

function renderButtons(ticket) {

    return `

        <div class="ticket-actions">

            <button onclick="updateTicketStatus(${ticket.id}, 'en_cours')">
                En cours
            </button>

            <button onclick="updateTicketStatus(${ticket.id}, 'traitee')">
                Traiter
            </button>

            <button onclick="updateTicketStatus(${ticket.id}, 'refusee')">
                Refuser
            </button>

            <button onclick="
                editTicket(
                    ${ticket.id},
                    '${escapeQuotes(ticket.title)}',
                    '${escapeQuotes(ticket.description)}',
                    '${ticket.priority}',
                    '${ticket.status}'
                )
            ">
                Modifier
            </button>

            <button onclick="deleteTicket(${ticket.id})">
                Supprimer
            </button>

        </div>
    `;
}

async function updateTicketStatus(id, status) {

    try {

        const result = await apiFetch(
            `/api/admin/tickets/${id}/status`,
            {
                method: "PUT",

                body: JSON.stringify({
                    status: status
                })
            }
        );

        if (result.success) {

            location.reload();

        } else {

            alert("Erreur changement statut");
        }

    } catch (error) {

        console.error(error);

        alert("Erreur changement statut");
    }
}

async function loadTickets() {

    const container =
        document.getElementById("ticketsContainer")
        || document.getElementById("tickets");

    if (!container) return;

    try {

        const result = await apiFetch(
            "/api/admin/tickets",
            {
                method: "GET"
            }
        );

        if (!result.success) {

            container.innerHTML =
                "<p>Erreur chargement tickets</p>";

            return;
        }

        const tickets = result.tickets || [];

        if (tickets.length === 0) {

            container.innerHTML =
                "<p>Aucun ticket</p>";

            return;
        }

        container.innerHTML = tickets.map(ticket => `

            <div class="ticket-card">

                <div class="ticket-header">

                    <h3>${ticket.title}</h3>

                    ${getStatusBadge(ticket.status)}

                </div>

                <p>
                    <b>ID :</b> ${ticket.id}
                </p>

                <p>
                    <b>Utilisateur :</b>
                    ${ticket.full_name}
                    (${ticket.email})
                </p>

                <p>
                    <b>Description :</b>
                    ${ticket.description}
                </p>

                <p>
                    <b>Priorité :</b>
                    ${ticket.priority}
                </p>

                ${renderButtons(ticket)}

            </div>

        `).join("");

    } catch (error) {

        console.error(error);

        container.innerHTML =
            "<p>Erreur chargement</p>";
    }
}

async function deleteTicket(id) {

    if (!confirm("Supprimer ce ticket ?")) return;

    try {

        const result = await apiFetch(
            `/api/admin/tickets/${id}`,
            {
                method: "DELETE"
            }
        );

        if (result.success) {

            location.reload();

        } else {

            alert("Erreur suppression");
        }

    } catch (error) {

        console.error(error);

        alert("Erreur suppression");
    }
}

async function editTicket(
    id,
    currentTitle,
    currentDescription,
    currentPriority,
    currentStatus
) {

    const title =
        prompt("Titre :", currentTitle);

    if (title === null) return;

    const description =
        prompt("Description :", currentDescription);

    if (description === null) return;

    const priority =
        prompt(
            "Priorité (low, medium, high) :",
            currentPriority
        );

    if (priority === null) return;

    const status =
        prompt(
            "Statut (en_cours, traitee, refusee) :",
            currentStatus
        );

    if (status === null) return;

    try {

        const result = await apiFetch(
            `/api/admin/tickets/${id}`,
            {
                method: "PUT",

                body: JSON.stringify({
                    title,
                    description,
                    priority,
                    status
                })
            }
        );

        if (result.success) {

            location.reload();

        } else {

            alert("Erreur modification");
        }

    } catch (error) {

        console.error(error);

        alert("Erreur modification");
    }
}

document.addEventListener("DOMContentLoaded", () => {

    loadTickets();
});