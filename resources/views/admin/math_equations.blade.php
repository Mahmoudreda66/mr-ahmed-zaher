@extends('admin.layouts.app', ['activePage' => 'math-equations', 'titlePage' => "الرموز الرياضية"])

@section('title')
الرموز الرياضية
@endsection

@section('content')
<div class="content">
    <div class="bg-white p-3">
    	<table style="border-collapse: collapse; width: 99.5798%; height: 666px;" border="1">
			<tbody>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">الرمز بالحروف</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">الرمز</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">الكود</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">التربيع</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">²</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">0178</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">أس N</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">n</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">252</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">التكعيب</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">³</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">0179</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">الربع</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">¼</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">0188</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">النصف</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">½</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">0189</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">ثلاث إربع</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">¾</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">0190</span></td>
			</tr>
			<tr style="height: 39px;">
			<td style="width: 30.4078%; height: 39px; text-align: center;"><span style="font-size: 14pt;">زائد أو ناقص (سالب أو موجب)</span></td>
			<td style="width: 30.4078%; height: 39px; text-align: center;"><span style="font-size: 14pt;">±</span></td>
			<td style="width: 30.4078%; height: 39px; text-align: center;"><span style="font-size: 14pt;">241</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">الجذر التربيعي</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">√</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">251</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">باي</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">π</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">227</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">درجة</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">°</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">248</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">أكبر من أو يساوي</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">≥</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">242</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">أصغر من أو يساوي</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">≤</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">243</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">يساوي تقريباً</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">≈</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">247</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">متطابق تماماً</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">≡</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">240</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">المجموع</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">Σ</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">228</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">نقطة سوداء</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">•</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">7</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">نقطة بيضاء</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">○</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">9</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">سهم لأعلى</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">↑</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">24</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">سهم لأسفل</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">↓</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">25</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">سهم لليمين</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">→</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">26</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">سهم لليسار</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">←</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">27</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">سهم يمين ويسار</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">↔</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">29</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">سهم للأعلى وللأسفل</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">↨</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">23</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">ألفا</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">α</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">224</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">بيتا</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">ß</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">225</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">دلتا</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">δ</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">235</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">أوميجا</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">Ω</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">234</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">جنية إسترليني</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">£</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">156</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">دولار</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">$</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">36</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">يورو</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">€</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">128</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">ين</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">¥</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">157</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">فرانك</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">ƒ</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">159</span></td>
			</tr>
			<tr style="height: 19px;">
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">فاي</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">Ø</span></td>
			<td style="width: 30.4078%; height: 19px; text-align: center;"><span style="font-size: 14pt;">216</span></td>
			</tr>
			</tbody>
		</table>
    </div>
</div>
@endsection