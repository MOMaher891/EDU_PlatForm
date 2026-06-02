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
	];

	public static function getCached(): self
	{
		return cache()->remember('app_settings_singleton', 60, function () {
			return static::query()->first() ?? static::create([
				'block_devtools' => false,
				'block_copy_text' => false,
			]);
		});
	}
}


