(function($) {
'use strict';
    // Roles data table
    $(document).ready(function()
    {
        var searchable = [];
        var selectable = []; 
        

        var dTable = $('#roles_table').DataTable({

            order: [],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            processing: true,
            responsive: true,
            serverSide: true,
            processing: true,
            language: {
              processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
            },
            scroller: {
                loadingIndicator: false
            },
            pagingType: "full_numbers",
            dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
            ajax: {
                url: 'role/get-list',
                type: "get"
            },
            columns: [
                {data:'name', name: 'name', orderable: false, searchable: false},
                {data:'permissions', name: 'permissions'},
                //only those have manage_role permission will get access
                {data:'action', name: 'action'}

            ],
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn-sm btn-info',
                    title: 'Roles',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    title: 'Roles',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    title: 'Roles',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible',
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    title: 'Roles',
                    pageSize: 'A2',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    className: 'btn-sm btn-default',
                    title: 'Roles',
                    // orientation:'landscape',
                    pageSize: 'A2',
                    header: true,
                    footer: false,
                    orientation: 'landscape',
                    exportOptions: {
                        // columns: ':visible',
                        stripHtml: false
                    }
                }
            ],
            initComplete: function () {
                var api =  this.api();
                api.columns(searchable).every(function () {
                    var column = this;
                    var input = document.createElement("input");
                    input.setAttribute('placeholder', $(column.header()).text());
                    input.setAttribute('style', 'width: 140px; height:25px; border:1px solid whitesmoke;');

                    $(input).appendTo($(column.header()).empty())
                    .on('keyup', function () {
                        column.search($(this).val(), false, false, true).draw();
                    });

                    $('input', this.column(column).header()).on('click', function(e) {
                        e.stopPropagation();
                    });
                });

                api.columns(selectable).every( function (i, x) {
                    var column = this;

                    var select = $('<select style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
                        .appendTo($(column.header()).empty())
                        .on('change', function(e){
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column.search(val ? '^'+val+'$' : '', true, false ).draw();
                            e.stopPropagation();
                        });

                    $.each(dropdownList[i], function(j, v) {
                        select.append('<option value="'+v+'">'+v+'</option>')
                    });
                });
            }
        });
    });

    //users data table
    $(document).ready(function()
    {

        var searchable = [];
        var selectable = []; 
        

        var dTable = $('#user_table').DataTable({

            order: [],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            processing: true,
            responsive: false,
            serverSide: true,
            processing: true,
            language: {
              processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
            },
            scroller: {
                loadingIndicator: false
            },
            pagingType: "full_numbers",
            dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
            ajax: {
                url: 'user/get-list',
                type: "get"
            },
            columns: [
                /*{data:'serial_no', name: 'serial_no'},*/
                {data:'id', name: 'id',orderable: false, searchable: false},
                {data:'name', name: 'name', orderable: false, searchable: false},
                {data:'email', name: 'email'},
                {data:'roles', name: 'roles'},
                {data:'permissions', name: 'permissions'},
                //only those have manage_user permission will get access
                {data:'action', name: 'action'}

            ],
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn-sm btn-info',
                    title: 'Users',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    title: 'Users',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    title: 'Users',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible',
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    title: 'Users',
                    pageSize: 'A2',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    className: 'btn-sm btn-default',
                    title: 'Users',
                    // orientation:'landscape',
                    pageSize: 'A2',
                    header: true,
                    footer: false,
                    orientation: 'landscape',
                    exportOptions: {
                        // columns: ':visible',
                        stripHtml: false
                    }
                }
            ],
            initComplete: function () {
                var api =  this.api();
                api.columns(searchable).every(function () {
                    var column = this;
                    var input = document.createElement("input");
                    input.setAttribute('placeholder', $(column.header()).text());
                    input.setAttribute('style', 'width: 140px; height:25px; border:1px solid whitesmoke;');

                    $(input).appendTo($(column.header()).empty())
                    .on('keyup', function () {
                        column.search($(this).val(), false, false, true).draw();
                    });

                    $('input', this.column(column).header()).on('click', function(e) {
                        e.stopPropagation();
                    });
                });

                api.columns(selectable).every( function (i, x) {
                    var column = this;

                    var select = $('<select style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
                        .appendTo($(column.header()).empty())
                        .on('change', function(e){
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column.search(val ? '^'+val+'$' : '', true, false ).draw();
                            e.stopPropagation();
                        });

                    $.each(dropdownList[i], function(j, v) {
                        select.append('<option value="'+v+'">'+v+'</option>')
                    });
                });
            }
        });
    });

    //smart report
    $(document).ready(function()
    {

        var searchable = [];
        var selectable = []; 
        var getUrl = window.location;
        var base_path = getUrl .protocol + "//" + getUrl.host;
        
        function format ( d ) {
            return function(){
                var images = d.images.split(',');
                console.log(images)
                var temp='';
                images.forEach(function (item, index) {
                    console.log(item)
                    temp = "<img style='width:250px' class='smart-image' src='"+base_path+'/uploads/'+item+"'/> "+temp
                });
                console.log(temp)
                return temp;
            }        
        }
        
        var dTableSmart = $('#smart_table').DataTable({  
            order: [],
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            processing: true,
            responsive: true,
            serverSide: true,
            processing: true,
            searching: true, 
            destroy: true,
            scrollX:false,
            ordering:true,
            language: {
              processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
            },
            scroller: {
                loadingIndicator: false
            },
            pagingType: "full_numbers",
            dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
            ajax: {
                url: 'smart-report-data',
                type: "get"
            },
            columns: [
                {
                    "class":          "details-control",
                    "orderable":      true,
                    "data":           null,
                    "defaultContent": ""
                },
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'id', name: 'id'},
                {data:'user_id', name: 'user_id'},
                {data:'smart_type', name: 'smart_type'},
                {data:'mobile_no', name: 'mobile_no'},
                {data:'credit', name: 'credit'},
                {data:'status', name: 'status'},
                {data:'status_code', name: 'status_code'},
                {data:'created_at', name: 'created_at'},
                {data:'delivery_time', name: 'delivery_time'},
                {data:'read_time', name: 'read_time'},
                {data:'messages', name: 'messages'}
                // { data: 'images', name: 'images',
                //     render: function( data, type, full, meta ) {
                //         if(data!=null)
                //             return "<img src='"+base_path+"/uploads/"+data+"' height='50'/>";
                //         else
                //         return "<p>No Image Found</p>";     
                //     }
                // },
            ],
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn-sm btn-info',
                    title: 'Smart Report',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-sm btn-success',
                    title: 'Smart Report',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-sm btn-warning',
                    title: 'Smart Report',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible',
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-primary',
                    title: 'Smart Report',
                    pageSize: 'A2',
                    header: false,
                    footer: true,
                    exportOptions: {
                        // columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    className: 'btn-sm btn-default',
                    title: 'Smart Report',
                    // orientation:'landscape',
                    pageSize: 'A2',
                    header: true,
                    footer: false,
                    orientation: 'landscape',
                    exportOptions: {
                        // columns: ':visible',
                        stripHtml: false
                    }
                }
            ],
            initComplete: function () {
                var api =  this.api();
                api.columns(searchable).every(function () {
                    var column = this;
                    var input = document.createElement("input");
                    input.setAttribute('placeholder', $(column.header()).text());
                    input.setAttribute('style', 'width: 140px; height:25px; border:1px solid whitesmoke;');

                    $(input).appendTo($(column.header()).empty())
                    .on('keyup', function () {
                        column.search($(this).val(), false, false, true).draw();
                    });

                    $('input', this.column(column).header()).on('click', function(e) {
                        e.stopPropagation();
                    });
                });

                api.columns(selectable).every( function (i, x) {
                    var column = this;

                    var select = $('<select style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
                        .appendTo($(column.header()).empty())
                        .on('change', function(e){
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            column.search(val ? '^'+val+'$' : '', true, false ).draw();
                            e.stopPropagation();
                        });

                    $.each(dropdownList[i], function(j, v) {
                        select.append('<option value="'+v+'">'+v+'</option>')
                    });
                });
            }
        });

        // Array to track the ids of the details displayed rows
    var detailRows = [];
 
    $('#smart_table tbody').on( 'click', 'tr td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = dTableSmart.row( tr );
        var idx = $.inArray( tr.attr('id'), detailRows );
 
        if ( row.child.isShown() ) {
            tr.removeClass( 'details' );
            row.child.hide();
 
            // Remove from the 'open' array
            detailRows.splice( idx, 1 );
        }
        else {
            tr.addClass( 'details' );
            row.child( format( row.data() ) ).show();
 
            // Add to the 'open' array
            if ( idx === -1 ) {
                detailRows.push( tr.attr('id') );
            }
        }
    } );
 
    // On each draw, loop over the `detailRows` array and show any child rows
    dTableSmart.on( 'draw', function () {
        $.each( detailRows, function ( i, id ) {
            $('#'+id+' td.details-control').trigger( 'click' );
        } );
    } );
    });

     //campaign report
     $(document).ready(function()
     {
 
         var searchable = [];
         var selectable = []; 
         var getUrl = window.location;
         var base_path = getUrl .protocol + "//" + getUrl.host;
         
         function format ( d ) {
             return function(){
                 var images = d.images.split(',');
                 console.log(images)
                 var temp='';
                 images.forEach(function (item, index) {
                     console.log(item)
                     temp = "<img  style='width:250px' class='smart-image' src='"+base_path+'/uploads/'+item+"'/> "+temp
                 });
                 console.log(temp)
                 return temp;
             }        
         }
         
         var dTableCampgain = $('#campaign_table').DataTable({  
             order: [],
             lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
             processing: true,
             responsive: true,
             serverSide: true,
             processing: true,
             searching: true, 
             destroy: true,
             scrollX:false,
             ordering:true,
             language: {
               processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
             },
             scroller: {
                 loadingIndicator: false
             },
             pagingType: "full_numbers",
             dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
             ajax: {
                 url: 'campaign-report-data',
                 type: "get"
             },
             columns: [
                 {
                     "class":          "details-control",
                     "orderable":      true,
                     "data":           null,
                     "defaultContent": ""
                 },
                 {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                 {data: 'id', name: 'id'},
                 {data:'smart_type',name:'smart_type'},
                 {data:'user_id', name: 'user_id'},
                 {data:'name', name: 'name'},
                 {data:'email', name: 'email'},
                 {data:'mobile_no', name: 'mobile_no'},
                 {data:'sms_count', name: 'sms_count'},
                 {data:'sms_failed', name: 'sms_failed'},
                 {data:'sms_success', name: 'sms_success'},
                 {data:'messages', name: 'messages'},
                 {data:'status', name: 'status'},
                 {data:'download', name: 'download'},
                 {data:'action', name: 'action'},
                 // { data: 'images', name: 'images',
                 //     render: function( data, type, full, meta ) {
                 //         if(data!=null)
                 //             return "<img src='"+base_path+"/uploads/"+data+"' height='50'/>";
                 //         else
                 //         return "<p>No Image Found</p>";     
                 //     }
                 // },
             ],
             buttons: [
                 {
                     extend: 'copy',
                     className: 'btn-sm btn-info',
                     title: 'Campgain Report',
                     header: false,
                     footer: true,
                     exportOptions: {
                         // columns: ':visible'
                     }
                 },
                 {
                     extend: 'csv',
                     className: 'btn-sm btn-success',
                     title: 'Campgain Report',
                     header: false,
                     footer: true,
                     exportOptions: {
                         // columns: ':visible'
                     }
                 },
                 {
                     extend: 'excel',
                     className: 'btn-sm btn-warning',
                     title: 'Campgain Report',
                     header: false,
                     footer: true,
                     exportOptions: {
                         // columns: ':visible',
                     }
                 },
                 {
                     extend: 'pdf',
                     className: 'btn-sm btn-primary',
                     title: 'Campgain Report',
                     pageSize: 'A2',
                     header: false,
                     footer: true,
                     exportOptions: {
                         // columns: ':visible'
                     }
                 },
                 {
                     extend: 'print',
                     className: 'btn-sm btn-default',
                     title: 'Campgain Report',
                     // orientation:'landscape',
                     pageSize: 'A2',
                     header: true,
                     footer: false,
                     orientation: 'landscape',
                     exportOptions: {
                         // columns: ':visible',
                         stripHtml: false
                     }
                 }
             ],
             initComplete: function () {
                 var api =  this.api();
                 api.columns(searchable).every(function () {
                     var column = this;
                     var input = document.createElement("input");
                     input.setAttribute('placeholder', $(column.header()).text());
                     input.setAttribute('style', 'width: 140px; height:25px; border:1px solid whitesmoke;');
 
                     $(input).appendTo($(column.header()).empty())
                     .on('keyup', function () {
                         column.search($(this).val(), false, false, true).draw();
                     });
 
                     $('input', this.column(column).header()).on('click', function(e) {
                         e.stopPropagation();
                     });
                 });
 
                 api.columns(selectable).every( function (i, x) {
                     var column = this;
 
                     var select = $('<select style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
                         .appendTo($(column.header()).empty())
                         .on('change', function(e){
                             var val = $.fn.dataTable.util.escapeRegex(
                                 $(this).val()
                             );
                             column.search(val ? '^'+val+'$' : '', true, false ).draw();
                             e.stopPropagation();
                         });
 
                     $.each(dropdownList[i], function(j, v) {
                         select.append('<option value="'+v+'">'+v+'</option>')
                     });
                 });
             }
         });
 
         // Array to track the ids of the details displayed rows
     var detailRows = [];
  
     $('#campaign_table tbody').on( 'click', 'tr td.details-control', function () {
         var tr = $(this).closest('tr');
         var row = dTableCampgain.row( tr );
         var idx = $.inArray( tr.attr('id'), detailRows );
  
         if ( row.child.isShown() ) {
             tr.removeClass( 'details' );
             row.child.hide();
  
             // Remove from the 'open' array
             detailRows.splice( idx, 1 );
         }
         else {
             tr.addClass( 'details' );
             row.child( format( row.data() ) ).show();
  
             // Add to the 'open' array
             if ( idx === -1 ) {
                 detailRows.push( tr.attr('id') );
             }
         }
     } );
  
     // On each draw, loop over the `detailRows` array and show any child rows
     dTableCampgain.on( 'draw', function () {
         $.each( detailRows, function ( i, id ) {
             $('#'+id+' td.details-control').trigger( 'click' );
         } );
     } );
     });
 
     //account report
     $(document).ready(function()
     {
 
         var searchable = [];
         var selectable = []; 
         
 
         var dTable = $('#account_report').DataTable({
 
             order: [],
             lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
             processing: true,
             responsive: false,
             serverSide: true,
             processing: true,
             language: {
               processing: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;"></i>'
             },
             scroller: {
                 loadingIndicator: false
             },
             pagingType: "full_numbers",
             dom: "<'row'<'col-sm-2'l><'col-sm-7 text-center'B><'col-sm-3'f>>tipr",
             ajax: {
                 url: '/account-report',
                 type: "get"
             },
             columns: [
                 {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                 {data: 'user_id', name: 'user_id'},
                 {data: 'name', name: 'name'},
                 {data: 'mobile_no', name: 'mobile_no'},
                 {data: 'type', name: 'type'},
                 {data: 'balance', name: 'balance'},
                 {data: 'validity', name: 'validity'},
                 {data: 'credit_remaining', name: 'credit_remaining'},
                 {data: 'created_at', name: 'created_at'},
                 {data: 'managed_by', name: 'managed_by'},
             ],
             buttons: [
                 {
                     extend: 'copy',
                     className: 'btn-sm btn-info',
                     title: 'Account Report',
                     header: false,
                     footer: true,
                     exportOptions: {
                         // columns: ':visible'
                     }
                 },
                 {
                     extend: 'csv',
                     className: 'btn-sm btn-success',
                     title: 'Account Report',
                     header: false,
                     footer: true,
                     exportOptions: {
                         // columns: ':visible'
                     }
                 },
                 {
                     extend: 'excel',
                     className: 'btn-sm btn-warning',
                     title: 'Account Report',
                     header: false,
                     footer: true,
                     exportOptions: {
                         // columns: ':visible',
                     }
                 },
                 {
                     extend: 'pdf',
                     className: 'btn-sm btn-primary',
                     title: 'Account Report',
                     pageSize: 'A2',
                     header: false,
                     footer: true,
                     exportOptions: {
                         // columns: ':visible'
                     }
                 },
                 {
                     extend: 'print',
                     className: 'btn-sm btn-default',
                     title: 'Account Report',
                     // orientation:'landscape',
                     pageSize: 'A2',
                     header: true,
                     footer: false,
                     orientation: 'landscape',
                     exportOptions: {
                         // columns: ':visible',
                         stripHtml: false
                     }
                 }
             ],
             initComplete: function () {
                 var api =  this.api();
                 api.columns(searchable).every(function () {
                     var column = this;
                     var input = document.createElement("input");
                     input.setAttribute('placeholder', $(column.header()).text());
                     input.setAttribute('style', 'width: 140px; height:25px; border:1px solid whitesmoke;');
 
                     $(input).appendTo($(column.header()).empty())
                     .on('keyup', function () {
                         column.search($(this).val(), false, false, true).draw();
                     });
 
                     $('input', this.column(column).header()).on('click', function(e) {
                         e.stopPropagation();
                     });
                 });
 
                 api.columns(selectable).every( function (i, x) {
                     var column = this;
 
                     var select = $('<select style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
                         .appendTo($(column.header()).empty())
                         .on('change', function(e){
                             var val = $.fn.dataTable.util.escapeRegex(
                                 $(this).val()
                             );
                             column.search(val ? '^'+val+'$' : '', true, false ).draw();
                             e.stopPropagation();
                         });
 
                     $.each(dropdownList[i], function(j, v) {
                         select.append('<option value="'+v+'">'+v+'</option>')
                     });
                 });
             }
         });
     });
    $('select').select2();
})(jQuery);