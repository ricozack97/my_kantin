<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\UserModel;
use Dompdf\Dompdf;

class Orders extends BaseController
{
    public function index()
    {
        $orderModel = model(OrderModel::class);

        $payment = $this->request->getGet('payment');

        $builder = $orderModel
            ->select('orders.*, users.name as customer_name')
            ->join('users', 'users.id = orders.user_id', 'left');

        if ($payment === 'lunas') {
            $builder->where('orders.payment_status', 'paid');
        } elseif ($payment === 'belum') {
            $builder->where('orders.payment_status !=', 'paid');
        }

        $orders = $builder
            ->orderBy('orders.created_at', 'DESC')
            ->paginate(10);

        $pager = $orderModel->pager;

        return view('admin/orders/index', [
            'orders'  => $orders,
            'pager'   => $pager,
            'payment' => $payment,
        ]);
    }


    public function show($id)
    {
        $id         = (int) $id;
        $orderModel = model(OrderModel::class);
        $itemModel  = model(OrderItemModel::class);

        $order = $orderModel
            ->select(
                'orders.*,
                 users.name AS customer_name,
                 users.no_hp AS no_hp,
                 ua.building AS address_building,
                 ua.room     AS address_room,
                 ua.note     AS address_note'
            )
            ->join('users', 'users.id = orders.user_id', 'left')
            ->join('user_addresses ua', 'ua.id = orders.delivery_address_id', 'left')
            ->where('orders.id', $id)
            ->first();

        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        $items = $itemModel->where('order_id', $id)->findAll();

        return view('admin/orders/show', [
            'order' => $order,
            'items' => $items,
        ]);
    }

    public function updateStatus($id)
    {
        $id     = (int) $id;
        $status = $this->request->getPost('status');

        $allowed = ['pending', 'processing', 'completed', 'canceled'];

        if (!in_array($status, $allowed, true)) {
            return redirect()->back()->with('error', 'Status tidak dikenal.');
        }

        $orderModel = model(OrderModel::class);
        $order      = $orderModel->find($id);

        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        $orderModel->update($id, ['status' => $status]);

        return redirect()->back()->with('success', 'Status pesanan diperbarui.');
    }

    public function markPaid($id)
    {
        $id = (int) $id;

        $orderModel = model(OrderModel::class);
        $order      = $orderModel->find($id);

        if (! $order) {
            return redirect()->to('/admin/orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        if ($order['status'] === 'canceled') {
            return redirect()->back()->with('error', 'Pesanan yang dibatalkan tidak bisa ditandai sudah dibayar.');
        }

        $orderModel->update($id, [
            'payment_status' => 'paid',
        ]);

        return redirect()->back()->with('success', 'Pembayaran ditandai sudah dibayar (tunai).');
    }


    public function nota($id)
    {
        $id = (int)$id;
        $orderModel = model(OrderModel::class);

        $order = $orderModel
            ->select('orders.*, users.name AS customer_name')
            ->join('users', 'users.id = orders.user_id', 'left')
            ->where('orders.id', $id)
            ->first();

        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        $items = model(OrderItemModel::class)->where('order_id', $id)->findAll();

        return view('orders/nota', [
            'order' => $order,
            'user'  => ['name' => $order['customer_name']],
        ]);
    }


    public function notaPdf($id)
    {
        $id = (int)$id;
        $orderModel = model(OrderModel::class);

        $order = $orderModel
            ->select('orders.*, users.name AS customer_name')
            ->join('users', 'users.id = orders.user_id', 'left')
            ->where('orders.id', $id)
            ->first();

        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        $items = model(OrderItemModel::class)->where('order_id', $id)->findAll();
        $order['items'] = $items;

        $html = view('orders/nota_pdf', ['order' => $order, 'user' => ['name' => $order['customer_name']]]);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper([0, 0, 210, 600]);
        $dompdf->render();
        $output = $dompdf->output();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', "inline; filename=nota_{$order['code']}.pdf")
            ->setBody($output);
    }


    // ===============================
    // ADMIN MENERIMA PEMBAYARAN QRIS
    // ===============================
    public function verify($id)
    {
        $id = (int)$id;

        $orderModel = model(OrderModel::class);
        $order = $orderModel->find($id);

        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        if ($order['payment_status'] !== 'waiting_confirmation') {
            return redirect()->back()->with('error', 'Tidak ada pembayaran yang menunggu verifikasi.');
        }

        $orderModel->update($id, [
            'payment_status' => 'paid',
            'status'         => 'processing',
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    // ==================================
    // ADMIN MENOLAK PEMBAYARAN QRIS
    // ==================================
    public function reject($id)
    {
        $id = (int)$id;

        $orderModel = model(OrderModel::class);
        $order = $orderModel->find($id);

        if (!$order) {
            return redirect()->to('/admin/orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        if (!empty($order['payment_proof'])) {
            $path = ROOTPATH . 'public/uploads/qris/bukti/' . $order['payment_proof'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $orderModel->update($id, [
            'payment_status' => 'failed',
            'payment_proof'  => null,
        ]);

        return redirect()->back()->with('success', 'Pembayaran ditolak.');
    }
} // ⬅️ INI adalah kurung penutup yang BENAR
