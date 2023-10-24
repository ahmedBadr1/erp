@extends('pdf.layouts.pdfMaster')
@php
    if($english==1){
        $name='name';
        $code='Code';
        $studentName='Student Name';
        $faculty='Faculty';
        $program='Program';

        $courseName='Course Name';
        $creditHours='Credit Hours';
        $marks='Marks Min/Max';
        $section='Group / Section';
    }else{
        $name='name_local';
        $code='الكود';
        $studentName='اسم الطالب';
        $faculty='الكلية';
        $program='البرنامج';

        $courseName='اسم المادة';
        $creditHours='عدد الساعات';
        $marks='الدرجات الاقل / الاعلى';
        $section='المجموعة / الفصل';
    }
@endphp
@section('pdf_content')
    {{-- student data --}}
    <table style="table-layout:fixed; padding-top: 3px">
        <tr>
            <th style="width: 100%;">
                <table class="table2"
                       style="width: 100%;table-layout:fixed;max-width: 100%;padding-top: 6px">
                    <thead>
                    <tr>
                        <th scope="col" style="width: 15%;font-size: 12px">{{$code}} :</th>
                        <th scope="col" style="width: 85%;font-size: 9px;line-height: 20px">
                            {{$studentData['code']}}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th scope="col" style="width: 25%;font-size: 12px">{{$studentName}} :
                        </th>
                        <th scope="col" style="width: 75%;font-size: 9px;line-height: 20px;">
                            {{$studentData['name'][$name]}}
                        </th>
                    </tr>
                    <tr>
                        <th scope="col" style="width: 14%;font-size: 12px;">{{$faculty}} :</th>
                        <th scope="col" style="width: 37%;font-size: 9px;line-height: 16px">
                            {{$studentData['faculty'][$name]}}
                        </th>
                        <th scope="col" style="width: 18%;font-size: 12px;">{{$program}} :
                        </th>
                        <th scope="col" style="width: 30%;font-size: 9px;line-height: 16px">
                            {{$studentData['program'][$name]}}
                        </th>
                    </tr>
                    </tbody>
                </table>
            </th>
        </tr>
    </table>
    {{--  Start data --}}
    <br><br>
    {{-- Start of filling transcript data --}}
    <table  class="table-transcript" style="table-layout:fixed;border: 1px solid #ccc;max-width: 100%;  padding: 5px;" >
        <thead >
        <tr >
            <th  scope="col" style="width: 20%;font-size: 10px">
                {{$code}}
            </th>
            <th  scope="col" style="width: 50%;font-size: 10px">
                {{$courseName}}
            </th>
            <th  scope="col" style="width: 10%;font-size: 10px">
                {{$creditHours}}
            </th>
            <th  scope="col" style="width: 10%;font-size: 10px">
                {{$marks}}
            </th>
            <th  scope="col" style="width: 10%;font-size: 10px">
                {{$section}}
            </th>
        </tr>
        </thead>
        <tbody >
        @foreach($registrations as $registration)
            <tr>
                <td  scope="col" style="width: 20%;font-size: 10px">
                    {{$registration['code']}}
                </td>
                <td  scope="col" style="width: 50%;font-size: 10px">
                    {{$registration['course_name']}}
                </td>
                <td  scope="col" style="width: 10%;font-size: 10px">
                    {{$registration['credit_hours']}}
                </td>
                <td  scope="col" style="width: 10%;font-size: 10px">
                    {{$registration['marks']}}
                </td>
                <td  scope="col" style="width: 10%;font-size: 10px">
                    {{$registration['section']}}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <br/><br/><br/>
@endsection
