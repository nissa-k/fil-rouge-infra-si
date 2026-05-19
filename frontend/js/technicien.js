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

async function loadMessages(ticketId) {

    const messagesDiv =
        document.getElementById(
            `messages-${ticketId}`
        );

    if (!messagesDiv) return;

    try {

        const result = await apiFetch(
            `/api/tickets/${ticketId}/messages`,
            {
                method: "GET"
            }
        );

        if (!result.success) {

            messagesDiv.innerHTML =
                "Erreur chargement messages";

            return;
        }

        const messages =
            result.messages || [];

        if (messages.length === 0) {

            messagesDiv.innerHTML =
                "<p>Aucun message</p>";

            return;
        }

        messagesDiv.innerHTML =
            messages.map(message => `

                <div class="message-item">

                    <b>
                        ${message.full_name}
                    </b>

                    <p>
                        ${message.message}
                    </p>

                    <small>
                        ${message.created_at}
                    </small>

                </div>

            `).join("");

    } catch (error) {

        console.error(error);

        messagesDiv.innerHTML =
            "Erreur chargement messages";
    }
}

async function sendMessage(ticketId) {

    const textarea =
        document.getElementById(
            `message-input-${ticketId}`
        );

    if (!textarea) return;

    const message =
        textarea.value.trim();

    if (message === "") {

        alert("Message vide");

        return;
    }

    try {

        const result = await apiFetch(
            `/api/tickets/${ticketId}/messages`,
            {
                method: "POST",

                body: JSON.stringify({
                    message
                })
            }
        );

        if (result.success) {

            textarea.value = "";

            loadMessages(ticketId);

        } else {

            alert("Erreur envoi");
        }

    } catch (error) {

        console.error(error);

        alert("Erreur envoi");
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

                <div class="ticket-messages">

                    <h4>Conversation</h4>

                    <div
                        class="messages-list"
                        id="messages-${ticket.id}"
                    >
                        Chargement...
                    </div>

                    <textarea
                        id="message-input-${ticket.id}"
                        placeholder="Écrire un message..."
                    ></textarea>

                    <button
                        onclick="sendMessage(${ticket.id})"
                    >
                        Envoyer
                    </button>

                </div>

                ${renderButtons(ticket)}

            </div>

        `).join("");

        tickets.forEach(ticket => {

            loadMessages(ticket.id);
        });

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