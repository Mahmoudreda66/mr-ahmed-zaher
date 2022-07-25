<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Settings;
use App\Models\Admin\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:system-settings');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.settings.index');        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $workedLevels = Level::where('works', 1)->select('name_en')->get();
        $expenses_values = [];

        for($i = 0; $i < count($workedLevels); $i++){
            $expenses_values[$workedLevels[$i]['name_en']] = $request->toArray()[$workedLevels[$i]['name_en']];
        }

        $items = [
            'expenses',
            'students_must_choose_teachers',
            'enable_students_online_application',
            'must_confirm_students_application',
            'print_after_add_student',
            'center_phone1',
            'show_answers_after_exam_ends',
            'place_name',
            'always_print_invoice_billing',
            'center_logo',
            'student_paper_text',
        ];

        for($i = 0; $i < count($items); $i++){
            if($items[$i] === 'expenses'){
                Settings::where('name', 'expenses')->update([
                    'value' => json_encode($expenses_values)
                ]);
            }else if($items[$i] === 'place_name'){
                if($items[$i] === 'place_name'){
                    cache()->put('app_name', $request->toArray()[$items[$i]] ?? '');
                }

                Settings::where('name', $items[$i])->update([
                    'value' => $request->toArray()[$items[$i]] ?? ''
                ]);
            }else if($items[$i] === 'center_logo') {
                if($request->hasFile('center_logo')){
                    $image = $request->file('center_logo');
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
                    $extension = $image->getClientOriginalExtension();
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imageSize = $image->getSize() / 1048576;

                    if (!$image->isValid()) {
                        return redirect()->back()->with(['image_error' => 'لم يتم تحميل الصورة بشكل صحيح'])->withInput();
                    }

                    if (!in_array($extension, $allowed_extensions)) {
                        return redirect()->back()->with(['image_error' => 'الإمتدادات المسموح بها فقط هي' . implode(', ', $allowed_extensions)])->withInput();
                    }

                    if ($imageSize > 5) {
                        return redirect()->back()->with(['image_error' => 'أقصى حجم للصورة هو ' .  5 . 'ميجا'])->withInput();
                    }

                    $old_logo = Settings::where('name', $items[$i])->first()['value'];

                    
                    if(Storage::disk('public')->exists($old_logo)){
                        Storage::disk('public')->delete($old_logo);
                    }

                    $new_path = Storage::disk('public')->putFileAs('uploads/images', $image, $imageName);
                    
                    Settings::where('name', $items[$i])->update([
                        'value' => $new_path
                    ]);

                    cache()->add('app_logo', $new_path, 68400);
                }

            }else if($items[$i] === 'center_phone1' || $items[$i] === 'student_paper_text'){
                Settings::where('name', $items[$i])->update([
                    'value' => $request->toArray()[$items[$i]] ?? ''
                ]);
            }else{
                Settings::where('name', $items[$i])->update([
                    'value' => $request->toArray()[$items[$i]] ?? 0
                ]);
            }
        }

        return redirect()->back()->with(['success' => 'تم حفظ التغييرات بنجاح']);
    }

    public function expenses($level)
    {
        $expenses = Settings::where('name', 'expenses')->select('value')->first();
        if ($expenses) {
            return json_decode(json_decode($expenses, true)['value'], true)[$level];
        }else{
            abort(404);
        }
    }
}
