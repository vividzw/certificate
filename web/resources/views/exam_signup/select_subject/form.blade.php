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
        var subjects = new Array();
        var i = 0;
        @foreach ($subject_names as $name)
                subjects[i++] = '{{ $name }}';
        @endforeach
        var check_subjects = function (sn) {
            found = false;
            for(var k2 in subjects) {
                $(":input[name=" + subjects[k2] + "]").each(function(){
                    if (subjects[k2] != sn) {
                        $(this).prop('checked', false);
                    } else {
                        if (!found && $(this).prop('checked')) found = true;
                    }
                })
            }
            if (found) return true;
            alert('Error: 必须选择一项科目');
            return false;
        };
        $(document).ready(function() {
            if (i > 1) {
                $("#examsignup").hide();
                for (var k in subjects) {
                    var subname = subjects[k];
                    var sub_div = $("#div_" + subname);
                    var button = $("<button class='btn btn-primary' data='" + subname + "'>{{ trans("comm.examsignup") }}</button>");
                    button.click(function () {
                        return check_subjects($(this).attr('data'));
                    });
                    var div = $("<div class='divbutton'><div>");
                    div.append(button);
                    sub_div.append(div);
                }
            } else {
                $("#examsignup").click(function() {
                    return check_subjects(subjects[0]);
                });
            }
        });
    </script>
@endsection
