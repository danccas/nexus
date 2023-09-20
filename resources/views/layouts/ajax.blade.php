<?php if(request()->ajax()) { ?>
@yield('styles')
@hasSection('title')
<div style="border-left: 5px solid #3c78ff;padding-left: 5px;margin-bottom:15px;">
  <h4 class="mb-sm-0">@yield('title')</h4>
</div>
@endif
<div style="max-width:600px;width:100%;">
  @yield('content')
</div>
@yield('scripts')
<?php } else { ?>
  @include('layouts.modern')
<?php } ?>
