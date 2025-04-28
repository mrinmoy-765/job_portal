@extends('front.layouts.app')

@section('main')
<section class="section-5 bg-2">
    <div class="container py-5">
        <div class="row">
            <div class="col">
                <nav aria-label="breadcrumb" class="rounded-3 p-3 mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                @include('admin.sidebar')
            </div>

            <div class="col-lg-9">
                @include('front.message')
                <div class="card border-0 shadow mb-4">

                    <div class="card-body card-form">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="fs-4 mb-1">Users</h3>
                            </div>

                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Mobile</th>
                                        <th scope="col">Role</th>
                                        <th scope="col">Created At</th>
                                    </tr>
                                </thead>
                                <tbody class="border-0">
                                    @if($users->isNotEmpty())
                                    @foreach($users as $user)
                                    <tr class="active">
                                        <td class="d-flex align-items-center">
                                            @if($user->image != '')
                                            <img src="{{ asset('profile_pic/thumb/' . $user->image) }}" alt="avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                            <img src="{{ asset('assets/images/avatar.jpg') }}" alt="avatar" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <div class="fw-500">{{ $user->name }}</div>
                                                <div class="text-muted small">{{ $user->designation ?? 'N/A' }}</div>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->mobile }}</td>
                                        <td>{{ ucfirst($user->role) }}</td>
                                        <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d M, Y') }}</td>
                                        <td>
                                            <div class="action-dots float-end">
                                                <button class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li><a class="dropdown-item" href="{{ route('admin.editUser', $user->id) }}"><i class="fa fa-edit" aria-hidden="true"></i> Edit</a></li>
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="deleteUser({{ $user->id }})">
                                                            <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="6" class="text-center">No users found.</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div>
                            {{ $users->links() }}
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
function deleteUser(id) {
    if (!confirm('Are you sure you want to delete this user?')) {
        return;
    }

    $.ajax({
        url: '/admin/users/' + id,  
        type: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.status) {
                alert(response.message);
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function(xhr) {
            alert('Something went wrong!');
        }
    });
}

</script>
@endsection