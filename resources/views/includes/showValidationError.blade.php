@if ($errors->any())
    <div dir="ltr" id="ero" style="text-align: right " class="alert alert-danger alert-dismissible">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
