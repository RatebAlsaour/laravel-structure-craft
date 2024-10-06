<?php

namespace RatebSa\Structure\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepository extends Command
{
    protected $signature = 'make:repo {name} {model}';
    protected $description = 'Create a new repository class';

    public function handle()
    {

    }
}
