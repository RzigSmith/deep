<?php
require_once 'includes/config.php';
require_once 'welcome.php';
$db = loginDatabase();

// Récupérer les commandes de l'utilisateur
$stmt = $db->prepare("
    SELECT o.id, o.order_date, o.total_amount, o.status, GROUP_CONCAT(p.name SEPARATOR ', ') AS products
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.order_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mes Commandes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <style>
        /* Reset and base */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f9fafb;
            color: #1f2937;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        a {
            color: #4f46e5;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        a:hover, a:focus {
            color: #4338ca;
            outline: none;
        }
        h1.section-title {
            font-weight: 600;
            font-size: 2rem;
            margin-bottom: 24px;
            color: #111827;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px 64px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar */
        #navbar {
            background: #ffffffcc;
            backdrop-filter: saturate(180%) blur(10px);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
            padding: 12px 24px;
            display: flex;
            flex-direction: column;
            user-select: none;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .logo {
            font-weight: 700;
            font-size: 1.75rem;
            color: #4f46e5;
            user-select: none;
        }

        .logo span {
            color: #6366f1;
            font-weight: 600;
        }

        /* Navigation Links */
        .nav-links {
            list-style: none;
            display: flex;
            align-items: center;
            gap: 28px;
            padding: 0;
            margin: 0;
        }

        .nav-links li a {
            font-weight: 500;
            font-size: 1rem;
            color: #374151;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        .nav-links li a:hover,
        .nav-links li a:focus {
            color: #4f46e5;
            background-color: #e0e7ff;
            outline-offset: 2px;
        }

        /* Notification Icon */
        .notification-icon {
            position: relative;
            cursor: pointer;
            color: #6b7280;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }

        .notification-icon:hover {
            color: #4f46e5;
        }

        .notification-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ef4444;
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 9999px;
            user-select: none;
            pointer-events: none;
        }

        /* Burger Menu Button */
        .burger-menu-button {
            background: none;
            border: none;
            cursor: pointer;
            display: none;
            padding: 8px;
            color: #4f46e5;
            font-size: 2rem;
            line-height: 1;
            user-select: none;
            transition: color 0.3s ease;
            z-index: 1100;
        }
        .burger-menu-button:hover, .burger-menu-button:focus {
            color: #4338ca;
            outline: none;
        }

        /* Orders container & table */
        .orders-container {
            flex-grow: 1;
            padding: 24px 0;
        }

        .orders-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 16px;
            font-size: 0.95rem;
            min-width: 320px;
        }

        .orders-table thead tr {
            background-color: transparent;
        }

        .orders-table thead th {
            text-align: left;
            padding: 12px 16px;
            font-weight: 600;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
            user-select: none;
        }

        .orders-table tbody tr {
            background-color: white;
            box-shadow: 0 3px 6px rgb(0 0 0 / 0.05);
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: default;
        }

        .orders-table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgb(0 0 0 / 0.1);
        }

        .orders-table tbody td {
            padding: 16px;
            color: #1f2937;
            vertical-align: middle;
        }

        /* Responsive table scrolling container */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 16px;
        }

        /* Responsive Styles */

        /* Mobile: <= 640px */
        @media screen and (max-width: 640px) {
            .nav-links {
                flex-direction: column;
                background: #ffffffee;
                position: fixed;
                top: 60px;
                right: 0;
                width: 75vw;
                max-width: 300px;
                height: 100vh;
                padding-top: 32px;
                box-shadow: -4px 0 16px rgb(0 0 0 / 0.15);
                transform: translateX(100%);
                transition: transform 0.3s ease;
                z-index: 1050;
                overflow-y: auto;
                border-left: 1px solid #e5e7eb;
            }
            .nav-links.open {
                transform: translateX(0);
            }
            .nav-links li {
                width: 100%;
                margin-bottom: 16px;
            }
            .nav-links li a {
                display: block;
                font-size: 1.25rem;
                padding: 16px 24px;
                font-weight: 600;
            }
            .burger-menu-button {
                display: inline-block;
            }
            .notification-icon {
                font-size: 1.8rem;
            }
            .container {
                padding: 0 16px 64px;
            }
            .orders-table {
                min-width: 600px;
                font-size: 0.9rem;
            }
            .orders-container {
                padding: 16px 0;
            }
        }

        /* Tablet: 641px to 1024px */
        @media screen and (min-width: 641px) and (max-width: 1024px) {
            .nav-links {
                flex-direction: row;
                position: static;
                transform: none !important;
                background: transparent;
                height: auto;
                padding: 0;
                box-shadow: none;
                border-left: none;
            }
            .nav-links li {
                margin-bottom: 0;
            }
            .nav-links li a {
                font-size: 1rem;
                padding: 8px 12px;
            }
            .burger-menu-button {
                display: none;
            }
            .orders-table {
                font-size: 0.95rem;
                min-width: 100%;
            }
        }

        /* Desktop: >= 1025px */
        @media screen and (min-width: 1025px) {
            .nav-links {
                flex-direction: row;
                position: static;
                transform: none !important;
                background: transparent;
                height: auto;
                padding: 0;
                box-shadow: none;
                border-left: none;
            }
            .nav-links li a {
                font-size: 1rem;
                padding: 8px 16px;
            }
        }
    </style>
