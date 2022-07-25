<?php

namespace App\Http\Controllers\Videos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Videos\Video;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::where('level_id', auth('videos')->user()->level_id)
        ->orderBy('id', 'DESC')
        ->paginate(18);

        return view('videos.index', compact('videos'));
    }

    public function show(Video $video)
    {
        $suggests_videos = Video::where('level_id', auth('videos')->user()->level_id)
        ->inRandomOrder()
        ->limit(10)
        ->get();

        return view('videos.video', compact('video', 'suggests_videos'));        
    }
}
