<?php
$title = 'Laporan Keuangan';
include APPPATH . 'Views/admin/partials/head.php';

$totalTransactions = (int)($sum['cnt'] ?? 0);
$grandTotal = (int)($sum['grand_total'] ?? 0);
$avgOrder = $totalTransactions > 0 ? (int) round($grandTotal / $totalTransactions) : 0;
$topQtyMax = 0;
$menuSold = 0;

foreach ($top as $t) {
  $qty = (int)$t['qty'];
  $topQtyMax = max($topQtyMax, $qty);
  $menuSold += $qty;
}
?>

<style>
  .reports-page {
    color: #162033;
  }

  .reports-titlebar {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 24px;
  }

  .reports-titlebar h1 {
    margin: 0;
    font-weight: 800;
    color: #102033;
  }

  .reports-subtitle {
    color: #778196;
    margin-top: 6px;
    font-size: 0.95rem;
  }

  .range-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 38px;
    padding: 8px 14px;
    border-radius: 999px;
    background: #fff;
    border: 1px solid #e7eaf2;
    color: #3b4760;
    font-weight: 700;
    box-shadow: 0 8px 24px rgba(31, 44, 71, 0.06);
    white-space: nowrap;
  }

  .reports-shell {
    background: #fff;
    border: 1px solid #edf0f6;
    border-radius: 18px;
    padding: 24px;
    box-shadow: 0 18px 44px rgba(31, 44, 71, 0.08);
  }

  .filter-panel {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 20px;
    padding: 18px;
    border-radius: 16px;
    background: #f8fafc;
    border: 1px solid #edf0f6;
  }

  .filter-fields {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 14px;
  }

  .field-group label {
    display: block;
    margin-bottom: 7px;
    color: #667085;
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }

  .field-control {
    min-width: 190px;
    min-height: 42px;
    border: 1px solid #dfe4ee;
    border-radius: 12px;
    background: #fff;
    color: #172033;
    padding: 8px 12px;
    font-weight: 700;
  }

  .field-control:focus {
    outline: none;
    border-color: #ff4766;
    box-shadow: 0 0 0 4px rgba(255, 71, 102, 0.12);
  }

  .reports-page .btn {
    min-height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 9px 14px;
    border-radius: 12px !important;
    font-weight: 800;
    box-shadow: none !important;
    white-space: nowrap;
  }

  .reports-page .btn-primary {
    background: #ff4766 !important;
    border-color: #ff4766 !important;
  }

  .reports-page .btn-outline-secondary {
    color: #344054;
    border-color: #d8deea;
    background: #fff;
  }

  .metric-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 18px;
  }

  .metric-card,
  .dashboard-card {
    background: #fff;
    border: 1px solid #edf0f6;
    border-radius: 16px;
    box-shadow: 0 14px 32px rgba(31, 44, 71, 0.06);
  }

  .metric-card {
    padding: 18px;
  }

  .metric-label {
    color: #778196;
    font-size: 0.76rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 8px;
  }

  .metric-value {
    color: #142033;
    font-size: 1.35rem;
    font-weight: 800;
  }

  .metric-note {
    margin-top: 8px;
    color: #7a8498;
    font-size: 0.86rem;
    font-weight: 600;
  }

  .content-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1.05fr);
    gap: 18px;
    margin-bottom: 18px;
  }

  .dashboard-card {
    overflow: hidden;
  }

  .card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 20px 24px;
    border-bottom: 1px solid #edf0f6;
  }

  .card-head h3 {
    margin: 0;
    color: #142033;
    font-size: 1.05rem;
    font-weight: 800;
  }

  .card-kicker {
    color: #7a8498;
    font-size: 0.82rem;
    font-weight: 700;
  }

  .dashboard-card .card-body {
    padding: 22px 24px;
  }

  .summary-list {
    display: grid;
    gap: 12px;
  }

  .summary-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 14px;
    border-radius: 12px;
    background: #f8fafc;
    border: 1px solid #edf0f6;
  }

  .summary-name {
    color: #687385;
    font-weight: 800;
  }

  .summary-number {
    color: #172033;
    font-weight: 800;
  }

  .top-chart {
    display: grid;
    gap: 16px;
  }

  .chart-row {
    display: grid;
    grid-template-columns: minmax(110px, 0.8fr) minmax(0, 2fr) minmax(90px, 0.65fr);
    align-items: center;
    gap: 14px;
  }

  .chart-name {
    color: #263247;
    font-weight: 800;
    line-height: 1.25;
  }

  .bar-track {
    height: 36px;
    border-radius: 999px;
    background: #f1f5f9;
    overflow: hidden;
    border: 1px solid #e8edf5;
  }

  .bar-fill {
    min-width: 34px;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 12px;
    border-radius: 999px;
    background: linear-gradient(90deg, #2674e8, #19c37d);
    color: #fff;
    font-size: 0.8rem;
    font-weight: 800;
  }

  .chart-omzet {
    color: #142033;
    font-weight: 800;
    text-align: right;
    white-space: nowrap;
  }

  .empty-state {
    min-height: 130px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: #f8fafc;
    border: 1px dashed #d8deea;
    color: #7a8498;
    font-weight: 700;
    text-align: center;
  }

  .report-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    overflow: hidden;
    border-radius: 14px;
    border: 1px solid #e4e8f1;
  }

  .report-table thead th {
    background: #f4f7fb;
    border: none;
    color: #5d6678;
    font-size: 0.82rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    padding: 15px 18px;
  }

  .report-table tbody td {
    border-top: 1px solid #edf0f6;
    color: #333d52;
    padding: 16px 18px;
    vertical-align: middle;
    font-weight: 600;
  }

  .report-table tbody tr:hover td {
    background: #fbfcfe;
  }

  .text-right {
    text-align: right;
  }

  @media (max-width: 992px) {
    .metric-grid,
    .content-grid {
      grid-template-columns: 1fr;
    }

    .filter-panel {
      align-items: stretch;
      flex-direction: column;
    }
  }

  @media (max-width: 640px) {
    .reports-titlebar {
      display: block;
    }

    .range-pill {
      margin-top: 14px;
    }

    .reports-shell {
      padding: 18px;
    }

    .field-group,
    .field-control,
    .filter-fields .btn,
    .filter-panel .btn {
      width: 100%;
    }

    .chart-row {
      grid-template-columns: 1fr;
      gap: 8px;
    }

    .chart-omzet {
      text-align: left;
    }
  }
