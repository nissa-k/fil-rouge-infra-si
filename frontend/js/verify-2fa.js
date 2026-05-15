const form = document.getElementById("verifyForm");

form.addEventListener("submit", async (e) => {

    e.preventDefault();

    const code =
        document.getElementById("code").value;

    const email =
        localStorage.getItem("2fa_email");

    try {

        const response = await fetch(
            "http://localhost/fil-rouge-infra-si/backend/public/index.php/api/verify-2fa",
            {
                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify({
                    email,
                    code
                })
            }
        );

        const result = await response.json();

        console.log(result);

        if (!result.success) {

            alert(result.message);

            return;
        }

        // 🔥 connecté
        window.location.href =
            "dashboard-client.html";

    } catch (error) {

        console.log(error);

        alert("Erreur serveur");
    }
});