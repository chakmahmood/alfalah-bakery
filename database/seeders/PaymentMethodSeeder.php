<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            // 💵 Tunai
            ['name' => 'Cash', 'code' => 'CASH', 'type' => 'cash'],

            // 🏦 Bank Transfer
            // ['name' => 'Transfer Bank BCA', 'code' => 'TF_BCA', 'type' => 'bank_transfer'],
            // ['name' => 'Transfer Bank BRI', 'code' => 'TF_BRI', 'type' => 'bank_transfer'],
            // ['name' => 'Transfer Bank BNI', 'code' => 'TF_BNI', 'type' => 'bank_transfer'],
            // ['name' => 'Transfer Bank Mandiri', 'code' => 'TF_MANDIRI', 'type' => 'bank_transfer'],
            // ['name' => 'Transfer Bank CIMB Niaga', 'code' => 'TF_CIMB', 'type' => 'bank_transfer'],
            // ['name' => 'Transfer Bank Permata', 'code' => 'TF_PERMATA', 'type' => 'bank_transfer'],
            // ['name' => 'Transfer Bank Lainnya', 'code' => 'TF_OTHER', 'type' => 'bank_transfer'],

            // 💳 Kartu
            // ['name' => 'Kartu Debit', 'code' => 'DEBIT', 'type' => 'card'],
            // ['name' => 'Kartu Kredit', 'code' => 'CREDIT', 'type' => 'card'],

            // 📱 E-Wallet
            // ['name' => 'QRIS', 'code' => 'QRIS', 'type' => 'e_wallet'],
            // ['name' => 'GoPay', 'code' => 'GOPAY', 'type' => 'e_wallet'],
            // ['name' => 'OVO', 'code' => 'OVO', 'type' => 'e_wallet'],
            // ['name' => 'DANA', 'code' => 'DANA', 'type' => 'e_wallet'],
            // ['name' => 'ShopeePay', 'code' => 'SHOPEEPAY', 'type' => 'e_wallet'],
            // ['name' => 'LinkAja', 'code' => 'LINKAJA', 'type' => 'e_wallet'],
            // ['name' => 'Sakuku', 'code' => 'SAKUKU', 'type' => 'e_wallet'],

            // 🧾 Virtual Account
            // ['name' => 'Virtual Account BCA', 'code' => 'VA_BCA', 'type' => 'virtual_account'],
            // ['name' => 'Virtual Account BNI', 'code' => 'VA_BNI', 'type' => 'virtual_account'],
            // ['name' => 'Virtual Account BRI', 'code' => 'VA_BRI', 'type' => 'virtual_account'],
            // ['name' => 'Virtual Account Mandiri', 'code' => 'VA_MANDIRI', 'type' => 'virtual_account'],

            // // 💰 Lain-lain
            // ['name' => 'Cicilan / PayLater', 'code' => 'PAYLATER', 'type' => 'other'],
            // ['name' => 'Voucher / Gift Card', 'code' => 'VOUCHER', 'type' => 'other'],
            // ['name' => 'Lainnya', 'code' => 'OTHER', 'type' => 'other'],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(
                ['code' => $method['code']],
                [
                    'name' => $method['name'],
                    'type' => $method['type'],
                    'is_active' => true,
                ]
            );
        }
    }
}