</style>

<div class="reports-page">
  <div class="reports-titlebar">
    <div>
      <h1 class="h3 text-gray-800">Laporan Keuangan</h1>
      <div class="reports-subtitle">Pantau omzet, transaksi selesai, dan menu terlaris dalam satu tampilan.</div>
    </div>
    <div class="range-pill">
      <i class="fas fa-calendar-alt"></i>
      <?= esc(date('d M Y', strtotime($from))); ?> - <?= esc(date('d M Y', strtotime($to))); ?>
    </div>
  </div>

  <div class="reports-shell">
    <form method="get" action="<?= base_url('admin/reports'); ?>">
      <div class="filter-panel">
        <div class="filter-fields">
          <div class="field-group">
            <label for="from">Dari</label>
            <input class="field-control" type="date" id="from" name="from" value="<?= esc($from); ?>">
          </div>
          <div class="field-group">
            <label for="to">Sampai</label>
            <input class="field-control" type="date" id="to" name="to" value="<?= esc($to); ?>">
          </div>
          <button class="btn btn-primary" type="submit">
            <i class="fas fa-filter"></i>
            Terapkan
          </button>
        </div>
        <a class="btn btn-outline-secondary" href="<?= base_url('admin/reports/export?from=' . $from . '&to=' . $to); ?>">
          <i class="fas fa-file-csv"></i>
          Export CSV
        </a>
      </div>
    </form>

    <div class="metric-grid">
      <div class="metric-card">
        <div class="metric-label">Total Transaksi</div>
        <div class="metric-value"><?= number_format($totalTransactions, 0, ',', '.'); ?></div>
        <div class="metric-note">Pesanan selesai dalam periode ini</div>
      </div>
      <div class="metric-card">
        <div class="metric-label">Omzet Paid</div>
        <div class="metric-value">Rp <?= number_format($grandTotal, 0, ',', '.'); ?></div>
        <div class="metric-note">Total nilai transaksi selesai</div>
      </div>
      <div class="metric-card">
        <div class="metric-label">Rata-rata Order</div>
        <div class="metric-value">Rp <?= number_format($avgOrder, 0, ',', '.'); ?></div>
        <div class="metric-note">Nilai rata-rata per transaksi</div>
      </div>
    </div>

    <div class="content-grid">
      <div class="dashboard-card">
        <div class="card-head">
          <h3>Ringkasan</h3>
          <span class="card-kicker">Completed orders</span>
        </div>
        <div class="card-body">
          <div class="summary-list">
            <div class="summary-row">
              <span class="summary-name">Transaksi selesai</span>
              <span class="summary-number"><?= number_format($totalTransactions, 0, ',', '.'); ?></span>
            </div>
            <div class="summary-row">
              <span class="summary-name">Omzet dibayar</span>
              <span class="summary-number">Rp <?= number_format($grandTotal, 0, ',', '.'); ?></span>
            </div>
            <div class="summary-row">
              <span class="summary-name">Menu terjual</span>
              <span class="summary-number"><?= number_format($menuSold, 0, ',', '.'); ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="dashboard-card">
        <div class="card-head">
          <h3>Top Menu</h3>
          <span class="card-kicker">Grafik qty terjual</span>
        </div>
        <div class="card-body">
          <?php if (!empty($top)): ?>
            <div class="top-chart">
              <?php foreach ($top as $t): ?>
                <?php
                $qty = (int)$t['qty'];
                $width = $topQtyMax > 0 ? max(12, (int)round(($qty / $topQtyMax) * 100)) : 0;
                ?>
                <div class="chart-row">
                  <div class="chart-name"><?= esc(ucwords($t['name'])); ?></div>
                  <div class="bar-track" aria-label="<?= esc(ucwords($t['name'])); ?> terjual <?= $qty; ?>">
                    <div class="bar-fill" style="width: <?= $width; ?>%;">
                      <?= $qty; ?>
                    </div>
                  </div>
                  <div class="chart-omzet">Rp <?= number_format((int)$t['omzet'], 0, ',', '.'); ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="empty-state">Tidak ada data menu di rentang tanggal ini.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="dashboard-card">
      <div class="card-head">
        <h3>Omzet Harian</h3>
        <span class="card-kicker">Urut berdasarkan tanggal</span>
      </div>
      <div class="card-body">
        <?php if (!empty($daily)): ?>
          <div class="table-responsive">
            <table class="report-table">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Orders</th>
                  <th class="text-right">Omzet</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($daily as $d): ?>
                  <tr>
                    <td><?= esc(date('d M Y', strtotime($d['d']))); ?></td>
                    <td><?= (int)$d['orders']; ?></td>
                    <td class="text-right">Rp <?= number_format((int)$d['omzet'], 0, ',', '.'); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="empty-state">Belum ada transaksi pada rentang tanggal ini.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include APPPATH . 'Views/admin/partials/foot.php'; ?>
