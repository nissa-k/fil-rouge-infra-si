async function requireAuth(expectedRole = null) {

    try {

        const response = await fetch(
            "http://localhost/fil-rouge-infra-si/backend/public/index.php/api/me",
            {
                credentials: "include"
            }
        );

        const result = await response.json();

        console.log(result);

        if (!response.ok || !result.user) {

            window.location.href = "login.html";
            return false;
        }

        const user = result.user;

        if (expectedRole && user.role !== expectedRole) {

            if (user.role === "admin") {

                window.location.href = "dashboard-admin.html";

            } else if (user.role === "technicien") {

                window.location.href = "dashboard-technicien.html";

            } else {

                window.location.href = "dashboard-client.html";
            }

            return false;
        }

        return user;

    } catch (error) {

        console.error(error);

        window.location.href = "login.html";
        return false;
    }
}