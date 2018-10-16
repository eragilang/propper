window.addEvent('domready', function() {
  var error = $('news_error_box');
  if (error)
  {
      var tweener = new Fx.Tween(error, {property: "opacity", duration: 500});
      tweener.start(1);
      var close_event = function() {tweener.start(0)};
      error.addEvent('click', close_event);
      close_event.delay(5000);
  }
});