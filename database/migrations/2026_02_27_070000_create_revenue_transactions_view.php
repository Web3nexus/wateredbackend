<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS revenue_transactions");
        DB::statement("
            CREATE VIEW revenue_transactions AS
            SELECT 
                CONCAT('sub_', s.id) as id,
                s.id as original_id,
                'Subscription' as source_type,
                s.plan_id as description,
                s.provider_subscription_id as reference,
                u.name as customer_name,
                u.email as customer_email,
                s.amount,
                s.status as payment_status,
                s.created_at
            FROM subscriptions s
            LEFT JOIN users u ON s.user_id = u.id

            UNION ALL

            SELECT 
                CONCAT('evt_', er.id) as id,
                er.id as original_id,
                'Event' as source_type,
                e.title as description,
                er.payment_reference as reference,
                er.full_name as customer_name,
                er.email as customer_email,
                er.amount,
                er.payment_status,
                er.created_at
            FROM event_registrations er
            LEFT JOIN events e ON er.event_id = e.id

            UNION ALL

            SELECT 
                CONCAT('apt_', a.id) as id,
                a.id as original_id,
                'Appointment' as source_type,
                COALESCE(ct.name, 'Consultation') as description,
                a.payment_reference as reference,
                COALESCE(u.name, a.full_name) as customer_name,
                COALESCE(u.email, a.email) as customer_email,
                a.amount,
                a.payment_status,
                a.created_at
            FROM appointments a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN consultation_types ct ON a.consultation_type_id = ct.id
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS revenue_transactions");
    }
};
