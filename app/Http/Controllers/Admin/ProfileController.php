<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('admin.profile.edit');
    }

    /**
     * Update the profile
     *
     * @param  \App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $extension = $image->getClientOriginalExtension();
            $imageName = time() . '_' . $image->getClientOriginalName();
            define('IMAGE_MAX_SIZE', 5);
            $imageSize = $image->getSize() / 1048576;

            if (!$image->isValid()) {
                return redirect()->back()->with(['image_error' => 'لم يتم تحميل الصورة بشكل صحيح'])->withInput();
            }

            if (!in_array($extension, $allowed_extensions)) {
                return redirect()->back()->with(['image_error' => 'الإمتدادات المسموح بها فقط هي' . implode(', ', $allowed_extensions)])->withInput();
            }

            if ($imageSize > IMAGE_MAX_SIZE) {
                return redirect()->back()->with(['image_error' => 'أقصى حجم للصورة هو ' .  IMAGE_MAX_SIZE . 'ميجا'])->withInput();
            }

            $imagePath = 'images/users/';
            if(auth()->user()->hasRole('teacher')){
                $imagePath = 'images/teachers/';
            }

            if (auth()->user()->image) {
                if (Storage::exists($imagePath . auth()->user()->image)) {
                    Storage::delete($imagePath . auth()->user()->image);
                }
            }

            Storage::putFileAs($imagePath, $image, $imageName);

            auth()->user()->update(['image' => $imageName]);
        }

        auth()->user()->update($request->only('name', 'phone'));

        if($request->has('remove_image')){
            $imagePath = 'images/users/';
            if(auth()->user()->hasRole('teacher')){
                $imagePath = 'images/teachers/';
            }

            if(Storage::exists($imagePath . auth()->user()->image)){
                Storage::delete($imagePath . auth()->user()->image);
            }
            
            auth()->user()->update(['image' => null]);
        }

        return redirect()->back()->with(['success' => 'تم تحديث البيانات بنجاح']);
    }

    /**
     * Change the password
     *
     * @param  \App\Http\Requests\PasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function password(PasswordRequest $request)
    {
        auth()->user()->update(['password' => bcrypt($request->new_password)]);

        return redirect()->back()->with(['success' => 'تم تغيير كلمة السر بنجاح']);
    }
}
