@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Items List</h3>
                    <a href="{{ route('items.create') }}" class="btn btn-primary">Add New Item</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning" role="alert">
                            {{ session('warning') }}
                        </div>
                    @endif

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Created At</th>
                                <th width="280px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                            <tr>
                                <td>{{ $item['id'] ?? 'N/A' }}</td>
                                <td>{{ $item['name'] ?? 'N/A' }}</td>
                                <td>{{ $item['description'] ?? 'N/A' }}</td>
                                <td>{{ isset($item['created_at']) ? date('Y-m-d H:i', strtotime($item['created_at'])) : 'N/A' }}</td>
                                <td>
                                    <form action="{{ route('items.destroy', $item['id'] ?? 0) }}" method="POST">
                                        <a class="btn btn-info btn-sm" href="{{ route('items.show', $item['id'] ?? 0) }}">View</a>
                                        <a class="btn btn-primary btn-sm" href="{{ route('items.edit', $item['id'] ?? 0) }}">Edit</a>
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No items found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection