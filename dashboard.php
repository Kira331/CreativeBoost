<?php
session_start();
require_once __DIR__ . '/backend/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.html');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header('Location: index.html');
    exit;
}

// Якщо в таблиці users немає поля created_at, закоментуй цю змінну нижче
$regDate = isset($user['created_at']) ? date('d.m.Y', strtotime($user['created_at'])) : '—';
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Кабінет користувача | CreativeBoost</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome CDN для іконок -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --accent: #6c63ff;
            --accent-dark: #4f46e5;
            --sidebar-bg: #f6f8fa;
            --header-bg: #fff;
            --card-bg: #fff;
            --border: #e5e7eb;
            --text-main: #22223b;
            --text-secondary: #6b7280;
            --shadow: 0 4px 24px rgba(80, 80, 120, 0.07);
            --radius: 18px;
            --radius-sm: 12px;
            --transition: .18s cubic-bezier(.4,0,.2,1);
        }
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Inter', 'DM Sans', Arial, sans-serif;
            background: #f4f6fb;
            color: var(--text-main);
        }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .dashboard-root {
            display: flex;
            min-height: 100vh;
        }
        /* SIDEBAR */
        .sidebar {
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border);
            min-width: 220px;
            max-width: 240px;
            padding: 32px 0 24px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 32px;
            box-shadow: 2px 0 16px 0 rgba(80,80,120,0.03);
            z-index: 2;
            transition: left var(--transition);
        }
        .sidebar .sidebar-logo {
            font-size: 2.1rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 12px;
            letter-spacing: 1px;
        }
        .sidebar-nav {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 32px;
            color: var(--text-secondary);
            font-size: 1.08rem;
            border-radius: var(--radius-sm);
            text-decoration: none;
            transition: background var(--transition), color var(--transition);
            cursor: pointer;
        }
        .sidebar-link.active,
        .sidebar-link:hover {
            background: var(--accent);
            color: #fff;
        }
        .sidebar-link i {
            font-size: 1.2em;
        }
        /* HEADER */
        .dashboard-header {
            width: 100%;
            background: var(--header-bg);
            box-shadow: 0 2px 16px rgba(80,80,120,0.06);
            padding: 0 36px;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .header-user {
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .header-user .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.3rem;
            box-shadow: 0 2px 8px rgba(108,99,255,0.08);
        }
        .header-user .user-name {
            font-weight: 600;
            font-size: 1.08rem;
        }
        .header-user .logout-btn {
            background: var(--bittersweet);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            padding: 8px 22px;
            font-size: 1rem;
            font-weight: 600;
            margin-left: 10px;
            cursor: pointer;
            transition: background var(--transition);
        }
        .header-user .logout-btn:hover {
            background: var(--accent-dark);
        }
        /* MAIN */
        .dashboard-main {
            flex: 1;
            padding: 40px 32px 32px 32px;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #f4f6fb;
        }
        .profile-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 38px 36px 30px 36px;
            max-width: 420px;
            width: 100%;
            margin-top: 32px;
            text-align: center;
            transition: box-shadow var(--transition);
        }
        .profile-card:hover {
            box-shadow: 0 8px 32px rgba(108,99,255,0.13);
        }
        .profile-avatar {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0 auto 18px auto;
            box-shadow: 0 2px 8px rgba(108,99,255,0.10);
        }
        .profile-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--text-main);
        }
        .profile-email {
            font-size: 1.08rem;
            color: var(--text-secondary);
            margin-bottom: 18px;
        }
        .profile-meta {
            font-size: 0.98rem;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }
        /* BURGER */
        .sidebar-burger {
            display: none;
            position: absolute;
            top: 22px;
            left: 18px;
            background: none;
            border: none;
            font-size: 2rem;
            color: var(--accent);
            z-index: 30;
            cursor: pointer;
        }
        @media (max-width: 900px) {
            .dashboard-root {
                flex-direction: column;
            }
            .sidebar {
                position: fixed;
                left: -260px;
                top: 0;
                height: 100vh;
                min-width: 220px;
                max-width: 240px;
                transition: left var(--transition);
                box-shadow: 2px 0 24px 0 rgba(80,80,120,0.08);
            }
            .sidebar.open {
                left: 0;
            }
            .sidebar-burger {
                display: block;
            }
            .dashboard-main {
                padding: 32px 8px 24px 8px;
            }
        }
        @media (max-width: 600px) {
            .profile-card {
                padding: 22px 6px 18px 6px;
            }
            .dashboard-header {
                padding: 0 12px;
            }
        }
    </style>
</head>
<body>
<div class="dashboard-root">
    <!-- Sidebar -->
    <button class="sidebar-burger" id="sidebar-burger" aria-label="Меню"><i class="fas fa-bars"></i></button>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo"><i class="fa-solid fa-bolt"></i> CreativeBoost</div>
        <nav class="sidebar-nav">
            <a href="#" class="sidebar-link active"><i class="fa-solid fa-user"></i> Мій профіль</a>
            <a href="#" class="sidebar-link"><i class="fa-solid fa-gear"></i> Налаштування</a>
            <a href="#" class="sidebar-link"><i class="fa-solid fa-clock-rotate-left"></i> Історія</a>
            <a href="#" class="sidebar-link"><i class="fa-solid fa-headset"></i> Підтримка</a>
        </nav>
    </aside>
    <div style="flex:1;display:flex;flex-direction:column;min-width:0;">
        <!-- Header -->
        <header class="dashboard-header">
            <div></div>
            <div class="header-user">
                <div class="user-avatar">
                    <?php
                    // ініціали
                    $initials = mb_strtoupper(mb_substr($user['username'],0,1));
                    echo $initials;
                    ?>
                </div>
                <span class="user-name"><?php echo htmlspecialchars($user['username']); ?></span>
                <form action="logout.php" method="post" style="display:inline;">
                    <button type="submit" class="logout-btn"><i class="fa-solid fa-arrow-right-from-bracket"></i> Вийти</button>
                </form>
            </div>
        </header>
        <!-- Main -->
        <main class="dashboard-main">
            <section class="profile-card">
                <div class="profile-avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="profile-name"><?php echo htmlspecialchars($user['username']); ?></div>
                <div class="profile-email"><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></div>
                <div class="profile-meta"><i class="fa-solid fa-calendar"></i> Дата реєстрації: <?php echo $regDate; ?></div>
            </section>
        </main>
    </div>
</div>
<script>
    // Sidebar burger toggle
    const burger = document.getElementById('sidebar-burger');
    const sidebar = document.getElementById('sidebar');
    burger.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });
    // Закриття сайдбару при кліку поза ним (на мобільному)
    document.addEventListener('click', function(e) {
        if(window.innerWidth <= 900 && sidebar.classList.contains('open')) {
            if (!sidebar.contains(e.target) && e.target !== burger) {
                sidebar.classList.remove('open');
            }
        }
    });
</script>
</body>
</html>
