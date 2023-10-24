{{--    heading of Logo data --}}
@php
    if($english==1){
        $lang='en';
    }else{
        $lang='ar';
    }

@endphp
<table class="table2" style="table-layout:fixed;max-width: 100%;text-align: center;padding:2px">
    <thead>
    <tr>
        <th scope="col" style="width: 50%;font-size: 9px">
            <span style="text-decoration: underline">{{$signs[0]['label'][$lang]}}</span>
        </th>
        <th scope="col" style="width: 50%;font-size: 9px">
            <span style="text-decoration: underline">{{$signs[1]['label'][$lang]}}</span>
        </th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th scope="col" style="width: 50%;font-size: 9px">
            <br>
            <br>
        </th>
        <th scope="col" style="width: 50%;font-size: 9px">
            <br>
            <br>
        </th>
    </tr>
    <tr>
        <th scope="col" style="width: 50%;font-size: 9px">
            {{$signs[0]['name'][$lang]}}
        </th>
        <th scope="col" style="width: 50%;font-size: 9px">
            {{$signs[1]['name'][$lang]}}
        </th>
    </tr>
    </tbody>
</table>
