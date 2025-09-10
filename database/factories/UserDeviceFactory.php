<?php

namespace Database\Factories;

use App\Models\UserDevice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserDevice>
 */
class UserDeviceFactory extends Factory
{
    protected $model = UserDevice::class;

    public function definition(): array
    {
        $deviceTypes = ['mobile', 'tablet', 'desktop', 'laptop', 'smartwatch', 'tv', 'other'];
        $platforms = ['iOS', 'Android', 'Windows', 'macOS', 'Linux', 'Chrome OS', 'Web', 'other'];
        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera', 'Brave', 'Mobile App', 'Desktop App', 'other'];
        
        $deviceType = $this->faker->randomElement($deviceTypes);
        $platform = $this->generatePlatform($deviceType);
        $browser = $this->generateBrowser($platform, $deviceType);
        $deviceName = $this->generateDeviceName($deviceType, $platform);

        return [
            'user_id' => User::factory(),
            'device_name' => $deviceName,
            'device_type' => $deviceType,
            'platform' => $platform,
            'browser' => $browser,
            'ip_address' => $this->faker->ipv4(),
            'token' => $this->generateToken(),
            'notifications_enabled' => $this->faker->boolean(85), // 85% habilitadas
        ];
    }

    private function generatePlatform(string $deviceType): string
    {
        return match ($deviceType) {
            'mobile' => $this->faker->randomElement(['iOS', 'Android']),
            'tablet' => $this->faker->randomElement(['iOS', 'Android', 'Windows']),
            'desktop' => $this->faker->randomElement(['Windows', 'macOS', 'Linux']),
            'laptop' => $this->faker->randomElement(['Windows', 'macOS', 'Linux', 'Chrome OS']),
            'smartwatch' => $this->faker->randomElement(['iOS', 'Android']),
            'tv' => $this->faker->randomElement(['Web', 'Android', 'other']),
            default => $this->faker->randomElement(['iOS', 'Android', 'Windows', 'macOS', 'Linux', 'Web', 'other']),
        };
    }

    private function generateBrowser(string $platform, string $deviceType): string
    {
        // Si es una app móvil o de escritorio, usar navegadores de app
        if ($deviceType === 'mobile' && $this->faker->boolean(30)) {
            return 'Mobile App';
        }
        
        if (in_array($deviceType, ['desktop', 'laptop']) && $this->faker->boolean(20)) {
            return 'Desktop App';
        }

        // Navegadores por plataforma
        return match ($platform) {
            'iOS' => $this->faker->randomElement(['Safari', 'Chrome', 'Firefox', 'Edge', 'Mobile App']),
            'Android' => $this->faker->randomElement(['Chrome', 'Firefox', 'Edge', 'Brave', 'Mobile App']),
            'Windows' => $this->faker->randomElement(['Chrome', 'Edge', 'Firefox', 'Opera', 'Desktop App']),
            'macOS' => $this->faker->randomElement(['Safari', 'Chrome', 'Firefox', 'Edge', 'Desktop App']),
            'Linux' => $this->faker->randomElement(['Chrome', 'Firefox', 'Edge', 'Opera', 'Desktop App']),
            'Chrome OS' => $this->faker->randomElement(['Chrome', 'Edge']),
            'Web' => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera']),
            default => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera', 'Brave']),
        };
    }

    private function generateDeviceName(string $deviceType, string $platform): string
    {
        $brands = match ($platform) {
            'iOS' => ['iPhone', 'iPad', 'MacBook', 'iMac', 'Mac Pro', 'Apple Watch'],
            'Android' => ['Samsung Galaxy', 'Google Pixel', 'OnePlus', 'Xiaomi', 'Huawei', 'LG'],
            'Windows' => ['Surface', 'Dell', 'HP', 'Lenovo', 'ASUS', 'Acer'],
            'macOS' => ['MacBook Air', 'MacBook Pro', 'iMac', 'Mac Pro', 'Mac Studio'],
            'Linux' => ['ThinkPad', 'Dell XPS', 'System76', 'Framework', 'Purism'],
            'Chrome OS' => ['Chromebook', 'Chromebox', 'Pixelbook'],
            default => ['Generic Device', 'Unknown Device'],
        };

        $brand = $this->faker->randomElement($brands);
        $model = $this->faker->randomElement(['Pro', 'Air', 'Max', 'Ultra', 'Plus', 'Mini', 'SE']);
        $year = $this->faker->numberBetween(2020, 2024);
        
        return "{$brand} {$model} ({$year})";
    }

    private function generateToken(): string
    {
        $prefixes = ['fcm_', 'apns_', 'web_', 'app_', ''];
        $prefix = $this->faker->randomElement($prefixes);
        
        return $prefix . $this->faker->regexify('[A-Za-z0-9]{32,64}');
    }

    public function mobile(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'mobile',
            'platform' => $this->faker->randomElement(['iOS', 'Android']),
            'browser' => $this->faker->randomElement(['Chrome', 'Safari', 'Firefox', 'Mobile App']),
            'device_name' => $this->faker->randomElement(['iPhone 14 Pro', 'Samsung Galaxy S23', 'Google Pixel 7', 'OnePlus 11']),
        ]);
    }

    public function tablet(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'tablet',
            'platform' => $this->faker->randomElement(['iOS', 'Android', 'Windows']),
            'browser' => $this->faker->randomElement(['Safari', 'Chrome', 'Edge', 'Firefox']),
            'device_name' => $this->faker->randomElement(['iPad Pro', 'Samsung Galaxy Tab', 'Surface Pro', 'iPad Air']),
        ]);
    }

    public function desktop(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'desktop',
            'platform' => $this->faker->randomElement(['Windows', 'macOS', 'Linux']),
            'browser' => $this->faker->randomElement(['Chrome', 'Edge', 'Firefox', 'Safari', 'Desktop App']),
            'device_name' => $this->faker->randomElement(['Dell OptiPlex', 'iMac', 'HP Pavilion', 'Custom PC']),
        ]);
    }

    public function laptop(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'laptop',
            'platform' => $this->faker->randomElement(['Windows', 'macOS', 'Linux', 'Chrome OS']),
            'browser' => $this->faker->randomElement(['Chrome', 'Edge', 'Firefox', 'Safari', 'Desktop App']),
            'device_name' => $this->faker->randomElement(['MacBook Pro', 'Dell XPS', 'ThinkPad', 'Surface Laptop', 'Chromebook']),
        ]);
    }

    public function ios(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'iOS',
            'browser' => $this->faker->randomElement(['Safari', 'Chrome', 'Firefox', 'Mobile App']),
            'device_name' => $this->faker->randomElement(['iPhone 14 Pro', 'iPhone 13', 'iPad Pro', 'iPad Air', 'MacBook Pro']),
        ]);
    }

    public function android(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'Android',
            'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Edge', 'Mobile App']),
            'device_name' => $this->faker->randomElement(['Samsung Galaxy S23', 'Google Pixel 7', 'OnePlus 11', 'Samsung Galaxy Tab']),
        ]);
    }

    public function windows(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'Windows',
            'browser' => $this->faker->randomElement(['Chrome', 'Edge', 'Firefox', 'Desktop App']),
            'device_name' => $this->faker->randomElement(['Surface Pro', 'Dell OptiPlex', 'HP Pavilion', 'Lenovo ThinkPad']),
        ]);
    }

    public function macos(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'macOS',
            'browser' => $this->faker->randomElement(['Safari', 'Chrome', 'Firefox', 'Desktop App']),
            'device_name' => $this->faker->randomElement(['MacBook Pro', 'iMac', 'MacBook Air', 'Mac Studio']),
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'notifications_enabled' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'notifications_enabled' => false,
        ]);
    }

    public function withToken(): static
    {
        return $this->state(fn (array $attributes) => [
            'token' => $this->generateToken(),
        ]);
    }

    public function withoutToken(): static
    {
        return $this->state(fn (array $attributes) => [
            'token' => null,
        ]);
    }

    public function chrome(): static
    {
        return $this->state(fn (array $attributes) => [
            'browser' => 'Chrome',
        ]);
    }

    public function firefox(): static
    {
        return $this->state(fn (array $attributes) => [
            'browser' => 'Firefox',
        ]);
    }

    public function safari(): static
    {
        return $this->state(fn (array $attributes) => [
            'browser' => 'Safari',
        ]);
    }

    public function edge(): static
    {
        return $this->state(fn (array $attributes) => [
            'browser' => 'Edge',
        ]);
    }

    public function mobileApp(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => 'mobile',
            'browser' => 'Mobile App',
            'platform' => $this->faker->randomElement(['iOS', 'Android']),
        ]);
    }

    public function desktopApp(): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type' => $this->faker->randomElement(['desktop', 'laptop']),
            'browser' => 'Desktop App',
            'platform' => $this->faker->randomElement(['Windows', 'macOS', 'Linux']),
        ]);
    }

    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => $this->faker->dateTimeBetween('-1 year', '-30 days'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', '-30 days'),
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function withCustomDeviceName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'device_name' => $name,
        ]);
    }

    public function withCustomToken(string $token): static
    {
        return $this->state(fn (array $attributes) => [
            'token' => $token,
        ]);
    }

    public function withCustomIp(string $ip): static
    {
        return $this->state(fn (array $attributes) => [
            'ip_address' => $ip,
        ]);
    }
}
