<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pembayaran QRIS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    body {
      font-family: Poppins, sans-serif;
      background: #fdeff0;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
    }
    .card {
      background: #fff;
      padding: 22px;
      border-radius: 16px;
      max-width: 360px;
      width: 100%;
      box-shadow: 0 10px 30px rgba(0,0,0,.08);
      text-align: center;
    }
    img {
      max-width: 240px;
      margin: 12px auto;
    }
    input[type=file] {
      margin-top: 12px;
    }
    button {
      margin-top: 14px;
      padding: 10px 18px;
      border-radius: 999px;
      border: none;
      background: #ff4766;
      color: #fff;
      font-weight: 600;
      cursor: pointer;
      width: 100%;
    }
  </style>
</head>
<body>

<div class="card">
  <h2>Bayar QRIS</h2>

  <p><b>Kode Pesanan</b><br><?= esc($order['code']); ?></p>
  <p><b>Total Bayar</b><br>Rp<?= number_format($order['total_amount']); ?></p>

  <img src="<?= base_url('assets/qris.png'); ?>" alt="QRIS">

  <p style="font-size:14px;color:#666">
    Scan QRIS di atas, lalu upload bukti pembayaran
  </p>

  <form method="post"
        action="<?= site_url('p/payment/upload-proof/'.$order['id']); ?>"
        enctype="multipart/form-data">

    <input type="file" name="proof" accept="image/*" required>

    <button type="submit">Kirim Bukti Pembayaran</button>
  </form>
</div>

</body>
</html>
