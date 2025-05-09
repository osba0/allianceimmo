@if (isset($breadcrumbs))
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-md-12">
                    <ul class="rounded-tabs">
                      <li><span class="title-page">{{ $title }}</span></li>
                    </ul>
                </div>
                <!--div class="col-sm-6">
                    <h1 class="m-0 text-dark">@if(isset($icon)) <i class="{{$icon}}"></i> @endif {{ $title }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        @foreach ($breadcrumbs as $key => $breadcrumb)
                            <li class="breadcrumb-item active">
                                <a href="{{ $breadcrumb['url'] }}">{{ $key }}</a>
                            </li>
                        @endforeach
                    </ol>
                </div-->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
@endif
