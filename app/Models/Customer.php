<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'cedula', 'phone', 'email', 'address', 'birthdate'];

    protected $casts = ['birthdate' => 'date'];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function layaways(): HasMany
    {
        return $this->hasMany(Layaway::class);
    }
}
