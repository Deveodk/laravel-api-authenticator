<?php

namespace DeveoDK\LaravelApiAuthenticator\Services;

use Illuminate\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\TransformerAbstract;

abstract class BaseService
{
    /** @var DatabaseManager */
    protected $database;

    /** @var Dispatcher */
    protected $dispatcher;

    /** @var TransformerAbstract */
    protected $transformer;

    /** @var array */
    protected $models;

    /** @var OptionService */
    protected $optionService;

    /** @var string */
    protected $defaultModel;

    /**
     * BaseService constructor.
     * @param Dispatcher $dispatcher
     * @param DatabaseManager $database
     * @param OptionService $optionService
     */
    public function __construct(Dispatcher $dispatcher, DatabaseManager $database, OptionService $optionService)
    {
        $this->optionService = $optionService;
        $this->database = $database;
        $this->defaultModel = $this->optionService->get('defaultAuthenticationModel');
        $this->dispatcher = $dispatcher;
    }

    public function transformItem($data)
    {
        $include =  isset($_GET['include']) ? $_GET['include'] : '';

        $resource = fractal()->item($data)
            ->parseIncludes($include)
            ->transformWith($this->transformer)
            ->toArray();

        return $resource;
    }

    public function transformCollection($data)
    {
        $include =  isset($_GET['include']) ? $_GET['include'] : '';

        $resource = fractal()->collection($data)
            ->parseIncludes($include)
            ->transformWith($this->transformer)
            ->toArray();

        return $resource;
    }

    public function transformCollectionPaginate($data)
    {
        $include =  isset($_GET['include']) ? $_GET['include'] : '';

        $Collection = $data->getCollection();

        $resource = fractal()->collection($Collection)
            ->paginateWith(new IlluminatePaginatorAdapter($data))
            ->parseIncludes($include)
            ->transformWith($this->transformer)
            ->toArray();

        return $resource;
    }

    /**
     * @param array
     * @return $this
     */
    public function setModels(array $model)
    {
        $this->models = $model;
        return $this;
    }

    /**
     * @return array
     */
    public function getModels()
    {
        return $this->models;
    }

    /**
     * @param TransformerAbstract $transformer
     * @return $this
     */
    public function setTransformer(TransformerAbstract $transformer)
    {
        $this->transformer = $transformer;
        return $this;
    }

    /**
     * @return TransformerAbstract
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * @return string
     */
    public function getDefaultModel(): string
    {
        return $this->defaultModel;
    }

    /**
     * @param string $defaultModel
     */
    public function setDefaultModel(string $defaultModel)
    {
        $this->defaultModel = $defaultModel;
    }
}
