const form = document.getElementById("loginForm");

form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    try {

        const res = await fetch("http://localhost/fil-rouge-infra-si/backend/public/index.php/api/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ email, password })
        });

        const data = await res.json();

        if (!data.success) {
            alert(data.message);
            return;
        }

        // 🔥 REDIRECTION CHANGE PASSWORD
        if (data.force_password_change) {
            window.location.href = "change-password.html";
            return;
        }

        // 🔥 ROLE
        if (data.user.role === "admin") {
            window.location.href = "dashboard-admin.html";
        } else {
            window.location.href = "dashboard-client.html";
        }

    } catch (err) {
        alert("Erreur serveur");
    }
});