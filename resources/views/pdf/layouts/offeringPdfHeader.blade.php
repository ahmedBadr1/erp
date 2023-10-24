{{--    heading of Logo data --}}
@php
    $title = $english==1?$title['en']:$title['ar'];
@endphp
@php
    if($english==1){
        $name='name';
        $code='Code';
        $bylaw= 'name';
        $bylaw= 'code';
        $studentName='Student Name';
        $faculty='name';
        $program='Program';
        
        $courseName='Course Name';
        $creditHours='Credit Hours';
        $marks='Marks Min/Max';
        $section='Group / Section';
        $universty= '6th of October University';
    }else{
        $name='name_local';
        $code='الكود';
        $bylaw= 'name_local';
        $bylaw= 'code';
        $studentName='اسم الطالب';
        $faculty='name_local';
        $program='البرنامج';
        
        $courseName='اسم المادة';
        $creditHours='عدد الساعات';
        $marks='الدرجات الاقل / الاعلى';
        $section='المجموعة / الفصل';
        $universty= 'جامعة ٦ أكتوبر';
    }
@endphp
<table style="table-layout:fixed; ">
    <tr>
        <th  scope="col" style="width:33.33%;font-size: 12px;text-align: left">
            <img style ="" src="{{ public_path('img/logos/logo.jpg') }}" width="30px" height="30px">
        </th>

        
        <th  scope="col" align="left" style="width: 66.67%;font-size:12px ">
            <div>
                <p style="vertical-align: middle">
                    {{$offeringData['code']}} - {{$offeringData['name'][$name]}}  - ({{$offeringData['bylaw']['code']}}) ({{$offeringData['bylaw'][$name]}}) 
                    <br>
                    {{$offeringData['faculty'][$name]}},
                    
                    {{$universty}}
                </p>

            </div>
        </th>

        {{-- <th align="right" style="width: 25.33%;">
            <div style="vertical-align: middle">
                <p style="font-size: 20px;">
                    <span >{{$title}}</span>
                </p>
            </div>
        </th> --}}
        
    </tr>
</table>
<span>_____________________________________________________________________________________________</span>
{{-- <span style="text-align: right;font-size: 16px">جامعة 6 أكتوبر</span> --}}

