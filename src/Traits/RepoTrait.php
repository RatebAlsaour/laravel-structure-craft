<?php

namespace rateb\structure\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait RepoTrait
{
    /**
     * Constructor to initialize the model and optionally the object data class.
     *
     * @param Model $model The model instance for the repository.
     * @param string|null $objectDataClass Optional data class for data transformation.
     */
    public function __construct(protected Model $model, protected ?string $objectDataClass = null)
    {
    }

    /**
     * Magic method to handle calls to undefined methods.
     *
     * This allows for method delegation to the underlying model instance.
     *
     * @param string $method The name of the method being called.
     * @param array $args The arguments passed to the method.
     * @return mixed The result of the called method on the model.
     */
    public function __call($method, $args)
    {
        return $this->model->$method(...$args);
    }

    /**
     * Updates a model instance with the given array of attributes.
     *
     * @param Model $model The model instance to be updated.
     * @param array $arr The array of attributes to update.
     * @return bool Indicates whether the update was successful.
     */
    public function updateModel(Model $model, $arr)
    {
        return $model->update($arr);
    }

    //-------------------------//

    /**
     * Searches for records in the model based on the provided parameters.
     *
     * @param array $parameters The search criteria as an associative array.
     * @return Collection The collection of matching model instances.
     */
    public function search(array $parameters)
    {
        return $this->model->where($parameters)->get();
    }

    /**
     * Searches for records that have a relation matching the specified category ID.
     *
     * @param int $categoryId The ID of the category to search for.
     * @return Collection The collection of matching model instances.
     */
    public function searchRealation($categoryId)
    {
        return $this->model->whereHas('realation', function ($query) use ($categoryId) {
            $query->where('id', $categoryId);
        })->get();
    }

    /**
     * Creates an instance of a filter class based on the provided filter key.
     *
     * @param string $filterKey The key to identify the filter.
     * @param mixed $filterData The data needed for the filter.
     * @param Model $model The model instance to apply the filter to.
     * @return mixed|null An instance of the filter class or null if not found.
     */
    public function createFilterFactory($filterKey, $filterData, $model)
    {
        $paths = $this->filtersKeys; // Assumes filtersKeys is defined elsewhere in the trait.
        $filterClassPath = $paths[$filterKey];

        if (class_exists($filterClassPath)) {
            return new $filterClassPath($filterData); // Create and return the filter instance.
        }
    }

    /**
     * Applies filters to a query based on the provided filters.
     *
     * @param array $filters The filters to apply to the query.
     * @return mixed The modified query instance with filters applied.
     */
    public function filter($filters)
    {
        // Create a query instance
        $query = $this->model->query();

        // Apply filters
        foreach ($filters as $key => $value) {
            $filterClass = $this->createFilterFactory($key, $value, $this->model);
            $filterClass->apply($query); // Apply filter to the query
        }

        // Return the filtered query result
        return $query;
    }

    /**
     * Searches for related fields based on the input provided.
     *
     * @param mixed $input The search key from the request.
     * @param mixed $query The query builder instance.
     * @return mixed The modified query instance.
     */
    public function searchRelation($input, $query)
    {
        $input = request()->input('search-key');

        // Iterate over relation fields to apply search
        foreach ($this->realationFileds as $key => $values) {
            $query->orwhereHas($key, function ($subQuery) use ($values, $input) {
                $subQuery->where(function ($subSubQuery) use ($values, $input) {
                    foreach ($values as $value) {
                        $subSubQuery->orWhere($value, 'LIKE', '%' . $input . '%'); // Apply LIKE filter
                    }
                });
            });
        }

        return $query;
    }

    /**
     * Searches specific fields based on the input provided.
     *
     * @param mixed $input The search key from the request.
     * @param mixed $query The query builder instance.
     * @return mixed The modified query instance.
     */
    public function searchField($input, $query)
    {
        foreach ($this->searchFileds as $values) {
            $query->orWhere(function ($subSubQuery) use ($values, $input) {
                $subSubQuery->orWhere($values, 'LIKE', '%' . $input . '%'); // Apply LIKE filter
            });
        }

        return $query;
    }

