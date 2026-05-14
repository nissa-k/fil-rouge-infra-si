document.getElementById("createUserForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const first_name = document.getElementById("first_name").value;
    const last_name = document.getElementById("last_name").value;
    const email = document.getElementById("email").value;
    const role = document.getElementById("role").value;

    const message = document.getElementById("message");

    try {

        const res = await fetch("http://localhost/fil-rouge-infra-si/backend/public/index.php/api/admin/users", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                first_name,
                last_name,
                email,
                role
            })
        });

        const data = await res.json();

        if (data.success) {
            message.textContent = "Utilisateur créé avec succès";
        } else {
            message.textContent = data.message;
        }

    } catch (e) {
        message.textContent = "Erreur serveur";
    }
});