<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $managerRole = Role::create([
            'name' => 'manager',
            'display_name' => 'مدير',
            'description' => 'المسئول عن كافة صلاحيات الموقع'
        ]);

        $teacherRole = Role::create([
            'name' => 'teacher',
            'display_name' => 'معلم',
            'description' => 'المسئول عن إضافة الإختبارات وتصحيحها وعرض الدرجات'
        ]);

        $assistantRole = Role::create([
            'name' => 'assistant',
            'display_name' => 'سكرتارية',
            'description' => 'مسئول عن كل شئ عدا المهام الإدارية'
        ]);

        $permissions = [
            'show-dashboard' => [
                'display_name' => 'عرض لوحة التحكم',
                'description' => 'عرض إحصائيات واللوحة الرئيسية للسنتر'
            ],
            'students' => [
                'display_name' => 'قسم الطلاب',
                'description' => 'قسم الطلاب الجانبي'
            ],
            'add-student' => [
                'display_name' => 'إضافة طالب',
                'description' => 'إضافة بيانات طالب جديد'
            ],
            'show-students' => [
                'display_name' => 'عرض الطلاب',
                'description' => 'عرض قائمة الطلاب بالسنتر'
            ],
            'students-list' => [
                'display_name' => 'كشف أسماء الطلاب',
                'description' => 'طباعة كشف أسماء الطلاب'
            ],
            'students-application-list' => [
                'display_name' => 'قائمة حجوزات الطلاب',
                'description' => 'صفحة تأكيد حجز الطلاب'
            ],
            'students-absence-list' => [
                'display_name' => 'كشف غياب الطلاب',
                'description' => 'طباعة كشف غياب الطلاب'
            ],
            'filled-absence-list' => [
                'display_name' => 'كشف غياب مملوء',
                'description' => 'عرض كشف غياب مملوء بالأيام'
            ],
            'students-expenses-list' => [
                'display_name' => 'كشف مصروفات الطلاب',
                'description' => 'طباعة كشف مصروفات الطلاب'
            ],
            'print-barcodes' => [
                'display_name' => 'طباعة باركود الطلاب',
                'description' => 'طباعة قائمة باركود الطلاب'
            ],
            'delete-student' => [
                'display_name' => 'حذف طالب',
                'description' => 'حذف كافة بيانات طالب'
            ],
            'add-student-expenses' => [
                'display_name' => 'إضافة مصروفات طالب',
                'description' => 'تغيير حالة دفع مصروفات طالب'
            ],
            'edit-student' => [
                'display_name' => 'تعديل طالب',
                'description' => 'تعديل بيانات طالب بالسنتر'
            ],
            'show-student' => [
                'display_name' => 'عرض طالب',
                'description' => 'عرض بيانات طالب معين'
            ],
            'videos' => [
                'display_name' => 'قسم الفيديوهات',
                'description' => 'عرض قسم الفيديوهات'
            ],
            'add-video' => [
                'display_name' => 'إضافة فيديو',
                'description' => 'إضافة فيديو'
            ],
            'edit-video' => [
                'display_name' => 'تعديل فيديو',
                'description' => 'تعديل فيديو'
            ],
            'delete-video' => [
                'display_name' => 'حذف فيديو',
                'description' => 'حذف فيديو'
            ],
            'videos' => [
                'display_name' => '',
                'description' => ''
            ],
            'videos' => [
                'display_name' => '',
                'description' => ''
            ],
            'videos' => [
                'display_name' => '',
                'description' => ''
            ],
            'videos' => [
                'display_name' => '',
                'description' => ''
            ],
            'money' => [
                'display_name' => 'قسم الأموال',
                'description' => 'عرض قسم الأموال'
            ],
            'show-statistics' => [
                'display_name' => 'الإحصائيات',
                'description' => 'عرض قائمة إحصائيات المكان'
            ],
            'show-out-money' => [
                'display_name' => 'الأموال الخارجة',
                'description' => 'قسم الأموال الخارجة'
            ],
            'add-out-money' => [
                'display_name' => 'إضافة أموال خارجة',
                'description' => 'إضافة عنصر مال خارج'
            ],
            'edit-out-money' => [
                'display_name' => 'تعديل المال الخارج',
                'description' => 'تعديل عنصر من عناصر الأموال الخارجة'
            ],
            'delete-out-money' => [
                'display_name' => 'حذف المال الخارج',
                'description' => 'حذف عنصر من عناصر الأموال الخارجة'
            ],
            'teachers' => [
                'display_name' => 'قسم المعلمين',
                'description' => 'قسم المعلمين الجانبي'
            ],
            'add-teacher' => [
                'display_name' => 'إضافة معلم',
                'description' => 'إضافة بيانات معلم للسنتر'
            ],
            'show-teachers' => [
                'display_name' => 'عرض المعلمين',
                'description' => 'عرض قائمة المعلمين بالسنتر'
            ],
            'delete-teacher' => [
                'display_name' => 'حذف معلم',
                'description' => 'حذف جميع بيانات معلم بالسنتر'
            ],
            'edit-teacher' => [
                'display_name' => 'تعديل معلم',
                'description' => 'تعديل بيانات معلم'
            ],
            'lessons' => [
                'display_name' => 'قسم الحصص',
                'description' => 'قسم الحصص الجانبي'
            ],
            'add-lesson' => [
                'display_name' => 'إضافة حصة',
                'description' => 'إضافة حصة جديدة بالسنتر'
            ],
            'edit-lesson' => [
                'display_name' => 'تعديل حصة',
                'description' => 'تعديل بيانات حصة'
            ],
            'show-lessons' => [
                'display_name' => 'عرض حصة',
                'description' => 'عرض  بيانات حصة'
            ],
            'delete-lesson' => [
                'display_name' => 'حذف حصة',
                'description' => 'حذف بيانات حصة'
            ],
            'exams' => [
                'display_name' => 'قسم الإختبارات',
                'description' => 'قسم الإختبارات الجانبي'
            ],
            'add-exam' => [
                'display_name' => 'إضافة إختبار',
                'description' => 'إضافة إختبار جديد'
            ],
            'print-exam-cards' => [
                'display_name' => 'طباعة كروت الدرجات',
                'description' => 'طباعة كروت درجات الإختبارات'
            ],
            'show-exams' => [
                'display_name' => 'عرض الإختبارات',
                'description' => 'عرض قائمة الإختبارات'
            ],
            'delete-exam' => [
                'display_name' => 'حذف إختبار',
                'description' => 'حذف بيانات إختبار'
            ],
            'toggle-exam' => [
                'display_name' => 'تغيير حالة إختبار',
                'description' => 'تغيير حالة إختبار بين مفعل وغير مفعل'
            ],
            'view-exam' => [
                'display_name' => 'عرض إختبار',
                'description' => 'عرض أسئلة وبيانات إختبار'
            ],
            'edit-exam' => [
                'display_name' => 'تعديل إختبار',
                'description' => 'تعديل بيانات الإختبار العامة'
            ],
            'exams-attemps' => [
                'display_name' => 'تفاصيل الدخول للإختبارات',
                'description' => 'عرض تفاصيل دخول الطلاب للإختبارات'
            ],
            'delete-exam-attemp' => [
                'display_name' => 'حذف محاولة دخول',
                'description' => 'حذف محاولة دخول طالب للإختبار'
            ],
            'exams-marks' => [
                'display_name' => 'درجات الإختبارات',
                'description' => 'عرض درجات الإختبار'
            ],
            'delete-exam-mark' => [
                'display_name' => 'حذف درجات إختبار',
                'description' => 'حذف درجات إختبارات الطلاب'
            ],
            'edit-exam-mark' => [
                'display_name' => 'تعديل درجات الإختبارات',
                'description' => 'تعديل درجات الطلاب الخاصة بالإختبارات'
            ],
            'exams-manual-marks' => [
                'display_name' => 'إضافة الدرجات يدوياً',
                'description' => 'إضافة درجات الإختبار يديواً'
            ],
            'exams-correcting' => [
                'display_name' => 'تصحيح الإختبارات الإلكترونية',
                'description' => 'تصحيح الإختبارات الإلكترونية'
            ],
            'certificates' => [
                'display_name' => 'الشهادات',
                'description' => 'طباعة شهادات الطلاب'
            ],
            'empty-marks-certificate' => [
                'display_name' => 'شهادات الدرجات الفارغة',
                'description' => 'طباعة شهادات الدرجات الفارغة'
            ],
            'filled-marks-certificate' => [
                'display_name' => 'شهادة درجات مملوئة',
                'description' => 'طباعة شهادة درجات مملوئة'
            ],
            'absences' => [
                'display_name' => 'قسم الغياب',
                'description' => 'قسم الغياب الجانبي'
            ],
            'last-students-absences-records' => [
                'display_name' => 'آخر تسجيلات غياب الطلاب',
                'description' => 'آخر التسجيلات التي تم تسجيلها بغياب الطلاب'
            ],
            'teachers-absences' => [
                'display_name' => 'غياب المعلمين',
                'description' => 'أخذ غياب المعلمين'
            ],
            'lessons-absence-mode' => [
                'display_name' => 'وضع غياب الحصص',
                'description' => 'وضع غياب بالحصص'
            ],
            'day-absence-mode' => [
                'display_name' => 'وضع غياب اليوم',
                'description' => 'وضع الغياب باليوم'
            ],
            'absence-report' => [
                'display_name' => 'تقارير الغياب',
                'description' => 'عرض تقارير الغياب'
            ],
            'users-permissions' => [
                'display_name' => 'إدارة المستخدمين والصلاحيات',
                'description' => 'إدارة المستخدمين والصلاحيات والمهام الخاصة بهم'
            ],
            'system-settings' => [
                'display_name' => 'الإعدادات',
                'description' => 'التعديل على إعدادات النظام'
            ],
            'database-management' => [
                'display_name' => 'قواعد البيانات',
                'description' => 'إدارة وتهيئة قواعد البيانات'
            ]
        ];

        foreach ($permissions as $key => $value) {
            $permission = Permission::create([
                'name' => $key,
                'display_name' => $value['display_name'],
                'description' => $value['description']
            ]);

            $managerRole->attachPermission($permission);
        }

        $managerUser1 = User::create([
            'name' => 'محمود رضا',
            'phone' => '01093668025',
            'password' => Hash::make('01093668025'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $managerUser1->attachRole('manager');
    }
}
