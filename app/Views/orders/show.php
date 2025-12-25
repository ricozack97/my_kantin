<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detail Pesanan - Kantin G'penk</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
  <style>
    :root {
      --bg-page: #fdeff0;
      --card-bg: #ffffff;
      --text-dark: #0b2130;
      --muted: #6b7280;
      --accent: #ff4766;
      --accent-dark: #e03f5d;
      --shadow: rgba(10, 25, 40, 0.06);
      --table-border: #f3f2f4;
      --danger: #ff4d4f;
      --ghost-border: #eee;
    }

    html,
    body {
      margin: 0;
      padding: 0;
      max-width: 100%;
      overflow-x: hidden;
    }

    body {
      background: var(--bg-page);
      margin: 0;
      font-family: 'Poppins', sans-serif;
      color: var(--text-dark);
      min-height: 100vh;
      padding: 0;
    }

    .container {
      max-width: 1100px;
      margin: 0 auto;
      padding: 20px 16px;
    }

    .section {
      background: var(--card-bg);
      border-radius: 16px;
      box-shadow: 0 6px 16px var(--shadow);
      padding: 16px;
      margin: 16px 0;
    }

    .section h2,
    .section h3 {
      margin: 0 0 8px 0;
      color: var(--accent);
      font-weight: 700;
    }

    p {
      color: var(--muted);
      margin: 6px 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
      background: transparent;
    }

    th,
    td {
      padding: 12px 10px;
      border-bottom: 1px solid var(--table-border);
      text-align: left;
      color: var(--text-dark);
      font-size: 0.95rem;
    }

    thead th {
      font-weight: 700;
      color: var(--text-dark);
    }

    .btn {
      padding: 10px 14px;
      border-radius: 10px;
      text-decoration: none;
      display: inline-block;
      font-weight: 600;
      cursor: pointer;
    }

    .btn-primary {
      background: var(--accent);
      color: #fff;
      border: none;
    }

    .btn-danger {
      background: var(--danger);
      color: #fff;
    }

    .badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 999px;
      font-size: .8rem;
      font-weight: 600;
    }

    .badge.pending {
      background: #fff3d6;
      color: #a26a00;
    }

    .badge.paid {
      background: #e8ffe8;
      color: #1b7a2e;
    }

    .badge.cancel {
      background: #ffe5e7;
      color: #b21d1d;
    }

    /* ===== TOAST ===== */
    #toast {
      position: fixed;
      top: -80px;
      left: 50%;
      transform: translateX(-50%);
      background: #16a34a;
      color: #fff;
      padding: 14px 20px;
      border-radius: 12px;
      font-size: 0.95rem;
      font-weight: 600;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.18);
      opacity: 0;
      z-index: 9999;
      transition: all .4s ease;
    }
  </style>
</head>

<body>

<!-- ========= TOAST NOTIFICATION ========= -->
<div id="toast"></div>

<?php
$status = $order['status'] ?? 'pending';

$labelMap = [
  'pending' => 'Menunggu',
  'menunggu' => 'Menunggu',
  'processing' => 'Diproses',
  'diproses' => 'Diproses',
  'completed' => 'Selesai',
  'selesai' => 'Selesai',
  'canceled' => 'Batal',
  'batal' => 'Batal',
];

$classMap = [
  'pending' => 'pending',
  'menunggu' => 'pending',
  'processing' => 'pending',
  'diproses' => 'pending',
  'completed' => 'paid',
  'selesai' => 'paid',
  'canceled' => 'cancel',
  'batal' => 'cancel',
];

$statusLabel = $labelMap[$status] ?? ucfirst($status);
$statusClass = $classMap[$status] ?? 'pending';

$deliveryRaw = $order['delivery_method'] ?? 'pickup';
$deliveryText = $deliveryRaw === 'delivery' ? 'Diantar' : 'Ambil Sendiri';
?>

<div class="container">

  <div class="section">
    <h2>Detail Pesanan #<?= esc($order['code']); ?></h2>
    <p>Tanggal: <?= date('d M Y H:i', strtotime($order['created_at'])); ?></p>
    <p>Nama: <b><?= esc($order['customer_name'] ?? $user['name']); ?></b></p>
    <p>Total: <b>Rp <?= number_format($order['total_amount'], 0, ',', '.'); ?></b></p>
    <p>Status: <span class="badge <?= $statusClass; ?>"><?= $statusLabel; ?></span></p>
    <p>
      Status Pembayaran:
      <b><?= $paymentStatus === 'paid' ? 'Sudah Dibayar' : 'Belum Dibayar'; ?></b>
    </p>
  </div>

  <div class="section">
    <h3>Item</h3>

    <table>
      <thead>
        <tr>
          <th>Menu</th>
          <th>Qty</th>
          <th>Harga</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $total = 0;
        foreach ($order['items'] as $it):
          $total += $it['subtotal'];
        ?>
        <tr>
          <td><?= esc($it['name']); ?></td>
          <td><?= $it['qty']; ?></td>
          <td>Rp <?= number_format($it['price'], 0, ',', '.'); ?></td>
          <td>Rp <?= number_format($it['subtotal'], 0, ',', '.'); ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
          <td><b>Total</b></td>
          <td></td>
          <td></td>
          <td><b>Rp <?= number_format($total, 0, ',', '.'); ?></b></td>
        </tr>
      </tbody>
    </table>

    <div class="btn-inline" style="margin-top:12px">
      <a href="<?= site_url('/'); ?>" class="btn btn-ghost">Kembali</a>

      <?php if (in_array($status, ['pending','menunggu']) && $paymentStatus !== 'paid'): ?>
        <a href="<?= site_url('p/payment/' . $order['id']); ?>" class="btn btn-primary">Bayar Sekarang</a>
      <?php endif; ?>

      <?php if (in_array($status, ['pending','menunggu'])): ?>
        <form action="<?= site_url('p/orders/' . $order['id'] . '/delete'); ?>"
              method="post"
              onsubmit="return confirm('Yakin ingin membatalkan pesanan?');"
              style="display:inline">
          <?= csrf_field(); ?>
          <button type="submit" class="btn btn-danger">Hapus / Batalkan</button>
        </form>
      <?php endif; ?>
    </div>

  </div>
</div>

<!-- ========= TOAST SCRIPT ========= -->
<script>
  function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');

    toast.innerText = message;

    toast.style.background = (type === 'success') ? '#16a34a' : '#dc2626';

    toast.style.top = '20px';
    toast.style.opacity = '1';

    setTimeout(() => {
      toast.style.top = '-80px';
      toast.style.opacity = '0';
    }, 3000);
  }

  <?php if (session('success')): ?>
      showToast("<?= esc(session('success')) ?>", 'success');
  <?php endif; ?>

  <?php if (session('error')): ?>
      showToast("<?= esc(session('error')) ?>", 'error');
  <?php endif; ?>
</script>

</body>
</html>
