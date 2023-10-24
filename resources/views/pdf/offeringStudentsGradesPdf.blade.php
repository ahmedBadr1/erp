@extends('pdf.layouts.pdfMaster')
@php
    if($english==1){
        $name='name';
        $code='Code';
        $sbn='SBN';
        $studentName='Student Name';
        $name='name';
        $total='Total';
        // $program='Program';
        
        // $creditHours='Credit Hours';
        // $marks='Marks Min/Max';
        // $section='Group / Section';
    }else{
        $name='name_local';
        $code='الكود';
        $sbn='رقم الجلوس';
        $studentName='اسم الطالب';
        $name='name_local';
        $total='المجموع';
        // $studentName='اسم الطالب';
        // $faculty='الكلية';
        // $program='البرنامج';

        // $creditHours='عدد الساعات';
        // $marks='الدرجات الاقل / الاعلى';
        // $section='المجموعة / الفصل';
    }
@endphp
@section('pdf_content')
    {{--  Start data --}}
    {{-- Start of filling transcript data --}};
    <table  class="table-transcript" style="table-layout:fixed;border: 1px solid #ccc; max-width: 100%;  padding: 3px" >
        <thead >
        <tr >
            <th  scope="col" style="width: 8%; font-size: 10px">
                {{$sbn}} 
            </th>
            <th  scope="col" style="width: 12%; font-size: 10px">
                {{$code}} 
            </th>
            <th  scope="col" style="width: 22%; font-size: 10px">
                {{$studentName}} 
            </th>
        @foreach($offeringGradeItemsHeaders as $offeringGradeItemsHeader)  
            <th  scope="col" style="width: 10%; font-size: 10px">
                {{$offeringGradeItemsHeader->mark->$name}}
            </th>
            @endforeach
            <th  scope="col" style="width: 8%; font-size: 10px">
                {{$total}}
            </th>
        </tr>
        </thead>
        <tbody>
 
        @foreach($registrations as $registration)
            <tr>
                <td  scope="col" style="width:8%; font-size: 10px">
                    {{$registration->student->exam_location_bn}}
                </td>
                <td  scope="col" style="width:12%; font-size: 10px">
                    {{$registration->student->user->code}}
                </td>
                <td  scope="col" style="width:22%; font-size: 10px">
                    {{Str::limit($registration->student->user->$name, 30) }}
                </td>

                @foreach($registration->registrationMarks as $mark)   
                    <td  scope="col" style="width:10%; font-size: 10px">
                        {{ $mark->value}}
                    </td>
                @endforeach
                <?php
                    $sum = 0;
                    foreach($registration->registrationMarks as $mark)
                    {
                    $sum+= $mark->value;
                    }
                ?>
                <td  scope="col" style="width:8%; font-size: 10px">
                    {{ $sum }}
                </td>
               
                  
            </tr> 
        @endforeach

    
        </tbody>
    </table>
    <br/><br/><br/>
@endsection
