<?php
include_once "config.php";

// Traitement du formulaire de mise à jour

if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
$errors = [];
$success = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation des données
    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Une adresse email valide est requise.";
    }

    if (!empty($new_password) && $new_password !== $confirm_password) {
        $errors[] = "Le nouveau mot de passe et sa confirmation ne correspondent pas.";
    }

    if (!empty($new_password) && strlen($new_password) < 6) {
        $errors[] = "Le nouveau mot de passe doit contenir au moins 6 caractères.";
    }

    // Vérification du mot de passe actuel si un nouveau mot de passe est défini
    if (!empty($new_password) && !password_verify($current_password, $user['password'])) {
        $errors[] = "Le mot de passe actuel est incorrect.";
    }

    $avatar = $user['avatar'] ?? null;

    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $maxSize = 2 * 1024 * 1024; // 2Mo
        $fileInfo = pathinfo($_FILES['avatar']['name']);
        $ext = strtolower($fileInfo['extension'] ?? '');
        if (!in_array($ext, $allowed)) {
            $errors[] = "Format de photo non autorisé.";
        } elseif ($_FILES['avatar']['size'] > $maxSize) {
            $errors[] = "La photo dépasse la taille maximale de 2Mo.";
        } else {
            $newName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
            $dest = __DIR__ . '/../assets/images/' . $newName;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                $avatar = $newName;
            } else {
                $errors[] = "Erreur lors de l’upload de la photo.";
            }
        }
    }

    // Si aucune erreur, mettre à jour les informations
    if (empty($errors)) {
        try {
            if ($avatar && $avatar !== $user['avatar']) {
                $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, avatar = ? WHERE id = ?");
                $stmt->execute([$username, $email, $avatar, $_SESSION['user_id']]);
                $_SESSION['avatar'] = $avatar;
            } else {
                $stmt = $db->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $stmt->execute([$username, $email, $_SESSION['user_id']]);
            }

            // Mettre à jour le mot de passe si un nouveau est défini
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['user_id']]);
            }

            // Mettre à jour les informations de session
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['avatar'] = $avatar;

            $success = "Votre profil a été mis à jour avec succès.";
        } catch (PDOException $e) {
            $errors[] = "Une erreur est survenue lors de la mise à jour : " . $e->getMessage();
        }
    }
}

// Traitement du formulaire de mise à jour
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    // Validation des données
    if (empty($username)) {
        $errors[] = "Le nom complet est requis.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Une adresse email valide est requise.";
    }

    if (!empty($new_password) && strlen($new_password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }

    // Si aucune erreur, mettre à jour les informations
    if (empty($errors)) {
        try {
            $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, avatar=? WHERE id = ?");
            $stmt->execute([$username, $email,$avatar, $_SESSION['user']['id']]);

            // Mettre à jour le mot de passe si un nouveau est défini
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['user']['id']]);
            }

            // Mettre à jour les informations de session
            $_SESSION['user']['username'] = $username;
            $_SESSION['user']['email'] = $email;
            $_SESSION['user']['avatar'] = $avatar;
            $success = "Profil mis à jour avec succès.";
        } catch (PDOException $e) {
            $errors[] = "Une erreur est survenue lors de la mise à jour : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/adminprofile.css">
</head>

<body>
    <div class="profile-container">
        <!-- Sidebar -->
        <aside class="profile-sidebar">
            <img src="../assets/images/<?= htmlspecialchars($_SESSION['avatar'] ?? 'default_avatar.png') ?>"
                 alt="Avatar" class="profile-avatar">
            <h2 class="profile-name"><?= htmlspecialchars($_SESSION['username']) ?></h2>
            <p class="profile-email"><?= htmlspecialchars($_SESSION['email']) ?></p>

        
            <ul>
                <li><a href="admin_profile.php" class="active">Mon Profil</a></li>
                <li><a href="dashboard.php">Tableau de bord</a></li>
                <li><a href="manage_users.php">Gestion Utilisateurs</a></li>
                <li><a href="admin.php">Gestion Produits</a></li>
                <li><a href="admin_settings.php">Paramètres</a></li>
                <li><a href="messages.php">Mes messages</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>

        </aside>

        <!-- Main Content -->
        <main class="profile-main">
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h1 class="section-title">Informations Personnelles</h1>
            <p class="member-since">Membre depuis le <?= date('d/m/Y', strtotime($user['created_at'])) ?></p>

            <form method="POST" action="admin_profile.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username" class="form-label">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" class="form-control"
                        value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Adresse Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                        value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="avatar" class="form-label">Photo de profil</label>
                    <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
                    <small class="error-message">Formats acceptés : jpg, png, jpeg, gif (max 2Mo)</small>
                </div>

                <h2 class="section-title" style="margin-top: 2.5rem;">Changer de mot de passe</h2>

                <div class="form-group">
                    <label for="current_password" class="form-label">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" class="form-control">
                </div>

                <div class="form-group">
                    <label for="new_password" class="form-label">Nouveau mot de passe</label>
                    <input type="password" id="new_password" name="new_password" class="form-control">
                    <small class="error-message">Laissez vide pour ne pas changer</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                </div>

                <button type="submit" class="btn btn-block">Mettre à jour le profil</button>
            </form>
        </main>
    </div>
</body>

</html>