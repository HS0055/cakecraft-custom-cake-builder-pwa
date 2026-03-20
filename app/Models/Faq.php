<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Faq
 *
 * Represents a Frequently Asked Question to be displayed on the storefront.
 * Includes attributes for sorting and toggling visibility.
 *
 * @property int $id
 * @property string $question
 * @property string $answer
 * @property bool $is_active
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Faq extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
