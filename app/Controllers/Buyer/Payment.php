<?php

namespace App\Controllers\Buyer;

use App\Controllers\BaseController;
use App\Models\OrderModel;

class Payment extends BaseController
{
    private function mustLogin()
    {
        $user = session('user');
        if (!$user) {
            return redirect()->to('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        return $user;
    }

    public function pay($id)
    {
        $user = $this->mustLogin();
        if ($user instanceof \CodeIgniter\HTTP\RedirectResponse) return $user;

        $order = (new OrderModel())
            ->where('id', $id)
            ->where('user_id', $user['id'])
            ->first();

        if (!$order) {
            return redirect()->to('p/orders')->with('error', 'Pesanan tidak ditemukan.');
        }

        return view('payment/qrmanual', ['order' => $order]);
    }

    public function uploadProof($id)
    {
        $user = $this->mustLogin();
        if ($user instanceof \CodeIgniter\HTTP\RedirectResponse) return $user;

        $orderModel = new OrderModel();

        $order = $orderModel
            ->where('id', $id)
            ->where('user_id', $user['id'])
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Pesanan tidak ditemukan.');
        }

        // AMBIL FILE
        $file = $this->request->getFile('proof');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid.');
        }

        // Nama Baru
        $newName = 'qris_' . $id . '_' . time() . '.' . $file->getExtension();

        // Pindahkan file
        $file->move(ROOTPATH . 'public/uploads/qris/bukti', $newName);

        // UPDATE DATABASE
        $orderModel->update($id, [
            'payment_status' => 'waiting_confirmation',
            'payment_proof'  => $newName
        ]);

        return redirect()->to('p/orders/'.$id)
            ->with('success', 'Bukti pembayaran berhasil dikirim.');
    }
}
