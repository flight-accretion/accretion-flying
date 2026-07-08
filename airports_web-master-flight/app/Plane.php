<?php

namespace FlyingCalculation;

use Illuminate\Database\Eloquent\Model;

class Plane extends Model
{
  protected $table = 'plane';
  protected $fillable = ['name', 'subheading'];
}
