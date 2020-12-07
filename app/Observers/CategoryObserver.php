<?php

namespace App\Observers;

use App\Category;

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
