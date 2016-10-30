@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">{{ trans('comm.examsignup') }}</div>

                <div class="panel-body">
                    {!! $form->header !!}
                    {!! $grid !!}
                    <div class="btn-toolbar" role="toolbar">
                        <div class="pull-left">
                            <input class="btn btn-primary" value="{{ trans('app.export') }}" type="submit">
                            &nbsp;&nbsp;
                            <a href="javascript:void(0)" onclick="return allselect(this);">全选/反选</a>
                        </div>
                    </div>
                    {!! $form->render('select_students') !!}
                    {!! $form->footer !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script>
        function allselect(e) {
            $(".cellid").each(function () {
                $(this).prop('checked', !$(this).prop('checked'));
            });
            return true;
        }
    </script>
@endsection
