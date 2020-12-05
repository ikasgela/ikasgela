<?php

namespace App\Observers;

use App\Curso;

class CursoObserver
{
    public function saved(Curso $curso)
    {
        Curso::flushCache();
    }

    public function deleted(Curso $curso)
    {
        Curso::flushCache();
    }
}
