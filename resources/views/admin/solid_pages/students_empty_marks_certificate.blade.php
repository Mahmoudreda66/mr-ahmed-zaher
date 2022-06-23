<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة شهادة درجات فارغة</title>
    <link rel="stylesheet" href="{{ asset('material/css/fontawesome.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;700;900&display=swap" rel="stylesheet">
    <link href="{{ asset('/material/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body {
            direction: rtl;
            font-family: cairo, 'sans-serif';
            color: #161616;
        }

        h1 {
            font-size: 31px;
            font-weight: 700;
        }

        .logo {
            width: 100%;
        }

        .logo-container {
            width: 100%;
            min-height: 230px;
            max-height: 230px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        @php
        $logo = App\Models\Admin\Settings::where('name', 'center_logo')->first()['value'];
        $phone = App\Models\Admin\Settings::where('name', 'center_phone1')->first()['value'];
        $image = empty($logo) ? 'smart center logo.png' : $logo;
        @endphp
        @forelse($students as $student)
        <h1 class="text-center">شهادة درجات الإختبارات الورقية</h1>
        <div class="row">
            <div class="col-3 mb-3">
                <div class="logo-container mt-2">
                    <img src="/{{ $image }}" alt="Center Logo" class="logo">
                </div>
            </div>
            <div class="col-9 position-relative" style="top: 40px;">
                <table class="table-striped table table-bordered">
                    <tr>
                        <td>الإسم</td>
                        <td>{{ $student->name }}</td>
                    </tr>
                    <tr>
                        <td>المرحلة</td>
                        <td>{{ $student->level->name_ar }}</td>
                    </tr>
                    <tr>
                        <td>التاريخ</td>
                        <td>{{ date('Y-m-d') }}</td>
                    </tr>
                </table>
                <p class="text-secondary">
                    يرجى إمضاء هذه الشهادة بواسطة ولي الأمر وإحضارها للسنتر مرة أخرى.
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table text-center table-bordered">
                    <tr>
                        <td>المادة <br> / الدرجة</td>
                        <td>اللغة
                            <br> العربية
                        </td>
                        <td>اللغة
                            <br> الإنجليزية
                        </td>
                        <td>الرياضيات</td>
                        <td>الكيمياء</td>
                        <td>الفيزياء</td>
                        <td>الأحياء</td>
                        <td>الجغرافيا</td>
                        <td>التاريخ</td>
                        <td>الفلسفة</td>
                        <td>اللغة
                            <br> الفرنسية
                        </td>
                        <td>المجموع</td>
                    </tr>
                    <tr>
                        <td>الدرجة</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>درجة الطالب</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-9">
                        <h5 class="mb-3"><strong>لمتابعة مستوى الطالب</strong></h5>
                        <ol class="text-secondary">
                            <li class="mb-2">يرجى زيارة الرابط التالي: <span class="text-decoration-underline">{{ route('students.exams.index') }}</span></li>
                            <li class="mb-2">تسجيل الدخول بواسطة كود الطالب {{ $student->code }}</li>
                            <li class="mb-2">فتح صفحة > الإختبارات السابقة</li>
                        </ol>
                        <div class="mt-3">
                            <span>رقم الهاتف: </span>
                            <span>{{ $phone }}</span>
                        </div>
                        <br><br>
                    </div>
                    <div class="col-3">
                        <div class="p-1 border me-auto" style="height: 160px; width: 160px;">
                            <span class="d-block mb-2 text-center">إمضاء ولي الأمر</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info text-center">
                    لا يوجد طلاب حتى الآن
                </div>
                <a href="{{ URL::previous() }}" class="btn btn-sm btn-info mx-auto d-block">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>
        @endforelse
    </div>
    <script>
        window.print();
    </script>
</body>

</html>