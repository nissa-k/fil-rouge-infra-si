document.addEventListener("DOMContentLoaded", () => {
    const registerForm = document.getElementById("registerForm");
    const errorElement = document.getElementById("error");

    if (!registerForm) {
        console.error("Formulaire registerForm introuvable");
        return;
    }

    registerForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const full_name = document.getElementById("full_name").value.trim();
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;

        if (errorElement) {
            errorElement.textContent = "";
        }

        try {
            const result = await apiFetch("/api/register", {
                method: "POST",
                body: JSON.stringify({ full_name, email, password })
            });

            console.log("Réponse API register :", result);

            if (result.success) {
                alert(result.message);
                window.location.href = "login.html";
            } else if (errorElement) {
                errorElement.textContent = result.message || "Erreur lors de l'inscription.";
            }
        } catch (error) {
            console.error("Erreur register :", error);
            if (errorElement) {
                errorElement.textContent = error.message || "Erreur réseau ou JavaScript.";
            }
        }
    });
});