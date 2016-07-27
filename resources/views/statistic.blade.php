@extends('layouts.admin')

@section('header')
    <h1>Просмотр статистики</h1>
@endsection

@section('content')

    <h2>По всему сайту</h2>
    <table class="table table-inverse">
        <tbody>
        @foreach($total as $key => $row)
            <tr>
                <th scope="row">{{$key}}</th>
                <td>
                    @foreach($row as $rowKey => $rowVal)

                        <strong>{{$rowKey}}</strong>
                        <div>
                            hits - {{(int)$rowVal['hits']}} <br>
                            unique ips - {{count($rowVal['ips'])}} <br>
                            unique cookies- {{count($rowVal['cookies'])}}
                        </div>
                        <br>

                    @endforeach

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>По отдельным страницам</h2>

    <table class="table table-inverse">
        <tbody>
        @foreach($perPage as $key => $row)
            <tr>
                <th scope="row">{{$key}}</th>
                <td>
                    @foreach($row as $rowPerPage)

                        @foreach($rowPerPage as $k => $v)
                            <strong>{{$k}}</strong>
                            <div>
                                hits - {{(int)$v['hits']}} <br>
                                unique ips - {{count($v['ips'])}} <br>
                                unique cookies- {{count($v['cookies'])}}
                            </div>
                        @endforeach

                    @endforeach

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>



@endsection
