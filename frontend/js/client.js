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

async function createTicket(event) {
    event.preventDefault();

    const title = document.getElementById("title").value.trim();
    const description = document.getElementById("description").value.trim();
    const priority = document.getElementById("priority").value;
    const message = document.getElementById("message");

    message.textContent = "";

    try {
        const result = await apiFetch("/api/client/tickets", {
            method: "POST",
            body: JSON.stringify({
                title,
                description,
                priority
            })
        });

        if (result.success) {
            message.style.color = "lightgreen";
            message.textContent = result.message;
            document.getElementById("ticketForm").reset();
        } else {
            message.style.color = "red";
            message.textContent = result.message;
        }
    } catch (error) {
        console.error(error);
        message.style.color = "red";
        message.textContent = "Erreur lors de la création du ticket.";
    }
}

async function loadMyTickets() {
    const container = document.getElementById("myTickets");
    if (!container) return;

    try {
        const result = await apiFetch("/api/client/tickets", {
            method: "GET"
        });

        if (!result.success) {
            container.innerHTML = `<p>${result.message}</p>`;
            return;
        }

        if (result.tickets.length === 0) {
            container.innerHTML = "<p>Aucune requête trouvée.</p>";
            return;
        }

        container.innerHTML = result.tickets.map(ticket => `
            <div class="card">
                <h3>${ticket.title}</h3>
                <p><strong>ID :</strong> ${ticket.id}</p>
                <p><strong>Description :</strong> ${ticket.description}</p>
                <p><strong>Priorité :</strong> ${ticket.priority}</p>
                <p><strong>Statut :</strong> ${ticket.status}</p>
                <p><strong>Créé le :</strong> ${ticket.created_at}</p>
            </div>
        `).join('');
    } catch (error) {
        console.error(error);
        container.innerHTML = "<p>Erreur lors du chargement des requêtes.</p>";
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const ticketForm = document.getElementById("ticketForm");

    if (ticketForm) {
        ticketForm.addEventListener("submit", createTicket);
    }

    loadMyTickets();
});