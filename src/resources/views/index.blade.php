@extends('layouts.app')

@section('content')
    <table class="table">
        <tbody id="dinoTable">
            @for($i = 1; $i <= 16; $i++)
                <tr class="row-{{$i}}">
                    <td class="col-dummy border-0 text-center">{{ $i }}</td>
                    @for($j = 65; $j <= 90; $j++)
                        <td class="col-{{chr($j) . $i}} border" id="{{chr($j) . $i}}"></td>
                    @endfor
                </tr>
            @endfor
            <tr class="row-dummy">
                <td class="col-dummy border-0 text-center"></td>
                @for($j = 65; $j <= 90; $j++)
                    <td class="col-dummy border-0 text-center">{{chr($j)}}</td>
                @endfor
            </tr>
        </tbody>
    </table>
@endsection