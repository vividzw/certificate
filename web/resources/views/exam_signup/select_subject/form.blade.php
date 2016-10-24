@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('comm.examsignup') }}</div>

                <div class="panel-body">
                    {!! $form !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <style>
        .divbutton {
            margin: 10px;
        }
    </style>
    <script>
        var subjects = {};
        @foreach ($subject_names as $name)
                subjects['{{ $name }}'] = '{{ $name }}';
        @endforeach
        $(document).ready(function() {
            for (var k in subjects) {
                var subname = subjects[k];
                var sub_div = $("#div_" + subname);
                var button = $("<a href='javascript:void(0);' class='btn btn-primary' data='" + subname + "'>报名</a>");
                button.click(function () {
                    alert($(this).attr('data'));
                });
                var div = $("<div class='divbutton'><div>");
                div.append(button);
                sub_div.append(div);
            }
        });
    </script>
@endsection
