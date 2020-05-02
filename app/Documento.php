<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as MongoModel;

class Documento extends MongoModel
{
    protected $connection = 'mongodb';

//    protected $fillable = [
//        'title', 'description'
//    ];

    protected $guarded = ['_id'];
}
