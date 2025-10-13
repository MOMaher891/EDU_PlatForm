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


