<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Project inquiry submissions from the contact form.
     * Stores every valid submission as a backup to email delivery.
     */
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();

            // Submitter details
            $table->string('name', 100)->comment('Full name of the submitter');
            $table->string('company', 150)->comment('Company or organisation name');
            $table->string('email', 254)->comment('Work email address');

            // Service and project info
            $table->string('service', 50)->comment('Selected service key');
            $table->text('project_details')->comment('Free-text project description');

            // Tracking
            $table->string('ip_address', 45)->nullable()->comment('Submitter IP (IPv4 or IPv6)');
            $table->string('user_agent', 500)->nullable()->comment('Browser user agent string');
            $table->decimal('recaptcha_score', 3, 2)->nullable()->comment('reCAPTCHA v3 score 0.0–1.0');

            // Status
            $table->boolean('mail_sent')->default(false)->comment('Whether notification email was sent');

            $table->timestamps();

            // Indexes for common queries
            $table->index('email');
            $table->index('created_at');
            $table->index('service');
            $table->index('mail_sent');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};