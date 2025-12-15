@extends('layouts.admin')

@section('title', 'Phân công giao hàng')

@section('content')
<div class="right_col" role="main">
    <div class="container">
        <h1 class="mb-4">Phân công giao hàng #{{ $order->id }}</h1>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.deliveries.assign', $order) }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Nhân viên giao hàng</label>
                        <select name="delivery_user_id" class="form-select" required>
                            <option value="">-- Chọn nhân viên --</option>
                            @foreach($deliveryUsers as $user)
                                <option value="{{ $user->id }}" {{ $order->delivery_user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->phone ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="delivery_note" class="form-control" rows="3">{{ $order->delivery_note }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Lưu phân công</button>
                    <a href="{{ route('admin.deliveries.index') }}" class="btn btn-secondary">Hủy</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection