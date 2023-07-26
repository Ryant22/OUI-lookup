<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oui extends Model
{
    use HasFactory;

    protected $table = 'ouis';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = ['mac_prefix', 'vendor', 'address'];

}
