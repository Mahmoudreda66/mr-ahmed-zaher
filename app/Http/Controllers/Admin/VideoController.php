<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Videos\Video;
use App\Models\Admin\Level;
use App\DataTables\VideoDataTable;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function __construct ()
    {
        $this->middleware('permission:videos');
        $this->middleware('permission:add-video')->only('create', 'upload');
        $this->middleware('permission:edit-video')->only('update');
        $this->middleware('permission:delete-video')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(VideoDataTable $table)
    {
        return $table->render('admin.videos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $levels = Level::all();
        
        return view('admin.videos.create', compact('levels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'videoPath' => 'required|string',
            'videoTitle' => 'required|max:191|min:3',
            'videoLevel' => 'required|exists:levels,id',
            'videoThumbanil' => 'required|file'
        ], [
            'videoPath.required' => 'لم يتم رفع الفيديو بشكل صحيح',
            'videoPath.string' => 'لم يتم رفع الفيديو بشكل صحيح',
            'videoTitle.required' => 'قم بكتابة عنوان الفيديو',
            'videoTitle.max' => 'يجب ألا يزيد عنوان الفيديو عن 191 حرف',
            'videoTitle.min' => 'يجب ألا يقل عنوان الفيديو عن 3 أحرف',
            'videoLevel.required' => 'قم بإختيار مرحلة الفيديو',
            'videoLevel.exists' => 'لم يتم العثور على المرحلة',
            'videoThumbanil.required' => 'قم بإختيار الصورة المُصغرة',
            'videoThumbanil.file' => 'يجب أن تكون الصورة المُصغرة على صيغة ملف'
        ]);

        if($request->hasFile('videoThumbanil')){
            // validation
            $videoThumbanil = $request->file('videoThumbanil');
            $allowed_extensions = ['jpeg', 'png', 'jpg'];
            $thumbnailExtension = $videoThumbanil->getClientOriginalExtension();
            $thumbnailName = time() . '_' . $videoThumbanil->getClientOriginalName();
            $errors = [];

            if(!in_array($thumbnailExtension, $allowed_extensions)){
                $errors[] = 'إمتداد الصورة  غير صالح. الإمتدادات المسموح بها هي: ' . implode($allowed_extensions, ', ');
            }

            if(!$videoThumbanil->isValid()){
                $errors[] = 'لم يتم رفع الصورة  بشكل صحيح';
            }

            if(count($errors) > 0){
                return response()->json([
                    'status' => false,
                    'message' => 'validation',
                    'data' => $errors
                ]);
            }else{
                $thumbnailPath = Storage::disk('public')->putFileAs('uploads/videos/thumbnails', $videoThumbanil, $thumbnailName);

                $video = Video::create([
                    'title' => $request->videoTitle,
                    'description' => $request->videoDescription,
                    'level_id' => $request->videoLevel,
                    'video' => $request->videoPath,
                    'thumbnail' => $thumbnailPath
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'uploaded succesfully',
                    'data' => $video
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'validation',
            'data' => ['لم يتم رفع الصورة المصغرة']
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Video $videos_management)
    {
        $video = $videos_management;
        $levels = Level::all();

        return view('admin.videos.show', compact('video', 'levels'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $videos_management)
    {
        $request->validate([
            'videoTitle' => 'required|max:191|min:3',
            'videoLevel' => 'required|exists:levels,id'
        ], [
            'videoTitle.required' => 'قم بكتابة عنوان الفيديو',
            'videoTitle.max' => 'يجب ألا يزيد عنوان الفيديو عن 191 حرف',
            'videoTitle.min' => 'يجب ألا يقل عنوان الفيديو عن 3 أحرف',
            'videoLevel.required' => 'قم بإختيار مرحلة الفيديو',
            'videoLevel.exists' => 'لم يتم العثور على المرحلة',
        ]);

        if($request->hasFile('videoThumbanil')){
            // validation
            $videoThumbanil = $request->file('videoThumbanil');
            $allowed_extensions = ['jpeg', 'png', 'jpg'];
            $thumbnailExtension = $videoThumbanil->getClientOriginalExtension();
            $thumbnailName = time() . '_' . $videoThumbanil->getClientOriginalName();
            $errors = [];

            if(!in_array($thumbnailExtension, $allowed_extensions)){
                $errors[] = 'إمتداد الصورة  غير صالح. الإمتدادات المسموح بها هي: ' . implode($allowed_extensions, ', ');
            }

            if(!$videoThumbanil->isValid()){
                $errors[] = 'لم يتم رفع الصورة  بشكل صحيح';
            }

            if(count($errors) > 0){
                return response()->json([
                    'status' => false,
                    'message' => 'validation',
                    'data' => $errors
                ]);
            }else{

                if(Storage::disk('public')->exists($videos_management->thumbnail)){
                    Storage::disk('public')->delete($videos_management->thumbnail);
                }

                $thumbnailPath = Storage::disk('public')->putFileAs('uploads/videos/thumbnails', $videoThumbanil, $thumbnailName);
            }
        }

        $video = $videos_management->update([
            'title' => $request->videoTitle,
            'description' => $request->videoDescription,
            'level_id' => $request->videoLevel,
            'thumbnail' => isset($thumbnailPath) ? $thumbnailPath : $videos_management->thumbnail
        ]);

        return response()->json([
            'status' => true,
            'message' => 'updated succesfully',
            'data' => $video
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Video $videos_management)
    {
        $video = $videos_management;

        if(Storage::disk('public')->exists($video->video)){
            Storage::disk('public')->delete($video->video);
        }

        if(Storage::disk('public')->exists($video->thumbnail)){
            Storage::disk('public')->delete($video->thumbnail);
        }

        $video->delete();

        return redirect()->to(route('videos-management.index'))->with('success', 'م حذف الفيديو بنجاح');
    }

    public function upload(Request $request)
    {
        if($request->hasFile('video')){
            // validation
            $videoFile = $request->file('video');
            $allowed_extensions = ['mp4', 'ogm', 'wmv', 'mbg', 'webm',
            'ogv', 'mov', 'asx', 'mpeg', 'm4v', 'avi'];
            $videoExtension = $videoFile->getClientOriginalExtension();
            $videoName = time() . '_' . $videoFile->getClientOriginalName();
            $errors = [];

            if(!in_array($videoExtension, $allowed_extensions)){
                $errors[] = 'إمتداد الفيديو غير صالح. الإمتدادات المسموح بها هي: ' . implode($allowed_extensions, ', ');
            }

            if(!$videoFile->isValid()){
                $errors[] = 'لم يتم رفع الفيديو بشكل صحيح';
            }

            if(count($errors) > 0){
                return response()->json([
                    'status' => false,
                    'message' => 'validation',
                    'data' => $errors
                ]);
            }else{
                $path = Storage::disk('public')->putFileAs('uploads/videos', $videoFile, $videoName);

                return response()->json([
                    'status' => true,
                    'message' => 'uploaded succesfully',
                    'data' => $path
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'message' => 'video not uploaded',
            'data' => []
        ]);
    }
}
