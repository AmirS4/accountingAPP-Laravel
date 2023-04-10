@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>{{__('All Customers')}}</h1>
        <a class="btn btn-success" href="javascript:void(0)" id="createNewCustomer">{{__('Create New Customer')}}</a>
        <table class="table table-bordered data-table">
            <thead>
            <tr>
                <th>{{__('ID')}}</th>
                <th>{{__('Name')}}</th>
                <th>{{__('Number')}}</th>
                <th>{{__('Email')}}</th>
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
                    <form id="customerForm" name="customerForm" class="form-horizontal">
                        <input type="hidden" name="customer_id" id="customer_id">
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">{{__('Name')}}</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="name" name="name"
                                       placeholder="{{__('Enter Name')}}"
                                       value="" maxlength="50" required="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="number" class="col-sm-2 control-label">{{__('Number')}}</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="number" name="number"
                                       placeholder="{{__(('Enter Number'))}}" value="" maxlength="50" required="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">{{__('Email')}}</label>
                            <div class="col-sm-12">
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="{{__('Enter Email')}}" value="" maxlength="50" required="">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row" style="align-items: center;">
                                <div class="col-md-10 dynamic-field" id="dynamic-field-1">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="field" class="hidden-md">{{__('Key')}}</label>
                                                <input type="text" id="field" class="form-control" name="key[]">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="secondField" class="hidden-md">{{__('Value')}}</label>
                                                <input type="text" id="secondField" class="form-control"
                                                       name="value[]">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="cButton" class="col-md-2 mt-30 append-buttons">
                                    <div class="clearfix">
                                        <button type="button" id="add-button"
                                                class="btn btn-secondary float-left text-uppercase shadow-sm"><i
                                                class="fa fa-plus fa-fw"></i>
                                            +
                                        </button>
                                        <button type="button" id="remove-button"
                                                class="btn btn-secondary float-left text-uppercase ml-1"
                                                disabled="disabled"><i
                                                class="fa fa-minus fa-fw"></i>
                                            -
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div id="cSubmit" class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary" id="saveBtn"
                                        value="create">{{__('Save changes')}}
                                </button>
                            </div>
                        </div>
                        <div id="errors"></div>
                        @include('includes.showValidationError')
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        /*------------------------------------------
        --------------------------------------------
        Add And Remove Buttons Code
        --------------------------------------------
        --------------------------------------------*/
        $(document).ready(function () {
            var buttonAdd = $("#add-button");
            var buttonRemove = $("#remove-button");
            var className = ".dynamic-field";
            var count = 0;
            var field = "";
            var maxFields = 50;

            function totalFields() {
                return $(className).length;
            }

            function addNewField() {
                count = totalFields() + 1;
                field = $("#dynamic-field-1").clone();
                field.attr("id", "dynamic-field-" + count);
                field.children("label").text("Field " + count);
                field.find("input").val("");
                $(className + ":last").after($(field));
            }

            function removeLastField() {
                if (totalFields() > 1) {
                    $(className + ":last").remove();
                }
            }

            function enableButtonRemove() {
                if (totalFields() === 2) {
                    buttonRemove.removeAttr("disabled");
                    buttonRemove.addClass("shadow-sm");
                }
            }

            function disableButtonRemove() {
                if (totalFields() === 1) {
                    buttonRemove.attr("disabled", "disabled");
                    buttonRemove.removeClass("shadow-sm");
                }
            }

            function disableButtonAdd() {
                if (totalFields() === maxFields) {
                    buttonAdd.attr("disabled", "disabled");
                    buttonAdd.removeClass("shadow-sm");
                }
            }

            function enableButtonAdd() {
                if (totalFields() === (maxFields - 1)) {
                    buttonAdd.removeAttr("disabled");
                    buttonAdd.addClass("shadow-sm");
                }
            }

            buttonAdd.click(function () {
                addNewField();
                enableButtonRemove();
                disableButtonAdd();
            });

            buttonRemove.click(function () {
                removeLastField();
                disableButtonRemove();
                enableButtonAdd();
            });
        });


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
                ajax: "{{ route('customers-ajax-crud.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'name', name: 'name'},
                    {data: 'number', name: 'number'},
                    {data: 'email', name: 'email'},
                    {data: 'details', name: 'details'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });

            /*------------------------------------------
            --------------------------------------------
            Click to Button
            --------------------------------------------
            --------------------------------------------*/
            $('#createNewCustomer').click(function () {
                $('#saveBtn').val("create-customer");
                $('#customer_id').val('');
                $('#customerForm').trigger("reset");
                $('#modelHeading').html("{{__('Create New Customer')}}");
                $('#ajaxModel').modal('show');
            });

            /*------------------------------------------
            --------------------------------------------
            Click to Edit Button
            --------------------------------------------
            --------------------------------------------*/
            $('body').on('click', '.editProduct', function () {
                var customer_id = $(this).data('id');
                $.get("{{ route('customers-ajax-crud.index') }}" + '/' + customer_id + '/edit', function (data) {
                    $('#modelHeading').html("{{__('Edit Customer')}}");
                    $('#saveBtn').val("edit-user");
                    $('#ajaxModel').modal('show');
                    $('#customer_id').val(data.id);
                    $('#name').val(data.name);
                    $('#field').val(data.details[key][i]);
                    $('#secondField').val(data.details[value][i]);
                })
            });

            /*------------------------------------------
            --------------------------------------------
            Create Customer Code
            --------------------------------------------
            --------------------------------------------*/
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html("{{__('Saving...')}}");

                $.ajax({
                    data: $('#customerForm').serialize(),
                    url: "{{ route('customers-ajax-crud.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {

                        $('#customerForm').trigger("reset");
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
                var customer_id = $(this).data("id");
                let deleteCustomer = confirm("{{__('Are You sure want to delete !')}}");
                if (deleteCustomer) {

                    $.ajax({
                        type: "DELETE",
                        url: "{{ route('customers-ajax-crud.store') }}" + '/' + customer_id,
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
