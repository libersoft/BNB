var last_zindex = 2000;

function popup(id)
{
    popup.x = popup.x + 20 || 20;
    popup.y = popup.y + 20 || 20;
    $(id).style.left = popup.x + 'px';
    $(id).style.top = popup.y + 'px';
    $(id).style.display = 'block';
    $(id).style.zindex = ++last_zindex;
}

function reservation_over(reservation_class)
{
    var f = $$(reservation_class);
    for (var i = 0; i < f.length; i++)
    {
        f[i].style.backgroundColor = "#FF9B74";
    }
}

function reservation_out(reservation_class)
{
    var f = $$(reservation_class);
    for (var i = 0; i < f.length; i++)
    {
        f[i].style.backgroundColor = "#85AA7C";
    }
}