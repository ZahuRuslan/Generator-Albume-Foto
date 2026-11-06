
// Generator de albume foto - funcționează pe GitHub Pages.
const formular = document.getElementById("formular-album");
const titlu = document.getElementById("titlu");
const poze = document.getElementById("poze");
const lista = document.getElementById("lista-albume");
const buton = document.getElementById("buton-creare");
const mesaj = document.getElementById("mesaj");
const vizualizare = document.getElementById("vizualizare");
const imagineMare = document.getElementById("imagine-mare");

// Citim albumele salvate în browser.
let albume = [];
try {
    albume = JSON.parse(localStorage.getItem("albumeFoto")) || [];
    if (!Array.isArray(albume)) albume = [];
} catch (eroare) {
    albume = [];
}

function notificare(text, eroare = false) {
    mesaj.textContent = text;
    mesaj.className = eroare ? "mesaj eroare" : "mesaj";
    mesaj.hidden = false;
}

// Micșorăm imaginile pentru a economisi spațiu în localStorage.
function pregatestePoza(fisier) {
    return new Promise((resolve, reject) => {
        const imagine = new Image();
        const adresa = URL.createObjectURL(fisier);

        imagine.onload = () => {
            const raport = Math.min(1, 960 / Math.max(imagine.width, imagine.height));
            const canvas = document.createElement("canvas");
            canvas.width = Math.round(imagine.width * raport);
            canvas.height = Math.round(imagine.height * raport);
            const context = canvas.getContext("2d");
            context.fillStyle = "white";
            context.fillRect(0, 0, canvas.width, canvas.height);
            context.drawImage(imagine, 0, 0, canvas.width, canvas.height);
            URL.revokeObjectURL(adresa);
            resolve(canvas.toDataURL("image/jpeg", 0.7));
        };

        imagine.onerror = () => {
            URL.revokeObjectURL(adresa);
            reject(new Error("Fotografia nu poate fi citită."));
        };
        imagine.src = adresa;
    });
}

function afiseazaAlbume() {
    lista.innerHTML = "";
    document.getElementById("total-albume").textContent = albume.length;
    document.getElementById("numar-albume").textContent = `(${albume.length})`;
    document.getElementById("total-poze").textContent = albume.reduce(
        (total, album) => total + album.fotografii.length, 0
    );

    if (albume.length === 0) {
        lista.innerHTML = '<div class="gol"><div class="gol-icon">▧</div><h3>Niciun album deocamdată</h3><p>Creează primul tău album folosind formularul de mai sus.</p></div>';
        return;
    }

    albume.forEach(album => {
        const articol = document.createElement("article");
        articol.className = "album";
        articol.innerHTML = `
      <div class="album-header">
        <div><span class="album-icon">▧</span><div><h3></h3><p></p></div></div>
        <button type="button" class="buton-stergere">Șterge albumul</button>
      </div>
      <div class="galerie"></div>`;

        articol.querySelector("h3").textContent = album.titlu;
        articol.querySelector("p").textContent = `${album.fotografii.length} fotografii`;
        articol.querySelector(".buton-stergere").onclick = () => {
            if (confirm("Sigur vrei să ștergi acest album?")) {
                albume = albume.filter(element => element.id !== album.id);
                localStorage.setItem("albumeFoto", JSON.stringify(albume));
                afiseazaAlbume();
            }
        };

        const galerie = articol.querySelector(".galerie");
        album.fotografii.forEach(fotografie => {
            const butonFoto = document.createElement("button");
            butonFoto.className = "foto";
            butonFoto.type = "button";
            butonFoto.setAttribute("aria-label", "Mărește fotografia");
            const img = document.createElement("img");
            img.src = fotografie;
            img.alt = `Fotografie din ${album.titlu}`;
            img.loading = "lazy";
            butonFoto.appendChild(img);
            butonFoto.onclick = () => {
                imagineMare.src = fotografie;
                vizualizare.showModal();
            };
            galerie.appendChild(butonFoto);
        });
        lista.appendChild(articol);
    });
}

poze.addEventListener("change", () => {
    document.getElementById("selectate").textContent =
        `${poze.files.length} fotografii selectate`;
});

formular.addEventListener("submit", async (eveniment) => {
    eveniment.preventDefault();
    const fisiere = Array.from(poze.files);

    if (!titlu.value.trim() || fisiere.length === 0 || fisiere.length > 10) {
        notificare("Introdu un nume și selectează între 1 și 10 fotografii.", true);
        return;
    }
    if (fisiere.some(f => !["image/jpeg", "image/png", "image/webp"].includes(f.type) || f.size > 5 * 1024 * 1024)) {
        notificare("Folosește imagini JPG, PNG sau WEBP de cel mult 5 MB fiecare.", true);
        return;
    }

    buton.disabled = true;
    buton.textContent = "Se creează albumul...";
    try {
        const fotografii = [];
        for (const fisier of fisiere) fotografii.push(await pregatestePoza(fisier));
        const albumNou = {
            id: Date.now().toString(),
            titlu: titlu.value.trim(),
            fotografii: fotografii
        };
        const actualizate = [albumNou, ...albume];
        localStorage.setItem("albumeFoto", JSON.stringify(actualizate));
        albume = actualizate;
        formular.reset();
        document.getElementById("selectate").textContent = "Nicio fotografie selectată";
        notificare("Albumul a fost creat cu succes!");
        afiseazaAlbume();
    } catch (eroare) {
        notificare("Nu s-a putut salva albumul. Încearcă mai puține fotografii.", true);
    } finally {
        buton.disabled = false;
        buton.textContent = "＋ Creează albumul";
    }
});

document.getElementById("inchide").onclick = () => vizualizare.close();
vizualizare.onclick = (event) => {
    if (event.target === vizualizare) vizualizare.close();
};
afiseazaAlbume();
