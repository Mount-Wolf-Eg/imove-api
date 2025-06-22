<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'imove');
        $this->migrator->add('general.app_payment_percentage', .05);
        // $this->migrator->add('general.urgent_grace_period', 0);
        $this->migrator->add('general.normal_grace_period', 0);
        $this->migrator->add('general.tax_percentage', 0);
        $this->migrator->add('general.general_session_price', 0);
        $this->migrator->add('general.sessions_per_specialty', 100);
        $this->migrator->add('general.specialties_per_sessions', 1);
    }
};
