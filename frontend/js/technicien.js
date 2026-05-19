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

function renderButtons(ticket) {

    // ticket déjà traité ou refusé
    if (
        ticket.status === "traitee" ||
        ticket.status === "refusee"
    ) {

        return "";
    }

    return `

        <div class="ticket-actions">

            <button onclick="
                updateTicketStatus(
                    ${ticket.id},
                    'traitee'
                )
            ">
                Traiter
            </button>

            <button onclick="
                updateTicketStatus(
                    ${ticket.id},
                    'refusee'
                )
            ">
                Refuser
            </button>

        </div>
    `;
}

async function updateTicketStatus(id, status) {

    let message = "";

    if (status === "traitee") {

        message =
            "Commentaire du traitement :";
    }

    if (status === "refusee") {

        message =
            "Pourquoi le ticket est refusé ?";
    }

    const commentaire = prompt(message);

    if (
        commentaire === null ||
        commentaire.trim() === ""
    ) {

        alert("Commentaire obligatoire");

        return;
    }

    try {

        const result = await apiFetch(
            `/api/admin/tickets/${id}/status`,
            {
                method: "PUT",

                body: JSON.stringify({
                    status,
                    commentaire
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

                <p>
                    <b>Commentaire :</b>
                    ${ticket.commentaire || "Aucun"}
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

document.addEventListener(
    "DOMContentLoaded",
    loadTickets
);