document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    const errorElement = document.getElementById("error");

    if (!loginForm) {
        console.error("Formulaire loginForm introuvable");
        return;
    }

    loginForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;

        if (errorElement) {
            errorElement.textContent = "";
        }

        try {
            const result = await apiFetch("/api/login", {
                method: "POST",
                body: JSON.stringify({ email, password })
            });

            if (result.success) {
                if (result.user.role === "admin") {
                    window.location.href = "dashboard-admin.html";
                } else {
                    window.location.href = "dashboard-client.html";
                }
            } else if (errorElement) {
                errorElement.textContent = result.message || "Erreur de connexion.";
            }
        } catch (error) {
            console.error("Erreur login :", error);
            if (errorElement) {
                errorElement.textContent = error.message || "Erreur réseau ou JavaScript.";
            }
        }
    });
});