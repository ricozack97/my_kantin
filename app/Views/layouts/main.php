<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kantin'); ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        window.APP_BASE = "<?= rtrim(base_url('/'), '/'); ?>/";
    </script>
    <script defer src="<?= base_url('assets/js/script.js'); ?>"></script>
</head>
<body>
    <!-- Navbar -->
    <header class="page-header">
        <div class="header-brand">
            <div class="brand-icon"><i class="fas fa-utensils"></i></div>
            <div>
                <span class="brand-title">Kantin G'penk</span>
                <span class="brand-subtitle">Pesan makanan & minuman modern</span>
            </div>
        </div>

        <div class="header-actions">
            <div class="header-nav">
                <nav aria-label="Primary navigation">
                    <ul class="nav-links">
                        <li><a href="<?= base_url('/'); ?>">Beranda</a></li>
                        <?php if(session('user')): ?>
                            <?php if(session('user.role')==='admin'): ?>
                                <li><a href="<?= base_url('admin/dashboard'); ?>">Dashboard</a></li>
                            <?php else: ?>
                                <li><a href="<?= base_url('orders'); ?>">Pesanan Saya</a></li>
                                <li>
                                    <a href="<?= site_url('p/orders'); ?>" class="icon-btn header-cart" aria-label="Keranjang">
                                        <i class="fas fa-shopping-bag"></i>
                                        <span class="badge cart-count">0</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li><a href="<?= base_url('logout'); ?>">Logout</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Konten utama -->
    <?= $this->renderSection('content'); ?>
</body>
</html>
