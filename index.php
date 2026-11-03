<?php
// Generator de albume foto - proiect individual PHP
$folder = __DIR__ . '/albume';
if (!is_dir($folder)) mkdir($folder, 0755, true);

function textSigur($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actiune = $_POST['actiune'] ?? '';

    if ($actiune === 'creeaza') {
        $titlu = trim($_POST['titlu'] ?? '');
        $poze = $_FILES['poze'] ?? null;
        $tipuri = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        if ($titlu === '' || !$poze || empty($poze['name'][0])) {
            header('Location: index.php?mesaj=lipsa');
            exit;
        }

        $id = 'album-' . date('YmdHis') . '-' . bin2hex(random_bytes(3));
        $cale = $folder . '/' . $id;
        mkdir($cale, 0755);
        $salvate = 0;

        foreach ($poze['tmp_name'] as $i => $temporar) {
            if ($i >= 10) break; // Maximum 10 fotografii într-un album
            if ($poze['error'][$i] !== UPLOAD_ERR_OK || $poze['size'][$i] > 5 * 1024 * 1024) continue;
            $tip = mime_content_type($temporar);
            if (!isset($tipuri[$tip]) || !getimagesize($temporar)) continue;

            $nume = 'foto-' . ($salvate + 1) . '.' . $tipuri[$tip];
            if (move_uploaded_file($temporar, $cale . '/' . $nume)) $salvate++;
        }

        if ($salvate > 0) {
            file_put_contents($cale . '/titlu.txt', $titlu);
            header('Location: index.php?mesaj=creat');
        } else {
            rmdir($cale);
            header('Location: index.php?mesaj=imagini');
        }
        exit;
    }

    if ($actiune === 'sterge') {
        $id = $_POST['id'] ?? '';
        if (preg_match('/^album-\d{14}-[a-f0-9]{6}$/', $id)) {
            $cale = $folder . '/' . $id;
            if (is_dir($cale)) {
                foreach (glob($cale . '/*') as $fisier) {
                    if (is_file($fisier)) unlink($fisier);
                }
                rmdir($cale);
            }
        }
        header('Location: index.php?mesaj=sters');
        exit;
    }
}

$albume = glob($folder . '/album-*', GLOB_ONLYDIR) ?: [];
rsort($albume);
$totalPoze = 0;
foreach ($albume as $album) {
    $totalPoze += count(glob($album . '/foto-*') ?: []);
}
$notificari = [
    'creat' => 'Albumul a fost creat cu succes!',
    'sters' => 'Albumul a fost șters.',
    'lipsa' => 'Introdu un nume și alege fotografii.',
    'imagini' => 'Alege imagini JPG, PNG sau WEBP (maximum 5 MB fiecare).'
];
$mesaj = $notificari[$_GET['mesaj'] ?? ''] ?? '';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FotoAlbum | Generator de albume foto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header class="bara">
        <div class="logo"><span class="simbol">▦</span> Foto<span>Album</span></div>
        <div class="eticheta">Proiect PHP · Studiu Individual Nr 2</div>
    </header>

    <section class="introducere">
        <div class="prezentare">
            <span class="eticheta-mica">✦ Momentele tale, într-un singur loc</span>
            <h1>Amintirile merită un <em>album frumos.</em></h1>
            <p>Creează albume foto în câteva secunde. Alege pozele preferate și păstrează-le organizate într-o galerie modernă.</p>
            <div class="statistici">
                <div><strong><?= count($albume) ?></strong><span>Albume create</span></div>
                <div class="linie"></div>
                <div><strong><?= $totalPoze ?></strong><span>Fotografii salvate</span></div>
            </div>
        </div>

        <div class="formular">
            <div class="antet-formular"><span class="iconita">＋</span><div><h2>Album nou</h2><p>Completează câmpurile de mai jos</p></div></div>
            <?php if ($mesaj): ?><div class="mesaj"><?= textSigur($mesaj) ?></div><?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="actiune" value="creeaza">
                <label for="titlu">Numele albumului</label>
                <input type="text" name="titlu" id="titlu" maxlength="70" placeholder="Ex: Vacanța de vară" required>
                <label for="poze">Fotografiile tale</label>
                <div class="zona-upload">
                    <div class="upload-icon">↥</div>
                    <span>Alege fotografiile din calculator</span>
                    <small>JPG, PNG, WEBP · max. 10 poze, 5 MB/foto</small>
                    <input type="file" name="poze[]" id="poze" accept=".jpg,.jpeg,.png,.webp" multiple required>
                </div>
                <button type="submit" class="buton-principal">＋ Creează albumul</button>
            </form>
        </div>
    </section>

    <section class="colectie">
        <div class="sectiune-titlu"><div><span class="subtitlu">COLECȚIA MEA</span><h2>Albumele tale <span>(<?= count($albume) ?>)</span></h2></div><span class="colectie-info">Fotografii păstrate pe server</span></div>
        <?php if (!$albume): ?>
            <div class="gol"><div class="gol-icon">▧</div><h3>Niciun album deocamdată</h3><p>Completează formularul de mai sus și primul tău album va apărea aici.</p></div>
        <?php else: ?>
            <div class="lista-albume">
                <?php foreach ($albume as $album):
                    $id = basename($album);
                    $titlu = is_file($album . '/titlu.txt') ? file_get_contents($album . '/titlu.txt') : 'Album fără titlu';
                    $fotografii = glob($album . '/foto-*') ?: [];
                ?>
                <article class="album">
                    <div class="album-header">
                        <div><span class="album-icon">▧</span><h3><?= textSigur($titlu) ?></h3><p><?= count($fotografii) ?> fotografii</p></div>
                        <form method="POST" onsubmit="return confirm('Ștergi acest album?')">
                            <input type="hidden" name="actiune" value="sterge">
                            <input type="hidden" name="id" value="<?= textSigur($id) ?>">
                            <button class="buton-stergere" type="submit">Șterge albumul</button>
                        </form>
                    </div>
                    <div class="galerie">
                        <?php foreach ($fotografii as $foto):
                            $url = 'albume/' . rawurlencode($id) . '/' . rawurlencode(basename($foto));
                        ?>
                            <a href="<?= $url ?>" target="_blank" rel="noopener"><img src="<?= $url ?>" alt="Fotografie din <?= textSigur($titlu) ?>" loading="lazy"></a>
                        <?php endforeach; ?>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    <footer>Creat cu HTML, CSS și PHP <span>•</span> Studiu individual Nr 2</footer>
</div>
</body>
</html>
