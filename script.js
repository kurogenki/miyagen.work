$(function() {
 

  $(window).on('scroll', function() {

    var heading = $('.slide-heading');

    heading.each(function() {

    var headingoffset =$(this).offset().top;
    var scroll =$(window).scrollTop();
    var wh =$(window).height();

    if(scroll > headingoffset - wh
    ) {
        $(this).addClass('active');
    }
  });
});

$(".slide-items").slick({
  autoplay: true, // 自動再生
  autoplaySpeed: 3000, // 再生速度（ミリ秒設定） 1000ミリ秒=1秒
  infinite: true, // 無限スライド
  arrows: true, // 矢印
});

$('#hamburger').on('click', function(){
  $('.icon').toggleClass('close');
  $('.sm').slideToggle();
  });


});