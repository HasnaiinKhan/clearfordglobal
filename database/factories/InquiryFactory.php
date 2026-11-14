<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inquiry>
 */
class InquiryFactory extends Factory
{
    protected $model = Inquiry::class;

    public function definition(): array
    {
        $services = [
            'software-development',
            'data-analytics',
            'software-qa',
            'business-analysis',
            'healthcare-technology',
            'not-sure',
        ];

        return [
            'name'            => $this->faker->name(),
            'company'         => $this->faker->company(),
            'email'           => $this->faker->unique()->safeEmail(),
            'service'         => $this->faker->randomElement($services),
            'project_details' => $this->faker->paragraph(3),
            'ip_address'      => $this->faker->ipv4(),
            'user_agent'      => 'Mozilla/5.0 Test Agent',
            'recaptcha_score' => 0.9,
            'mail_sent'       => false,
        ];
    }
}
