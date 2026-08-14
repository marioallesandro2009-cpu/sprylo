<section class="section">
  <div class="container">
    <div class="cats-grid">
      @foreach($allCategories as $category)
        <a href="{{ route('shop.index', ['categories' => [$category->id]]) }}" class="cat-tile">
          <div class="pic"><img src="{{ $category->image }}" alt="{{ $category->name }}" /></div>
          <div class="name">{{ $category->name }}</div>
          <div class="count">{{ $category->products_count }} Products</div>
        </a>
      @endforeach
    </div>
  </div>
</section>
