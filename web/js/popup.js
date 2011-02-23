function popup(id)
{
  $(id).style.display = 'block';
  $(id).style.zindex = '2000';
}

function reservation_over(reservation_class)
{
  var f = $$(reservation_class);
  for (var i = 0; i < f.length; i++)
  {
     f[i].style.backgroundColor = "#ff5500";
  }
}

function reservation_out(reservation_class)
{
  var f = $$(reservation_class);
  for (var i = 0; i < f.length; i++)
  {
     f[i].style.backgroundColor = "#00aa00";
  }
}
