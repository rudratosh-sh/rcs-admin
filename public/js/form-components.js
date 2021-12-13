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

  // //limit text message to 1000
  // $("#messageText").keypress(function() {  
  //   var maxlen = 1000;
  //   if ($(this).val().length > maxlen) {  
  //     return false;
  //   }  
  // })

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


    /****TEXT AREA COUNT */
    //Now Assuming your text area has an id of "#text"

    // var text = document.getElementById('#messageText').val();

    // var trim = text.trim();

    var characters = 1000;  //you could change the number of characters you want    

    // $("#counter").append("You have <strong>" + characters + "</strong> characters remaining");

    $("#messageText").on("keyup change", function(e) {
        console.log(e)
        if ($(this).val().length > characters) {
            $(this).val($(this).val().substr(0, characters));
        }

        var remaining = characters - $(this).val().length;
        $("#counter").html("You have <strong>" + remaining + "</strong> characters remaining");
        if (remaining <= 10) {
            $("#counter").css("color", "red");
        }
        else {
            $("#counter").css("color", "black");
        }

    });

    /**** Advance message box 1 */
    var charactersBox1 = 200;  //you could change the number of characters you want  
    $("#messageTextCard1").on("keyup change", function(e) {
      console.log(e)
      if ($(this).val().length > charactersBox1) {
          $(this).val($(this).val().substr(0, charactersBox1));
      }

      var remaining = charactersBox1 - $(this).val().length;
      $("#counterBox1").html("You have <strong>" + remaining + "</strong> characters remaining");
      if (remaining <= 10) {
          $("#counterBox1").css("color", "red");
      }
      else {
          $("#counterBox1").css("color", "black");
      } 
  });

  /**** Advance message box 2 */
  var charactersBox2 = 200;  //you could change the number of characters you want  
  $(".messageTextCard2").on("keyup change", function(e) {
    console.log(e)
    if ($(this).val().length > charactersBox2) {
        $(this).val($(this).val().substr(0, charactersBox2));
    }

    var remaining = charactersBox2 - $(this).val().length;
    $("#counterBox2").html("You have <strong>" + remaining + "</strong> characters remaining");
    if (remaining <= 10) {
        $("#counterBox2").css("color", "red");
    }
    else {
        $("#counterBox2").css("color", "black");
    } 
});

/**** Advance message box 3 */
var charactersBox3 = 200;  //you could change the number of characters you want  
$(".messageTextCard3").on("keyup change", function(e) {
  console.log(e)
  if ($(this).val().length > charactersBox3) {
      $(this).val($(this).val().substr(0, charactersBox3));
  }

  var remaining = charactersBox3 - $(this).val().length;
  $("#counterBox3").html("You have <strong>" + remaining + "</strong> characters remaining");
  if (remaining <= 10) {
      $("#counterBox3").css("color", "red");
  }
  else {
      $("#counterBox3").css("color", "black");
  } 
});

/**** Advance message box 4 */
var charactersBox4 = 200;  //you could change the number of characters you want  
$(".messageTextCard4").on("keyup change", function(e) {
  console.log(e)
  if ($(this).val().length > charactersBox4) {
      $(this).val($(this).val().substr(0, charactersBox4));
  }

  var remaining = charactersBox4 - $(this).val().length;
  $("#counterBox4").html("You have <strong>" + remaining + "</strong> characters remaining");
  if (remaining <= 10) {
      $("#counterBox4").css("color", "red");
  }
  else {
      $("#counterBox4").css("color", "black");
  } 
});
});
