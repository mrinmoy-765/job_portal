@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class=" rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Account Settings</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                @include('front.account.sidebar')
            </div>
            <div class="col-lg-9">
                @include('front.message')
                <div class="card border-0 shadow mb-4 p-3">
                    <div class="card-body card-form">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="fs-4 mb-1">Saved Jobs</h3>
                            </div>
                            <!-- <div style="margin-top: -10px;">
                                <a href="{{ route('account.createJob') }}" class="btn btn-primary">Post a Job</a>
                            </div> -->

                        </div>
                        <class="table-responsive">
                            <table class="table ">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col">Title</th>
                                        <th scope="col">Applicants</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="border-0">
                                    @if($savedJobs->isNotEmpty())
                                    @foreach($savedJobs as $savedJob)
                                    <tr class="active">
                                        <td>
                                            <div class="job-name fw-500">{{ $savedJob->jobs_post?->title }}</div>
                                            <div class="info1">
                                                {{ $savedJob->jobs_post?->jobType?->name }} • {{ $savedJob->jobs_post?->location }}
                                            </div>
                                        </td>
                                        <td>{{ $savedJob->jobs_post->applications->count() }} </td>
                                        <td>

                                            @if($savedJob->jobs_post?->status == 1)
                                            <div class="job-status text-capitalize">Active</div>
                                            @else
                                            <div class="job-status text-capitalize">Inactive</div>
                                            @endif


                                        </td>
                                        <td>
                                            <div class="action-dots">
                                                <button href="#" class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="{{ route('jobDetail',$savedJob->jobs_post->id) }}"> <i class="fa fa-eye" aria-hidden="true"></i> View</a></li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="removeSavedJob({{ $savedJob->id }})">
                                                            <i class="fa fa-trash" aria-hidden="true"></i> Remove
                                                        </a>
                                                    </li>

                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="5" class="text-center">No saved jobs found.</td>
                                    </tr>
                                    @endif
                                </tbody>

                            </table>
                    </div>
                    <div>
                        {{ $savedJobs->links() }}
                    </div>
                </div>
            </div>


        </div>
    </div>
    </div>
</section>

@endsection


@section('customJs')
<script>
    function removeSavedJob(id) {
        if (confirm("Are you sure want to remove this job?")) {
            $.ajax({
                url: '{{ route("account.removeSaveJob") }}',
                type: 'post',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(response) {

                    window.location.href = '{{ route("account.savedJobs") }}';
                }
            })
        }
    }
</script>
@endsection