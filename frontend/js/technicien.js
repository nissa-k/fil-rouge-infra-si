function getModeFromUrl() {

    const params =
        new URLSearchParams(window.location.search);

    return params.get("status") || "en_cours";
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

async function loadTickets() {

    const container =
        document.getElementById("ticketsContainer");

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
                "<p>Erreur chargement</p>";

            return;
        }

        const mode = getModeFromUrl();

        let tickets = result.tickets || [];

        tickets = tickets.filter(
            ticket => ticket.status === mode
        );

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

            </div>

        `).join("");

    } catch (error) {

        console.error(error);

        container.innerHTML =
            "<p>Erreur chargement</p>";
    }
}

document.addEventListener(
    "DOMContentLoaded",
    loadTickets
);