<?php 

namespace tweeterapp\model;

class Dislike extends \Illuminate\Database\Eloquent\Model {

    protected $table    = 'dislike';
    protected $primaryKey       = 'id';
    public $timestamps  = false;
    
}