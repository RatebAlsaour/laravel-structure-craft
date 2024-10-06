<?php

namespace RatebSa\Structure\Filters;


use RatebSa\Structure\Filters\Filter as  FiltersFilter;

class FilterFactory
{
    /**
     *  Create method for filter facotry
     * @param string $filterKey
     * @param array $fitlerData
     * @param \App\Models\Model|\App\Models\AuthModel $model
     * @return Filter
     * @throws \App\Exceptions\ErrorMsgException
     */
    public static function create($filterKey, $filterData, $model): FiltersFilter
    {

        $paths = $model->filtersKeys;


        $filterClassPath = $paths[$filterKey];

        if (class_exists($filterClassPath)) {

            return new $filterClassPath($filterData);
        }

        //  throwError('Trying to declare invalid filter class');


        // throwError(get_class($model) . ' Model doesnt have filtersKeys method');
    }
}
