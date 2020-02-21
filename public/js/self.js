$(function(){
  var entry_url = $("#app_url").val();

  $("#edit_start").click(function(){
      var post_id = $("#post_id").val();
      location.href = entry_url + "edit.php?post_id=" + post_id;
  });
});