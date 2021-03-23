<?php

namespace App\Models;

use App\Models\Experiment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class ExperimentFiles extends Model
{
    use HasFactory;

    // protected $with = ['url'];

    public function experiment()
    {
        return $this->belongsTo(Experiment::class);
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->file_path);
    }
}
