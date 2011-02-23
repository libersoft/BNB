<ul class="language_switch">
  <?php foreach ($available_languages as $id => $title): ?>
    <?php if ($id != $sf_user->getCulture()): ?>
      <li>
        <?php echo link_to(image_tag('flags/' . $id . '.png', array('alt' => $title))/* . '&nbsp;' . $title*/, 'reservation/' . $action_name . '?sf_culture=' . $id) ?>
      </li>
    <?php endif ?>
  <?php endforeach ?>
</ul>
