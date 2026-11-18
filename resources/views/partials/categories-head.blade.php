<section class="page-head">
  <div class="container">
    <div class="crumbs"><a href="{{ route('home') }}">{{ $ui('ui_crumb_home') }}</a> <span class="sep">›</span> <span>{{ $ui('ui_categories_crumb') }}</span></div>
    <h1>{{ $settings['categories_head_title'] ?? 'Shop by category' }}</h1>
    <p>{{ $allCategories->count() }} {{ $settings['categories_head_text'] ?? 'categories to browse — pick one to jump straight into filtered results.' }}</p>
  </div>
</section>