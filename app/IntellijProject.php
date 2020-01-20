<?php

namespace App;

use GitLab;
use Illuminate\Database\Eloquent\Model;
use Log;

class IntellijProject extends Model
{
    protected $fillable = [
        'repositorio', 'titulo', 'descripcion'
    ];

    public function actividades()
    {
        return $this
            ->belongsToMany(Actividad::class)
            ->withTimestamps()
            ->withPivot([
                'fork',
                'archivado',
                'is_forking'
            ]);
    }

    public function gitlab()
    {
        try {
            if (!$this->isForked())
                return GitLab::projects()->show($this->repositorio);
            else
                return GitLab::projects()->show($this->pivot->fork);
        } catch (\Exception $e) {
            Log::critical($e);
            $fake = [
                'id' => '?',
                'name' => '?',
                'description' => '?',
                'http_url_to_repo' => '',
                'path_with_namespace' => $this->repositorio
            ];
            return $fake;
        }
    }

    public function isForked()
    {
        return isset($this->pivot->fork) && strlen($this->pivot->fork) > 2;
    }

    public function isArchivado()
    {
        return $this->pivot->archivado;
    }

    public function isForking()
    {
        return $this->pivot->is_forking;
    }
}
