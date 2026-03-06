<?php

namespace App\Events;

use App\Models\User;
use App\Models\Lesson;
use Illuminate\Foundation\Events\Dispatchable;

class LessonCompleted
{
    use Dispatchable;

    public User   $user;
    public Lesson $lesson;

    public function __construct(User $user, Lesson $lesson)
    {
        $this->user   = $user;
        $this->lesson = $lesson;
    }
}
