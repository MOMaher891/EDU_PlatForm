<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
	use HasFactory;

	protected $fillable = [
		'block_devtools',
		'block_copy_text',
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

	public static function getCached(): self
	{
		return cache()->remember('app_settings_singleton', 60, function () {
			return static::query()->first() ?? static::create([
				'block_devtools' => false,
				'block_copy_text' => false,
				'platform_name' => 'منصة التعلم الإلكتروني',
				'support_email' => 'support@example.com',
				'support_phone' => '+966 50 123 4567',
				'platform_description' => 'منصة تعليمية متكاملة تقدم دورات تعليمية عالية الجودة',
			]);
		});
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
}


