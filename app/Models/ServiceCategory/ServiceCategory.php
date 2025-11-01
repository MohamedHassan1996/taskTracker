<?php

namespace App\Models\ServiceCategory;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceCategory extends Model
{
    use HasFactory, CreatedUpdatedBy, SoftDeletes;
    protected $fillable = [
        'name',
        'description',
        'start_at',
        'end_at',
    ];
}
