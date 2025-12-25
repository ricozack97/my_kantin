<?php
$title = 'Detail Pesanan';
include APPPATH . 'Views/admin/partials/head.php';
?>

<style>
    .badge.wait { background-color: #f6c23e; color: #fff; }
    .badge.proc { background-color: #4e73df; color: #fff; }
    .badge.done { background-color: #1cc88a; color: #fff; }
    .badge.cancel { background-color: #e74a3b; color: #fff; }
</style>

<h1 class="h3 mb-4 text-gray-800">Detail Pesanan</h1>

<?php if (session('success')): ?>
    <div class="alert alert-success"><?= esc(session('success')) ?></div>
<?php endif; ?>
<?php if (session('error')): ?>
    <div class="alert alert-danger"><?= esc(session('error')) ?></div>
<?php endif; ?>

<?php
// ===== STATUS ORDER =====
$status = $order['status'] ?? 'pending';

$labelMap = [
    'pending'    => 'Menunggu',
    'processing' => 'Diproses',
    'completed'  => 'Selesai',
    'canceled'   => 'Batal',
];

$badgeMap = [
    'pending'    => 'badge wait',
    'processing' => 'badge proc',
    'completed'  => 'badge done',
    'canceled'   => 'badge cancel',
];

$label      = $labelMap[$status] ?? ucfirst($status);
$badgeClass = $badgeMap[$status] ?? 'badge';

// ===== STATUS PEMBAYARAN =====
$paymentStatus = $order['payment_status'] ?? 'unpaid';

$payLabelMap = [
    'unpaid' => 'Belum Dibayar',
    'paid'   => 'Sudah Dibayar',
    'failed' => 'Gagal / Ditolak',
    'waiting_confirmation' => 'Menunggu Konfirmasi',
];

$payBadgeMap = [
    'unpaid' => 'badge wait',
    'paid'   => 'badge done',
    'failed' => 'badge cancel',
    'waiting_confirmation' => 'badge wait',
];

$paymentLabel      = $payLabelMap[$paymentStatus] ?? ucfirst($paymentStatus);
$paymentBadgeClass = $payBadgeMap[$paymentStatus] ?? 'badge';

// ===== LINK WHATSAPP =====
$waLink = null;
if (!empty($order['no_hp'])) {
    $waPhone = preg_replace('/^0/', '62', $order['no_hp']);
    $message =
        "Halo {$order['customer_name']},\n" .
        "Pesanan Anda dengan kode {$order['code']} sudah *SELESAI*.\n" .
        "Terima kasih 🙏";
    $waLink = "https://wa.me/{$waPhone}?text=" . urlencode($message);
}
?>

<div class="row">

    <!-- ================= LEFT ================= -->
    <div class="col-lg-6">

        <div class="card shadow mb-4">
            <div class="card-header">
                <strong>Detail Pesanan #<?= esc($order['code']) ?></strong>
            </div>

            <div class="card-body">

                <p><strong>Tanggal:</strong> <?= esc(date('d M Y H:i', strtotime($order['created_at']))) ?></p>
                <p><strong>Pemesan:</strong> <?= esc($order['customer_name']) ?></p>
                <p><strong>Total:</strong> Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></p>

                <p>
                    <strong>Status Pesanan:</strong>
                    <span class="<?= $badgeClass ?>"><?= $label ?></span>
                </p>

                <p>
                    <strong>Status Pembayaran:</strong>
                    <span class="<?= $paymentBadgeClass ?>"><?= $paymentLabel ?></span>
                </p>
<!-- ====== BUKTI QRIS + POPUP ====== -->
<?php if (!empty($order['payment_proof'])): ?>
    <div class="mt-3 p-3 border rounded" style="background:#fff8e1;">
        <strong class="text-warning d-block mb-2">Bukti Pembayaran QRIS</strong>

        <!-- Thumbnail -->
        <img 
            src="<?= base_url('uploads/qris/bukti/' . $order['payment_proof']) ?>" 
            class="img-fluid rounded"
            style="max-width:180px; cursor:pointer; border:1px solid #ccc"
            onclick="$('#modalQris').modal('show')"
        >

        <!-- Tombol TEKS LIHAT – WARNA HIJAU -->
<div class="mt-2">
    <button class="btn btn-success btn-sm" onclick="$('#modalQris').modal('show')">
        Lihat Bukti
    </button>
</div>

    </div>
<?php endif; ?>
<!-- Modal Popup -->
 <?php if (!empty($order['payment_proof'])): ?>
<div class="modal fade" id="modalQris" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Bukti Pembayaran QRIS</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>

      <div class="modal-body text-center">

        <!-- FULL IMAGE -->
        <img 
            src="<?= base_url('uploads/qris/bukti/' . $order['payment_proof']) ?>" 
            class="img-fluid rounded mb-3"
            style="max-height:75vh; cursor:zoom-in"
            onclick="window.open(this.src, '_blank')"
        >

        <!-- VERIFIKASI -->
        <?php if ($order['payment_status'] === 'waiting_confirmation'): ?>
            <form action="<?= base_url('admin/orders/' . $order['id'] . '/verify') ?>" 
                  method="post" class="d-inline">
                <?= csrf_field() ?>
                <button class="btn btn-success">✔ Terima Pembayaran</button>
            </form>

            <form action="<?= base_url('admin/orders/' . $order['id'] . '/reject') ?>" 
                  method="post" class="d-inline">
                <?= csrf_field() ?>
                <button class="btn btn-danger">✖ Tolak Pembayaran</button>
            </form>
        <?php endif; ?>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>

    </div>
  </div>
</div>
<?php endif; ?>



<!-- ====== TOMBOL BAYAR TUNAI ====== -->
<?php if ($order['payment_status'] !== 'paid'): ?>
    <form method="post" action="<?= base_url('admin/orders/' . $order['id'] . '/paid') ?>" class="mt-3">
        <?= csrf_field() ?>
        <button class="btn btn-success btn-sm">💵 Tandai Sudah Dibayar (Tunai)</button>
    </form>
<?php endif; ?>
                <!-- ===== ADMIN TOMBOL UBAH STATUS & TANDAI SUDAH DIBAYAR ===== -->
                <?php if ($paymentStatus !== 'paid' && $paymentStatus !== 'waiting_confirmation'): ?>
                    <form method="post" action="<?= base_url('admin/orders/' . $order['id'] . '/paid') ?>">
                        <?= csrf_field() ?>
                        <button class="btn btn-success btn-sm">
                            Tandai Sudah Dibayar (Tunai)
                        </button>
                    </form>
                <?php endif; ?>

                <form method="post" action="<?= base_url('admin/orders/' . $order['id'] . '/status') ?>" class="mt-3">
                    <?= csrf_field() ?>
                    <div class="btn-group">
                        <button name="status" value="pending" class="btn btn-light border">Menunggu</button>
                        <button name="status" value="processing" class="btn btn-info text-white">Diproses</button>
                        <button name="status" value="completed" class="btn btn-success">Selesai</button>
                        <button name="status" value="canceled" class="btn btn-danger">Batal</button>
                    </div>
                </form>

                <?php if ($status === 'completed' && $waLink): ?>
                    <a href="<?= $waLink ?>" target="_blank" class="btn btn-success mt-3">
                        📲 Kirim WhatsApp
                    </a>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- ================= RIGHT ================= -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header">
                <strong>Item Pesanan</strong>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Qty</th>
                                <th>Harga</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total = 0; ?>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td><?= esc($it['name']) ?></td>
                                    <td><?= $it['qty'] ?></td>
                                    <td>Rp <?= number_format($it['price'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($it['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                                <?php $total += $it['subtotal']; ?>
                            <?php endforeach; ?>
                            <tr>
                                <th colspan="3">Total</th>
                                <th>Rp <?= number_format($total, 0, ',', '.') ?></th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <a href="<?= base_url('admin/orders') ?>" class="btn btn-light border">Kembali</a>
                <a target="_blank" href="<?= base_url('admin/orders/' . $order['id'] . '/nota') ?>" class="btn btn-primary">Cetak Nota</a>
                <a target="_blank" href="<?= base_url('admin/orders/' . $order['id'] . '/nota/pdf') ?>" class="btn btn-outline-secondary">PDF</a>

            </div>
        </div>
    </div>
</div>

<?php include APPPATH . 'Views/admin/partials/foot.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
