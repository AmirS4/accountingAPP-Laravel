@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{__('All Orders')}}</h1>
        <a class="btn btn-success" href="javascript:void(0)" id="createNewOrder">{{__('Create New Order')}}</a>
        <table class="table table-bordered data-table">
            <thead>
            <tr>
                <th>{{__('ID')}}</th>
                <th>{{__('Customer Name')}}</th>
                <th>{{__('Product Title')}}</th>
                <th>{{__('Price')}}</th>
                <th>{{__('Date')}}</th>
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
                    <form id="ordersForm" name="ordersForm" class="form-horizontal">
                        <input type="hidden" name="order_id" id="order_id">
                        <div class="form-group">
                            <label for="customer_name">{{__('Customer Name')}}:</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name"
                                   placeholder="{{__('Enter Customer Name')}}">
                            <ul id="customerList"></ul>
                            <input type="hidden" id="customer_id" name="customer_id">
                        </div>

                        <div class="form-group">
                            <label for="product_name">{{__('Product Title')}}:</label>
                            <input type="text" class="form-control" id="product_name" name="product_name"
                                   placeholder="{{__('Enter Product Title')}}">
                            <ul id="productList"></ul>
                            <input type="hidden" id="product_id" name="product_id">
                        </div>

                        <div class="form-group">
                            <label for="price" class="col-sm-2 control-label">{{__('Price')}}</label>
                            <div class="col-sm-12">
                                <input type="number" class="form-control" id="price" name="price"
                                       placeholder="{{__('Enter Price')}}" value="" maxlength="50" required="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="date" class="col-sm-2 control-label">{{__('Date')}}</label>
                            <div class="col-sm-12">
{{--                                <input type="date" class="form-control" id="date" name="date"--}}
{{--                                       placeholder="{{__('Enter Date')}}" value="" maxlength="50" required="">--}}
{{--                                <input type="text" name="date" id="date" class="form-control" value="{{ old('date') ? old('date') : jdate()->format('Y/m/d') }}">--}}
                                <input type="text" name="date" id="date" class="form-control" value="{{ old('date') ? old('date') : \Morilog\Jalali\Jalalian::now()->format('Y/m/d') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="details" class="col-sm-2 control-label">{{__('Details')}}</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="details" name="details"
                                       placeholder="{{__('Enter Details')}}" value="" maxlength="50" required="">
                            </div>
                        </div>

                        <div id="cSubmit" class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary" id="saveBtn"
                                    value="create">{{__('Save changes')}}
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
            Autocomplete for customer input field
            --------------------------------------------
            --------------------------------------------*/
            $('#customer_name').autocomplete({
                source: function (request, response) {
                    $.getJSON('/autocomplete/customers', {
                        term: request.term
                    }, response);
                },
                minLength: 1,
                select: function (event, ui) {
                    $(this).val(ui.item.label);
                    $('#customer_id').val(ui.item.value);
                    $('#customerList').empty();
                    return false;
                }
            }).data('ui-autocomplete')._renderItem = function (ul, item) {
                return $('<li>')
                    .append($('<a>').text(item.label).data('value', item.value).on('click', selectSuggestion))
                    .appendTo($('#customerList'));
            };

            function selectSuggestion(event) {
                event.preventDefault();
                $('#customer_name').val($(this).text());
                $('#customer_id').val($(this).data('value'));
                $('#customerList').empty();
            }



            /*------------------------------------------
             --------------------------------------------
            Autocomplete for product input field
            --------------------------------------------
            --------------------------------------------*/
            $('#product_name').autocomplete({
                source: function (request, response) {
                    $.getJSON('/autocomplete/products', {
                        term: request.term
                    }, response);
                },
                minLength: 1,
                select: function (event, ui) {
                    $('#product_name').val(ui.item.label);
                    $('#product_id').val(ui.item.value);
                    $('#productList').empty();
                    return false;
                }
            }).data('ui-autocomplete')._renderItem = function (ul, item) {
                return $('<li>')
                    .append('<a href="#">' + item.label + '</a>')
                    .click(function() {
                        $('#product_name').val(item.label);
                        $('#product_id').val(item.value);
                        $('#productList').empty();
                        return false;
                    })
                    .appendTo($('#productList'));
            };




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
                ajax: "{{ route('orders-ajax-crud.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'customer.name', name: 'customer.name'},
                    {data: 'product.title', name: 'product.title'},
                    {data: 'price', name: 'price'},
                    // {data: 'date', name: 'date'},
                    {
                        data: 'date',
                        name: 'date',
                        render: function (data, type, row) {
                            var pd = new persianDate(data);
                            return pd.format('YYYY/MM/DD');
                        }
                    },
                    {data: 'details', name: 'details'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            /*------------------------------------------
            --------------------------------------------
            Click to Button
            --------------------------------------------
            --------------------------------------------*/
            $('#createNewOrder').click(function () {
                $('#saveBtn').val("create-order");
                $('#order_id').val('');
                $('#ordersForm').trigger("reset");
                $('#modelHeading').html("{{__('Create New Order')}}");
                $('#ajaxModel').modal('show');
            });

            /*------------------------------------------
            --------------------------------------------
            Click to Edit Button
            --------------------------------------------
            --------------------------------------------*/
            $('body').on('click', '.editProduct', function () {
                var order_id = $(this).data('id');
                $.get("{{ route('orders-ajax-crud.index') }}" + '/' + order_id + '/edit', function (data) {
                    $('#modelHeading').html("{{__('Edit Order')}}");
                    $('#saveBtn').val("edit-order");
                    $('#ajaxModel').modal('show');
                    $('#order_id').val(data.id);
                    $('#customer_id').val(data.customer_id);
                    $('#product_id').val(data.product_id);
                    $('#price').val(data.price);
                    $('#date').val(data.date);
                    $('#details').val(data.details);
                })
            });

            /*------------------------------------------
            --------------------------------------------
            Create Order Code
            --------------------------------------------
            --------------------------------------------*/
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html("{{__('Saving...')}}");
                $.ajax({
                    data: $('#ordersForm').serialize(),
                    url: "{{ route('orders-ajax-crud.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#ordersForm').trigger("reset");
                        $('#ajaxModel').modal('hide');
                        table.draw();
                    },
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

            $('body').on('click', '.deleteProduct', function () {
                var order_id = $(this).data("id");
                let deleteOrder = confirm("{{__('Are You sure want to delete !')}}");
                if (deleteOrder) {

                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('orders-ajax-crud.store') }}" + '/' + order_id,
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
