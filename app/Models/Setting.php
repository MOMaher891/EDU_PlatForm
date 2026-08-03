<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
	use HasFactory;

	protected $fillable = [
		'block_devtools',
		'terms_and_conditions',
		'privacy_policy',
		'refund_and_cancellation_policy',
		'platform_name',
		'platform_logo',
		'support_email',
		'support_phone',
		'platform_description',
		'max_courses_per_instructor',
		'max_lessons_per_course',
		'max_file_size',
		'allowed_file_types',
		'default_currency',
		'commission_rate',
		'minimum_withdrawal',
		'payment_processing_days',
		'mail_provider',
		'from_email',
		'from_name',
		'email_notifications',
	];

	public function getPlatformLogoUrlAttribute()
	{
		if ($this->platform_logo) {
			if (str_starts_with($this->platform_logo, 'http://') || str_starts_with($this->platform_logo, 'https://')) {
				return $this->platform_logo;
			}
			$cleanPath = ltrim(str_replace(['/storage/', '/media/'], '', $this->platform_logo), '/');
			return url('media/' . $cleanPath);
		}
		return null;
	}

	public static function getCached(): self
	{
		return cache()->remember('app_settings_singleton', 60, function () {
			return static::query()->first() ?? static::create([
				'block_devtools' => false,
				'platform_name' => 'منصة التعلم الإلكتروني',
				'support_email' => 'support@example.com',
				'support_phone' => '+966 50 123 4567',
				'platform_description' => 'منصة تعليمية متكاملة تقدم دورات تعليمية عالية الجودة',
			]);
		});
	}

	public static function getMaxFileSizeKB(): int
	{
		try {
			$settings = static::getCached();
			$maxMb = (int) ($settings->max_file_size ?? 10);
			return ($maxMb > 0 ? $maxMb : 10) * 1024;
		} catch (\Throwable $e) {
			return 10 * 1024;
		}
	}

	public static function formatPrice($amount)
	{
		try {
			$settings = self::getCached();
			$currency = $settings->default_currency ?? 'USD';
		} catch (\Throwable $e) {
			$currency = 'USD';
		}
		
		switch ($currency) {
			case 'USD':
				return '$' . number_format($amount, 2);
			case 'EGP':
				return number_format($amount, 2) . ' ج.م';
			case 'SAR':
				return number_format($amount, 2) . ' ر.س';
			case 'EUR':
				return '€' . number_format($amount, 2);
			case 'GBP':
				return '£' . number_format($amount, 2);
			default:
				return $currency . ' ' . number_format($amount, 2);
		}
	}

	/**
	 * Get the latest exchange rates relative to USD (1 USD = X Currency)
	 * with local caching for 12 hours.
	 *
	 * @return array
	 */
	public static function getRates(): array
	{
		$defaultRates = [
			'USD' => 1.0,
			'EGP' => 50.0,
			'SAR' => 3.75,
			'EUR' => 0.92,
			'GBP' => 0.79,
		];

		try {
			$rates = cache()->remember('currency_exchange_rates', 43200, function () use ($defaultRates) {
				$response = \Illuminate\Support\Facades\Http::timeout(5)->get('https://open.er-api.com/v6/latest/USD');
				if ($response->successful()) {
					$fetchedRates = $response->json('rates');
					if (is_array($fetchedRates)) {
						return array_intersect_key($fetchedRates, $defaultRates) + $defaultRates;
					}
				}
				return $defaultRates;
			});
			return is_array($rates) ? $rates : $defaultRates;
		} catch (\Throwable $e) {
			\Illuminate\Support\Facades\Log::error('Currency API fetch failed, using defaults: ' . $e->getMessage());
			return $defaultRates;
		}
	}

	/**
	 * Convert an amount from one currency to another using dynamic rates.
	 *
	 * @param float|int $amount
	 * @param string $from
	 * @param string $to
	 * @return float
	 */
	public static function convert($amount, string $from, string $to): float
	{
		$from = strtoupper(trim($from));
		$to = strtoupper(trim($to));

		if ($from === $to) {
			return (float) $amount;
		}

		$rates = self::getRates();

		if (!isset($rates[$from]) || !isset($rates[$to])) {
			return (float) $amount;
		}

		// Convert to base USD
		$amountInUSD = (float) $amount / $rates[$from];

		// Convert from USD to target
		return (float) ($amountInUSD * $rates[$to]);
	}
}


