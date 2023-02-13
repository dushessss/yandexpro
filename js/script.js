

$(document).ready(function () {
  

  $(".btn").click(function () {
    $("body, html").animate(
      {
        scrollTop: $(".from-scrol").offset().top - 66,
      },
      1000
    );
  });
  $(".WindowsTop").click(function () {
    $("body, html").animate(
      {
        scrollTop: $(".from-scrol").offset().top - 66,
      },
      1000
    );
  });
  $(".uslov").click(function () {
    $("body, html").animate(
      {
        scrollTop: $(".container-1246").offset().top - 116,
      },
      1000
    );
  });
  $(".treb").click(function () {
    $("body, html").animate(
      {
        scrollTop: $(".Block4").offset().top - 66,
      },
      1000
    );
  });
  $(".podkl").click(function () {
    $("body, html").animate(
      {
        scrollTop: $(".Block3").offset().top - 66,
      },
      1000
    );
  });
  $(".app").click(function () {
    $("body, html").animate(
      {
        scrollTop: $(".section7").offset().top - 66,
      },
      1000
    );
  });
  
});