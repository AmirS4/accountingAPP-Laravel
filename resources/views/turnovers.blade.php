@extends('layouts.app')

@section('content')

    <div class="container">
        <h1>{{__('All Turnovers')}}</h1>
        <a class="btn btn-success" href="javascript:void(0)" id="createNewTurnover">{{__('Create New Turnover')}}</a>
        <table class="table table-bordered data-table">
            <thead>
            <tr>
                <th>{{__('Customer')}}</th>
                <th>{{__('Income')}}</th>
                <th>{{__('Outcome')}}</th>
                <th>{{__('Turnover Description')}}</th>
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
                    <form id="turnoversForm" name="turnoversForm" class="form-horizontal">
                        <input type="hidden" name="turnover_id" id="turnover_id">
                        <div class="form-group">
                            <label for="income">{{__('Income')}}</label>
                            <input type="text" class="form-control" id="income" name="income"
                                   placeholder="{{__("Enter Income")}}">
                        </div>

                        <div class="form-group">
                            <label for="outcome">{{__('Outcome')}}</label>
                            <input type="text" class="form-control" id="outcome" name="outcome"
                                   placeholder="{{__('Enter Outcome')}}">
                        </div>

                        <div class="form-group">
                            <label for="description">{{__('Turnover Description')}}</label>
                            <input type="text" class="form-control" id="description" name="description"
                                   placeholder="{{__('Enter Turnover Description')}}">
                        </div>

                        <div class="form-group">
                            <label for="details">{{__('Details')}}</label>
                            <input type="text" class="form-control" id="details" name="details"
                                   placeholder="{{__('Enter Details')}}">
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
@endsection

@section('scripts')
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
                ajax: "{{ route('turnovers-ajax-crud.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'customer.name', name: 'customer.name'},
                    {data: 'income', name: 'income'},
                    {data: 'outcome', name: 'outcome'},
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
            $('#createNewTurnover').click(function () {
                $('#saveBtn').val("create-turnover");
                $('#turnover_id').val('');
                $('#turnoversForm').trigger("reset");
                $('#modelHeading').html("{{__('Create New Turnover')}}");
                $('#ajaxModel').modal('show');
            });

            /*------------------------------------------
            --------------------------------------------
            Click to Edit Button
            --------------------------------------------
            --------------------------------------------*/
            $('body').on('click', '.editProduct', function () {
                var turnover_id = $(this).data('id');
                $.get("{{ route('turnovers-ajax-crud.index') }}" + '/' + turnover_id + '/edit', function (data) {
                    $('#modelHeading').html("{{__('Edit Turnover')}}");
                    $('#saveBtn').val("edit-turnover");
                    $('#ajaxModel').modal('show');
                    $('#turnover_id').val(data.id);
                    $('#income').val(data.income);
                    $('#outcome').val(data.outcome);
                    $('#description').val(data.description);
                    $('#details').val(data.details);
                })
            });

            /*------------------------------------------
            --------------------------------------------
            Create Turnover Code
            --------------------------------------------
            --------------------------------------------*/
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html("{{__('Saving...')}}");
                $.ajax({
                    data: $('#turnoversForm').serialize(),
                    url: "{{ route('turnovers-ajax-crud.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        $('#turnoversForm').trigger("reset");
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
                var turnover_id = $(this).data("id");
                confirm("{{__('Are you sure you want to delete this turnover?')}}");
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('turnovers-ajax-crud.store') }}" + '/' + turnover_id,
                    success: function (data) {
                        table.draw();
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            });
        });
    </script>
@endsection



