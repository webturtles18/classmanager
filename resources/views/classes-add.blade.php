@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6"><h3>Add New Class</h3></div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('home') }}" class="btn btn-primary">Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @include('includes.status')

                    <form action="{{ route('classes.create') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                <label>College<span class="text-danger">*</span></label>
                                @if(!empty($colleges))
                                <select name="college_id" class="form-control">
                                    <option value="">-- Select College --</option>
                                    @foreach($colleges as $college)
                                    <option value="{{ $college->id }}" @if(old('college_id') == $college->id) selected @endif >{{ $college->name }}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                            <div class="form-group col-md-7">
                                <label>Title<span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}"/>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Contact<span class="text-danger">*</span></label>
                                <input type="text" name="contact_no" class="form-control" value="{{ old('title') }}"/>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Email<span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" />
                            </div>
                            <div class="form-group col-md-4">
                                <label>Price<span class="text-danger">*</span></label>
                                <input type="text" name="price" class="form-control" value="{{ old('price') }}" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Levels<span class="text-danger">*</span></label>
                                <div id="levels-cantainer">
                                    @php
                                    $levels = old('levels')
                                    @endphp
                                    @if(!empty($levels))
                                        @foreach($levels as $level)
                                            <input type="text" name="levels[]" value="{{ $level }}" class="form-control mb-1" required="" />
                                        @endforeach
                                    @else
                                    <input type="text" name="levels[]" class="form-control mb-1" required="" />
                                    @endif
<!--                                    <div class="row">
                                        <div class="col-md-7 mb-1"><input type="text" name="levels[]" class="form-control" required="" /></div>
                                        <div class="col-md-1"><a href="javascript:void(0)" class="" onclick="return removeLevel(1)"><i class="fa fa-fw fa-trash text-danger"></i></a></div>
                                    </div>-->
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" onclick="return addLevel()">Add Level</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Description<span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control">{!! old('description') !!}</textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Syllabus<span class="text-danger">*</span></label>
                                <input type="file" name="syllabus" class="form-control">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('home') }}" type="button" class="btn btn-danger text-white">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    function addLevel(){
//        var ele = '<div class="mb-1"><input type="text" name="levels[]" class="form-control" required="" /><i class="fa fa-fw fa-trash text-danger"></i></div>';
        var ele = '<input type="text" name="levels[]" class="form-control mb-1" required="" />';
        var selector = "#levels-cantainer";
        
        $(selector).append(ele);
    }
$(document).ready(function(){
    
});
</script>
@endsection