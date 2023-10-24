<!doctype html>
<html >
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style media="all">
        body{
            font-size: 81.25%;
            line-height: 1.2;
        }
        td{
            vertical-align: bottom;
        }
        .table-transcript td, .table-transcript tr{
            border: 1px solid #323232;
            padding: 10px;
        }
        .table-transcript th{
            border: 1px solid #323232;
            padding: 3px;
        }

    </style>
</head>
<body>
<div style="direction: rtl">
{{--   @include('pdf.layouts.pdfHeader',['title_ar'=>$title_ar,'title_en'=>$title_en])--}}

    @yield('pdf_content')

</div>
</body>
</html>
