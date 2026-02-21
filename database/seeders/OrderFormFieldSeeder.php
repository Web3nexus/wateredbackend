<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderFormField;
use Illuminate\Database\Seeder;

class OrderFormFieldSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::all();

        $defaultFields = [
            [
                'label' => 'Full Legal Name',
                'field_type' => 'text',
                'placeholder' => 'Enter your full name',
                'is_required' => true,
                'sort_order' => 1,
            ],
            [
                'label' => 'Place of Origin',
                'field_type' => 'text',
                'placeholder' => 'City, State/Province, Country',
                'is_required' => true,
                'sort_order' => 2,
            ],
            [
                'label' => 'Spiritual Background',
                'field_type' => 'textarea',
                'placeholder' => 'Briefly describe your spiritual upbringing or background',
                'is_required' => true,
                'sort_order' => 3,
            ],
            [
                'label' => 'Why do you wish to join this circle?',
                'field_type' => 'textarea',
                'placeholder' => 'Tell us about your intentions',
                'is_required' => true,
                'sort_order' => 4,
            ],
            [
                'label' => 'Do you have any spiritual references or mentors?',
                'field_type' => 'text',
                'placeholder' => 'Name of mentor or reference (Optional)',
                'is_required' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($orders as $order) {
            foreach ($defaultFields as $field) {
                OrderFormField::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'label' => $field['label'],
                    ],
                    $field
                );
            }
        }
    }
}