</head>

<body>
    <div class="container" id="navbar">
        <nav class="navbar" role="navigation" aria-label="Main Navigation">
            <div class="logo" tabindex="0">Smith<span>Collection</span></div>

            <button
                class="burger-menu-button"
                aria-label="Toggle navigation menu"
                aria-expanded="false"
                aria-controls="primary-navigation"
                id="burger-button"
            >
                <span class="material-icons">menu</span>
            </button>

            <ul class="nav-links" id="primary-navigation" role="menu">
                <?php if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) : ?>
                    <li role="none"><a role="menuitem" href="login.php">Connexion</a></li>
                <?php elseif (isset($_SESSION["role"]) && $_SESSION["role"] === "admin") : ?>
                    <li role="none"><a role="menuitem" href="admin/dashboard.php">Dashboard</a></li>
                <?php else : ?>
                    <li role="none"><a role="menuitem" href="profile.php">Profile</a></li>
                <?php endif; ?>
                <li role="none"><a role="menuitem" href="classes/Product.php">Boutique</a></li>
                <li role="none"><a role="menuitem" href="#">Nouveautés</a></li>
                <li role="none"><a role="menuitem" href="classes/contact.php">Contact</a></li>
            </ul>
            <div class="notification-icon" role="button" aria-label="Notifications" tabindex="0">
                <span class="material-icons" aria-hidden="true">notifications</span>
                <span class="notification-badge" aria-live="polite" aria-atomic="true">0</span>
            </div>
        </nav>
    </div>

    <div class="container orders-container" role="main">
        <h1 class="section-title">Mes Commandes</h1>
        <div class="table-wrapper" tabindex="0" aria-label="Liste des commandes">
            <table class="orders-table" role="table" aria-describedby="commande-description">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Date</th>
                        <th scope="col">Produits</th>
                        <th scope="col">Montant Total</th>
                        <th scope="col">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id']) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($order['order_date']))) ?></td>
                            <td><?= htmlspecialchars($order['products']) ?></td>
                            <td><?= htmlspecialchars(number_format($order['total_amount'], 2)) ?> $</td>
                            <td><?= htmlspecialchars($order['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Burger menu button toggle
        const burgerButton = document.getElementById('burger-button');
        const navLinks = document.getElementById('primary-navigation');

        burgerButton.addEventListener('click', () => {
            const expanded = burgerButton.getAttribute('aria-expanded') === 'true' || false;
            burgerButton.setAttribute('aria-expanded', !expanded);
            navLinks.classList.toggle('open');
        });

        // Close menu if clicked outside on mobile
        document.addEventListener('click', (e) => {
            if (!navLinks.contains(e.target) && !burgerButton.contains(e.target)) {
                if (navLinks.classList.contains('open')) {
                    navLinks.classList.remove('open');
                    burgerButton.setAttribute('aria-expanded', false);
                }
            }
        });

        // Keyboard accessibility for notification icon
        const notificationIcon = document.querySelector('.notification-icon');
        notificationIcon.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                alert('Notifications clicked'); // Placeholder action
            }
        });
        notificationIcon.addEventListener('click', () => {
            alert('Notifications clicked'); // Placeholder action
        });
    </script>
</body>

</html>

