<!DOCTYPE html>
<html>
    <head>
        <title>Event Calendar</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <body>
        <div class="container">
            <h1>Laravel FullCalender</h1>
            <div id='calendar'></div>
            <div>
                <div class="modal fade" id="editModal">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h4 id="title" style="width: 50%">Add Event</h4>
                            <div style="width: 50%; text-align: right">
                                <button class="btn btn-danger btn-sm d-none" id="hapus"><i class="fa fa-trash"></i></button>
                                <button type="button" class="close" id="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            </div>
                            <div class="modal-body">
                            <div class="form-group" id="time">
                            </div>
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" name="name" id="name">
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Description</label>
                                    <input type="text" class="form-control" name="description" id="description">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" id="cancel" data-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" data-dismiss="modal" id="save">Add</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var SITEURL = "{{ url('/') }}";
            let sd, ed, allDays, types, ids;
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
    
            var calendar = $('#calendar').fullCalendar({
                editable: true,
                events: SITEURL + "/fullcalender",
                displayEventTime: false,
                editable: true,
                eventRender: function (event, element, view) {
                    if (event.allDay === 'true') {
                            event.allDay = true;
                    } else {
                            event.allDay = false;
                    }
                },
                selectable: true,
                selectHelper: true,
                select: function (start, end, allDay) {
                    showModal(start, end, allDay, "add")
                    
                },
                eventDrop: function (event, delta) {
                    sd = $.fullCalendar.formatDate(event.start, "Y-MM-DD");
                    ed = $.fullCalendar.formatDate(event.end, "Y-MM-DD");

                    $.ajax({
                        url: SITEURL + '/fullcalenderAjax',
                        data: {
                            title: event.title,
                            start: start,
                            end: end,
                            id: event.id,
                            type: 'update'
                        },
                        type: "POST",
                        success: function (response) {
                            displayMessage("Event Updated Successfully");
                        }
                    });
                },
                eventClick: function (event) {

                    $('#save').text('Update');
                    
                    $('#title').text(event.title);
                    $('#name').val(event.title);
                    $('#description').val(event.description);
                    $('#editModal').modal();
                    showModal(event.start, event.end, event.allDay, "update", event.id)
                    

                }
            });
        });

        function showModal(s,e,a,t,id) {
            $('#save').text('save');
            if (t == 'update') {
                $('#hapus').removeClass('d-none');
            }else{
                $('#hapus').addClass('d-none');
            }
            $('#editModal').modal();
            sd = $.fullCalendar.formatDate(s, "Y-MM-DD");
            ed = $.fullCalendar.formatDate(e, "Y-MM-DD");
            days = $.fullCalendar.formatDate(s, "dddd");
            tanggal = $.fullCalendar.formatDate(s, "DD MMMM YYYY");
            format = days+", "+tanggal;
           console.log(tanggal);
            
            $('#time').html('<i class="fa fa-clock"></i>  '+format);
            allDays = a;
            types = t;
            ids = id;
        }

       
        $("#save").click(function(e){
            e.preventDefault();                     
            if (types) {
                $.ajax({
                url: "{{ url('/fullcalenderAjax') }}",
                data: {
                        _token: '{{ csrf_token() }}',
                        title: $('#name').val(),
                        description: $('#description').val(),
                        start: sd,
                        end: ed,
                        id: ids,
                        type: types
                    },
                type: "POST",
                success: function (data) {
                    console.log(data);
                    displayMessage("Event Created Successfully");
                    $('#calendar').fullCalendar( 'refetchEvents' );

                        $('#calendar').fullCalendar('unselect');

                        $('#title').text("Add Event");
                                $('#name').val('');
                                $('#description').val('');
                     }
                });
            }else{
                $.ajax({
                url: "{{ url('/fullcalenderAjax') }}",
                data: {
                        _token: '{{ csrf_token() }}',
                        title: $('#name').val(),
                        description: $('#description').val(),
                        start: sd,
                        end: ed,
                        type: 'add'
                    },
                type: "POST",
                success: function (data) {
                    
                    displayMessage("Event Created Successfully");
                    $('#calendar').fullCalendar( 'refetchEvents' );

                    // $('#calendar').fullCalendar('renderEvent',
                    //     {
                    //         id: data.id,
                    //         title: data.title,
                    //         description: data.description,
                    //         start: data.start,
                    //         end: data.end,
                    //         allDay: allDays
                    //     },true);

                        $('#calendar').fullCalendar('unselect');

                    $('#name').val('');
                    $('#description').val('');
                }
            });
            }
            
        });

        $("#hapus").click(function(e){
                var deleteMsg = confirm("Do you really want to delete?");
                    if (deleteMsg) {
                        $('#editModal').modal('hide');
                        $.ajax({
                            type: "POST",
                            url: "{{ url('/fullcalenderAjax') }}",
                            data: {
                                    id: ids,
                                    type: 'delete'
                            },
                            success: function (data) {
                                $('#calendar').fullCalendar('removeEvents', ids);
                                displayMessage("Event Deleted Successfully");
                                $('#calendar').fullCalendar( 'refetchEvents' );
                                $('#calendar').fullCalendar('unselect');
                                $('#title').text("Add Event");
                                $('#name').val('');
                                $('#description').val('');
                            }
                        });
                    }
            });

        
        $("#cancel").click(function(e){
            $('#title').text("Add Event");
            $('#name').val('');
            $('#description').val('');
        });
        $("#close").click(function(e){
            $('#title').text("Add Event");
            $('#name').val('');
            $('#description').val('');
        });
        function displayMessage(message) {
            toastr.success(message, 'Event');
        } 
  
    </script>
</html>