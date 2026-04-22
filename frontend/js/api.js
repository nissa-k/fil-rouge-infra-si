const API_BASE = "http://localhost/fil-rouge-infra-si/backend/public/index.php";

async function apiFetch(endpoint, options = {}) {
    const response = await fetch(API_BASE + endpoint, {
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include",
        ...options
    });

    const rawText = await response.text();
    console.log("Réponse brute API :", rawText);

    try {
        return JSON.parse(rawText);
    } catch (error) {
        throw new Error("Réponse non JSON : " + rawText);
    }
}