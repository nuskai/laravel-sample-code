<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Product extends Model
{

    public $table = 'Products';



    public $fillable = [
        'productName',
        'stripeProductId',
        'summary',
        'productGroupID',
        'detail',
        'availableToPurchase'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'productName' => 'string',
        'summary' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'productName' => 'required|unique:Products,productName',
        'summary' => 'required',
        'productGroupID' => 'required'
    ];

    public function prices(){
        return $this->hasMany(ProductPrice::class,'productId', 'id');
    }

    public function priceByNumUsers($numUsers)
    {
        return $this->prices()->where(["numberOfUsers" => $numUser])->first();
    }
}
