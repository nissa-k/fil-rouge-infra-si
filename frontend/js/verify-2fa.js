const form = document.getElementById("verifyForm");

form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const code = document.getElementById("code").value;

    try {

        const response = await fetch(
            "http://localhost/fil-rouge-infra-si/backend/public/index.php/api/verify-2fa",
            {
                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                credentials: "include",

                body: JSON.stringify({
                    code: code
                })
            }
        );

        const result = await response.json();

        console.log(result);

        if (!response.ok) {
            alert(result.error || "Code invalide");
            return;
        }

        const role = result.user?.role;

        if (role === "admin") {

            window.location.href = "dashboard-admin.html";

        } else if (role === "technicien") {

            window.location.href = "dashboard-technicien.html";

        } else {

            window.location.href = "dashboard-client.html";
        }

    } catch (error) {

        console.error(error);
        alert("Erreur serveur");
    }
});