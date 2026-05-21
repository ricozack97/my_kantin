<?php
$title = 'Kelola Menu';
include APPPATH . 'Views/admin/partials/head.php';
?>

<style>
  /* theme variables moved to admin partial head.php */
  .img-thumb {
    width: 72px;
    height: 72px;
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.06);
    box-shadow: 0 6px 18px rgba(0,0,0,0.04);
  }

  /* Card and header */
  .card {
    border-radius: 16px;
    border: none;
    background: var(--card);
    box-shadow: 0 12px 30px rgba(0,0,0,0.06);
  }
  .card-header {
    background: transparent;
    border-bottom: none;
    padding: 18px 24px;
  }
  .card-header h6 {
    color: var(--primary);
    font-weight:700;
    margin:0;
  }

  /* Table style */
  .table {
    border-collapse: separate;
    border-spacing: 0 10px;
  }
  .table thead th {
    background: transparent;
    border: none;
    color: var(--muted);
    font-weight:600;
    padding: 12px 18px;
  }
  .table tbody tr {
    background: var(--card);
    border-radius: 12px;
    box-shadow: 0 6px 14px rgba(0,0,0,0.03);
  }
  .table td {
    vertical-align: middle;
    border: none !important;
    padding: 14px 18px;
  }

  /* Badges */
  .table .badge {
    padding: 6px 10px;
    border-radius: 999px;
    font-weight:600;
  }
  .badge-success { background:#1b7a2e; color:#fff; }
  .badge-warning { background:#ffb020; color:#fff; }
  .badge-secondary { background:#e9ecef; color:#4a4a57; }
  .badge-light { background:#f7f7fb; color:#6b6b75; border:1px solid rgba(0,0,0,0.03); }

  /* Buttons consistent with theme */
  .btn-primary, .btn.btn-sm.btn-primary {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: #fff !important;
    box-shadow: 0 8px 20px rgba(255,71,102,0.12);
  }
  .btn-edit { background: #2b8cff; color:#fff; border:none; }
  .btn-delete { background: #ff5566; color:#fff; border:none; }
  .btn-edit, .btn-delete { padding:6px 10px; border-radius:8px; font-weight:600; }

  /* Pagination */
  .pagination li { display:inline-block; margin:0 4px; }
  .pagination li a, .pagination li span {
    padding:8px 12px; border-radius:8px; border:1px solid transparent; background:#fff; color:#555;
  }
  .pagination li.active span { background: var(--primary); color:#fff; border-color:var(--primary); }
  /* Button text visibility for small screens */
  .btn-text { margin-left:8px; display:inline-block; }
  @media (max-width:576px) {
    .img-thumb { width:48px; height:48px; }
    .table thead th { font-size:0.86rem; }
    .btn-text { display:none; }
    .table td { padding:10px 12px; }
    .card { padding:12px; }
  }
</style>

<h1 class="h3 mb-4 text-gray-800">Kelola Menu</h1>

<div class="card shadow mb-4">

  <div class="card-header py-3 d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">Daftar Menu</h6>

    <div class="d-flex align-items-center">
      <form method="get" class="form-inline mr-2">
        <label class="mr-2 mb-0 small text-muted">Filter:</label>
        <select name="jenis" class="form-control form-control-sm" onchange="this.form.submit()">
          <option value="">Semua</option>
          <option value="makanan" <?= isset($jenis) && $jenis === 'makanan' ? 'selected' : '' ?>>Makanan</option>
          <option value="minuman" <?= isset($jenis) && $jenis === 'minuman' ? 'selected' : '' ?>>Minuman</option>
        </select>
      </form>

      <a class="btn btn-sm btn-primary" href="<?= base_url('admin/menus/create'); ?>">
        <i class="fas fa-plus"></i> Tambah Menu
      </a>
    </div>
  </div>

  <div class="card-body">

    <?php if (session('success')): ?>
      <div class="alert alert-success" role="alert">
        <?= esc(session('success')); ?>
      </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
      <div class="alert alert-danger" role="alert">
        <?= esc(session('error')); ?>
      </div>
    <?php endif; ?>

    <div class="table-responsive">
      <table class="table table-bordered table-hover" width="100%" cellspacing="0">
        <thead class="thead-light">
          <tr>
            <th>Gambar</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aktif</th>
            <th>Populer</th>
            <th style="width: 180px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($menus as $m): ?>
            <tr>
              <td>
                <?php if ($m['image']): ?>
                  <img class="img-thumb" src="<?= base_url('assets/img/' . $m['image']); ?>" alt="">
                <?php endif; ?>
              </td>

              <td><?= esc(ucwords($m['name'])); ?></td>

              <td><?= esc(ucwords($m['category_name'] ?? '-')); ?></td>

              <td>Rp <?= number_format($m['price'], 0, ',', '.'); ?></td>

              <td><?= (int)($m['stock'] ?? 0); ?></td>

              <td>
                <?php if ($m['is_active']): ?>
                  <span class="badge badge-success"><i class="fas fa-check-circle"></i> <span class="badge-text">Ya</span></span>
                <?php else: ?>
                  <span class="badge badge-secondary"><i class="fas fa-times-circle"></i> <span class="badge-text">Tidak</span></span>
                <?php endif; ?>
              </td>

              <td>
                <?php if (!empty($m['is_popular'])): ?>
                  <span class="badge badge-warning"><i class="fas fa-star"></i> <span class="badge-text">Ya</span></span>
                <?php else: ?>
                  <span class="badge badge-light"><i class="far fa-star"></i> <span class="badge-text">Tidak</span></span>
                <?php endif; ?>
              </td>

              <td class="text-nowrap">
                <a class="btn btn-sm btn-edit" href="<?= base_url('admin/menus/' . $m['id'] . '/edit'); ?>" title="Edit">
                  <i class="fas fa-pen"></i><span class="btn-text">Edit</span>
                </a>

                <form action="<?= base_url('admin/menus/' . $m['id'] . '/delete'); ?>"
                  method="post"
                  style="display:inline"
                  onsubmit="return confirm('Hapus menu ini?')">
                  <?= csrf_field(); ?>
                  <button type="submit" class="btn btn-sm btn-delete" title="Hapus">
                    <i class="fas fa-trash"></i><span class="btn-text">Hapus</span>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php if (isset($pager)): ?>
        <div class="d-flex justify-content-center mt-3">
          <?= $pager->links('default', 'arrows'); ?>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<?php include APPPATH . 'Views/admin/partials/foot.php'; ?>