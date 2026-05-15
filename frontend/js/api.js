const API_BASE =
"http://localhost/fil-rouge-infra-si/backend/public/index.php";

async function apiFetch(endpoint, options = {}) {

    const response = await fetch(
        API_BASE + endpoint,
        {
            headers: {
                "Content-Type": "application/json"
            },

            credentials: "include",

            ...options
        }
    );

    return await response.json();
}