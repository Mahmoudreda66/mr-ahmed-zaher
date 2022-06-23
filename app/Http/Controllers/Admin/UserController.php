<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:users-permissions');
    }

    /**
     * Display a listing of the users
     *
     * @param  \App\Models\User  $model
     * @return \Illuminate\View\View
     */
    public function index(User $model)
    {
        $users = User::orderBy('id', 'DESC')->paginate(30);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request){
        $validation = Validator::make($request->all(), [
            'user_name' => 'required|max:191|min:2',
            'phone' => 'required|numeric|min:1000000|max:9999999999',
            'password' => 'required|min:8|max:35',
            'password_confirmation' => 'required|same:password',
            'roles' => 'required|array',
        ], [
            'user_name.required' => 'إسم المستخدم مطلوب',
            'user_name.max' => 'أقصى عدد أحرف مسموح به هو 191 حرف',
            'user_name.min' => 'أقل عدد أحرف مسموح به هو 2 حرف',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.numeric' => 'يجب أن يتكون رقم الهاتف من أرقام فقط',
            'phone.max' => 'قم بكتابة رقم هاتف صالح',
            'phone.min' => 'قم بكتابة رقم هاتف صالح',
            'password.required' => 'كلمة السر مطلوبة',
            'password.max' => 'كلمة السر يجب ألا تزيد عن 35 حرف',
            'password.min' => 'كلمة السر يجب ألا تقل عن 8 حرف',
            'password_confirmation.required' => 'قم بإعادة كتابة كلمة السر',
            'password_confirmation.same' => 'قم بإعادة كتابة كلمة السر بشكل صحيح',
            'roles.required' => 'قم بإختيار المهام',
            'roles.array' => 'يجب أن تتكون المهام من مصفوفة',
        ]);
        
        if($validation->fails()){
            return redirect()->back()->withErrors($validation)->withInput()->with(['open_add_modal' => true]);
        }

        if($request->hasFile('image')){
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

            Storage::putFileAs('images/users', $image, $imageName);
        }

        $user = User::create([
            'name' => $request->user_name,
            'phone' => $request->phone,
            'image' => $imageName ?? null,
            'password' => bcrypt($request->password),
        ]);

        $user->attachRoles($request->roles);

        return redirect()->back()->with(['success' => 'تم حفظ المستخدم بنجاح']);
    }
    
    public function update($id, Request $request)
    {
        $user = User::where('id', $id)->with('roles')->first();

        $validation = Validator::make($request->all(), [
            'user_name' => 'required|max:191|min:2',
            'phone' => 'required|numeric|min:1000000|max:9999999999|unique:users,phone,' . $user->id,
            'roles' => 'required|array'
        ], [
            'user_name.required' => 'إسم المستخدم مطلوب',
            'user_name.max' => 'أقصى عدد أحرف مسموح به هو 191 حرف',
            'user_name.min' => 'أقل عدد أحرف مسموح به هو 2 حرف',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.numeric' => 'يجب أن يتكون رقم الهاتف من أرقام فقط',
            'phone.max' => 'قم بكتابة رقم هاتف صالح',
            'phone.min' => 'قم بكتابة رقم هاتف صالح',
            'phone.unique' => 'رقم الهاتف موجود بالفعل',
            'roles.required' => 'قم بإختيار المهام',
            'roles.array' => 'يجب أن تتكون المهام من مصفوفة',
        ]);
        
        if($validation->fails()){
            return redirect()->back()->withErrors($validation)->with(['open_edit_modal' => true]);
        }

        if($request->hasFile('image')){
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

            if($user->image && Storage::exists('images/teachers/' . $user->image)){
                Storage::delete('images/teachers/' . $user->image);
            }
            
            Storage::putFileAs('images/users', $image, $imageName);
        }

        $user->update([
            'name' => $request->user_name,
            'phone' => $request->phone,
            'image' => $imageName ?? $user->image
        ]);

        $user->syncRoles($request->roles);
        
        if(!empty($request->old_password)){
            $validation = Validator::make($request->all(), [
                'old_password' => 'required|min:8|max:35',
                'new_password' => 'required|min:8|max:35',
                'password_confirmation' => 'required|same:new_password',
            ], [
                'old_password.required' => 'كلمة السر القديمة مطلوبة',
                'old_password.max' => 'كلمة السر القديمة يجب ألا تزيد عن 35 حرف',
                'old_password.min' => 'كلمة السر القديمة يجب ألا تقل عن 8 حرف',
                'new_password.required' => 'كلمة السر الجديدة مطلوبة',
                'new_password.max' => 'كلمة السر الجديدة يجب ألا تزيد عن 35 حرف',
                'new_password.min' => 'كلمة السر الجديدة يجب ألا تقل عن 8 حرف',
                'password_confirmation.required' => 'قم بإعادة كتابة كلمة السر',
                'password_confirmation.same' => 'قم بإعادة كتابة كلمة السر بشكل صحيح',
            ]);
            
            if($validation->fails()){
                return redirect()->back()->withErrors($validation)->with(['open_edit_modal' => true]);
            }
            
            if(Hash::check($request->old_password, $user->password)){
                $user->update(['password' => bcrypt($request->new_password)]);
            }else{
                return redirect()->back()->with(['open_edit_modal' => true, 'password_check' => 'كلمة السر القديمة غير صحيحة']);
            }
        }

        return redirect()->back()->with(['success' => 'تم تحديث المستخدم بنجاح']); 
    }
    
    public function toggle_activity($id)
    {
        $user = User::findOrFail($id);
        
        $user->update([
            'active' => $user->active == 1 ? 0 : 1
        ]);
        
        return redirect()->back()->with(['success' => 'تم تغيير حالة المستخدم بنجاح']);
    }
    
    public function destroy($id){
        $user = User::findOrFail($id);
        
        if ($user->image && $user->hasRole(['assistant', 'manager']) && Storage::exists('images/users/' . $user->image)) {
            Storage::delete('images/users/' . $user->image);
        }else if($user->image && $user->hasRole('teacher') && Storage::exists('images/teachers/' . $user->image)){
            Storage::delete('images/teachers/' . $user->image);
        }
        
        $user->delete();
        
        return redirect()->back()->with(['success' => 'تم حذف المستخدم بنجاح']);
    }
}