    /**
     * Combines searchField and searchRelation for a comprehensive search.
     *
     * @param mixed $query The query builder instance.
     * @return mixed The modified query instance.
     */
    public function searchs($query)
    {
        $input = request()->input('search-key');

        // Create a nested query to handle both field and relation searches
        $query->where(function ($subQuery) use ($input) {
            $subQuery->where(function ($subSubQuery) use ($input) {
                $this->searchField($input, $subSubQuery); // Search specific fields
            })->orWhere(function ($subSubQuery) use ($input) {
                $this->searchRelation($input, $subSubQuery); // Search relations
            });
        });

        return $query;
    }

    /**
     * Calls the filter method if filters are present in the request.
     *
     * @param mixed $query The query builder instance.
     * @return mixed The modified query instance with filters applied.
     */
    public function callFilter($query)
    {
        if (request()->has('filters')) {
            return $query = $this->filter(request()->input('filters'), $this->model);
        }
        return $query; // Return original query if no filters are present
    }

    /**
     * Retrieves all records, applying filters and search as needed.
     *
     * @return mixed The ordered collection of model instances.
     */
    public function index()
    {
        $query = $this->model->query(); // Create a new query instance
        if (request()->has('filters')) {
            $query = $this->callFilter($query); // Apply filters if present
        }
        if (request()->input('search-key')) {
            $query = $this->searchs($query); // Apply search if present
        }

        $allData = $query; // Store the modified query
        return $allData->orderBy('created_at', 'asc'); // Return ordered results
    }

    /**
     * Applies a global scope to the model's query.
     *
     * @param string $scope The name of the global scope to apply.
     */
    public function applyScope(string $scope)
    {
        $this->model->addGlobalScope(new $scope());
    }

    /**
     * Ignores a specified global scope for the model's query.
     *
     * @param string $scope The name of the global scope to ignore.
     */
    public function igonreScope(string $scope)
    {
        $this->model->query()->withoutGlobalScope($scope);
    }

    /**
     * Retrieves and transforms the request data into an object.
     *
     * @param Request|array $data The request instance or data array.
     * @return mixed The transformed data object.
     */
    public function getData(Request|array $data)
    {
        return match(true) {
            $data instanceof Request => $this->objectDataClass::fromRequest($data), // Transform from Request
            is_array($data)          => $this->objectDataClass::fromRequest($data), // Transform from array
            default                  => throw new ErrorMsgException('Invalid data type') // Handle invalid data type
        };
    }

    /*********************************************************************************** */

    /**
     * Stores a new model instance using the provided data.
     *
     * @param Request|array $data The data for the new model instance.
     * @return Model The newly created model instance.
     */
    public function store(Request|array $data): Model
    {
        return $this->create($this->getData($data)->toArray()); // Create model with transformed data
    }

    /**
     * Updates an existing model instance with the provided data.
     *
     * @param Request|array $data The data for updating the model.
     * @param Model $model The model instance to update.
     * @return Model The updated model instance.
     */
    public function update(Request|array $data, Model $model): Model
    {
        $model->update($this->getData($data)->toArray()); // Update model with transformed data
        return $model; // Return the updated model
    }

    /**
     * Deletes media files associated with the given collection of models.
     *
     * @param Collection $arries The collection of models whose media will be deleted.
     */
    public function deleteMedia(Collection $arries)
    {
        foreach ($arries as $rr) {
            File::delete(public_path($rr->media)); // Delete the media file from storage
            $rr->delete(); // Delete the model instance
        }
    }

    /**
     * Retrieves the class path of the model.
     *
     * @return string The class name of the model.
     */
    public function getModelPath()
    {
        return get_class($this->model); // Return the model's fully qualified class name
    }
}
