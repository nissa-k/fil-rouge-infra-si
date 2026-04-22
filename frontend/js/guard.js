async function requireAuth(expectedRole = null) {
    try {
        const result = await apiFetch("/api/me", {
            method: "GET"
        });

        if (!result.success || !result.user) {
            window.location.href = "login.html";
            return false;
        }

        if (expectedRole && result.user.role !== expectedRole) {
            if (result.user.role === "admin") {
                window.location.href = "dashboard-admin.html";
            } else {
                window.location.href = "dashboard-client.html";
            }
            return false;
        }

        return true;
    } catch (error) {
        console.error(error);
        window.location.href = "login.html";
        return false;
    }
}