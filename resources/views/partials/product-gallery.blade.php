<div class="gallery">
  <div class="gallery-thumbs">
    @foreach($product->images as $image)
      <button class="@if($loop->first) is-active @endif"><img src="{{ $image->image }}" alt="" /></button>
    @endforeach
  </div>
  <figure class="gallery-main">
    <img src="{{ str_replace('w=500', 'w=900', $mainImage) }}" alt="{{ $product->name }}" />
  </figure>
</div>
