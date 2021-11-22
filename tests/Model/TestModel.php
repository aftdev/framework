<?php

namespace AftDev\Test\Model;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    public $timestamps = false;

    protected $table = 'test';
}
