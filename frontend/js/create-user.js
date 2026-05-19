document.getElementById("createUserForm").addEventListener("submit", async (e) => {

    e.preventDefault();

    const first_name = document.getElementById("first_name").value.trim();
    const last_name = document.getElementById("last_name").value.trim();
    const email = document.getElementById("email").value.trim();
    const role = document.getElementById("role").value;

    const message = document.getElementById("message");

    message.textContent = "";

    try {

        const res = await fetch(
            "http://localhost/fil-rouge-infra-si/backend/public/index.php/api/admin/users",
            {
                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                credentials: "include",

                body: JSON.stringify({
                    first_name,
                    last_name,
                    email,
                    role
                })
            }
        );

        const data = await res.json();

        console.log(data);

        if (res.ok && data.success) {

            message.style.color = "green";
            message.textContent = "Utilisateur créé avec succès";

            document.getElementById("createUserForm").reset();

        } else {

            message.style.color = "red";
            message.textContent =
                data.message ||
                data.error ||
                "Erreur lors de la création";
        }

    } catch (error) {

        console.error(error);

        message.style.color = "red";
        message.textContent = "Erreur serveur";
    }
});