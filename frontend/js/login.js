const form = document.getElementById("loginForm");

form.addEventListener("submit", async (e) => {

    e.preventDefault();

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

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

        // 🔥 évite le crash JSON
        if (text.startsWith("<")) {

            alert("Erreur PHP backend");

            return;
        }

        const result = JSON.parse(text);

        if (!result.success) {

            alert(result.message);
            return;
        }

        if (result.requires_2fa) {

            localStorage.setItem(
                "2fa_email",
                result.email
            );

            window.location.href =
                "verify-2fa.html";

            return;
        }

    } catch (error) {

        console.log(error);

        alert("Erreur serveur");
    }
});