@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Create New Item</h3>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('items.store') }}" method="POST">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="name">Name:</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" placeholder="Enter name">
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">email:</label>
                            <textarea class="form-control" name="email" id="email" rows="4" placeholder="Enter email">{{ old('email') }}</textarea>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a class="btn btn-secondary" href="{{ route('items.index') }}">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection