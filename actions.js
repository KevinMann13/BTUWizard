var comingFromAdvanced = false;

$(function() {
  var slideshow = $("#slideshow");

  $(slideshow).slick({
    infinite: false,
    initialSlide: 0,
    adaptiveHeight: true,
    draggable: false,
    arrows: false
  });

  $(".btn_next").click(function(e) {
    e.preventDefault();
    $(slideshow).slick('slickNext');
  })

  $(".btn_back").click(function(e) {
    e.preventDefault();
    $(slideshow).slick('slickPrev');
  })

  $(".btn_back").click(function(e) {
    e.preventDefault();
    $(slideshow).slick('slickPrev');
  })

  $("#btn_to_disclaimer").click(function(e) {
    e.preventDefault();
    $(slideshow).slick('slickGoTo', 6, true);
  });

  $("#btn_disclaimer_back").click(function(e) {
    e.preventDefault();
    if (!comingFromAdvanced) {
      $(slideshow).slick('slickPrev');
    } else {
      $(slideshow).slick('slickGoTo', 2, true);
    }
  })

  $(".btn_back_to_start").click(function(e) {
    e.preventDefault();
    $(slideshow).slick('slickGoTo', 0, true);
  });

  $("#btn_advanced").click(function(e) {
    e.preventDefault();
    comingFromAdvanced = true;
    $("#form_type").val("advanced");
    $(slideshow).slick('slickGoTo', 1, true);
  })

  $(".btn_advanced_after_params").click(function(e) {
    e.preventDefault();
    setupWalls();
  })

  $("#btn_simple").click(function(e) {
    e.preventDefault();
    comingFromAdvanced = false;
    $("#form_type").val("simple");
    $(slideshow).slick('slickGoTo', 3, true);
  });

  $(".temp").click(function(e) {
    e.preventDefault();
    $(this).toggleClass('selected').siblings().removeClass("selected");
    $("#exterior_temp").val($(this).data("temp"));
  });

  $("#btn_submit").click(function(e){
    e.preventDefault();
    submitForm();
  });

  $(".enabler > input").change(function() {
    var id = $(this).attr("id");
    if ($(this).is(":checked")) {
      $("#" + id.substr(7)).show();
    } else {
      $("#" + id.substr(7)).hide();
    }
  });
});

function submitForm() {
  var url = "api/simpleCalc.php"; // the script where you handle the form input.
  $.ajax({
    type: "POST",
    url: url,
    data: $("#cellar_form").serialize(), // serializes the form's elements.
    success: function(data) {
      fillOutResults(data);
    }
  });
}

function fillOutResults(data) {
  var results = jQuery.parseJSON(data);

  if ($("#form_type").val() == "simple") {
    $("#simple_inputs").show();
    $("#advanced_inputs").hide();

    if (results.glass_dimensions == "' x '" || results.glass_dimensions == "0' x 0'") {
      $(".glass_output").hide();
    } else {
      $(".glass_output").show();
    }

    $("#summary_room_dimensions").text(results.dimensions);
    $("#summary_wall_material").text(results.wall_material);
    $("#summary_glass_dimensions").text(results.glass_dimensions);
    $("#summary_glass_material").text(results.glass_material);
    $("#exterior_temp_output").text(results.exterior_temp);
  } else {
    $("#simple_inputs").hide();
    $("#advanced_inputs").show();

    var x = 1;
    $(".summary_output").hide();
    results.walls.forEach(function(wall) {
      var prefix = "#summary_wall_" + x;
      $(prefix).show();
      $(prefix + "_height").text(wall.height);
      $(prefix + "_width").text(wall.width);
      $(prefix + "_material").text(wall.contruction_type);
      $(prefix + "_exterior_temp").text(wall.exterior_temp);

      if (wall.doors.length > 0) {
        prefix = "#summary_door_" + x;
        $(prefix).show()
        $(prefix + "_height").text(wall.doors[0].height);
        $(prefix + "_width").text(wall.doors[0].width);
        $(prefix + "_material").text(wall.doors[0].contruction_type);
      }

      if (wall.windows.length > 0) {
        prefix = "#summary_window_" + x;
        $(prefix).show()
        $(prefix + "_height").text(wall.windows[0].height);
        $(prefix + "_width").text(wall.windows[0].width);
        $(prefix + "_material").text(wall.windows[0].contruction_type);
      }

      x++;
    });
    prefix = "#summary_ceiling_"
    $(prefix + "height").text(results.ceiling.height);
    $(prefix + "width").text(results.ceiling.width);
    $(prefix + "material").text(results.ceiling.contruction_type);
    $(prefix + "exterior_temp").text(results.ceiling.exterior_temp);

    prefix = "#summary_floor_"
    $(prefix + "height").text(results.floor.height);
    $(prefix + "width").text(results.floor.width);
    $(prefix + "material").text(results.floor.contruction_type);
    $(prefix + "exterior_temp").text(results.floor.exterior_temp);
  }

  $("#load_output").html(results.load + " BTU/h");

  var systems = "";
  $.each(results.models, function(key, value) {
    if (typeof cc !== 'undefined') {
      url = "http://www.cellarcool.com/cooling-systems/";
      image = "images/units/cc/" + value['image'] + ".jpg";
    } else {
      url = value["link"];
      image = "images/units/wk/" + value['image'] + ".jpg";
    }

    if (typeof cc !== 'undefined' || typeof wk !== 'undefined') {
      systems += "<a href='" + url + "' target='_blank'>";
    }

    systems += "<div class='unit'>";
    systems += "<img src='" + image + "'>";
    systems += "<div class='model'>" + value['model'] + "</div>";
    systems += "<div class='btuh'>" + value['btuh'] + " BTUh</div>";
    systems += "<div class='series'>" + value['series'] + "</div>";
    systems += "</div>";

    if (typeof cc !== 'undefined' || typeof wk !== 'undefined') {
      systems += "</a>";
    }
  });
  $("#recommended_units").html(systems);

  if( results.models.length == 0 ){
    var nounits_message = "<b>The BTU Load is larger than the capacity of our single systems.  PLEASE CALL WHISPERKOOL SALES at 800-343-9463 Ext 2 for assistance.</b>"
    $("#recommended_units").html(nounits_message);
  }

  $(slideshow).slick('slickGoTo', 7, comingFromAdvanced);
}

function setupWalls() {
  var walls = $("#num_walls").val();
  for (var i = 1; i <= 8; i++) {
    if (i <= walls) {
      $("#wall_" + i).show(0);
      $("#wall" + i + "_temp").val($("#conditioned_temp").val());
    } else {
      $("#wall_" + i).hide(0);
      $("#door_" + i).hide(0);
      $("#window_" + i).hide(0);
    }
  }
}
