<?php

namespace Database\Factories;

use App\Models\Pemagang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pemagang>
 */
class PemagangFactory extends Factory
{
    protected $model = Pemagang::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_lengkap' => $this->faker->name(),
            'no_hp' => $this->faker->unique()->numerify('081#########'),
            'kampus' => $this->faker->randomElement([
                'Universitas Indonesia',
                'Universitas Gadjah Mada',
                'Institut Teknologi Bandung',
                'Universitas Diponegoro',
                'Universitas Brawijaya',
                'Universitas Airlangga',
                'Universitas Sebelas Maret',
                'Universitas Padjadjaran',
                'Institut Teknologi Sepuluh Nopember',
                'Politeknik Negeri Jakarta',
                'Politeknik Negeri Bandung',
                'Politeknik Elektronika Negeri Surabaya',
                'Universitas Telkom',
                'Universitas Bina Nusantara',
            ]),
            'divisi' => $this->faker->randomElement([
                "Administrasi",
                "UI/UX Designer",
                "Programmer",
                "Human Resource",
                "Social Media Specialist",
                "Photographer/Videographer",
                "Content Writer",
                "Marketing & Sales",
                "Content Creative",
                "Digital Marketing",
                "Public Relations",
                "TikTok Creator",
                "Content Planner",
                "Project Manager",
                "Las",
                "Animasi",
                "SEO",
                "Machine Learning",
            ]),
        ];
    }
}
