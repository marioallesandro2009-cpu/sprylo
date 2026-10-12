<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">Home</a> <span class="sep">›</span> <span>Brands</span></div>
    <h1>{{ cms('brands.head.title', 'Shop by brand') }}</h1>
    <p>{{ $allBrands->count() }} brands we carry — pick one to jump straight into filtered results.</p>
  </div>
</section>
