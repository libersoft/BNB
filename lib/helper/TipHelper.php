<?php

function tip_usage($text)
{
  return 'Tip(\'<div class=\\\'tip-usage\\\'>' . str_replace("\n", '', nl2br(htmlentities(str_replace('\'', '', $text), ENT_QUOTES, 'UTF-8'))) . '</div>\', BORDERWIDTH, 0, OPACITY, 90, PADDING, 0, DELAY, 0)';
}

function tip_note($text)
{
    return 'Tip(\'<div class=\\\'tip-note\\\'>' . str_replace("\n", '', nl2br(htmlentities(str_replace('\'', '', $text), ENT_QUOTES, 'UTF-8'))) . '</div>\', BORDERWIDTH, 0, OPACITY, 90, PADDING, 0, DELAY, 0, FOLLOWMOUSE, false)';
}

function tip_info($text)
{
    return 'Tip(\'<div class=\\\'tip-info\\\'>' . str_replace("\n", '', nl2br(htmlentities(str_replace('\'', '', $text), ENT_QUOTES, 'UTF-8'))) . '</div>\', BORDERWIDTH, 0, OPACITY, 90, PADDING, 0, DELAY, 0, FOLLOWMOUSE, false)';
}

function untip()
{
  return 'UnTip()';
}
