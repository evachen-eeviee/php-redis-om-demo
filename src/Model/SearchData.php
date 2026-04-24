<?php

// src/Model/SearchData.php

namespace App\Model;

use App\Entity\Category;
use App\Entity\User;

class SearchData
{
    public ?string $title = null;
    public ?User $author = null;
    public ?Category $category = null;
    public bool $unavailable = false;
    public ?float $priceMin = null;
    public ?float $priceMax = null;
    public ?BookEnum $enum = null;
}
