<?php

namespace RatebSa\Structure\Repositories;




use RatebSa\Structure\Traits\RepoTrait;

class BaseRepo
{
use RepoTrait;


    // Array with filterable fields
    protected $filtersKeys = [];

    // Array containing fields that can be searched within
    protected $searchFileds = [];

    // Array containing relations
    protected $relations = [];

    // An Array containing fields that exist within searchable relationships
    protected $realationFileds = [];




}
