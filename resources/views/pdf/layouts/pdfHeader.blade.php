{{--    heading of Logo data --}}
@php
    $title = $english==1?$title['en']:$title['ar'];
@endphp
<table style="table-layout:fixed; ">
    <tr>
        <th align="center" style="width: 40.33%;font-size:12px ">
            <div>
                <p style="vertical-align: middle">
                    تأسست بالقرار (234) السنه 1996
                    <br>
                    إدارة شؤون التعليم و الطلاب
                </p>

            </div>
        </th>
        <th align="left" style="width: 25.33%;">
            <div style="vertical-align: middle">
                <p style="font-size: 20px;">
                    <span >{{$title}}</span>
                </p>
            </div>
        </th>
        <th style="width:33.33%;font-size: 12px;text-align: right">
            <img style ="" src="{{ public_path('img/logos/logo.jpg') }}" width="80px" height="80px">
        </th>
    </tr>
</table>
<span>______________________________________________________________________________</span>
<span style="text-align: right;font-size: 16px">جامعة 6 أكتوبر</span>
<br><br>
