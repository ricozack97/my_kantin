<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Pembayaran QRIS</title>
  <style>
    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f6f0f4;
      font-family: 'Poppins', sans-serif;
      color: #2b2b34;
    }
    .card {
      width: 100%;
      max-width: 420px;
      background: #ffffff;
      border-radius: 24px;
      box-shadow: 0 18px 45px rgba(0, 0, 0, 0.08);
      padding: 28px;
      box-sizing: border-box;
    }
    .card h1 {
      margin-top: 0;
      margin-bottom: 8px;
      font-size: 24px;
      color: #e63950;
    }
    .card p {
      margin: 8px 0;
      line-height: 1.5;
      color: #4a4a57;
    }
    .meta {
      display: grid;
      gap: 12px;
      margin-bottom: 22px;
    }
    .meta span {
      display: block;
      font-size: 0.95rem;
    }
    .meta strong {
      display: block;
      margin-top: 4px;
      font-size: 1rem;
      color: #1f1f27;
    }
    .qr-box {
      width: 100%;
      padding: 18px;
      border: 1px solid rgba(0,0,0,.08);
      border-radius: 20px;
      background: #fafafa;
      text-align: center;
      margin-bottom: 22px;
    }
    .qr-label {
      margin-bottom: 14px;
      font-size: 0.95rem;
      color: #6b6b75;
    }
    .qr-code {
      width: 260px;
      height: 260px;
      margin: 0 auto;
      display: block;
    }
    .note {
      margin-top: 10px;
      font-size: 0.88rem;
      color: #71717a;
    }
    .btn-row {
      display: flex;
      gap: 12px;
      justify-content: flex-end;
      flex-wrap: wrap;
    }
    .btn {
      border: none;
      border-radius: 999px;
      padding: 12px 18px;
      font-weight: 600;
      cursor: pointer;
    }
    .btn-primary {
      background: #ff4866;
      color: #fff;
    }
    .btn-secondary {
      background: #f3f3f8;
      color: #4a4a57;
    }
  </style>
</head>
<body>
  <div class="card">
    <h1>QRIS Pembayaran</h1>
    <p>Scan QR berikut untuk simulasi pembayaran pesanan.</p>

    <div class="meta">
      <span>Kode Pesanan <strong>#<?= esc($order['code']); ?></strong></span>
      <span>Total Tagihan <strong>Rp <?= number_format((int)$order['total_amount'], 0, ',', '.'); ?></strong></span>
      <span>Metode <strong>QRIS (Simulasi)</strong></span>
    </div>

    <div class="qr-box">
      <div class="qr-label">QRIS Simulasi</div>
      <svg class="qr-code" viewBox="0 0 260 260" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="QRIS Simulasi">
        <rect width="260" height="260" fill="#fff"/>
        <g fill="#111">
          <rect x="12" y="12" width="76" height="76" rx="6"/>
          <rect x="172" y="12" width="76" height="76" rx="6"/>
          <rect x="12" y="172" width="76" height="76" rx="6"/>
          <rect x="28" y="28" width="40" height="40" rx="4" fill="#fff"/>
          <rect x="188" y="28" width="40" height="40" rx="4" fill="#fff"/>
          <rect x="28" y="188" width="40" height="40" rx="4" fill="#fff"/>
          <rect x="98" y="52" width="18" height="18"/>
          <rect x="128" y="28" width="18" height="18"/>
          <rect x="158" y="52" width="18" height="18"/>
          <rect x="52" y="98" width="18" height="18"/>
          <rect x="82" y="98" width="18" height="18"/>
          <rect x="112" y="98" width="18" height="18"/>
          <rect x="142" y="98" width="18" height="18"/>
          <rect x="172" y="98" width="18" height="18"/>
          <rect x="28" y="128" width="18" height="18"/>
          <rect x="58" y="128" width="18" height="18"/>
          <rect x="88" y="128" width="18" height="18"/>
          <rect x="118" y="128" width="18" height="18"/>
          <rect x="148" y="128" width="18" height="18"/>
          <rect x="178" y="128" width="18" height="18"/>
          <rect x="28" y="158" width="18" height="18"/>
          <rect x="58" y="158" width="18" height="18"/>
          <rect x="88" y="158" width="18" height="18"/>
          <rect x="118" y="158" width="18" height="18"/>
          <rect x="148" y="158" width="18" height="18"/>
          <rect x="178" y="158" width="18" height="18"/>
          <rect x="98" y="188" width="18" height="18"/>
          <rect x="128" y="188" width="18" height="18"/>
          <rect x="158" y="188" width="18" height="18"/>
          <rect x="172" y="172" width="18" height="18"/>
          <rect x="142" y="142" width="18" height="18"/>
        </g>
      </svg>
      <p class="note">Ini hanya simulasi. Gunakan aplikasi e-wallet atau QRIS untuk melakukan scan di implementasi sebenarnya.</p>
    </div>

    <div class="btn-row">
      <a href="<?= site_url('p/orders/' . $order['id']); ?>" class="btn btn-secondary">Kembali ke Pesanan</a>
      <form action="<?= site_url('p/payment/' . $order['id'] . '/confirm'); ?>" method="post" style="margin:0;">
        <?= csrf_field(); ?>
        <button type="submit" class="btn btn-primary">Saya Sudah Bayar</button>
      </form>
    </div>
  </div>
</body>
</html>
