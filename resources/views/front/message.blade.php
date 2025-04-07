@if(Session::has('success'))
<div class="alert alert-success alert-dosmissible fade show" role="alert">
     {{ Session::get('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" area-label="Close"></button>
</div>
@endif




@if(Session::has('error'))
<div class="alert alert-danger alert-dosmissible fade show" role="alert">
     {{ Session::get('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" area-label="Close"></button>
</div>
@endif