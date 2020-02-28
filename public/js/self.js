$(function() {
  var app_url = $("#app_url").val();
  var entry_url = $("#entry_url").val();

  var page = $("#page-number").val();
  if($("#page-tag-" + page).val() == $("#page-number").val()) {
    $("#page-item-" + page).addClass("active");
  }

  $(".edit_start").click(function() {
    // 対象はどのゲームか、どの投稿なのかGETを送る
    var target_game_id = $("#target_game_id").val();
    var post_id = $("#get_post_id").val();
    location.href =
      app_url +
      "edit.php?target_game_id=" +
      target_game_id +
      "&post_id=" +
      post_id;
  });

  $("#delete_post").click(function() {
    var post_id = $("#get_post_id").val();
    var target_game_id = $("#target_game_id").val();

    if (!confirm("投稿を削除しますか？")) {
      /* キャンセルの時の処理 */
      return false;
    } else {
      /*OKの時の処理 */
      $.ajax({
        url: entry_url + "/functions/deletePost.php",
        type: "post",
        data: {
          post_id: post_id
        }
      }).then(
        function() {
          alert("削除しました");
          location.href = app_url + "game.php?game_id=" + target_game_id;
        },
        function() {
          alert("削除に失敗しました。再度お試しください");
        }
      );
    }
  });

  $(".delete_comment").click(function() {
    var comment_id = $(this).attr("id");

    if (!confirm("コメントを削除しますか？")) {
      /* キャンセルの時の処理 */
      return false;
    } else {
      /*OKの時の処理 */
      $.ajax({
        url: entry_url + "/functions/deleteComment.php",
        type: "post",
        data: {
          comment_id: comment_id
        }
      }).then(
        function() {
          $("#comment-list" + comment_id).remove();
          // $("#content-wrapper" + comment_id).text('削除しました');
        },
        function() {
          alert("削除に失敗しました。再度お試しください");
        }
      );
    }
  });

  $("#send_comment").click(function() {
    var user_id = $("#login_user_id").val();
    var comment_text = $("#comment_text").val();
    var post_id = $("#get_post_id").val();

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
      function(datas) {
        $(".comments-area").empty();
        $(".comments-area").append("<h4>コメント</h4>");
        datas.map(data =>
          $(".comments-area").append(
            "<div class='comment-list'><div class='single-comment justify-content-between d-flex'><div class='user justify-content-between d-flex'><div class='thumb'><img src='img/comment/comment_1.png' alt=''></div><div class='desc'><p class='comment'>" +
              data.body.replace(/\n/g, "<br>") +
              "</p><div class='d-flex justify-content-between'><div class='d-flex align-items-center'><h5><a href='#'>" +
              data.user_name +
              "</a></h5><p class='date'>" +
              data.created_date +
              "</p></div><div class='reply-btn'><a href='#' class='btn-reply text-uppercase'>reply</a></div></div></div></div></div></div>"
          )
        );
        $("#comment_text").val("");
      },
      function() {
        alert("コメントが送れませんでした。");
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
