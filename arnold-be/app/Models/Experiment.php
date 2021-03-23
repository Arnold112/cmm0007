<?php

namespace App\Models;

use App\Models\User;
use App\Models\ExperimentFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Experiment extends Model
{
    use HasFactory;

    protected $with = ['student', 'files', 'eaos'];

    public function eaos()
    {
        return $this->belongsToMany(User::class, 'experiment_user', 'experiment_id', 'user_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function files()
    {
        return $this->hasMany(ExperimentFiles::class);
    }
}
