<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = [
            [
                'title' => 'First Degree',
                'description' => 'The foundational level of initiation.',
                'status' => 'open',
                'cta_text' => 'Apply Now',
                'action_type' => 'application_form',
                'order_level' => 1,
            ],
            [
                'title' => 'Second Degree',
                'description' => 'The intermediate level of spiritual progression.',
                'status' => 'invite_only',
                'cta_text' => 'Request Invite',
                'action_type' => 'application_form',
                'order_level' => 2,
            ],
            [
                'title' => 'Third Degree',
                'description' => 'The advanced level of sacred knowledge.',
                'status' => 'invite_only',
                'cta_text' => 'Request Invite',
                'action_type' => 'application_form',
                'order_level' => 3,
            ],
            [
                'title' => 'Kemetic Council',
                'description' => 'The high council of wisdom keepers.',
                'status' => 'closed',
                'cta_text' => 'Currently Closed',
                'action_type' => 'internal_route',
                'order_level' => 4,
            ],
            [
                'title' => 'Leopard Council',
                'description' => 'The council of the spiritual warriors.',
                'status' => 'closed',
                'cta_text' => 'Currently Closed',
                'action_type' => 'internal_route',
                'order_level' => 5,
            ],
            [
                'title' => 'Uzih Chambers',
                'description' => 'The highest echelon of ancestral connection.',
                'status' => 'closed',
                'cta_text' => 'Currently Closed',
                'action_type' => 'internal_route',
                'order_level' => 6,
            ],
        ];

        foreach ($orders as $orderData) {
            Order::updateOrCreate(
                ['title' => $orderData['title']],
                $orderData
            );
        }
    }
}
