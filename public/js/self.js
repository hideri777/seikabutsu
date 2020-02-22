$(function() {
  var app_url = $("#app_url").val();
  var entry_url = $("#entry_url").val();

  $("#edit_start").click(function() {
    var post_id = $("#post_id").val();
    location.href = app_url + "edit.php?post_id=" + post_id;
  });

  $("#send_comment").click(function() {
    var user_id = $("#login_user_id").val();
    var comment_text = $("#comment_text").val();
    var post_id = $("#post_id").val();

    $.ajax({
      url: entry_url + "/functions/commentProvider.php",
      type: "post",
      dataType: 'json',
      data: {
        user_id: user_id,
        comment: comment_text,
        post_id: post_id,
      },
    }).then(
      function(data) {
        $(".comment_area").append('<p>' + data.post_id + '</p>');
        $(".comment_area").append('<p>' + data.user_id + '</p>');
        $(".comment_area").append('<p>' + data.comment +'</p>');
        $("#comment_text").val('');
      },
      function() {
        alert('特殊文字は入力できません');
      },
    );
  });

  $("#good_button").click(function() {
    var user_id = $("#login_user_id").val();
    var post_id = $("#post_id").val();

    $.ajax({
      url: entry_url + "/functions/likedProvider.php",
      type: "post",
      data: {
        user_id: user_id,
        post_id: post_id,
      },
    }).then(
      function(data) {
        // いいね数を1増減させる
      },
      function() {
        alert('うまく行かなかったようです。。');
      },
    );
  });
});
