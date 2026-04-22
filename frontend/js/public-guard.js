async function redirectIfLoggedIn() {
    try {
        const result = await apiFetch("/api/me", {
            method: "GET"
        });

        if (result.success && result.user) {
            if (result.user.role === "admin") {
                window.location.href = "dashboard-admin.html";
            } else {
                window.location.href = "dashboard-client.html";
            }
        }
    } catch (error) {
        console.error("Public guard :", error);
    }
}