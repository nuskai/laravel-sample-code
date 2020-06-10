<?php

namespace App\Repositories;

use App\Models\Product;
use InfyOm\Generator\Common\BaseRepository;

class ProductRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
      'productName', 'summary'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Product::class;
    }

    public function getProducts($limit = null, $request)
    {
        $page = $request->start/$request->length+1;
        $this->applyCriteria();
        $this->applyScope();
        $limit = is_null($limit) ? config('repository.pagination.limit', 15) : $limit;
        $results = $this->model->paginate($limit, ['*'], 'page', $page);
        $results->appends(app('request')->query());
        $this->resetModel();
        return $this->parserResult($results);
    }
}
