const API_URL =
    "http://localhost/fil-rouge-infra-si/backend/public/index.php/api/assets";

const assetsContainer =
    document.getElementById("assetsContainer");

/*telecharger les assets*/

async function loadAssets() {

    try {

        const response = await fetch(API_URL, {
            credentials: "include"
        });

        const data = await response.json();

        console.log(data);

        if (!data.success) {

            assetsContainer.innerHTML =
                "<p>Erreur chargement parc informatique</p>";

            return;
        }

        if (data.assets.length === 0) {

            assetsContainer.innerHTML =
                "<p>Aucun matériel trouvé</p>";

            return;
        }

        assetsContainer.innerHTML = "";

        data.assets.forEach(asset => {

            const div = document.createElement("div");

            div.classList.add("card");

            div.innerHTML = `

                <h3>${asset.name}</h3>
                <p><strong>Type :</strong> ${asset.type}</p>
                <p><strong>Marque :</strong> ${asset.marque || '-'}</p>
                <p><strong>Modèle :</strong> ${asset.modele || '-'}</p>
                <p><strong>OS :</strong> ${asset.os || '-'}</p>
                <p><strong>IP :</strong> ${asset.ip_address || '-'}</p>
                <p><strong>MAC :</strong> ${asset.mac_address || '-'}</p>
                <p><strong>Numéro série :</strong>
                    ${asset.serial_number || '-'}
                </p>
                <p><strong>Statut :</strong> ${asset.statut}</p>
                <p><strong>Utilisateur :</strong>
                    ${asset.assigned_user || 'Non assigné'}
                </p>
                <p><strong>Date achat :</strong>
                    ${asset.purchase_date || '-'}
                </p>
                <button onclick="deleteAsset(${asset.id})">
                    Supprimer
                </button>

            `;

            assetsContainer.appendChild(div);
        });

    } catch (error) {

        console.error(error);

        assetsContainer.innerHTML =
            "<p>Erreur serveur</p>";
    }
}

/*supprimer asset */

async function deleteAsset(id) {

    const confirmDelete = confirm(
        "Supprimer ce matériel ?"
    );

    if (!confirmDelete) {
        return;
    }

    try {

        const response = await fetch(
            `${API_URL}/${id}`,
            {
                method: "DELETE",
                credentials: "include"
            }
        );

        const data = await response.json();

        console.log(data);

        if (!data.success) {

            alert(data.message);

            return;
        }

        loadAssets();

    } catch (error) {

        console.error(error);

        alert("Erreur serveur");
    }
}

/*creer asset */

const createAssetForm =
    document.getElementById("createAssetForm");

if (createAssetForm) {

    createAssetForm.addEventListener(
        "submit",
        async (e) => {

            e.preventDefault();

            const assetData = {

                name:
                    document.getElementById("name").value,

                type:
                    document.getElementById("type").value,

                marque:
                    document.getElementById("marque").value,

                modele:
                    document.getElementById("modele").value,

                serial_number:
                    document.getElementById("serial_number").value,

                os:
                    document.getElementById("os").value,

                ip_address:
                    document.getElementById("ip_address").value,

                mac_address:
                    document.getElementById("mac_address").value,

                statut:
                    document.getElementById("statut").value,

                assigned_to:
                    document.getElementById("assigned_to").value
                        ? parseInt(document.getElementById("assigned_to").value)
                        : null,

                purchase_date:
                    document.getElementById("purchase_date").value
            };

            try {

                const response = await fetch(
                    API_URL,
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/json"
                        },

                        credentials: "include",

                        body: JSON.stringify(assetData)
                    }
                );

                const data = await response.json();

                console.log(data);

                if (!data.success) {

                    alert(data.message);

                    return;
                }

                alert("Matériel ajouté");

                createAssetForm.reset();

                window.location.href = "assets.html";

            } catch (error) {

                console.error(error);

                alert("Erreur serveur");
            }
        }
    );
}

/* auto load */

if (assetsContainer) {
    loadAssets();
}