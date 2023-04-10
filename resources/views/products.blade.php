@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{__('All Products')}}</h1>
        <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct">{{__('Create New Product')}}</a>
        <table class="table table-bordered data-table">
            <thead>
            <tr>
                <th>{{__('ID')}}</th>
                <th>{{__('Title')}}</th>
                <th>{{__('Description')}}</th>
                <th>{{__('Details')}}</th>
                <th width="280px">{{__('Action')}}</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                </div>
                <div class="modal-body">
                    <form id="productsForm" name="productsForm" class="form-horizontal">
                        <input type="hidden" name="product_id" id="product_id">
                        <div class="form-group">
                            <label for="title">{{__('Title')}}</label>
                            <input type="text" class="form-control" id="title" name="title"
                                   placeholder="{{__("Enter Product Title")}}">
                        </div>

                        <div class="form-group">
                            <label for="description">{{__('Description')}}</label>
                            <input type="text" class="form-control" id="description" name="description"
                                   placeholder="{{__('Enter Description')}}">
                        </div>

                        <div class="form-group">
                            <label for="details">{{__('Details')}}</label>
                            <input type="text" class="form-control" id="details" name="details"
                                   placeholder="{{__('Enter Details')}}">
                        </div>

                        <div id="cSubmit" class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary" id="saveBtn"
                                    value="create">{{__('Save Changes')}}
                            </button>
                        </div>

                        <div id="errors"></div>
                        @include('includes.showValidationError')
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        $(function () {

            /*------------------------------------------
             --------------------------------------------
             Pass Header Token
             --------------------------------------------
             --------------------------------------------*/
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            /*------------------------------------------
            --------------------------------------------
            Render DataTable
            --------------------------------------------
            --------------------------------------------*/
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('products-ajax-crud.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'title', name: 'title'},
                    {data: 'description', name: 'description'},
                    {data: 'details', name: 'details'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            /*------------------------------------------
            --------------------------------------------
            Click to Button
            --------------------------------------------
            --------------------------------------------*/
            $('#createNewProduct').click(function () {
                $('#saveBtn').val("create-product");
                $('#product_id').val('');
                $('#productsForm').trigger("reset");
                $('#modelHeading').html("{{__('Create New Product')}}");
                $('#ajaxModel').modal('show');
            });

            /*------------------------------------------
            --------------------------------------------
            Click to Edit Button
            --------------------------------------------
            --------------------------------------------*/
            $('body').on('click', '.editProduct', function () {
                var product_id = $(this).data('id');
                $.get("{{ route('products-ajax-crud.index') }}" + '/' + product_id + '/edit', function (data) {
                    $('#modelHeading').html("{{__('Edit Product')}}");
                    $('#saveBtn').val("edit-product");
                    $('#ajaxModel').modal('show');
                    $('#product_id').val(data.id);
                    $('#title').val(data.title);
                    $('#description').val(data.description);
                    $('#details').val(data.details);
                })
            });

            /*------------------------------------------
            --------------------------------------------
            Create Product Code
            --------------------------------------------
            --------------------------------------------*/
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html("{{__('Saving...')}}");
                $.ajax({
                    data: $('#productsForm').serialize(),
                    url: "{{ route('products-ajax-crud.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#productsForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.draw();
                    },

                    /*------------------------------------------
                   --------------------------------------------
                   Show Errors Code
                   --------------------------------------------
                   --------------------------------------------*/
                    error: function (data) {
                        console.log('Error:', data);
                        $('#saveBtn').html('{{__('Save Changes')}}');

                        let str = data.responseText; // "\u6f22\u5b57" === "漢字"
                        let newStr = JSON.parse(str)
                        let errorMessage = newStr.errors
                        console.log(errorMessage);

                        $.each(errorMessage, function (key, value) {
                            $('#errors').append('<div class="alert alert-danger">' + value + '</div')
                        })

                        setTimeout(function () {
                            $('#errors').fadeOut(500, function () {
                                $(this).text('').show();
                            });
                        }, 5000);

                    }
                });
            });

            /*------------------------------------------
              --------------------------------------------
              Delete Product Code
              --------------------------------------------
              --------------------------------------------*/
            $('body').on('click', '.deleteProduct', function () {
                var product_id = $(this).data("id");
                let deleteProduct = confirm("{{__('Are You sure want to delete !')}}");
                if (deleteProduct) {

                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('products-ajax-crud.store') }}" + '/' + product_id,
                        success: function (data) {
                            table.draw();
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });

                } else {
                    // alert('Canceled')
                    console.log('Canceled')
                }
            });

        });
    </script>
@endsection
