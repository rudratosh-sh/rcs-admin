(function ($) {
  'use strict';
  //for basic smart message
  $(function () {
    $('.file-upload-browse-basic').on('click', function () {
      var file = $(this).parent().parent().parent().find('.file-upload-default');
      file.trigger('click');
    });
    $('.file-upload-default-basic').on('change', function () {
      // $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
      // $(this).parent().find('.form-control').val() 
      $("#image-alert-basic").show();
      $("#image-alert-basic").html('<strong>Image Selected! </strong>'+$(this).val().replace(/C:\\fakepath\\/i, '')+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i class="ik ik-x"></i> </button>');
    });
  });

  //for advance smart message
  $(function () {
    $('.file-upload-browse-card-1').on('click', function () {
      var file = $(this).parent().parent().parent().find('.file-upload-default-card-1');
      file.trigger('click');
    });
    $('.file-upload-default-card-1').on('change', function () {
      // $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
      // console.log(/C:\\fakepath\\/i);
      $("#image-alert-card-1").show();
      $("#image-alert-card-1").html('<strong>Image Selected! </strong>'+$(this).val().replace(/C:\\fakepath\\/i, '')+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i class="ik ik-x"></i> </button>');
    });
  });

  $(function () {
    $('.file-upload-browse-card-2').on('click', function () {
      var file = $(this).parent().parent().parent().find('.file-upload-default-card-2');
      file.trigger('click');
    });
    $('.file-upload-default-card-2').on('change', function () {
      // $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
      // console.log(/C:\\fakepath\\/i);
      $("#image-alert-card-2").show();
      $("#image-alert-card-2").html('<strong>Image Selected! </strong>'+$(this).val().replace(/C:\\fakepath\\/i, '')+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i class="ik ik-x"></i> </button>');
    });
  });

  $(function () {
    $('.file-upload-browse-card-3').on('click', function () {
      var file = $(this).parent().parent().parent().find('.file-upload-default-card-3');
      file.trigger('click');
    });
    $('.file-upload-default-card-3').on('change', function () {
      // $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
      // console.log(/C:\\fakepath\\/i);
      $("#image-alert-card-3").show();
      $("#image-alert-card-3").html('<strong>Image Selected! </strong>'+$(this).val().replace(/C:\\fakepath\\/i, '')+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i class="ik ik-x"></i> </button>');
    });
  });

  $(function () {
    $('.file-upload-browse-card-4').on('click', function () {
      var file = $(this).parent().parent().parent().find('.file-upload-default-card-4');
      file.trigger('click');
    });
    $('.file-upload-default-card-4').on('change', function () {
      // $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));
      // console.log(/C:\\fakepath\\/i);
      $("#image-alert-card-4").show();
      $("#image-alert-card-4").html('<strong>Image Selected! </strong>'+$(this).val().replace(/C:\\fakepath\\/i, '')+'<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i class="ik ik-x"></i> </button>');
    });
  });
})(jQuery);

$(document).ready(function () {
  $('#mobile-numbers').tagsinput('items');
  
  //count mobile number 
  $("#countMobileNumbers").on('click',function(){
    //console.log($("#mobile-numbers").val().split(","));
    //const count = $("#mobile-numbers").val().split(",").length;
    alert($("#mobile-numbers").tagsinput('items').length);

  })
 	 
  //clear mobile number fields
  $("#clearMobileNumbers").on('click',function(){
    $("#mobile-numbers").tagsinput('removeAll');
  })

  //limit text message to 1000
  $("#messageText").keypress(function() {  
    var maxlen = 1000;
    if ($(this).val().length > maxlen) {  
      return false;
    }  
  })

  //advance message card 1
  if($('.js-success-card-1').length > 0){     
    var elemprimary = document.querySelector('.js-success-card-1');
    var switchery = new Switchery(elemprimary, {
        color: '#2ed8b6',
        jackColor: '#fff'
    });
}
 //advance message card 2
 if($('.js-success-card-2').length > 0){     
  var elemprimary = document.querySelector('.js-success-card-2');
  var switchery = new Switchery(elemprimary, {
      color: '#2ed8b6',
      jackColor: '#fff'
  });
}

 //advance message card 3
 if($('.js-success-card-3').length > 0){     
  var elemprimary = document.querySelector('.js-success-card-3');
  var switchery = new Switchery(elemprimary, {
      color: '#2ed8b6',
      jackColor: '#fff'
  });
}

 //advance message card 4
 if($('.js-success-card-4').length > 0){     
  var elemprimary = document.querySelector('.js-success-card-4');
  var switchery = new Switchery(elemprimary, {
      color: '#2ed8b6',
      jackColor: '#fff'
  });
}

//select atleast two cards
$(".submit-btn").on('click',function(e){
  e.preventDefault();
  console.log($('.selected-card:checkbox:checked').length)
  if($('.selected-card:checkbox:checked').length < 1){     
    alert('Atleast two cards have to be selected')
    return false;
 }
  $(this).unbind('click').click();
})

});
