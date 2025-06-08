<?php

namespace App\Events;

use App\Models\IntellijProject;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GiteaRepoForked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public IntellijProject $intellij_project;

    public function __construct(IntellijProject $intellij_project)
    {
        $this->intellij_project = $intellij_project;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('forks.' . $this->intellij_project->id),
        ];
    }
}
