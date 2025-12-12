<h3>Đánh giá của khách hàng</h3>

@if($product->reviews->count() > 0)
    <div class="review-summary">
        <span>{{ number_format($product->reviews->avg('rating'), 1) }}/5 ⭐</span>
        <span>({{ $product->reviews->count() }} Đánh giá)</span>
    </div>

    @foreach ($product->reviews as $review)
        <div class="review-item">
            <div class="reviewer-info">
                <strong>{{ $review->user->name }}</strong>
                <div class="rating-stars">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="{{ $i <= $review->rating ? 'fas fa-star' : 'far fa-star' }}"></i>
                    @endfor
                </div>
            </div>
            <p>{{ $review->comment }}</p>
            <span class="review-date">{{ $review->created_at->format('d/m/Y H:i') }}</span>
        </div>
    @endforeach
@else
    <p class="no-reviews">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá sản phẩm này!</p>
@endif
