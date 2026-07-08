<?php
    include('../inc/functions.php');

    // Mode édition si un dept_no valide est passé dans l'URL
    $dept_no_url = $_GET['dept_no'] ?? '';
    $editing = $dept_no_url !== '' && get_one_department($dept_no_url);

    $error   = '';
    $success = false;
    // Valeurs affichées dans le formulaire
    $dept_no   = $dept_no_url;
    $dept_name = $editing ? $editing['dept_name'] : '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $mode      = $_POST['mode'] ?? 'add';
        $dept_no   = trim($_POST['dept_no'] ?? '');
        $dept_name = trim($_POST['dept_name'] ?? '');

        if ($dept_no === '' || $dept_name === '') {
            $error = "Le numéro et le nom du département sont obligatoires.";
        } elseif (strlen($dept_no) > 4) {
            $error = "Le numéro de département fait au maximum 4 caractères.";
        } elseif ($mode === 'add' && get_one_department($dept_no)) {
            $error = "Un département avec le numéro '$dept_no' existe déjà.";
        } else {
            if ($mode === 'edit') {
                update_department($dept_no, $dept_name);
            } else {
                add_department($dept_no, $dept_name);
            }
            $success = true;
            $editing = true; // après ajout, on passe en mode édition
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $editing ? "Modifier" : "Ajouter" ?> un département</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <ul>
            <li class="brand">Employés DB</li>
            <li><a href="index.php">Départements</a></li>
            <li><a href="search.php">Rechercher</a></li>
            <li><a href="stats.php">Statistiques</a></li>
            <li><a href="dept_form.php" class="active">Ajouter un département</a></li>
            <li><a href="emp_form.php">Ajouter un employé</a></li>
        </ul>
    </nav>

    <div class="container">
        <p><a href="index.php">&larr; Retour aux départements</a></p>
        <h1><?= $editing ? "Modifier le département $dept_no" : "Ajouter un département" ?></h1>

        <?php if ($success) { ?>
            <div class="alert alert-success">Enregistré.</div>
        <?php } ?>
        <?php if ($error !== '') { ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <div class="card">
            <form method="post" action="dept_form.php<?= $editing ? '?dept_no=' . urlencode($dept_no) : '' ?>">
                <input type="hidden" name="mode" value="<?= $editing ? 'edit' : 'add' ?>">
                <div class="form-group">
                    <label for="dept_no">Numéro (4 car. max)</label>
                    <input class="form-control" type="text" id="dept_no" name="dept_no" maxlength="4"
                           value="<?= htmlspecialchars($dept_no) ?>"
                           <?= $editing ? 'readonly' : '' ?>>
                </div>
                <div class="form-group">
                    <label for="dept_name">Nom</label>
                    <input class="form-control" type="text" id="dept_name" name="dept_name" value="<?= htmlspecialchars($dept_name) ?>">
                </div>
                <button type="submit" class="btn"><?= $editing ? 'Modifier' : 'Ajouter' ?></button>
            </form>
        </div>
    </div>
</body>
</html>
