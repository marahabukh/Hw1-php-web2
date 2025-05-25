@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Users Details</h3>
                </div>

                <div class="card-body">
                    <div class="form-group mb-3">
                        <strong>ID:</strong>
                        {{ $item['id'] }}
                    </div>

                    <div class="form-group mb-3">
                        <strong>Name:</strong>
                        {{ $item['name'] }}
                    </div>

                    <div class="form-group mb-3">
                        <strong>email:</strong>
                        {{ $item['email'] }}
                    </div>

                    <div class="form-group text-center">
                        <a class="btn btn-primary" href="{{ route('items.edit', $item['id']) }}">Edit</a>
                        <a class="btn btn-secondary" href="{{ route('items.index') }}">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection