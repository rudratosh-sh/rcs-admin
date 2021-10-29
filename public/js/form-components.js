(function ($) {
  'use strict';
  $(function () {
    $('.file-upload-browse').on('click', function () {
      var file = $(this).parent().parent().parent().find('.file-upload-default');
      file.trigger('click');
    });
    $('.file-upload-default').on('change', function () {
      $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
    });
  });
})(jQuery);

$(document).ready(function () {
  $('#mobile-numbers').tagsinput('items');
  
  //count mobile number 
  $("#countMobileNumbers").on('click',function(){
    console.log($("#mobile-numbers").val().split(","));
    const count = $("#mobile-numbers").val().split(",").length;
    alert('Total Mobile Numbers: '+count);
  })
 	 
  //clear mobile number fields
  $("#clearMobileNumbers").on('click',function(){
    $("#mobile-numbers").val('');
  })

  //limit text message to 1000
  $("#messageText").keypress(function() {  
    var maxlen = 1000;
    if ($(this).val().length > maxlen) {  
      return false;
    }  
  })

});