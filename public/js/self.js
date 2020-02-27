$(function() {
  var app_url = $("#app_url").val();
  var entry_url = $("#entry_url").val();

  $(".edit_start").click(function() {
    // 対象はどのゲームか、どの投稿なのかGETを送る
    var target_game_id = $("#target_game_id").val();
    var post_id = $("#get_post_id").val();
    location.href = app_url + "edit.php?target_game_id=" + target_game_id + "&post_id=" + post_id;
  });

  $("#send_comment").click(function() {
    var user_id = $("#login_user_id").val();
    var comment_text = $("#comment_text").val();
    var post_id = $("#post_id").val();

    $.ajax({
      url: entry_url + "/functions/commentProvider.php",
      type: "post",
      dataType: "json",
      data: {
        user_id: user_id,
        comment: comment_text,
        post_id: post_id
      }
    }).then(
      function(data) {
        console.log(data);
        $(".comment_area").append("<p>" + data.post_id + "</p>");
        $(".comment_area").append("<p>" + data.user_id + "</p>");
        $(".comment_area").append("<p>" + data.comment + "</p>");
        $("#comment_text").val("");
      },
      function() {
        alert("特殊文字は入力できません");
      }
    );
  });

  $(".good_button").click(function() {
    var isFirst = false;
    var user_id = $("#login_user_id").val();
    var post_id = $(this).attr("id");
    if ($(".isLiked_" + post_id).val() == "first") {
      isFirst = true;
    } else if ($(".isLiked_" + post_id).val() == 1) {
      var isLiked = true;
    } else {
      var isLiked = false;
    }

    $.ajax({
      url: entry_url + "/functions/likedProvider.php",
      type: "post",
      dataType: "json",
      data: {
        user_id: user_id,
        post_id: post_id,
        isLiked: isLiked,
        isFirst: isFirst
      }
    }).then(
      function(data) {
        // いいね数を1増減させる
        console.log(data);
        if (data.updateLiked == 1) {
          $(".like_text_" + post_id).text("いいねしたよ");
          $(".isLiked_" + post_id).val(data.updateLiked);
        } else {
          $(".like_text_" + post_id).text("いいねしてない");
          $(".isLiked_" + post_id).val(data.updateLiked);
        }
        $("#liked_count_" + post_id).text(data.likedCount);
      },
      function() {
        alert("うまく行かなかったようです。。");
      }
    );
  });
});
