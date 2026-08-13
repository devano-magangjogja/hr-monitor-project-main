<?php

namespace App\Repositories;

use App\Models\TaskAssignment;

class TaskAssignmentRepository
{
    public function __construct(protected TaskAssignment $model) {}

    // Query & manipulasi data task assignment ke database
}