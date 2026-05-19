const form = document.getElementById("loginForm");

form.addEventListener("submit", async (e) => {

    e.preventDefault();

    const email =
        document.getElementById("email").value;

    const password =
        document.getElementById("password").value;

    try {

        const response = await fetch(
            "http://localhost/fil-rouge-infra-si/backend/public/index.php/api/login",
            {
                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify({
                    email,
                    password
                })
            }
        );

        const text = await response.text();

        console.log(text);

        const result = JSON.parse(text);

        //erreur de connexion

        if (!result.success) {

            alert(result.message);

            return;
        }

        //modification du mot de passe à la première connexion

        if (result.must_change_password) {

            window.location.href =
                "change-password.html";

            return;
        }

        //a2f

        if (result.requires_2fa) {

            localStorage.setItem(
                "2fa_email",
                result.email
            );

            window.location.href =
                "verify-2fa.html";

            return;
        }

        //redirection selon le rôle

        const role = result.user?.role ?? result.role;

        if (role === 'admin') {

            window.location.href = "dashboard-admin.html";

        } else if (role === 'technicien') {

            window.location.href = "dashboard-technicien.html";

        } else {

            window.location.href = "dashboard-client.html";
        }

    } catch (error) {

        console.log(error);

        alert("Erreur serveur");
    }
});