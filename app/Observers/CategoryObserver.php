<?php

namespace App\Observers;

use App\Models\Category;

class CategoryObserver
{
    public function saved(Category $category)
    {
        Category::flushCache();
    }

    public function deleted(Category $category)
    {
        Category::flushCache();
    }
}
