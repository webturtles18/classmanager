@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-6"><h3>Class List</h3></div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('classes.create') }}" class="btn btn-primary">Add Class</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="get" action="{{ route('classes.search') }}">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-6 col-xs-12">
                                <input type="text" name="search" placeholder="Search..." value="{{request()->get('search')}}" class="form-control">
                            </div>
                            <div class="form-group col-md-6 col-sm-12">
                                <div>
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <a type="button" class="btn btn-danger" href="{{ route('home') }}">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="mb-3 mt-5"></div>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @include('includes.status')
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">College</th>
                                    <th scope="col">Class</th>
                                    <th scope="col">Levels</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($classes) && $classes->count())
                                    @foreach($classes as $class)
                                        <tr>
                                            <td>{{ $class->college }}</td>
                                            <td>{{ $class->title }}</td>
                                            <td>{{ $class->levels }}</td>
                                            <td>
                                                <a href="{{ route('classes.edit',$class->id) }}" data-toggle="tooltip" data-original-title="Edit"><i class="fa fa-fw fa-pencil text-success"></i></a> |
                                                <a href="javascript:void(0);" data-toggle="tooltip" data-original-title="Delete" onclick="return confirmDelete('{{ route('classes.delete',$class->id) }}');"><i class="fa fa-fw fa-trash text-danger"></i></a> |
                                                @if(!empty($class->syllabus))
                                                <a href="{{ route('classes.download',$class->id) }}" target="_blank" data-toggle="tooltip" data-original-title="Download PDF"><i class="fa fa-fw fa-download"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                <tr><td colspan="100%" class="text-center">No Data Found</td></tr>
                                @endif
                            </tbody>
                        </table>
                        {!! $classes->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
